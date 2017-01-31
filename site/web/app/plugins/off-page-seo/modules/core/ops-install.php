<?php

class OPS_Install
{
    public function __construct()
    {
        $this->ops_create_tables();
        $this->ops_create_settings();

        $helper = array();
        $helper['updated_to'] = '2.2';
        Off_Page_SEO::ops_update_master_helper($helper);
    }

    function ops_create_tables()
    {

        global $wpdb;

        $create_table_query_ranks = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}ops_rank_report` (
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
              searches text NOT NULL
            ) DEFAULT CHARSET=utf8;";
        $d = $wpdb->query($create_table_query_ranks);

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
            ) DEFAULT CHARSET=utf8;";
        $d = $wpdb->query($create_table_query_backlinks);

    }

    function ops_create_settings()
    {
        if (!is_multisite()) {
            self::ops_initiate_settings();
        }

    }

    public static function  ops_initiate_settings()
    {

        $settings = Off_Page_SEO::ops_get_settings();
        if(isset($settings['core'])){
            // don't update if we are just reactivating the plugin
            return;
        }

        $settings = array(
            'module' => array(
                'rank_report' => 1,
                'backlinks' => 0,
                'google_api' => 0,
                'share_counter' => 0
            ),
            'core' => array(
                'language' => 'en',
                'google_domain' => 'com',
                'notification_email' => '',
                'date_format' => 'j.n.Y',
                'currency' => 'USD',
                'post_types' => array('page', 'post')
            ),
            'google_api' => array(
                'access_token' => '',
                'period' => 'month'
            ),
            'rank_report' => array(
                'show_estimation' => '',
                'send_notification' => 'on'
            )
        );

        Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));

    }

}
