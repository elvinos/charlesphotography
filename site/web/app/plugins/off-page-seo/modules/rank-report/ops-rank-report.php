<?php

class OPS_Rank_Report
{

    /**
     * Initialization of Rank Reporter Class
     * */
    public function __construct()
    {

//        $timestamp = wp_next_scheduled('ops_rank_update');
//        wp_unschedule_event( $timestamp, 'ops_rank_update' );

        // register new keyword form
        add_action('save_post', array($this, 'ops_save_keywords'));


        // register cron if not registered already
        if (wp_next_scheduled('ops_rank_update') == '') {
            wp_schedule_event(time() + 30, 'ops_three_days', 'ops_rank_update');
        }
//        wp_schedule_event(time() + 30, 'ops_three_days', 'ops_rank_update');

        // hook action to the cron
        add_action('ops_rank_update', array($this, 'ops_rank_update_callback'));


        //  update dashboard
        add_action('wp_ajax_ops_update_dashboard_master_sort', array($this, 'ops_update_dashboard_master_sort'));
        add_action('wp_ajax_nopriv_ops_update_dashboard_master_sort', array($this, 'ops_update_dashboard_master_sort'));

        //  delete dashboard kw
        add_action('wp_ajax_ops_dashboard_delete_kw', array($this, 'ops_dashboard_delete_kw'));
        add_action('wp_ajax_nopriv_ops_dashboard_delete_kw', array($this, 'ops_dashboard_delete_kw'));

        //  update dashboard kw
        add_action('wp_ajax_ops_dashboard_update_kw', array($this, 'ops_dashboard_update_kw'));
        add_action('wp_ajax_nopriv_ops_dashboard_update_kw', array($this, 'ops_dashboard_update_kw'));

    }

    function ops_save_keywords()
    {

        if (!isset($_POST['ops_null'])) {
            return;
        }

        $blog_id = get_current_blog_ID();
        $pid = sanitize_text_field($_POST['post_ID']);

        // first deactivate all keywords
        global $wpdb;
        $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET active ="0" WHERE post_id ="' . $pid . '" AND blog_id="' . $blog_id . '"');

        // if no keyword is set up, break this function, we don't need to activate any
        if (!isset($_POST['ops'])) {
            return;
        }

        // get result for each keyword
        $sort = 0;
        foreach ($_POST['ops']['keywords'] as $kw) {
            $sort++;

            // don't do anything if user haven't filled keyword
            if ($kw['keyword'] == '') {
                continue;
            }
            $kw['keyword'] = strtolower($kw['keyword']);

            $url = sanitize_text_field($kw['url']);
            $keyword = sanitize_text_field($kw['keyword']);
            $searches = isset($kw['searches']) ? sanitize_text_field($kw['searches']) : '';

            $db_results = array();
            $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_rank_report WHERE url = "' . trim($url) . '" AND keyword = "' . trim($keyword) . '" AND blog_id=' . $blog_id, ARRAY_A);

            // if we have found
            if (isset($db_results[0]['id'])) {
                // reactivate
                $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET active = 1 WHERE id = ' . $db_results[0]['id']);

                // set ID if is not set to the post
                if ($pid != $db_results[0]['post_id']) {
                    $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET post_id = ' . $pid . ' WHERE id = ' . $db_results[0]['id']);
                }

                // set blog id if not set
                if ($blog_id != $db_results[0]['blog_id']) {
                    $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET blog_id = ' . $blog_id . ' WHERE id = ' . $db_results[0]['id']);
                }

                // searches
                if ($searches != $db_results[0]['searches']) {
                    $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET searches ="' . $searches . '" WHERE id = ' . $db_results[0]['id']);
                }

                // sort
                if ($sort != $db_results[0]['sort']) {
                    $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET sort = ' . $sort . ' WHERE id = ' . $db_results[0]['id']);
                }
            } else {
                // get first position
                $position = OPS_Rank_Report::ops_get_position($url, $keyword);

                // if there are no rows in wp_ops_rank_report, add one
                $data = array(
                    'url' => trim($url),
                    'keyword' => trim($keyword),
                    'positions' => serialize(array(
                        0 => array(
                            'position' => $position,
                            'time' => time()
                        )
                    )),
                    'post_id' => $pid,
                    'active' => 1,
                    'blog_id' => $blog_id,
                    'sort' => $sort,
                    'searches' => $searches
                );
                $insert = $wpdb->insert($wpdb->base_prefix . 'ops_rank_report', $data);

                if ($insert == 1) {
                    Off_Page_SEO::ops_create_log_entry('new_keyword', 'info', __('A new keyword was added.', 'off-page-seo'));
                } else {
                    Off_Page_SEO::ops_create_log_entry('new_keyword', 'error', __('Error adding new keyword.', 'off-page-seo'));
                }

                continue;
            }
        }

    }

