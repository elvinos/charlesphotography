<?php

if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

if (defined('OPS_PREMIUM')) {
    // do nothing if we have premium plugin active

} else {
// Get the timestamp of the next scheduled run
    $timestamp = wp_next_scheduled('ops_reciprocal_check');
    wp_unschedule_event($timestamp, 'ops_reciprocal_check');

    $timestamp = wp_next_scheduled('ops_rank_update');
    wp_unschedule_event($timestamp, 'ops_rank_update');

    /*
     * DROP Table with positions and backlinks
     */
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}ops_rank_report");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}ops_backlinks");

    /*
     * Delete options
     */
    delete_option('ops_settings');
    delete_option('ops_google_api_access_token');
    delete_option('ops_master_helper');
    delete_option('ops_local_helper');
    delete_option('ops_log');
    delete_option('ops_premium'); // from the old version


    $types = get_post_types();
    $args = array(
        'post_type' => $types,
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'ops_shares',
                'compare' => 'EXISTS',
            ),
        )
    );
    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()): $wp_query->the_post();
            delete_post_meta(get_the_ID(), 'ops_shares');
            delete_post_meta(get_the_ID(), 'ops_shares_total');
            delete_post_meta(get_the_ID(), 'ops_share_timer');
        endwhile;
    endif;


// delete data on multisite
    if (is_multisite()) {
        $blogs = wp_get_sites();
        foreach ($blogs as $blog) {

            set_time_limit(180);

            if ($blog['blog_id'] == 1) {
                delete_blog_option($blog['blog_id'], 'ops_master_helper');
            }
            delete_blog_option($blog['blog_id'], 'ops_google_api_access_token');
            delete_blog_option($blog['blog_id'], 'ops_settings');
            delete_blog_option($blog['blog_id'], 'ops_log');
            delete_blog_option($blog['blog_id'], 'ops_local_helper');
            delete_blog_option($blog['blog_id'], 'ops_premium'); // from the old version


            // switch to blog
            switch_to_blog($blog['blog_id']);

            // delete share counter data
            $types = get_post_types();
            $args = array(
                'post_type' => $types,
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'ops_shares',
                        'compare' => 'EXISTS',
                    ),
                )
            );
            $wp_query = new WP_Query($args);

            if ($wp_query->have_posts()) :
                while ($wp_query->have_posts()): $wp_query->the_post();
                    delete_post_meta(get_the_ID(), 'ops_shares');
                    delete_post_meta(get_the_ID(), 'ops_shares_total');
                    delete_post_meta(get_the_ID(), 'ops_share_timer');
                endwhile;
            endif;

        }
    }
}