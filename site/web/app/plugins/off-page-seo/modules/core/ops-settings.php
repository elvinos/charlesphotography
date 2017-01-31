<?php

class OPS_Settings
{

    /**
     * Initialization of Settings Class
     * */
    public function __construct()
    {

        if (isset($_GET['log']) && $_GET['log'] == 'show') {
            $this->show_log();
            return;
        }

        $settings = Off_Page_SEO::ops_get_settings();

        // delete inactive keywords
        if (isset($_GET['ops_control']) && $_GET['ops_control'] == 'run_reciprocal_check') {
            $this->ops_run_reciprocal_check();
        }

        // delete inactive keywords
        if (isset($_GET['ops_control']) && $_GET['ops_control'] == 'run_rank_report') {
            $this->ops_run_rank_report();
        }

        // delete inactive keywords
        if (isset($_GET['ops_control']) && $_GET['ops_control'] == 'delete_inactive') {
            $this->ops_delete_inactive_keyword();
        }

        // forget authorization
        if (isset($_GET['ops_control']) && $_GET['ops_control'] == 'forget_authorization') {
            $this->ops_forget_authorization_code();
        }


        // if we are saving data from form
        if (isset($_POST['core']['language']) && check_admin_referer('save_ops_form') == 1) {
            $this->ops_save_settings();
        }

        // display message that settings was updated
        if (isset($_GET['saved']) && $_GET['saved'] == true) {
            ?>
            <div class="updated" style="padding: 8px 20px;">
                <?php _e('Settings were updated.', 'off-page-seo') ?>
            </div>
            <?php
        }

        // renders settings form
        $this->ops_render_settings_form($settings);
    }