    function ops_rank_update_callback()
    {
        $this->ops_update_all_positions();
    }


    public function ops_update_all_positions()
    {

        Off_Page_SEO::ops_create_log_entry('rank_report', 'info', __('Ranking update started - good luck!', 'off-page-seo'));

        // check if we are having all data required
        $allow_rank_test = self::ops_validate_settings_for_report();
        if ($allow_rank_test == 0) {
            Off_Page_SEO::ops_create_log_entry('rank_report', 'error', __('Ranking update ended after settings (Google domain, language) validation error.', 'off-page-seo'));
            return;
        }

        // update local helper to try most precise method - google directly
        $local_helper = unserialize(Off_Page_SEO::ops_get_option('ops_local_helper'));
        $local_helper['get_position_from'] = 'google';
        Off_Page_SEO::ops_update_option('ops_local_helper', serialize($local_helper));

        // we are good to go
        $settings = Off_Page_SEO::ops_get_settings();
        $now = time();

        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE active = '1' AND blog_id = '" . get_current_blog_id() . "'", ARRAY_A);
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $position = self::ops_get_position($row['url'], $row['keyword']);

                $positions = unserialize($row['positions']);

                $new_position = array('position' => $position, 'time' => $now);

                // prepend element to array
                array_unshift($positions, $new_position);


                if(count($positions) > 52){
                    $positions = array_slice($positions, 0, 50);
                }

                // serialize
                $positions_save = serialize($positions);

                // save
                $wpdb->update($wpdb->base_prefix . "ops_rank_report", array('positions' => $positions_save), array('id' => $row['id']));

            }

