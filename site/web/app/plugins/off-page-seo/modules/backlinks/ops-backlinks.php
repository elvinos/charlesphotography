<?php

class OPS_Backlinks
{
    public function __construct()
    {
        //  update dashboard kw
        add_action('wp_ajax_ops_update_backlink_keyword', array($this, 'ops_update_backlink_keyword'));
        add_action('wp_ajax_nopriv_ops_update_backlink_keyword', array($this, 'ops_update_backlink_keyword'));

        // delete keyword
        add_action('wp_ajax_ops_delete_bl', array($this, 'ops_delete_bl'));
        add_action('wp_ajax_nopriv_ops_delete_bl', array($this, 'ops_delete_bl'));
    }

    public static function ops_get_keyword_backlinks($kwid)
    {
        global $wpdb;
        $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_backlinks WHERE keyword_id = "' . $kwid . '" AND blog_id ="' . get_current_blog_id() . '"', ARRAY_A);
        return $db_results;
    }


    public function ops_update_backlink_keyword()
    {
        $blid = sanitize_text_field($_POST['blid']);
        $kwid = sanitize_text_field($_POST['keyword_id']);
        $url = sanitize_text_field($_POST['url']);
        $type = sanitize_text_field($_POST['type']);
        $comment = sanitize_text_field($_POST['comment']);
        $price = sanitize_text_field($_POST['price']);
        $price_monthly = sanitize_text_field($_POST['price_monthly']);
        $reciprocal_check = (isset($_POST['reciprocal_check']) && sanitize_text_field($_POST['reciprocal_check'])) == 'checked' ? 1 : 0;
        $start_date_ymd = sanitize_text_field($_POST['start_date']);
        $start_date = strtotime($start_date_ymd);
        $contact = sanitize_text_field($_POST['contact']);

        global $wpdb;
        $wpdb->update(
            $wpdb->base_prefix . 'ops_backlinks',
            array(
                'url' => $url,
                'type' => $type,
                'comment' => $comment,
                'price' => $price,
                'price_monthly' => $price_monthly,
                'reciprocal_check' => $reciprocal_check,
                'start_date' => $start_date,
                'keyword_id' => $kwid,
                'contact' => $contact
            ),
            array('id' => $blid)
        );
        die();
    }

    public static function ops_add_backlink()
    {
        $kwid = sanitize_text_field($_POST['keyword_id']);
        $url = sanitize_text_field($_POST['url']);
        $type = sanitize_text_field($_POST['type']);
        $comment = sanitize_text_field($_POST['comment']);
        $price = sanitize_text_field($_POST['price']);
        $price_monthly = sanitize_text_field($_POST['price_monthly']);
        $reciprocal_check = (isset($_POST['reciprocal_check']) && sanitize_text_field($_POST['reciprocal_check'])) == 'on' ? 1 : 0;
        $start_date_ymd = sanitize_text_field($_POST['start_date']);
        $start_date = strtotime($start_date_ymd);
        $contact = sanitize_text_field($_POST['contact']);

        global $wpdb;
        $in = $wpdb->insert(
            $wpdb->base_prefix . 'ops_backlinks',
            array(
                'url' => $url,
                'type' => $type,
                'comment' => $comment,
                'price' => $price,
                'price_monthly' => $price_monthly,
                'reciprocal_check' => $reciprocal_check,
                'start_date' => $start_date,
                'keyword_id' => $kwid,
                'blog_id' => get_current_blog_id(),
                'contact' => $contact
            )
        );
    }


    public function ops_delete_bl()
    {
        Off_Page_SEO::ops_delete_backlink(sanitize_text_field($_POST['blid']));
        die();
    }

    public static function ops_process_backlinks($db_results)
    {
        $assigned = array();
        $unassigned = array();
        foreach ($db_results as $result) {
            if ($result['keyword_id'] != 0) {
                $assigned[$result['keyword_id']][] = $result;
            } else {
                $unassigned[] = $result;
            }
        }

        // get assigned kws
        if (isset($assigned) && count($assigned) > 0) {
            foreach ($assigned as $kw_id => $assigned_kw) {

                // sort according to date
                usort($assigned_kw, create_function('$a, $b', 'return $b[\'start_date\'] - $a[\'start_date\'];'));

                $output['assigned'][$kw_id] = $assigned_kw;
            }
        } else {
            $output['assigned'] = array();
        }

        // get unasigned kw
        if (isset($unassigned) && count($unassigned) > 0) {
            $output['unassigned'] = $unassigned;

            usort($output['unassigned'], create_function('$a, $b', 'return $b[\'start_date\'] - $a[\'start_date\'];'));
        } else {
            $output['unassigned'] = array();
        }

        return $output;
    }


    public static function ops_get_total_backlink_costs()
    {
        $links = Off_Page_SEO::ops_get_all_backlinks();
        $total = array('fixed' => 0, 'monthly' => 0);
        if (count($links) == 0) {
            return $total;
        }
        foreach ($links as $link) {
            $total['fixed'] = $total['fixed'] + $link['price'];
            $total['monthly'] = $total['monthly'] + $link['price_monthly'];
        }
        return $total;
    }

    public static function ops_get_nice_reciprocal_status($backlink)
    {

        if($backlink['reciprocal_check'] == 0){
            return 'no-keyword';
        }
        if ($backlink['keyword_id'] == 0) {
            return 'no-keyword';
        }

        if ($backlink['reciprocal_status'] == '') {
            return 'no-status';
        }

        if ($backlink['reciprocal_status'] == '1') {
            return 'not-present';
        }

        if ($backlink['reciprocal_status'] == '2') {
            return 'present-nofollow';
        }

        if ($backlink['reciprocal_status'] == '3') {
            return 'present';
        }


    }

}