    public function ops_forget_authorization_code()
    {
        $settings = Off_Page_SEO::$settings;
        $settings['google_api']['authorization_code'] = '';
        Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));
        Off_Page_SEO::ops_update_option('ops_google_api_access_token', '');


        // make a note to the log
        Off_Page_SEO::ops_create_log_entry('settings_change', 'info', __('Forget authorization code.', 'off-page-seo'));

        ?>
        <!--REDIRECTS-->
        <script type="text/javascript">
            window.location.href = "<?php echo admin_url() . 'admin.php?page=ops_settings&saved=true'; ?>";
        </script>
        <?php
        exit;
    }

    public function ops_run_rank_report()
    {
        $when = wp_next_scheduled('ops_rank_update');
        wp_unschedule_event($when, 'ops_rank_update');
        wp_schedule_event(time() + 10, 'ops_three_days', 'ops_rank_update');

        // make a note to the log
        Off_Page_SEO::ops_create_log_entry('settings_change', 'info', __('Force rank report control.', 'off-page-seo'));
        ?>
        <!--REDIRECTS-->
        <script type="text/javascript">
            window.location.href = "<?php echo admin_url() . 'admin.php?page=ops_settings&saved=true'; ?>";
        </script>
        <?php
        exit;
    }

    public function ops_run_reciprocal_check()
    {
        $when = wp_next_scheduled('ops_reciprocal_check');
        wp_unschedule_event($when, 'ops_reciprocal_check');
        wp_schedule_event(time() + 10, 'ops_six_days', 'ops_reciprocal_check');

        // make a note to the log
        Off_Page_SEO::ops_create_log_entry('settings_change', 'info', __('Force global reciprocal check.', 'off-page-seo'));

        ?>
        <!--REDIRECTS-->
        <script type="text/javascript">
            window.location.href = "<?php echo admin_url() . 'admin.php?page=ops_settings&saved=true'; ?>";
        </script>
        <?php
        exit;
    }


    public function ops_delete_inactive_keyword()
    {
        global $wpdb;
        $wpdb->get_results('DELETE FROM ' . $wpdb->base_prefix . 'ops_rank_report WHERE active = 0 AND blog_id = "' . get_current_blog_id() . '"');

        // make a note to the log
        Off_Page_SEO::ops_create_log_entry('settings_change', 'info', __('Inactive keywords were deleted.', 'off-page-seo'));
        ?>
        <!--REDIRECTS-->
        <script type="text/javascript">
            window.location.href = "<?php echo admin_url() . 'admin.php?page=ops_settings&saved=true'; ?>";
        </script>
        <?php
        exit;
    }

    /**
     * Receive $_POST and saves it as serialized array into database
     * @global type $wpdb
     */
    public function ops_save_settings()
    {
        Off_Page_SEO::ops_update_option('ops_settings', serialize($_POST));

        // make not to the log
        Off_Page_SEO::ops_create_log_entry('settings_change', 'info', __('Settings saved.', 'off-page-seo'));
        ?>

        <!--REDIRECTS-->
        <script type="text/javascript">
            window.location.href = "<?php echo admin_url() . 'admin.php?page=ops_settings&saved=true'; ?>";
        </script>
        <?php
        exit;
    }


    /**
     * Render Main Settings Form
     */
    public function ops_render_settings_form($settings)
    {
        ?>
        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
        <script>
            jQuery(document).ready(function ($) {
                $('.ops-select2').select2();
            });
        </script>
        <div class="wrap ops-wrapper" id="ops-settings">
            <h2><?php _e('Off Page SEO Settings', 'off-page-seo') ?></h2>

            <form action="" method="POST">
                <?php wp_nonce_field('save_ops_form'); ?>
                <div class="postbox ops-padding" id="ops-modules">


                    <?php $active_rank_report = (isset($settings['module']['rank_report']) && $settings['module']['rank_report'] == 1) ? true : false; ?>
                    <div class="ops-module <?php echo $active_rank_report ? "active" : ""; ?>">
                        <div class="ops-left">
                            <div class="ops-icon-rank-report"></div>
                        </div>
                        <div class="ops-right">
                            <div class="title"><?php _e('Rank Report', 'off-page-seo') ?>
                                <span class="ops-hint right"><i><?php _e('Check the position in Google every 3 days.', 'off-page-seo') ?></i></span>
                            </div>
                            <a href="#" class="button <?php echo $active_rank_report ? "" : "button-primary"; ?>">
                                <?php echo $active_rank_report ? __('Deactivate', 'off-page-seo') : __('Activate', 'off-page-seo'); ?>
                            </a>
                            <input type="hidden" name="module[rank_report]" value="<?php echo $active_rank_report ? $settings['module']['rank_report'] : "0"; ?>"/>
                        </div>
                    </div>


                    <?php $active_backlinks = (isset($settings['module']['backlinks']) && $settings['module']['backlinks'] == 1) ? true : false; ?>
                    <div class="ops-module <?php echo $active_backlinks ? "active" : ""; ?>">
                        <div class="ops-left">
                            <div class="ops-icon-backlinks"></div>
                        </div>
                        <div class="ops-right">
                            <div class="title"><?php _e('Backlinks', 'off-page-seo') ?>
                                <span class="ops-hint left"><i><?php _e('Record acquired backlinks and control effect on your position.', 'off-page-seo') ?></i></span>
                            </div>
                            <a href="#" class="button <?php echo $active_backlinks ? "" : "button-primary"; ?>">
                                <?php echo $active_backlinks ? __('Deactivate', 'off-page-seo') : __('Activate', 'off-page-seo'); ?>
                            </a>
                            <input type="hidden" name="module[backlinks]" value="<?php echo $active_backlinks ? $settings['module']['backlinks'] : "0"; ?>"/>
                        </div>
                    </div>

                    <?php $active_google_api = (isset($settings['module']['google_api']) && $settings['module']['google_api'] == 1) ? true : false; ?>
                    <div class="ops-module <?php echo $active_google_api ? "active" : ""; ?>">
                        <div class="ops-left">
                            <div class="ops-icon-google-api"></div>
                        </div>
                        <div class="ops-right">
                            <div class="title"><?php _e('Google API', 'off-page-seo') ?>
                                <span class="ops-hint right"><i><?php _e('Get value data from Google Webmaster Tools.', 'off-page-seo') ?></i></span>
                            </div>
                            <a href="#" class="button <?php echo $active_google_api ? "" : "button-primary"; ?>">
                                <?php echo $active_google_api ? __('Deactivate', 'off-page-seo') : __('Activate', 'off-page-seo'); ?>
                            </a>
                            <input type="hidden" name="module[google_api]" value="<?php echo $active_google_api ? $settings['module']['google_api'] : "0"; ?>"/>
                        </div>
                    </div>

                    <?php $active_share_counter = (isset($settings['module']['share_counter']) && $settings['module']['share_counter'] == 1) ? true : false; ?>
                    <div class="ops-module <?php echo $active_share_counter ? "active" : ""; ?>">
                        <div class="ops-left">
                            <div class="ops-icon-share-counter"></div>
                        </div>
                        <div class="ops-right">
                            <div class="title"><?php _e('Share Counter', 'off-page-seo') ?>
                                <span class="ops-hint left"><i><?php _e('Check how many shares you have on various social websites.', 'off-page-seo') ?></i></span>
                            </div>
                            <a href="#" class="button <?php echo $active_share_counter ? "" : "button-primary"; ?>">
                                <?php echo $active_share_counter ? __('Deactivate', 'off-page-seo') : __('Activate', 'off-page-seo'); ?>
                            </a>
                            <input type="hidden" name="module[share_counter]" value="<?php echo $active_share_counter ? $settings['module']['share_counter'] : "0"; ?>"/>
                        </div>
                    </div>


                </div>


                <!-- CORE SETTINGS-->
                <h3><?php _e('Core Settings', 'off-page-seo') ?></h3>

                <div class="postbox ops-padding ops-tab" id="ops-tab-core">
                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[permission]">
                                <?php _e("Who can view this plugin's data?", "off-page-seo") ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <select name="core[permission]" class="ops-select2">
                                <?php if (is_multisite()): ?>
                                    <option value="manage_sites" <?php echo (isset($settings['core']['permission']) && 'manage_sites' == $settings['core']['permission']) ? "selected" : ""; ?>>
                                        <?php _e('Super administrator', 'off-page-seo') ?>
                                    </option>
                                <?php endif; ?>
                                <option value="edit_theme_options" <?php echo (isset($settings['core']['permission']) && 'edit_theme_options' == $settings['core']['permission']) ? "selected" : ""; ?>>
                                    <?php _e('Administrator', 'off-page-seo') ?>
                                </option>
                                <option value="read_private_pages" <?php echo (isset($settings['core']['permission']) && 'read_private_pages' == $settings['core']['permission']) ? "selected" : ""; ?>>
                                    <?php _e('Editor', 'off-page-seo') ?>
                                </option>
                                <option value="upload_files" <?php echo (isset($settings['core']['permission']) && 'upload_files' == $settings['core']['permission']) ? "selected" : ""; ?>>
                                    <?php _e('Author', 'off-page-seo') ?>
                                </option>
                                <option value="edit_posts" <?php echo (isset($settings['core']['permission']) && 'edit_posts' == $settings['core']['permission']) ? "selected" : ""; ?>>
                                    <?php _e('Contributor', 'off-page-seo') ?>
                                </option>
                                <option value="read" <?php echo (isset($settings['core']['permission']) && 'read' == $settings['core']['permission']) ? "selected" : ""; ?>>
                                    <?php _e('Subscriber', 'off-page-seo') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[language]">
                                <?php _e('Select your language', 'off-page-seo'); ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <select name="core[language]" class="ops-select2">
                                <?php $languages = Off_Page_SEO::ops_lang_array() ?>
                                <?php foreach ($languages as $key => $value): ?>
                                    <option value="<?php echo $key ?>" <?php echo ($key == $settings['core']['language']) ? "selected" : ""; ?>><?php echo $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[google_domain]">
                                <?php _e('Select the Google domain', 'off-page-seo') ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <select name="core[google_domain]" class="ops-select2">
                                <?php $google_domains = Off_Page_SEO::ops_google_domains_array() ?>
                                <?php foreach ($google_domains as $key => $value): ?>
                                    <option value="<?php echo $key ?>" <?php echo ($key == $settings['core']['google_domain']) ? "selected" : ""; ?>><?php echo $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[notification_email]">
                                <?php _e('Notification email', 'off-page-seo') ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <input type="email" name="core[notification_email]" value="<?php echo (isset($settings['core']['notification_email'])) ? $settings['core']['notification_email'] : ""; ?>" placeholder="your@email.com"/>
                        </div>
                    </div>
                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[date_format]">
                                <?php _e('Date format', 'off-page-seo') ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <select name="core[date_format]" class="select2">
                                <option value="m/d/Y" <?php echo (isset($settings['core']['date_format']) && 'm/d/Y' == $settings['core']['date_format']) ? "selected" : ""; ?>>
                                    04/16/2015
                                </option>
                                <option value="F d, Y" <?php echo (isset($settings['core']['date_format']) && 'F d, Y' == $settings['core']['date_format']) ? "selected" : ""; ?>>
                                    April 16, 2015
                                </option>
                                <option value="j.n.Y" <?php echo (isset($settings['core']['date_format']) && 'j.n.Y' == $settings['core']['date_format']) ? "selected" : ""; ?>>
                                    16. 4. 2015
                                </option>
                                <option value="jS M Y" <?php echo (isset($settings['core']['date_format']) && 'jS M Y' == $settings['core']['date_format']) ? "selected" : ""; ?>>
                                    16th Apr 2015
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[currency]">
                                <?php _e('Currency', 'off-page-seo') ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <input type="text" name="core[currency]" value="<?php echo (isset($settings['core']['currency'])) ? $settings['core']['currency'] : ""; ?>" placeholder="$"/>
                        </div>
                    </div>
                    <div class="ops-row" id="ops-select-post-types">
                        <div class="ops-d"><?php _e('Use this plugin on the following post types:', 'off-page-seo') ?></div>
                        <p><?php _e('By selecting post type, you will be able to track shares and keywords.', 'off-page-seo') ?></p>

                        <div class="ops-left-col">
                            <h4><?php _e('All post types', 'off-page-seo') ?> </h4>

                            <div class="ops-all-post-types">
                                <?php
                                $pts = Off_Page_SEO::ops_get_allowed_post_types();
                                ?>
                                <?php foreach ($pts as $pt): ?>
                                    <?php if (!self::ops_post_type_is_checked($pt)): ?>
                                        <div class="ops-post-type" data-pt="<?php echo $pt ?>">
                                            <?php echo $pt ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="ops-right-col">
                            <h4><?php _e('Selected post types', 'off-page-seo') ?></h4>

                            <div class="ops-selected-post-types">
                                <?php foreach ($pts as $pt): ?>
                                    <?php if (self::ops_post_type_is_checked($pt)): ?>
                                        <div class="ops-post-type" data-pt="<?php echo $pt ?>">
                                            <input type="hidden" value="<?php echo $pt ?>" name="core[post_types][]"/>
                                            <?php echo $pt ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="ops-row">
                        <a href="<?php echo admin_url() ?>admin.php?page=ops_settings&log=show"><?php _e('Show log', 'off-page-seo') ?></a>
                    </div>


                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[currency]">
                                <?php _e('curl version', 'off-page-seo') ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <?php
                            if (function_exists('curl_version')) {
                                echo curl_version()['libz_version'];
                            } else {
                                _e("ERROR: you don't have CURL enabled, most of the features for this plugin will not work.", "off-page-seo");
                                Off_Page_SEO::ops_create_log_entry('hosting_error', 'error', __("You don't have the CURL functionality active on your server. Most of this plugin's features will not work.", 'off-page-seo'));
                            }
                            ?>
                        </div>
                    </div>
                    <div class="ops-row">
                        <div class="ops-left">
                            <label for="core[currency]">
                                <?php _e('max_ecec_time', 'off-page-seo') ?>
                            </label>
                        </div>
                        <div class="ops-right">
                            <b><?php echo ini_get('max_execution_time') . ' s'; ?></b>
                            <?php _e('(how long a script can run on the server before it stops. To determine how long a ranking update lasts, check the log and compare the first and last keyword checked)', 'off-page-seo') ?>
                        </div>
                    </div>
                </div>


                <?php if ($active_rank_report): ?>
                    <!-- RANK REPORT SETTINGS -->
                    <h3><?php _e('Rank Report Settings', 'off-page-seo') ?></h3>

                    <div class="postbox ops-padding ops-tab">
                        <!--                        <div class="ops-row">-->
                        <!--                            <div class="ops-left">-->
                        <!--                                <label for="rank_report[show_estimation]">-->
                        <!--                                    --><?php //_e('Show visitor estimation', 'off-page-seo') ?>
                        <!--                                </label>-->
                        <!--                            </div>-->
                        <!--                            <div class="ops-right">-->
                        <!--                                <input type="checkbox" name="rank_report[show_estimation]" --><?php //echo isset($settings['rank_report']['show_estimation']) && $settings['rank_report']['show_estimation'] == 'on' ? 'checked="checked"' : ''; ?><!--/>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <div class="ops-row">
                            <div class="ops-left">
                                <label for="rank_report[show_estimation]">
                                    <?php _e('Send email with rank report', 'off-page-seo') ?>
                                </label>
                            </div>
                            <div class="ops-right">

                                <?php
                                if (OPS_PREMIUM == false) {
                                    ?>
                                    <div class="ops-sad-premium-message no-right-left-margin">
                                        <?php _e('We are sorry, this feature is available in premium version only.', 'off-page-seo') ?>
                                        <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <input type="checkbox" name="rank_report[send_notification]" <?php echo isset($settings['rank_report']['send_notification']) && $settings['rank_report']['send_notification'] == 'on' ? 'checked="checked"' : ''; ?>/>
                                    <?php
                                }
                                ?>


                            </div>
                        </div>
                        <div class="ops-row">
                            <div class="ops-left">
                                <label for="">
                                    <?php _e('Delete inactive keywords', 'off-page-seo') ?>
                                </label>
                            </div>
                            <div class="ops-right">
                                <a href="<?php echo admin_url() ?>admin.php?page=ops_settings&ops_control=delete_inactive" class="button" id="ops-delete-inactive-keywords"><?php _e('Delete now', 'off-page-seo') ?></a>

                                <p><?php _e("When you delete keyword, it stays in the database as 'inactive', so you dont lose any data. By clicking this button, you will delete these inactive keywords.", "off-page-seo") ?></p>
                            </div>
                        </div>
                        <div class="ops-row">
                            <div class="ops-left">
                                <label for="">
                                    <?php _e('Perform ranking test now', 'off-page-seo') ?>
                                </label>
                            </div>
                            <div class="ops-right">
                                <a href="<?php echo admin_url() ?>admin.php?page=ops_settings&ops_control=run_rank_report" class="button" id="ops-run-rank-report"><?php _e('Schedule in 10s', 'off-page-seo') ?></a>

                                <p><?php _e('CRON will be set to perform ranking test in 10 seconds.', 'off-page-seo') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if ($active_backlinks): ?>
                    <!-- RANK REPORT SETTINGS -->
                    <h3><?php _e('Backlinks Settings', 'off-page-seo') ?></h3>
                    <div class="postbox ops-padding ops-tab">
                        <div class="ops-row">
                            <div class="ops-left">
                                <label for="backlinks[reciprocal_check]">
                                    <?php _e('Run reciprocal check', 'off-page-seo') ?>
                                </label>
                            </div>
                            <div class="ops-right">
                                <input type="checkbox" class="ops-reciprocal-trigger" name="backlinks[reciprocal_check]" <?php echo isset($settings['backlinks']['reciprocal_check']) && $settings['backlinks']['reciprocal_check'] == 'on' ? 'checked="checked"' : ''; ?>/>
                            </div>
                        </div>
                        <div class="ops-reciprocal-settings <?php echo isset($settings['backlinks']['reciprocal_check']) && $settings['backlinks']['reciprocal_check'] == 'on' ? 'active' : ''; ?>">
                            <div class="ops-row">
                                <div class="ops-left">
                                    <label for="backlinks[send_email_not_found]">
                                        <?php _e('Nofity me if the backlink is not found', 'off-page-seo') ?>
                                    </label>
                                </div>
                                <div class="ops-right">
                                    <input type="checkbox" name="backlinks[send_email_not_found]" <?php echo isset($settings['backlinks']['send_email_not_found']) && $settings['backlinks']['send_email_not_found'] == 'on' ? 'checked="checked"' : ''; ?>/>

                                    <p><?php _e('We will send you an email if the backlink is not found on the control website and it was present last time.', 'off-page-seo') ?></p>
                                </div>
                            </div>
                            <div class="ops-row">
                                <div class="ops-left">
                                    <label>
                                        <?php _e('Run reciprocal check now', 'off-page-seo') ?>
                                    </label>
                                </div>
                                <div class="ops-right">
                                    <a href="<?php echo admin_url() ?>admin.php?page=ops_settings&ops_control=run_reciprocal_check" class="button" id="ops-delete-inactive-keywords"><?php _e('Schedule in 10s', 'off-page-seo') ?></a>

                                    <p><?php _e('This will trigger backlink control. The reciprocal check is split in to batches of 5 controls per script trigger, so it can take a while to check all backlinks.', 'off-page-seo') ?></p>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>

                <?php if ($active_google_api): ?>
                    <!-- RANK REPORT SETTINGS -->
                    <h3><?php _e('Google API', 'off-page-seo') ?></h3>
                    <div class="postbox ops-padding ops-tab" id="ops-tab-report">
                        <div class="ops-row">
                            <div class="ops-left">
                                <label for="google_api[authorization_code]">
                                    <?php _e('Authorization code', 'off-page-seo') ?>
                                </label>
                            </div>

                            <div class="ops-right">
                                <?php if (isset($settings['google_api']['authorization_code']) && $settings['google_api']['authorization_code'] != ''): ?>
                                    <a href="<?php echo admin_url(); ?>admin.php?page=ops_settings&ops_control=forget_authorization" class="button" id="ops-forget-authorization">Forget authorization code</a>
                                    <input type="hidden" name="google_api[authorization_code]" value="<?php echo isset($settings['google_api']['authorization_code']) && $settings['google_api']['authorization_code'] != '' ? $settings['google_api']['authorization_code'] : ''; ?>"/>
                                <?php else : ?>
                                    <input type="text" name="google_api[authorization_code]" value="<?php echo isset($settings['google_api']['authorization_code']) && $settings['google_api']['authorization_code'] != '' ? $settings['google_api']['authorization_code'] : ''; ?>"/>
                                    <p>
                                        <?php _e('You need to authorize this plugin to get your Google Webmaster Tools data.', 'off-page-seo') ?>
                                        <?php $google_client = new OPS_Google_API(); ?>
                                        <a href="<?php echo $google_client->authUrl; ?>" target="_blank"><?php _e('Get authorization code.', 'off-page-seo') ?></a>
                                    </p>
                                <?php endif; ?>
                            </div>


                        </div>
                        <div class="ops-row">
                            <div class="ops-left">
                                <label for="google_api[period]">
                                    <?php _e('Compare period', 'off-page-seo') ?>
                                </label>
                            </div>
                            <div class="ops-right">
                                <select name="google_api[period]">
                                    <option value="week" <?php echo (isset($settings['google_api']['period']) && $settings['google_api']['period'] == 'week') ? 'selected="selected"' : ''; ?>><?php _e('1 week', 'off-page-seo') ?></option>
                                    <option value="month" <?php echo (isset($settings['google_api']['period']) && $settings['google_api']['period'] == 'month') ? 'selected="selected"' : ''; ?>><?php _e('1 month', 'off-page-seo') ?></option>
                                    <option value="quarter" <?php echo (isset($settings['google_api']['period']) && $settings['google_api']['period'] == 'quarter') ? 'selected="selected"' : ''; ?>><?php _e('3 months', 'off-page-seo') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($active_share_counter): ?>
                    <!-- SHARE COUNTER SETTINGS -->
                    <h3><?php _e('Share Counter Settings', 'off-page-seo') ?></h3>

                    <div class="postbox ops-padding ops-tab" id="ops-tab-share-counter">
                        <div class="ops-row">
                            <div class="ops-left">
                                <label for="share_counter[perform_every]">
                                    <?php _e('Check shares every', 'off-page-seo') ?>
                                </label>
                            </div>
                            <div class="ops-right">
                                <select name="share_counter[perform_every]">
                                    <option value="86400" <?php echo (isset($settings['share_counter']['perform_every']) && $settings['share_counter']['perform_every'] == '86400') ? 'selected="selected"' : ''; ?>><?php _e('1 day', 'off-page-seo') ?></option>
                                    <option value="172800" <?php echo (isset($settings['share_counter']['perform_every']) && $settings['share_counter']['perform_every'] == '172800') ? 'selected="selected"' : ''; ?>><?php _e('2 days', 'off-page-seo') ?></option>
                                    <option value="259200" <?php echo (isset($settings['share_counter']['perform_every']) && $settings['share_counter']['perform_every'] == '259200') ? 'selected="selected"' : ''; ?>><?php _e('3 days', 'off-page-seo') ?></option>
                                    <option value="432000" <?php echo (isset($settings['share_counter']['perform_every']) && $settings['share_counter']['perform_every'] == '432000') ? 'selected="selected"' : ''; ?>><?php _e('5 days', 'off-page-seo') ?></option>
                                    <option value="604800" <?php echo (isset($settings['share_counter']['perform_every']) && $settings['share_counter']['perform_every'] == '604800') ? 'selected="selected"' : ''; ?>><?php _e('7 days', 'off-page-seo') ?></option>
                                    <option value="1209600" <?php echo (isset($settings['share_counter']['perform_every']) && $settings['share_counter']['perform_every'] == '1209600') ? 'selected="selected"' : ''; ?>><?php _e('14 days', 'off-page-seo') ?></option>
                                    <option value="2419200" <?php echo (isset($settings['share_counter']['perform_every']) && $settings['share_counter']['perform_every'] == '2419200') ? 'selected="selected"' : ''; ?>><?php _e('1 month', 'off-page-seo') ?></option>
                                </select>

                                <p><?php _e('The more content you have, the longer the period should be as this can be expensive on web hosting. The posts are checked individually once the user reads them.', 'off-page-seo') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <input type="submit" class="button button-primary" value="<?php _e('Save Settings', 'off-page-seo') ?>">
            </form>
        </div>
        <?php
    }

    /**
     * If the post types is checked
     */
    public static function ops_post_type_is_checked($type)
    {
        $settings = Off_Page_SEO::ops_get_settings();
        if (isset($settings['core']['post_types'])) {
            foreach ($settings['core']['post_types'] as $pt) {
                if ($pt == $type) {
                    return true;
                }
            }
        }
        return false;
    }

    function show_log()
    {
        $settings = Off_Page_SEO::$settings;
        $logs = unserialize(Off_Page_SEO::ops_get_option('ops_log'));
        ?>
        <div class="wrap">
            <h2><?php _e('Off Page SEO Log', 'off-page-seo') ?></h2>

            <p><?php _e('If you find any bug or if you have any idea how to improve this plugin, feel free to let us know at info@offpageseoplugin.com.', 'off-page-seo') ?></p>

            <div class="postbox ops-padding">
                <table class="ops-table" id="ops-log-table">
                    <tr>
                        <th class="time"><?php _e('Time', 'off-page-seo') ?></th>
                        <th class="action"><?php _e('Action', 'off-page-seo') ?></th>
                        <th class="type"><?php _e('Type', 'off-page-seo') ?></th>
                        <th><?php _e('Message', 'off-page-seo') ?></th>
                    </tr>
                    <?php if (count($logs) > 0): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="<?php echo isset($log['type']) ? $log['type'] : 'info' ?>">
                                <td>
                                    <?php echo date($settings['core']['date_format'] . ' - H:i:s', $log['time']); ?>
                                </td>
                                <td>
                                    <?php echo isset($log['action']) ? $log['action'] : '' ?>
                                </td>
                                <td>
                                    <?php echo isset($log['type']) ? $log['type'] : '' ?>
                                </td>
                                <td>
                                    <?php echo isset($log['message']) ? $log['message'] : '' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <?php

    }

}
