<?php

class OPS_Update
{
    public function __construct()
    {
        $helper = Off_Page_SEO::ops_get_master_helper();

        if (!isset($helper['updated_to']) || $helper['updated_to'] < '2.1') {
            add_action('init', array($this, 'ops_update_all_to_2_1'));
            $helper['updated_to'] = '2.1';
            Off_Page_SEO::ops_update_master_helper($helper);
        }
    }

    public function ops_update_all_to_2_1()
    {
        $this->ops_update_database_2_1();
        $this->ops_set_blog_id_2_1();
        $this->ops_move_backlinks_2_1();
        $this->ops_reorder_settings();
    }


    public function ops_update_database_2_1()
    {
        global $wpdb;
        // delete table
        /*  SORT THIS */
        $wpdb->query('ALTER TABLE ' . $wpdb->base_prefix . 'ops_rank_report DROP COLUMN feature1;');
        $wpdb->query('ALTER TABLE ' . $wpdb->base_prefix . 'ops_rank_report DROP COLUMN feature2;');
        $wpdb->query('ALTER TABLE ' . $wpdb->base_prefix . 'ops_rank_report DROP COLUMN feature3;');
        /*
         * UPDATING DATABASE
         */

        $create_table_query_backlinks = "
          CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}ops_backlinks` (
              id int(11) NOT NULL DEFAULT NULL AUTO_INCREMENT PRIMARY KEY,
              keyword_id int(11) NOT NULL,
              url text NOT NULL,
              price text NOT NULL,
              type text NOT NULL,
              comment text NOT NULL,
              reciprocal_check text NOT NULL,
              reciprocal_referer text NOT NULL,
              reciprocal_status text NOT NULL,
              reciprocal_last_test text NOT NULL,
              start_date text NOT NULL,
              end_date text NOT NULL,
              blog_id int(11) NOT NULL,
              contact text NOT NULL,
              price_monthly text NOT NULL
            ) DEFAULT CHARSET=utf8;
        ";

        $wpdb->query($create_table_query_backlinks);


        $create_table_query = "
            CREATE TABLE `{$wpdb->base_prefix}ops_rank_report` (
              id int(11) NOT NULL DEFAULT NULL AUTO_INCREMENT PRIMARY KEY,
              url text NOT NULL,
              keyword text NOT NULL,
              positions text NOT NULL,
              post_id text NOT NULL,
              active int(11) NOT NULL,
              links text NOT NULL,
              blog_id text NOT NULL,
              sort text NOT NULL,
              sort_dashboard text NOT NULL,
              searches text NOT NULL,
            ) DEFAULT CHARSET=utf8;
        ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($create_table_query);

    }


    public function ops_set_blog_id_2_1()
    {
        global $wpdb;
        if (is_multisite()) {
            $sites = wp_get_sites();
            foreach ($sites as $site) {
                switch_to_blog($site['blog_id']);
                $settings = Off_Page_SEO::ops_get_settings();
                if (isset($settings['graphs'])) {
                    foreach ($settings['graphs'] as $kw) {
                        $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET blog_id =' . get_current_blog_id() . ', active = "1" WHERE url ="' . $kw['url'] . '"');
                    }
                }
            }
            restore_current_blog();
            return;
        } else {
            // we are in single site
            $settings = Off_Page_SEO::ops_get_settings();
            if (isset($settings['graphs'])) {
                foreach ($settings['graphs'] as $kw) {
                    $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET blog_id =' . get_current_blog_id() . ', active = "1" WHERE url ="' . $kw['url'] . '"');
                }
            }
        }
    }


    public function ops_move_backlinks_2_1()
    {
        global $wpdb;
        $results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_rank_report');
        foreach ($results as $result) {
            $backlinks = unserialize($result->links);
            if ($backlinks != '' && count($backlinks) > 0) {

                foreach ($backlinks as $backlink) {
                    $data = array(
                        'keyword_id' => $result->id,
                        'url' => isset($backlink['url']) ? $backlink['url'] : '',
                        'price' => isset($backlink['price']) ? $backlink['price'] : '',
                        'type' => isset($backlink['type']) ? $backlink['type'] : '',
                        'comment' => isset($backlink['comment']) ? $backlink['comment'] : '',
                        'reciprocal_check' => isset($backlink['reciprocal']) ? $backlink['reciprocal'] : '',
                        'reciprocal_referer' => isset($backlink['reciprocal_referer']) ? $backlink['reciprocal_referer'] : '0',
                        'reciprocal_status' => isset($backlink['reciprocal_status']) ? $backlink['reciprocal_status'] : '',
                        'start_date' => isset($backlink['date']) ? $backlink['date'] : '',
                        'end_date' => null,
                        'blog_id' => $result->blog_id
                    );

                    $dbin = $wpdb->insert($wpdb->base_prefix . 'ops_backlinks', $data);
                    if ($dbin) {
                        $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET links = "" WHERE id = ' . $result->id);
                    }
                }
            }
        }

        $wpdb->query('ALTER TABLE ' . $wpdb->base_prefix . 'ops_rank_report DROP COLUMN links;');

    }


    function ops_reorder_settings()
    {
        if (is_multisite()) {
            $sites = wp_get_sites();
            foreach ($sites as $site) {
                switch_to_blog($site['blog_id']);
                $settings = Off_Page_SEO::ops_get_settings();
                $new_settings = $this->ops_process_settings($settings);
                Off_Page_SEO::ops_update_option('ops_settings', $new_settings);

            }
            restore_current_blog();

        } else {

            $settings = Off_Page_SEO::ops_get_settings();
            $new_settings = $this->ops_process_settings($settings);
            Off_Page_SEO::ops_update_option('ops_settings', $new_settings);
        }

    }

    function ops_process_settings($settings)
    {
        $new_settings = array(
            'module' => array(
                'rank_report' => 1,
                'google_api' => 0,
                'backlinks' => 1,
                'share_counter' => 0
            ),
            'core' => array(
                'language' => isset($settings['lang']) ? $settings['lang'] : '',
                'google_domain' => isset($settings['google_domain']) ? $settings['google_domain'] : '',
                'notification_email' => isset($settings['notification_email']) ? $settings['notification_email'] : '',
                'date_format' => isset($settings['date_format']) ? $settings['date_format'] : '',
                'currency' => isset($settings['currency']) ? $settings['currency'] : '',
                'post_types' => array('post', 'page')
            ),
            'google_api' => array(
                'access_token' => '',
                'period' => 'month'
            ),
            'rank_report' => array(
                'show_estimation' => '',
                'send_notification' => 'on'
            ),
            'backlinks' => array(
                'reciprocal_check' => 'on'
            )
        );
        return $new_settings;
    }

}
