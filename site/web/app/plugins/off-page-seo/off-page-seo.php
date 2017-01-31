<?php
/*
  Plugin Name: Off Page SEO
  Plugin URI: http://www.offpageseoplugin.com/
  Description: Provides various tools to help you with the off-page SEO.
  Version: 2.2.22.
  Author: Jakub Glos
  Author URI: http://www.offpageseoplugin.com/
  License: GPLv3
  Text Domain: off-page-seo
 */
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(-1);

// don't call the file directly
if (!defined('ABSPATH'))
    return;

if (version_compare(phpversion(), '5.4.0', '<')) {
    echo "Off Page SEO Plugin requires PHP version 5.4 above.";
    return;
}

require_once('modules/core/ops.php');

define('OPS_PLUGIN_PATH', plugins_url() . '/off-page-seo');
define('OPS_PREMIUM', ops_is_the_plugin_premium());

/*
 * INCLUDE CORE
 */
require_once('modules/core/ops-ads.php');
require_once('modules/core/ops-curl.php');
require_once('modules/core/ops-email.php');
require_once('modules/core/ops-update.php');
require_once('modules/core/ops-dashboard.php');
require_once('modules/core/ops-settings.php');
require_once('modules/core/ops-install.php');

// Opportunities
require_once('modules/core/ops-opportunities.php');
require_once('modules/opportunities/ops-opportunity-comment.php');
require_once('modules/opportunities/ops-opportunity-buy.php');

new Off_Page_SEO();
new OPS_Ads();

/*
 * ACTIVATE
 */
register_activation_hook(__FILE__, 'ops_on_activate');
function ops_on_activate()
{
    require_once('modules/core/ops-install.php');
    new OPS_install();
}

/* RUN Update */
new OPS_Update();


$settings = Off_Page_SEO::ops_get_settings();
if (isset($settings['module']['rank_report']) && $settings['module']['rank_report'] == 1) {
    require_once('modules/rank-report/ops-rank-report.php');
    require_once('modules/rank-report/ops-rank-report-ui.php');
    new OPS_Rank_Report();
    new OPS_Rank_Report_UI();
}

if (isset($settings['module']['google_api']) && $settings['module']['google_api'] == 1 && is_admin()) {
    require_once('modules/google-api/ops-google-api.php');
    require_once('modules/google-api/ops-google-api-ui.php');
    new OPS_Google_API_UI();
}

if (isset($settings['module']['backlinks']) && $settings['module']['backlinks'] == 1) {
    require_once('modules/backlinks/ops-backlinks.php');
    require_once('modules/backlinks/ops-backlinks-reciprocal-check.php');
    require_once('modules/backlinks/ops-backlinks-ui.php');
    new OPS_Backlinks();
    new OPS_Backlinks_Reciprocal_Check();
    new OPS_Backlinks_UI();
}

if (isset($settings['module']['share_counter']) && $settings['module']['share_counter'] == 1) {
    require_once('modules/share-counter/ops-share-counter.php');
    require_once('modules/share-counter/ops-share-counter-ui.php');
    new OPS_Share_Counter();
    new OPS_Share_Counter_UI();
}

