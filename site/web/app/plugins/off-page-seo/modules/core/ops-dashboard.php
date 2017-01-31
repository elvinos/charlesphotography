<?php

/**
 * Main plugin class
 * */
class OPS_Dashboard
{


    /**
     * Initialization of main class
     * */
    public function __construct()
    {
//        wp_unschedule_event('1444074423','ops_reciprocal_check');

        $this->ops_render_dashboard();
    }

    public function ops_render_dashboard()
    {

        $settings = Off_Page_SEO::$settings;
        wp_enqueue_script('jquery-ui-datepicker');

        ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

        <div class="wrap ops-wrapper" id="ops-dashboard">
            <h2><?php _e('Off Page SEO', 'off-page-seo') ?></h2>

            <div class="postbox ops-padding" id="ops-dashboard-tabs">
                <?php
                if (Off_Page_SEO::ops_is_any_module_on() == false) {
                    echo "<p class='ops-error'>";
                    _e("You don't have a module activated. Please go to settings and activate one.", "off-page-seo");
                    echo "</p>";
                }
                ?>
                <ul>
                    <?php do_action('ops/dashboard_tabs'); ?>
                </ul>

            </div>

            <?php $tab = sanitize_text_field(isset($_GET['tab']) ? $_GET['tab'] : ''); ?>
            <?php
            if ($tab == '') {
                if (!isset($settings['module']['rank_report']) || $settings['module']['rank_report'] == 0) {
                    $this->ops_rank_report_is_not_activated();
                }
                do_action('ops/dashboard_rank_report');
            } elseif ($tab == 'backlinks') {
                do_action('ops/dashboard_backlinks');
            } elseif ($tab == 'share_counter') {
                do_action('ops/dashboard_share_counter');
            } elseif ($tab == 'google_api') {
                do_action('ops/dashboard_google_api');
            }
            ?>
            <div class="ops-dashboard-right">
                <div class="postbox ops-padding">
                    <?php do_action('ops/dashboard_sidebar'); ?>
                </div>
                <?php do_action('ops/dashboard_sidebar_ads'); ?>

            </div>

            <div class="ops-clearfix"></div>
        </div>
        <?php
    }

    public function ops_rank_report_is_not_activated()
    {
        ?>
        <div class="ops-dashboard-left">
            <div class="postbox ops-padding">
                <p><?php _e('To get the best use out of this plugin, please activate the Rank Report module in the settings. You can still use other functionalities separately.', 'off-page-seo') ?></p>
                <p><a href="<?php echo admin_url() ?>admin.php?page=ops_settings"><?php _e('Settings','off-page-seo') ?></a></p>
            </div>
        </div>
        <?php
    }
}