            // send email
            if (isset($settings['rank_report']['send_notification']) && $settings['rank_report']['send_notification'] == 'on') {
                OPS_Email::ops_master_update_email();
            }

        } else {

            Off_Page_SEO::ops_create_log_entry('rank_report', 'alert', __('You do not have any active keywords, please add some.', 'off-page-seo'));

        }
        // update
        Off_Page_SEO::ops_create_log_entry('rank_report', 'info', __('Ranking update finished.', 'off-page-seo'));

    }


    public static function ops_get_position($url, $keyword)
    {

        // validate settings - this is for page added checks
        $allow_rank_test = self::ops_validate_settings_for_report();
        if ($allow_rank_test == 0) {
            Off_Page_SEO::ops_create_log_entry('rank_report', 'error', __('Ranking update ended after settings (Google domain, language) validation error.', 'off-page-seo'));
            return;
        }


        // we are good to go
        $local_helper = unserialize(Off_Page_SEO::ops_get_option('ops_local_helper'));

        if (!isset($local_helper['get_position_from'])) {
            $local_helper['get_position_from'] = 'google';
        }

        Off_Page_SEO::ops_create_log_entry('rank_report', 'info', sprintf(__('Starting control - method: "<b>%1$s</b>", keyword: "<b>%2$s</b>".', 'off-page-seo'), $local_helper['get_position_from'], $keyword));

        if ($local_helper['get_position_from'] == 'google') {
            $position = self::ops_get_position_directly_from_google($url, $keyword);
        }

        if ($local_helper['get_position_from'] == 'hidemyass') {
            $position = self::ops_get_position_with_hide_my_ass($url, $keyword);
        }

        if ($local_helper['get_position_from'] == 'api') {
            $position = self::ops_get_position_with_api($url, $keyword);
        }

        return $position;


    }

    public static function ops_get_position_directly_from_google($url, $keyword)
    {
        $pos = 0;
        $request_url = Off_Page_SEO::ops_get_request_url($keyword);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
        curl_setopt($ch, CURLOPT_COOKIEFILE, "./cookie.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "./cookie.txt");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $html = ops_follow_curl_location($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch); // close connection

        if ($curl_info['http_code'] == 200) {

            // google requires captcha
            if (stristr($html, 'protect our users') || stristr($html, 'answer/86640')) {

                // create log
                Off_Page_SEO::ops_create_log_entry('rank_report', 'alert', __('Google requires captcha. Changing to a different method – Hide My Ass.', 'off-page-seo'));

                // update rank report method for this check
                $local_helper = unserialize(Off_Page_SEO::ops_get_option('ops_local_helper'));
                $local_helper['get_position_from'] = 'hidemyass';
                Off_Page_SEO::ops_update_option('ops_local_helper', serialize($local_helper));

                // try hide my ass for this keyword
                return self::ops_get_position_with_hide_my_ass($url, $keyword);
            }

            // continue with direct google data fetch
            $html = str_get_html($html);
            $linkObjs = $html->find('h3.r a');
            $pos = 1;
            foreach ($linkObjs as $linkObj) {

                // check url
                if (str_replace('/', '', $url) == str_replace('/', '', trim($linkObj->href))) {
                    return $pos;
                }

                // increase position
                $pos++;
            }

        } else {
            // we have a problem with google
            if (stristr($html, 'the characters below')) {
                Off_Page_SEO::ops_create_log_entry('rank_report', 'alert', __('Google requires captcha.', 'off-page-seo'));
            }

            Off_Page_SEO::ops_create_log_entry('rank_report', 'alert', __('Error fetching Google data. Trying another method - Hide My Ass.', 'off-page-seo'));

            // update rank report method for this check
            $local_helper = unserialize(Off_Page_SEO::ops_get_option('ops_local_helper'));
            $local_helper['get_position_from'] = 'hidemyass';
            Off_Page_SEO::ops_update_option('ops_local_helper', serialize($local_helper));

            // try hide my ass for this keyword
            return self::ops_get_position_with_hide_my_ass($url, $keyword);

        }

        // nothing found
        return 100;
    }

    /* Zalmos replaced with HideMyAss */
    public static function ops_get_position_with_hide_my_ass($url, $keyword, $try = 1)
    {
        // curl ini
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.hidemyass.com/');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:28.0) Gecko/20100101 Firefox/28.0');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        @curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $request_url = Off_Page_SEO::ops_get_request_url($keyword);

        // set post
        // $curlurl = "https://proxy.zalmos.com/includes/process.php?action=update";

        $hide_my_ass_urls = array(
            '1' => 'https://4.hidemyass.com/includes/process.php?action=update&idx=2',
            '2' => 'https://3.hidemyass.com/includes/process.php?action=update&idx=2',
            '3' => 'https://2.hidemyass.com/includes/process.php?action=update&idx=2'
        );

        $curlurl = $hide_my_ass_urls[$try];

        $curlpost = "u=" . urlencode($request_url);
        curl_setopt($ch, CURLOPT_URL, $curlurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlpost);
        curl_exec($ch);
        $cuinfo = curl_getinfo($ch);

        if ($cuinfo['http_code'] == 302 && trim($cuinfo['redirect_url'] != '')) {

            // get the data after redirection from Hide my Ass
            $redirect_url = $cuinfo['redirect_url'];
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_URL, trim($redirect_url));

            $html = ops_follow_curl_location($ch);

            if(stristr($html, 'Edit the first number in the URL')){
//                echo $try . '...';

                // this happens when hide my ass is fucking our ass
                $new_try = $try + 1;

                // if 3rd try was not successful, go for Google Ajax API
                if($new_try == 4){
                    return self::ops_get_position_with_api($url, $keyword);
                }

                // recursively call this function
                return self::ops_get_position_with_hide_my_ass($url, $keyword, $new_try);

            }

            // extract data
            preg_match_all('{<cite class="_Rm.*?">(.*?)</cite>}', $html, $matches);

            // check if our keyword is present on the page
            if (stristr($html, ($keyword))) {

                // set position to 0
                $pos = 1;

                // get our host
                $url = self::ops_get_url_host($url);

                // go through
                foreach ($matches[0] as $link) {

                    //clear html
                    $cleanLink = str_replace(array('<b>', '</b>'), '', $link);
                    $cleanLink = preg_replace('{<.*?>}', '', $cleanLink);
                    // extract >
                    if (stristr($cleanLink, '&rsaquo')) {
                        $cleanLink = explode('&rsaquo', $cleanLink)[0];
                    }

                    // extract ...
                    if (stristr($cleanLink, '...')) {
                        $cleanLink = explode('...', $cleanLink)[0];
                    }

                    // get rid of https, http
                    if (stristr($cleanLink, 'https://')) {
                        $cleanLink = str_replace('https://', '', $cleanLink);
                    }
                    if (stristr($cleanLink, 'http://')) {
                        $cleanLink = str_replace('http://', '', $cleanLink);
                    }

                    // finaly, explode /
                    $cleanLink = explode('/', $cleanLink)[0];

                    // compare clean link with our url
                    if (trim($url) == trim($cleanLink)) {
                        return $pos;
                    }

                    $pos++;

                }

                // we didn't find our link
                return 100;


            } else {

                Off_Page_SEO::ops_create_log_entry('rank_report', 'alert', __('There was no keyword in the HideMyAss fetched page. Trying another method: google ajax api for keyword: ', 'off-page-seo') . $keyword);

                return self::ops_get_position_with_api($url, $keyword);

            }


        } else {

            // create log
            Off_Page_SEO::ops_create_log_entry('rank_report', 'alert', __('The HideMyAss first page does not redirect. Trying again with Google ajax api for keyword:', 'off-page-seo') . $keyword);

            // try google API for this keyword
            return self::ops_get_position_with_api($url, $keyword);
        }


    }


    public static function ops_get_position_with_api($url, $keyword)
    {
        $ip = Off_Page_SEO::ops_request_ip();
        $settings = Off_Page_SEO::ops_get_settings();
        $url_stripped = str_replace('/', '', $url);
        $pos = 0;
        $start = 0;
        for ($i = 0; $i < 13; $i++) {

            // documentation https://developers.google.com/web-search/docs/reference
            $request_url = 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=' . urlencode($keyword) . '&start=' . $start . '&rsz=large&userip=' . $ip . '&safe=off&gl=' . $settings['core']['google_domain'] . '&lr=lang_' . $settings['core']['language'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, 'off-page-seo-plugin');
            $body = curl_exec($ch);
            curl_close($ch);

            $json = json_decode($body);

            if (!isset($json->responseData->results)) {
                // create log
                Off_Page_SEO::ops_create_log_entry('rank_report', 'alert', __('We did not receive any data from the requested URL:', 'off-page-seo') . $request_url);
                return 100;
            }

            // we have data, go through
            foreach ($json->responseData->results as $result) {
                $pos++;
                $result_stripped = str_replace('/', '', $result->url);
                if ($result_stripped == $url_stripped) {
                    // we have found the match
                    return $pos;
                }
            }

            // increase start and try another call
            $start = $start + 8;

            // we can't get more than 64 results from google API, therefore, report keyword as 100 position :(
            if($start == 64){
                return 100;
            }
        }

        // if nothing found, return 100
        return 100;

    }


    public static function ops_get_url_host($url)
    {
        // get rid of https, http
        if (stristr($url, 'https://')) {
            $url = str_replace('https://', '', $url);
        }

        if (stristr($url, 'http://')) {
            $url = str_replace('http://', '', $url);
        }

        // finaly, explode /
        $url = explode('/', $url)[0];

        return $url;

    }

    public static function ops_validate_settings_for_report()
    {
        // validate all settings
        $settings = Off_Page_SEO::$settings;

        $allow = 1;

        if (!isset($settings['core']['language']) || $settings['core']['language'] == '') {
            Off_Page_SEO::ops_create_log_entry('rank_report', 'error', __('Settings validation error for field: language.', 'off-page-seo'));
            $allow = 0;
        }

        if (!isset($settings['core']['google_domain']) || $settings['core']['google_domain'] == '') {
            Off_Page_SEO::ops_create_log_entry('rank_report', 'error', __('Settings validation error for field: Google domain.', 'off-page-seo'));
            $allow = 0;
        }

        return $allow;

    }

    public static function ops_get_keyword_row_by_id($id)
    {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE id = '" . $id . "'", ARRAY_A);
        return $row;
    }

    public static function ops_get_keyword_row_by_url_and_keyword($url, $keyword)
    {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE url = '" . $url . "' AND keyword = '" . $keyword . "'", ARRAY_A);
        return $row;
    }


    public function ops_dashboard_delete_kw()
    {
        Off_Page_SEO::ops_deactivate_keyword(sanitize_text_field($_POST['kwid']));
        die();
    }

    public function ops_dashboard_update_kw()
    {
        $searches = sanitize_text_field($_POST['searches']);
        $pid = sanitize_text_field($_POST['pid']);
        $kwid = sanitize_text_field($_POST['kwid']);
        global $wpdb;
        $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET searches ="' . $searches . '" WHERE id =' . $kwid);
        $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET post_id ="' . $pid . '" WHERE id =' . $kwid);
        die();
    }

    function ops_update_dashboard_master_sort()
    {
        $positions = sanitize_text_field($_POST['positions']);
        $positions = str_replace('[', '', $positions);
        $positions = str_replace(']', '', $positions);
        $ids = explode(',', $positions);

        global $wpdb;
        $k = 0;
        foreach ($ids as $id) {
            $k++;
            $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET sort_dashboard = ' . $k . ' WHERE id = ' . $id);
        }
        die();
    }

}
