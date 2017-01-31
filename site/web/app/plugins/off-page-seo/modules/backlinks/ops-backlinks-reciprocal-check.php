<?php

class OPS_Backlinks_Reciprocal_Check
{
    public function __construct()
    {

        if (wp_next_scheduled('ops_reciprocal_check') == '') {
            wp_schedule_event(time() + 30, 'ops_six_days', 'ops_reciprocal_check');
        }

        // hook action to the cron
        add_action('ops_reciprocal_check', array($this, 'ops_reciprocal_check'));

//        echo $this->ops_test_reciprocal_backlink('http://www.czechstudio.cz/', 'http://www.sexy-moda.cz/');
    }

    function ops_reciprocal_check()
    {
        $settings = Off_Page_SEO::$settings;
        if (isset($settings['backlinks']['reciprocal_check']) && $settings['backlinks']['reciprocal_check'] == 'on') {
            $this->ops_perform_test();
        } else {
            Off_Page_SEO::ops_create_log_entry('backlink_check', 'info', __('We tried to control your backlinks, but you have this functionality turned off in the settings.', 'off-page-seo'));
        }
    }

    function ops_perform_test()
    {
        $checked = 0;
        $settings = Off_Page_SEO::$settings;

        $helper = unserialize(Off_Page_SEO::ops_get_option('ops_local_helper'));

        if (!isset($helper['reciprocal_test_finished'])) {
            $helper['reciprocal_test_finished'] = 0;
        }

        // let the user know what's happening
        Off_Page_SEO::ops_create_log_entry('backlink_check', 'info', __('Starting new backlink batch control. (Will control 5 backlinks if they are present)', 'off-page-seo'));

        $bls = Off_Page_SEO::ops_get_all_backlinks_with_keywords();
        if (isset($bls) && count($bls) > 0) {
            foreach ($bls as $bl) {

                // we have not tested backlink yet
                if ($bl['reciprocal_last_test'] == '') {
                    $bl['reciprocal_last_test'] = 0;
                }

                // we tested this backlink already
                if ($helper['reciprocal_test_finished'] < $bl['reciprocal_last_test']) {
                    continue;
                }

                // run the test
                $keyword = Off_Page_SEO::ops_get_keyword($bl['keyword_id']);
                $new_status = $this->ops_test_reciprocal_backlink($keyword['url'], $bl['url']);

                // backlink was deleted, let the user know
                if (($new_status == 1) && (isset($settings['backlinks']['send_email_not_found']) && $settings['backlinks']['send_email_not_found'] == 'on')) {
                    if ($bl['reciprocal_status'] == '2' || $bl['reciprocal_status'] == '3') {
                        OPS_Email::ops_backlink_was_deleted($bl['id']);
                    }
                }

                $this->ops_save_backlink_status_and_time($bl['id'], $new_status);

                // stop 5 checks
                $checked++;
                if ($checked == 5) {
                    // set new cron
                    wp_schedule_single_event(time() + 30, 'ops_reciprocal_check');

                    // exit function
                    return;
                }
            }
        } else {
            Off_Page_SEO::ops_create_log_entry('backlink_check', 'info', __('There are no backlinks to be checked.', 'off-page-seo'));
        }

        Off_Page_SEO::ops_create_log_entry('backlink_check', 'info', __('Backlink reciprocal control was finished.', 'off-page-seo'));

        // if we get here, we have finished all keywords check and its time to increase ops_helper to current date
        $helper['reciprocal_test_finished'] = time();
        Off_Page_SEO::ops_update_option('ops_local_helper', serialize($helper));

//        mail('info@czechstudio.cz', 'reciprocal test finished' . get_current_blog_id(), 'finished' . get_home_url());

    }

    function ops_save_backlink_status_and_time($blid, $status)
    {
        global $wpdb;
        $d = $wpdb->update(
            $wpdb->base_prefix . 'ops_backlinks',
            array(
                'reciprocal_status' => $status,
                'reciprocal_last_test' => time()
            ),
            array('id' => $blid)
        );
    }

    function ops_test_reciprocal_backlink($my_url, $target_url)
    {
        $html = ops_curl($target_url);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//a");
        $result = $this->ops_is_my_link_there($hrefs, $my_url);
        return $result;
    }

    /**
     * 1 - not found
     * 2 - nofollow
     * 3 - all ok
     *
     * @param $hrefs
     * @param $my_url
     * @return int
     */
    function ops_is_my_link_there($hrefs, $my_url)
    {
        for ($i = 0; $i < $hrefs->length; $i++) {
            $href = $hrefs->item($i);
            $url = $href->getAttribute('href');
            if (str_replace('/', '', $my_url) == str_replace('/', '', $url)) {
                $rel = $href->getAttribute('rel');
                if ($rel == 'nofollow') {
                    return 2;
                }
                return 3;
            }
        }
        return 1;
    }

}
