<?php
if (!defined('ABSPATH'))
    exit();

if (!class_exists('OPS_Google_API_UI')) {

    class OPS_Google_API_UI
    {

        public $googleQuery;

        public $googlePages;

        public $googleQueryPreviousPeriod;

        function __construct()
        {
            /*
             * Dasbhoard functions
             */
            add_action('ops/dashboard_tabs', array($this, 'ops_google_api_dashboard_tab'), 8);

            if (isset($_GET['page']) && $_GET['page'] == 'ops') {
                // we need to run this before fetching any data
                $google_client = new OPS_Google_API();
                $tokens = $google_client->get_cached_tokens();

                if (strlen($tokens) < 5) {
                    $settings = Off_Page_SEO::$settings;
                    if (isset($settings['google_api']['authorization_code']) && strlen($settings['google_api']['authorization_code']) > 3) {
                        // proceed with authentication
                        $google_client->authenticate_google_api();

                    } else {
                        // put into log
                        Off_Page_SEO::ops_create_log_entry('google_api', 'alert', __('You have not set up the authorization code.', 'off-page-seo'));

                        // insert data into keywords
                        add_action('ops/dashboard_google_api', array($this, 'ops_google_api_setup_message'));

                        return;
                    }

                }

                // insert data to dashboard
                add_action('ops/dashboard_google_api', array($this, 'ops_google_api_dashboard_body'));

                // set up period
                $period = isset(Off_Page_SEO::$settings['google_api']['period']) ? Off_Page_SEO::$settings['google_api']['period'] : 'month';

                // set up data for dashboard
                $dates = $this->ops_get_dates($period);
                $dates_previous = $this->ops_get_dates($period, 1);

                // save data in class instance for later user accross the website
                $this->googlePages = $this->ops_get_google_data('page', $dates['start'], $dates['end'], 7);
                $this->googleQuery = $this->ops_get_google_data('query', $dates['start'], $dates['end']);
                $this->googleQueryPreviousPeriod = $this->ops_get_google_data('query', $dates_previous['start'], $dates_previous['end']);

                // insert data into keywords
                add_action('ops/keyword_data_from_api', array($this, 'ops_keyword_data_from_api'));

                // add data to sidebar
                add_action('ops/dashboard_sidebar', array($this, 'ops_dashboard_sidebar_google'));
            }

        }

        function ops_dashboard_sidebar_google()
        {
            $settings = Off_Page_SEO::$settings;
            $data = $this->googleQuery;
            $data_pages = $this->googlePages;
            $data_previous = $this->googleQueryPreviousPeriod;

            $total_visitors = $this->ops_get_total_organic_visitors($data) == 0 ? 1 : $this->ops_get_total_organic_visitors($data);
            $total_visitors_previous = $this->ops_get_total_organic_visitors($data_previous) == 0 ? 1 : $this->ops_get_total_organic_visitors($data_previous);

            $total_impressions = $this->ops_get_total_organic_impressions($data) == 0 ? 1 : $this->ops_get_total_organic_impressions($data);
            $total_impressions_previous = $this->ops_get_total_organic_impressions($data_previous) == 0 ? 1 : $this->ops_get_total_organic_impressions($data_previous);
            ?>

            <?php
            if (OPS_PREMIUM == false) {
                ?>
                <img src="<?php echo OPS_PLUGIN_PATH . '/img/google-api-example.jpg' ?>" alt="google api example">
                <div class="ops-sad-premium-message no-right-left-margin">
                    <?php _e('We are sorry, this feature is available in premium version only.', 'off-page-seo') ?>
                    <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                </div>
                <?php
                return;
            }
            ?>
            <h4>
                <?php _e('Organic Traffic', 'off-page-seo') ?>
                <span class="ops-hint left">
                    <i>
                        <?php _e('All data are generated from 50 most successful keywords and are delayed a week. Selected period: ', 'off-page-seo') ?>
                        <b><?php echo isset($settings['google_api']['period']) ? $settings['google_api']['period'] : 'month'; ?></b>
                    </i>
                </span>
            </h4>
            <div class="ops-stat">
                <?php $visitors_change = ($total_visitors / $total_visitors_previous) - 1; ?>
                <div class="ops-title">
                    <?php _e('Total Organic Visitors', 'off-page-seo') ?>

                </div>
                <div class="ops-value <?php echo $visitors_change > 0 ? 'good' : 'bad'; ?>">
                    <?php echo $total_visitors ?>&nbsp;&nbsp;
                    <span>(<b>&Delta; <?php echo round($visitors_change * 100) ?>%</b>, <?php echo $total_visitors_previous ?>)</span>
                </div>
            </div>
            <div class="ops-stat">
                <?php $impressions_change = ($total_impressions / $total_impressions_previous) - 1; ?>
                <div class="ops-title">
                    <?php _e('Total Impressions', 'off-page-seo') ?>
                </div>
                <div class="ops-value <?php echo $impressions_change > 0 ? 'good' : 'bad'; ?>">
                    <?php echo $total_impressions ?>&nbsp;&nbsp;
                    <span>(<b>&Delta; <?php echo round($impressions_change * 100) ?>%</b>, <?php echo $total_impressions_previous ?>)</span>
                </div>
            </div>


            <h4>
                <?php _e('Most successful keywords', 'off-page-seo') ?>
            </h4>
            <div class="ops-stat ops-stat-keywords">
                <?php if (count($data) > 0): ?>
                    <table>
                        <thead>
                        <tr>
                            <th>
                                &nbsp;
                            </th>
                            <th class="ops-small">
                                <?php _e('Clicks', 'off-page-seo'); ?>
                            </th>
                            <th class="ops-small">
                                <?php _e('Position', 'off-page-seo'); ?>
                            </th>
                        </tr>

                        </thead>
                        <tbody>
                        <?php $kw_n = 0; ?>
                        <?php foreach ($data as $keyword => $values): ?>
                            <?php
                            $kw_n++;
                            if ($kw_n == 7) {
                                break;
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php echo $keyword ?>
                                    <a href="<?php echo Off_Page_SEO::ops_get_request_url($keyword) ?>" target="_blank" class="ops-link-to-page"></a>
                                </td>
                                <td>
                                    <?php echo $values['clicks'] ?>
                                </td>
                                <td>
                                    <?php echo number_format($values['position'], 0) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="<?php echo get_admin_url() ?>admin.php?page=ops&tab=google_api&dimension=query" class="ops-view-more button">
                        <?php _e('View more','off-page-seo');?>
                    </a>
                <?php else : ?>
                    <?php _e('No keywords were fetched from the Google API.', 'off-page-seo'); ?>
                <?php endif; ?>


            <h4>
                <?php _e('Most successful pages', 'off-page-seo') ?>
            </h4>
            <div class="ops-stat ops-stat-pages">
                <?php if (count($data_pages) > 0): ?>
                    <table>
                        <thead>
                        <tr>
                            <th>
                                &nbsp;
                            </th>
                            <th class="ops-small">
                                <?php _e('Clicks', 'off-page-seo'); ?>
                            </th>
                            <th class="ops-small">
                                <?php _e('Position', 'off-page-seo'); ?>
                            </th>
                        </tr>

                        </thead>
                        <tbody>
                        <?php $kw_n = 0; ?>
                        <?php foreach ($data_pages as $page => $values): ?>
                            <?php
                            $kw_n++;
                            if ($kw_n == 7) {
                                break;
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php echo str_replace(get_home_url(), '', $page); ?>
                                    <a href="<?php echo $page ?>" target="_blank" class="ops-link-to-page"></a>
                                </td>
                                <td>
                                    <?php echo $values['clicks'] ?>
                                </td>
                                <td>
                                    <?php echo number_format($values['position'], 0) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="<?php echo get_admin_url() ?>admin.php?page=ops&tab=google_api&dimension=page" class="ops-view-more button">
                        <?php _e('View more','off-page-seo');?>
                    </a>
                <?php else : ?>
                    <?php _e('No pages were fetched from the Google API.', 'off-page-seo'); ?>
                <?php endif; ?>

            </div>

            <?php
        }

        public function ops_google_api_dashboard_tab()
        {
            ?>
            <li class="<?php echo isset($_GET['tab']) && $_GET['tab'] == 'google_api' ? 'active' : '' ?>">
                <a href="admin.php?page=ops&tab=google_api" class="ops-tab-switcher <?php echo isset($_GET['tab']) && $_GET['tab'] == 'google_api' ? 'active' : '' ?>">
                    <?php _e('Google API', 'off-page-seo') ?>
                </a>

                <div class="ops-down-arrow"></div>
                <ul>
                    <li>
                        <a href="admin.php?page=ops&tab=google_api&dimension=query" class="<?php echo isset($_GET['dimension']) && $_GET['dimension'] == 'query' ? 'active' : '' ?>">
                            <?php _e('Keywords', 'off-page-seo') ?>
                        </a>
                    </li>
                    <li>
                        <a href="admin.php?page=ops&tab=google_api&dimension=page" class="<?php echo isset($_GET['dimension']) && $_GET['dimension'] == 'page' ? 'active' : '' ?>">
                            <?php _e('Pages', 'off-page-seo') ?>
                        </a>
                    </li>
                    <li>
                        <a href="admin.php?page=ops&tab=google_api&dimension=country" class="<?php echo isset($_GET['dimension']) && $_GET['dimension'] == 'country' ? 'active' : '' ?>">
                            <?php _e('Country', 'off-page-seo') ?>
                        </a>
                    </li>
                    <li>
                        <a href="admin.php?page=ops&tab=google_api&dimension=device" class="<?php echo isset($_GET['dimension']) && $_GET['dimension'] == 'device' ? 'active' : '' ?>">
                            <?php _e('Device', 'off-page-seo') ?>
                        </a>
                    </li>
                </ul>
            </li>
            <?php
        }


        public function ops_google_api_dashboard_body()
        {

            if (isset($_GET['dimension']) && $_GET['dimension'] == 'query') {
                $this->ops_dashboard_query();
            } elseif (isset($_GET['dimension']) && $_GET['dimension'] == 'page') {
                $this->ops_dashboard_page();
            } elseif (isset($_GET['dimension']) && $_GET['dimension'] == 'country') {
                $this->ops_dashboard_country();
            } elseif (isset($_GET['dimension']) && $_GET['dimension'] == 'device') {
                $this->ops_dashboard_device();
            } else {
                $this->ops_dashboard_clean();
            }
        }

        public function ops_google_api_setup_message()
        {
            ?>
            <div class="ops-dashboard-left" id="ops-dashboard-shares">
                <div class="postbox ops-padding">
                    <?php _e('Please set up Google account in the Settings.', 'off-page-seo') ?>
                    <?php
                    if (OPS_PREMIUM == false) {
                        ?>
                        <div class="ops-sad-premium-message no-right-left-margin">
                            <?php _e('We are sorry, this feature is available in premium version only.', 'off-page-seo') ?>
                            <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }

        public function ops_dashboard_clean()
        {
            ?>
            <div class="ops-dashboard-left" id="ops-dashboard-shares">
                <div class="postbox ops-padding">
                    <?php _e('Please select item from the dropdown menu.', 'off-page-seo') ?>
                </div>
            </div>
            <?php
        }

        public function ops_dashboard_query()
        {

            if (OPS_PREMIUM == false) {
                ?>
                <div class="ops-dashboard-left" id="ops-dashboard-shares">
                    <div class="postbox ops-padding">
                        <h3><?php _e('Keywords', 'off-page-seo') ?></h3>

                        <div class="ops-sad-premium-message no-right-left-margin">
                            <?php _e('We are sorry, this feature is available in premium version only.', 'off-page-seo') ?>
                            <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                        </div>
                    </div>
                </div>
                <?php
                return;
            }


            $dates = $this->ops_get_dates(isset($_GET['base']) ? $_GET['base'] : 'month');
            $data = $this->ops_get_google_data('query', $dates['start'], $dates['end'], 100);
            ?>
            <div class="ops-dashboard-left" id="ops-dashboard-shares">
                <div class="postbox ops-padding">
                    <h3><?php _e('Keywords', 'off-page-seo') ?></h3>

                    <div class="ops-posts-from">
                        <?php _e('Display data for last', 'off-page-seo') ?>
                        <a href="admin.php?page=ops&tab=google_api&dimension=query&base=week" class="<?php echo $dates['base'] == 'week' ? 'active' : '' ?>"><?php _e('week', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=query&base=month" class="<?php echo $dates['base'] == 'month' ? 'active' : '' ?>"><?php _e('month', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=query&base=quarter" class="<?php echo $dates['base'] == 'quarter' ? 'active' : '' ?>"><?php _e('3 months', 'off-page-seo') ?></a>
                    </div>
                    <table class="ops-table">
                        <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><?php _e('Clicks', 'off-page-seo') ?></th>
                            <th><?php _e('Position', 'off-page-seo') ?></th>
                            <th><?php _e('Impressions', 'off-page-seo') ?></th>
                            <th><?php _e('CTR', 'off-page-seo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($data) != 0): ?>
                            <?php foreach ($data as $key => $row): ?>
                                <tr>
                                    <td>
                                        <?php echo $key ?>
                                        <a href="<?php echo Off_Page_SEO::ops_get_seach_url($key) ?>" target="_blank">
                                            <span class="ops-go-link"></span>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['clicks'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['position'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['impressions'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['ctr'], 2) * 100 ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">
                                    <p>
                                        <?php _e("If you can't see anything, you may not have any data in Webmaster Tools, or you may need to check the error", "off-page-seo") ?>
                                        <a href="<?php echo get_admin_url() ?>/admin.php?page=ops_settings&log=show"><?php _e('error log', 'off-page-seo') ?></a>.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }


        public function ops_dashboard_page()
        {
            if (OPS_PREMIUM == false) {
                ?>
                <div class="ops-dashboard-left" id="ops-dashboard-shares">
                    <div class="postbox ops-padding">
                        <h3><?php _e('Most successful pages', 'off-page-seo') ?></h3>

                        <div class="ops-sad-premium-message no-right-left-margin">
                            <?php _e('We are sorry, this feature is available in premium version only.', 'off-page-seo') ?>
                            <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                        </div>
                    </div>
                </div>
                <?php
                return;
            }

            $dates = $this->ops_get_dates(isset($_GET['base']) ? $_GET['base'] : 'month');
            $data = $this->ops_get_google_data('page', $dates['start'], $dates['end'], 100);
            ?>
            <div class="ops-dashboard-left" id="ops-dashboard-shares">
                <div class="postbox ops-padding">

                    <div class="ops-posts-from">
                        <?php _e('Display data for last', 'off-page-seo') ?>
                        <a href="admin.php?page=ops&tab=google_api&dimension=page&base=week" class="<?php echo $dates['base'] == 'week' ? 'active' : '' ?>"><?php _e('week', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=page&base=month" class="<?php echo $dates['base'] == 'month' ? 'active' : '' ?>"><?php _e('month', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=page&base=quarter" class="<?php echo $dates['base'] == 'quarter' ? 'active' : '' ?>"><?php _e('3 months', 'off-page-seo') ?></a>
                    </div>
                    <table class="ops-table">
                        <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><?php _e('Clicks', 'off-page-seo') ?></th>
                            <th><?php _e('Position', 'off-page-seo') ?></th>
                            <th><?php _e('Impressions', 'off-page-seo') ?></th>
                            <th><?php _e('CTR', 'off-page-seo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($data) != 0): ?>
                            <?php foreach ($data as $key => $row): ?>
                                <tr>
                                    <td>
                                        <?php echo $key ?>
                                        <a href="<?php echo $key ?>" target="_blank">
                                            <span class="ops-go-link"></span>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['clicks'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['position'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['impressions'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['ctr'], 2) * 100 ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">
                                    <p>
                                        <?php _e("If you can't see anything, you may not have any data in Webmaster Tools, or you may need to check the error", "off-page-seo") ?>
                                        <a href="<?php echo get_admin_url() ?>/admin.php?page=ops_settings&log=show"><?php _e('error log', 'off-page-seo') ?></a>.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }


        public function ops_dashboard_country()
        {
            if (OPS_PREMIUM == false) {
                ?>
                <div class="ops-dashboard-left" id="ops-dashboard-shares">
                    <div class="postbox ops-padding">
                        <h3><?php _e('Which countries are your visitors from?', 'off-page-seo') ?></h3>

                        <div class="ops-sad-premium-message no-right-left-margin">
                            <?php _e('We are sorry, this feature is available in premium version only.', 'off-page-seo') ?>
                            <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                        </div>
                    </div>
                </div>
                <?php
                return;
            }

            $dates = $this->ops_get_dates(isset($_GET['base']) ? $_GET['base'] : 'month');
            $data = $this->ops_get_google_data('country', $dates['start'], $dates['end'], 100);
            ?>
            <div class="ops-dashboard-left" id="ops-dashboard-shares">
                <div class="postbox ops-padding">
                    <h3><?php _e('Which countries are your visitors from?', 'off-page-seo') ?></h3>

                    <div class="ops-posts-from">
                        <?php _e('Display data for last', 'off-page-seo') ?>
                        <a href="admin.php?page=ops&tab=google_api&dimension=country&base=week" class="<?php echo $dates['base'] == 'week' ? 'active' : '' ?>"><?php _e('week', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=country&base=month" class="<?php echo $dates['base'] == 'month' ? 'active' : '' ?>"><?php _e('month', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=country&base=quarter" class="<?php echo $dates['base'] == 'quarter' ? 'active' : '' ?>"><?php _e('3 months', 'off-page-seo') ?></a>
                    </div>
                    <table class="ops-table">
                        <thead>
                        <tr>
                            <th class="ops-api-dimension">&nbsp;</th>
                            <th><?php _e('Clicks', 'off-page-seo') ?></th>
                            <th><?php _e('Position', 'off-page-seo') ?></th>
                            <th><?php _e('Impressions', 'off-page-seo') ?></th>
                            <th><?php _e('CTR', 'off-page-seo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($data) != 0): ?>
                            <?php foreach ($data as $key => $row): ?>
                                <tr>
                                    <td>
                                        <?php echo $key ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['clicks'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['position'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['impressions'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['ctr'], 2) * 100 ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">
                                    <p>
                                        <?php _e("If you can't see anything, you may not have any data in Webmaster Tools, or you may need to check the error", "off-page-seo") ?>
                                        <a href="<?php echo get_admin_url() ?>/admin.php?page=ops_settings&log=show"><?php _e('error log', 'off-page-seo') ?></a>.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }


        public function ops_dashboard_device()
        {
            if (OPS_PREMIUM == false) {
                ?>
                <div class="ops-dashboard-left" id="ops-dashboard-shares">
                    <div class="postbox ops-padding">
                        <h3><?php _e('On what devices is your website displayed in Google search results?', 'off-page-seo') ?></h3>

                        <div class="ops-sad-premium-message no-right-left-margin">
                            <?php _e('We are sorry, this feature is available in premium version only.', 'off-page-seo') ?>
                            <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                        </div>
                    </div>
                </div>
                <?php
                return;
            }

            $dates = $this->ops_get_dates(isset($_GET['base']) ? $_GET['base'] : 'month');
            $data = $this->ops_get_google_data('device', $dates['start'], $dates['end'], 100);
            ?>
            <div class="ops-dashboard-left" id="ops-dashboard-shares">
                <div class="postbox ops-padding">
                    <h3><?php _e('On what devices is your website displayed in Google search results?', 'off-page-seo') ?></h3>

                    <div class="ops-posts-from">
                        <?php _e('Display data for last', 'off-page-seo') ?>
                        <a href="admin.php?page=ops&tab=google_api&dimension=device&base=week" class="<?php echo $dates['base'] == 'week' ? 'active' : '' ?>"><?php _e('week', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=device&base=month" class="<?php echo $dates['base'] == 'month' ? 'active' : '' ?>"><?php _e('month', 'off-page-seo') ?></a>&nbsp;-
                        <a href="admin.php?page=ops&tab=google_api&dimension=device&base=quarter" class="<?php echo $dates['base'] == 'quarter' ? 'active' : '' ?>"><?php _e('3 months', 'off-page-seo') ?></a>
                    </div>
                    <table class="ops-table">
                        <thead>
                        <tr>
                            <th class="ops-api-dimension">&nbsp;</th>
                            <th><?php _e('Clicks', 'off-page-seo') ?></th>
                            <th><?php _e('Position', 'off-page-seo') ?></th>
                            <th><?php _e('Impressions', 'off-page-seo') ?></th>
                            <th><?php _e('CTR', 'off-page-seo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($data) != 0): ?>
                            <?php foreach ($data as $key => $row): ?>
                                <tr>
                                    <td>
                                        <?php echo $key ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['clicks'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['position'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['impressions'], 0) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['ctr'], 2) * 100 ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">
                                    <p>
                                        <?php _e("If you can't see anything, you may not have any data in Webmaster Tools, or you may need to check the error", "off-page-seo") ?>
                                        <a href="<?php echo get_admin_url() ?>/admin.php?page=ops_settings&log=show"><?php _e('error log', 'off-page-seo') ?></a>.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }


        public function ops_keyword_data_from_api($kw)
        {
            $settings = Off_Page_SEO::$settings;
            $data = $this->googleQuery;
            $dataPrevious = $this->googleQueryPreviousPeriod;
            $kw = mb_strtolower($kw); // in case user have uppercase in his keyword
            if (isset($data[$kw])):
                ?>
                <div class="ops-keyword-google-data">
                    <div class="ops-row">
                        <?php
                        if (isset($dataPrevious[$kw]['clicks'])) {
                            $diff_class = $this->ops_get_diff_class($dataPrevious[$kw]['clicks'] - $data[$kw]['clicks']);
                        } else {
                            $diff_class = 'neutral';
                        }
                        ?>
                        <div class="ops-number <?php echo $diff_class ?>">
                            <span class="previous"><?php echo isset($dataPrevious[$kw]['clicks']) ? number_format($dataPrevious[$kw]['clicks'], 0) : ''; ?></span>
                            <span class="current"><?php echo number_format($data[$kw]['clicks'], 0) ?></span>
                        </div>
                        <?php _e('Clicks', 'off-page-seo') ?>
                        <span class="ops-hint lighter right">
                            <i>
                                <?php _e('Number of times that users clicked on your website in search results compared to the previous period:', 'off-page-seo') ?>
                                <b><?php echo $settings['google_api']['period'] ?></b>.
                            </i>
                        </span>
                    </div>
                    <div class="ops-row">
                        <?php
                        if (isset($dataPrevious[$kw]['position'])) {
                            $diff_class = $this->ops_get_diff_class($dataPrevious[$kw]['position'] - $data[$kw]['position'], 1);
                        } else {
                            $diff_class = 'neutral';
                        }
                        ?>
                        <div class="ops-number <?php echo $diff_class ?>">
                            <span class="previous"><?php echo isset($dataPrevious[$kw]['position']) ? number_format($dataPrevious[$kw]['position'], 0) : ''; ?></span>
                            <span class="current"><?php echo number_format($data[$kw]['position'], 0) ?></span>
                        </div>
                        <?php _e('Avg. position', 'off-page-seo') ?>
                        <span class="ops-hint lighter right">
                            <i>
                                <?php _e('What is the average ranking of the keyword compared to the previous period:', 'off-page-seo') ?>
                                <b><?php echo $settings['google_api']['period'] ?></b>.
                            </i>
                        </span>
                    </div>
                    <div class="ops-row">
                        <?php
                        if (isset($dataPrevious[$kw]['impressions'])) {
                            $diff_class = $this->ops_get_diff_class($dataPrevious[$kw]['impressions'] - $data[$kw]['impressions']);
                        } else {
                            $diff_class = 'neutral';
                        }
                        ?>
                        <div class="ops-number <?php echo $diff_class ?>">
                            <span class="previous"><?php echo isset($dataPrevious[$kw]['impressions']) ? number_format($dataPrevious[$kw]['impressions'], 0) : ''; ?></span>
                            <span class="current"><?php echo number_format($data[$kw]['impressions'], 0) ?></span>
                        </div>
                        <?php _e('Impressions', 'off-page-seo') ?>
                        <span class="ops-hint lighter right">
                            <i>
                                <?php _e('Number of times this keyword was viewed in search results compared to the previous period:', 'off-page-seo') ?>
                                <b><?php echo $settings['google_api']['period'] ?></b>.
                            </i>
                        </span>
                    </div>
                </div>
            <?php endif;
        }


        public function ops_get_dates($period = 'month', $last_period = 0)
        {
            // offset all data for week as we don't have data in webmaster tools instantly
            $now = time() - 604800;
            if (1 == $last_period) {
                if (isset($period) && $period == 'quarter') {
                    $dates['start'] = $now - (2592000 * 6);
                    $dates['end'] = $now - (2592000 * 3);
                    $dates['base'] = 'quarter';
                } elseif (isset($period) && $period == 'week') {
                    $dates['start'] = $now - (604800 * 2);
                    $dates['end'] = $now - 604800;
                    $dates['base'] = 'week';
                } else {
                    $dates['start'] = $now - (2592000 * 2);
                    $dates['end'] = $now - 2592000;
                    $dates['base'] = 'month';
                }
            } else {
                if (isset($period) && $period == 'quarter') {
                    $dates['start'] = $now - (2592000 * 3);
                    $dates['end'] = $now;
                    $dates['base'] = 'quarter';
                } elseif (isset($period) && $period == 'week') {
                    $dates['start'] = $now - 604800;
                    $dates['end'] = $now;
                    $dates['base'] = 'week';
                } else {
                    $dates['start'] = $now - 2592000;
                    $dates['end'] = $now;
                    $dates['base'] = 'month';
                }
            }

            $dates['start'] = date('Y-m-d', $dates['start']);
            $dates['end'] = date('Y-m-d', $dates['end']);
            return $dates;
        }

        public function ops_get_google_data($dimension, $start, $end, $limit = '50', $type = 'web')
        {
            $client = new OPS_Google_API();

            try {
                $client->connect_to_google_api($client);
            } catch (Exception $e) {
                Off_Page_SEO::ops_create_log_entry('google_api', 'error', __('We could not connect to Google API, following error was returned: ', 'off-page-seo') . $e->getMessage());
                return array();
            }

            $service = new Google_Service_Webmasters($client->client);
            $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest;

            $request->setStartDate($start);
            $request->setEndDate($end);
            $request->setDimensions(array($dimension)); // page, query, country, device, https://developers.google.com/webmaster-tools/v3/searchanalytics/query
            $request->setRowLimit($limit);
            $request->setSearchType($type);
            try {
                $qsearch = $service->searchanalytics->query(get_home_url(), $request);
            } catch (Exception $e) {
                Off_Page_SEO::ops_create_log_entry('google_api', 'error', __("We've connected to Google API, but we got this error: ", 'off-page-seo') . $e->getMessage());
                return array();
            }

            $response = $qsearch->getRows();
            if ('' == $response || 0 == count($response)) {
                Off_Page_SEO::ops_create_log_entry('google_api', 'alert', __('Google API data request returned zero results.', 'off-page-seo'));
            }

            $nice_reponse = $client->process_query_response($response);

            return $nice_reponse;

        }


        public function ops_get_diff_class($diff, $revert = 0)
        {
            $diff = number_format($diff, 0);
            if ($revert == 0) {
                if ($diff > 0) {
                    return 'negative';
                } elseif ($diff < 0) {
                    return 'positive';
                } else {
                    return '';
                }
            } else {
                if ($diff > 0) {
                    return 'positive';
                } elseif ($diff < 0) {
                    return 'negative';
                } else {
                    return '';
                }
            }
        }

        public static function ops_get_total_organic_visitors($data)
        {
            $total = 0;
            if (count($data) < 1) {
                return $total;
            }

            foreach ($data as $keyword) {
                $total = $total + $keyword['clicks'];
            }

            return $total;
        }

        public static function ops_get_total_organic_impressions($data)
        {
            $total = 0;
            if (count($data) < 1) {
                return $total;
            }

            foreach ($data as $keyword) {
                $total = $total + $keyword['impressions'];
            }

            return $total;
        }

    }
}
