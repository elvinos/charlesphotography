<?php

class OPS_Rank_Report_UI
{

    /**
     * Initialization of Rank Reporter Class
     * */
    public function __construct()
    {

        add_action('ops/main_metabox_tabs', array($this, 'ops_meta_box_rank_report_body'));

        // insert rank report part to the email
        add_action('ops/ops_update_email_body', array($this, 'ops_rank_report_email_part'));

        // insert new form ajax
        add_action('wp_ajax_ops_insert_new_kw_to_metabox', array($this, 'ops_insert_new_kw_to_metabox'));
        add_action('wp_ajax_nopriv_ops_insert_new_kw_to_metabox', array($this, 'ops_insert_new_kw_to_metabox'));

        /*
         * Dasbhoard functions
         */
        add_action('ops/dashboard_tabs', array($this, 'ops_rank_report_dashboard_tab'), 2);

        // edit keyword
        add_action('ops/dashboard_rank_report', array($this, 'ops_rank_report_dashboard_body'));


    }

    function ops_rank_report_dashboard_tab()
    {
        ?>
        <li>
            <a href="admin.php?page=ops" class="ops-tab-switcher <?php echo !isset($_GET['tab']) ? 'active' : '' ?>" data-tab="core">
                <?php _e('Rank Report', 'off-page-seo') ?>
            </a>
        </li>
        <?php
    }


    function ops_rank_report_dashboard_body()
    {
//        do_action('ops_rank_update');
        $keywords = Off_Page_SEO::ops_get_all_keywords();
        $total_active_keywords = Off_Page_SEO::ops_get_total_active_keywords();
        usort($keywords, array($this, 'ops_sort_keyword_dashboard'));

        if (count($keywords) == 0) {
            ?>
            <div class="ops-dashboard-left">
                <div class="postbox ops-padding">
                    <p>
                        <?php _e("You don't have any keyword set up. Please edit any post or page and add it from there.", "off-page-seo") ?>
                    </p>
                </div>
            </div>
            <?php
            return; // stop script
        }
        ?>


        <?php $k = 0; ?>
        <div class="ops-dashboard-left">
            <div class="postbox ops-padding">
                <?php $this->ops_render_master_graph($keywords); ?>
            </div>
            <?php $this->ops_display_limit_message($keywords) ?>
            <div class="postbox ops-sortable-dashboard">
                <?php $p = 0; ?>
                <?php foreach ($keywords as $kw): ?>
                    <?php $positions = unserialize($kw['positions']) ?>
                    <?php
                    $p++;
                    if ($p == 2 && $total_active_keywords >= 1 && OPS_PREMIUM == 0) {
                        break;
                    }
                    ?>
                    <div class="ops-keyword-wrapper" data-kwid="<?php echo $kw['id'] ?>">
                        <div class="ops-keyword-analyze">
                            <div class="ops-total-left">
                                <div class="ops-move-kw">
                                    <img src="<?php echo OPS_PLUGIN_PATH ?>/img/ops-move-circle.png" alt="move circle">
                                </div>
                                <a href="#" class="ops-edit-kw"><?php _e('Edit', 'off-page-seo') ?></a>
                                <a href="#" class="ops-delete-kw"><?php _e('Delete', 'off-page-seo') ?></a>
                            </div>
                            <div class="ops-left">
                                <div class="ops-title">
                                    <?php echo $kw['keyword']; ?>
                                    <a href="<?php echo Off_Page_SEO::ops_get_seach_url($kw['keyword']); ?>" target="_blank">
                                        <span class="ops-go-link"></span>
                                    </a>
                                </div>
                                <div class="ops-url">
                                    <?php echo $kw['url']; ?>
                                    <a href="<?php echo $kw['url']; ?>" target="_blank">
                                        <span class="ops-go-link"></span>
                                    </a>
                                    <?php if (isset($kw['searches']) && $kw['searches'] != ''): ?>
                                        &nbsp;(
                                        <span class="ops-searches"><?php echo $kw['searches']; ?></span> <?php _e('per month', 'off-page-seo') ?>)
                                    <?php endif; ?>
                                </div>
                                <?php do_action('ops/keyword_data_from_api', $kw['keyword']); ?>
                            </div>
                            <div class="ops-right">
                                <ul>
                                    <li>
                                        <?php if (isset($positions[4]['position'])): ?>
                                            <?php echo $positions[4]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if (isset($positions[3]['position'])): ?>
                                            <?php echo $positions[3]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if (isset($positions[2]['position'])): ?>
                                            <?php echo $positions[2]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if (isset($positions[1]['position'])): ?>
                                            <?php echo $positions[1]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <?php
                                    if (isset($positions[1]['position'])) {
                                        $diff = $positions[0]['position'] - $positions[1]['position'];
                                    } else {
                                        $diff = 0;
                                    }
                                    ?>
                                    <li class="<?php echo $this->ops_get_diff_class($diff); ?>">
                                        <?php if (isset($positions[0]['position'])): ?>
                                            <?php echo $positions[0]['position'] ?>
                                            <?php if (isset($positions[1]['position'])) { ?>
                                                <span><?php echo $diff * -1 ?></span>
                                            <?php } else { ?>
                                                <span>&nbsp;</span>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                                <?php do_action('ops/keyword_backlinks_trigger', $kw['id']); ?>
                            </div>
                        </div>
                        <div class="ops-keyword-setting">
                            <form method="post" class="ops-dashboard-update-kw">
                                <input type="hidden" name="kwid" value="<?php echo $kw['id'] ?>">

                                <!--                                <label for="keyword">Keyword</label>-->
                                <!--                                <input type="text" name="keyword" value="-->
                                <?php //echo $kw['keyword']; ?><!--" class="ops-change-text" data-field="ops-title"/>-->
                                <!---->
                                <!--                                <br/>-->
                                <!--                                <label for="url">URL</label>-->
                                <!--                                <input type="text" name="url" value="-->
                                <?php //echo $kw['url']; ?><!--" class="ops-change-text" data-field="ops-url"/>-->
                                <label for="searches">
                                    <a href="https://adwords.google.com/ko/KeywordPlanner" target="_blank">
                                        <?php _e('Searches per month', 'off-page-seo') ?></a></label>
                                <input type="number" name="searches" value="<?php echo $kw['searches']; ?>" class="ops-change-text" data-field="ops-searches"/>


                                <br/>
                                <label for="pid"><?php _e('Post ID', 'off-page-seo') ?></label>
                                <input type="number" name="post_id" value="<?php echo ($kw['post_id'] != 0) ? $kw['post_id'] : ''; ?>"/>
                                <?php if ($kw['post_id'] != 0): ?>
                                    <a href="<?php echo get_home_url() ?>/wp-admin/post.php?post=<?php echo $kw['post_id'] ?>&action=edit" target="_blank">
                                        <?php _e('Edit post', 'off-page-seo') ?>
                                    </a>
                                <?php endif; ?>
                                <br/>
                                <label for="pid">&nbsp;</label>
                                <input type="submit" value="Save" class="button"/>

                            </form>
                        </div>
                        <?php do_action('ops/keyword_backlinks', $kw['id']); ?>
                    </div>
                    <?php $k++; ?>
                <?php endforeach; ?>

                <?php
                if ($total_active_keywords >= 1 && OPS_PREMIUM == false) {
                    ?>
                    <div class="ops-sad-premium-message">
                        <?php _e('We are sorry, you can only track one keyword in the free version.', 'off-page-seo') ?>
                        <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="postbox">
                <div class="ops-blue-box">
                    <?php _e('Next update will be on', 'off-page-seo') ?>
                    <b>
                        <?php
                        $timestamp = wp_next_scheduled('ops_rank_update');
                        echo Off_Page_SEO::ops_format_date($timestamp);
                        ?>
                    </b>
                </div>
            </div>

            <div class="postbox ops-another-dashboard-tools">
                <ul>
                    <li>
                        <a href="#" class="ops-show-keyword-list"><?php _e('Show keyword list', 'off-page-seo') ?></a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url() ?>admin.php?page=ops_settings&log=show"><?php _e('Show log', 'off-page-seo') ?></a>
                    </li>
                </ul>

                <div class="ops-keyword-list">
                    <?php foreach ($keywords as $keyword): ?>
                        <?php echo $keyword['keyword']; ?><br/>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php
    }


    public function ops_meta_box_rank_report_body()
    {
        $settings = Off_Page_SEO::ops_get_settings();
        $keywords = isset($_GET['post']) ? Off_Page_SEO::ops_get_post_keywords(sanitize_text_field($_GET['post'])) : array();
        usort($keywords, array($this, 'ops_sort_keyword_metabox'));
        $k = 0;
        $total_active_keywords = Off_Page_SEO::ops_get_total_active_keywords();
        ?>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <input type="hidden" name="ops_null" value="1">
        <div class="ops-tab" id="ops-tab-rank-report" data-total="<?php echo count($keywords) ?>" data-pid="<?php echo isset($_GET['post']) ? sanitize_text_field($_GET['post']) : ''; ?>" data-preloader="<?php echo OPS_PLUGIN_PATH ?>/img/preloader.gif">
            <div class="ops-metabox-master-graph">
                <?php $this->ops_render_master_graph($keywords); ?>
            </div>
            <?php if (isset($settings['rank_report']['show_estimation']) && $settings['rank_report']['show_estimation'] == 'on'): ?>
                <div class="ops-blue-box ops-text-right">
                    Your current positions could generate you
                    <b>
                        <?php echo $this->ops_estimate_visitors($keywords); ?> visitors per day.
                    </b>
                    <span class="ops-hint left">
                        <i>
                            <?php _e('Based on your current positions, filled search volumes and', 'off-page-seo') ?>
                            <a href="http://marketingland.com/new-click-rate-study-google-organic-results-102149" target="_blank">
                                <?php _e('this resarch', 'off-page-seo') ?>
                            </a>.
                        </i>
                    </span>
                </div>
            <?php endif; ?>
            <div class="ops-sortable-metabox">
                <?php $p = 0 ?>
                <?php foreach ($keywords as $kw): ?>
                    <?php $positions = unserialize($kw['positions']) ?>
                    <?php
                    $p++;
                    if ($p == 2 && $total_active_keywords >= 1 && OPS_PREMIUM == 0) {
                        break;
                    }
                    ?>
                    <div class="ops-keyword-wrapper">
                        <div class="ops-keyword-analyze">
                            <div class="ops-total-left">
                                <div class="ops-move-kw">
                                    <img src="<?php echo OPS_PLUGIN_PATH ?>/img/ops-move-circle.png" alt="move circle">
                                </div>
                                <a href="#" class="ops-edit-kw"><?php _e('Edit', 'off-page-seo') ?></a>
                                <a href="#" class="ops-delete-kw"><?php _e('Delete', 'off-page-seo') ?></a>
                            </div>
                            <div class="ops-left">
                                <div class="ops-title">
                                    <?php echo $kw['keyword']; ?>
                                    <a href="<?php echo Off_Page_SEO::ops_get_seach_url($kw['keyword']); ?>" target="_blank">
                                        <span class="ops-go-link"></span>
                                    </a>
                                </div>
                                <div class="ops-url">
                                    <?php echo $kw['url']; ?>
                                    <a href="<?php echo $kw['url']; ?>" target="_blank">
                                        <span class="ops-go-link"></span>
                                    </a>
                                    <?php if (isset($kw['searches']) && $kw['searches'] != ''): ?>
                                        (
                                        <span class="ops-searches"><?php echo $kw['searches']; ?></span> <?php _e('per month', 'off-page-seo') ?>)
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="ops-right">
                                <ul>
                                    <li>
                                        <?php if (isset($positions[4]['position'])): ?>
                                            <?php echo $positions[4]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if (isset($positions[3]['position'])): ?>
                                            <?php echo $positions[3]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if (isset($positions[2]['position'])): ?>
                                            <?php echo $positions[2]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if (isset($positions[1]['position'])): ?>
                                            <?php echo $positions[1]['position'] ?>
                                        <?php endif; ?>
                                    </li>
                                    <?php
                                    if (isset($positions[1]['position'])) {
                                        $diff = $positions[0]['position'] - $positions[1]['position'];
                                    } else {
                                        $diff = 0;
                                    }
                                    ?>
                                    <li class="<?php echo $this->ops_get_diff_class($diff); ?>">
                                        <?php if (isset($positions[0]['position'])): ?>
                                            <?php echo $positions[0]['position'] ?>
                                            <?php if (isset($positions[1]['position'])) { ?>
                                                <span><?php echo $diff * -1 ?></span>
                                            <?php } else { ?>
                                                <span>&nbsp;</span>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                                <?php do_action('ops/keyword_backlinks_trigger', $kw['id']); ?>
                            </div>
                        </div>
                        <div class="ops-keyword-setting">
                            <label for="ops[keywords][<?php echo $k ?>][keyword]"><?php _e('Keyword', 'off-page-seo') ?></label>
                            <input type="text" name="ops[keywords][<?php echo $k ?>][keyword]" value="<?php echo $kw['keyword']; ?>" class="ops-change-text" data-field="ops-title"/>
                            <br/>
                            <label for="ops[keywords][<?php echo $k ?>][url]"><?php _e('URL', 'off-page-seo') ?></label>
                            <input type="text" name="ops[keywords][<?php echo $k ?>][url]" value="<?php echo $kw['url']; ?>" class="ops-change-text" data-field="ops-url"/>
                            <br/>
                            <label for="ops[keywords][<?php echo $k ?>][searches]"><a href="https://adwords.google.com/ko/KeywordPlanner" target="_blank">
                                    <?php _e('Searches per month', 'off-page-seo') ?></a></label>
                            <input type="number" name="ops[keywords][<?php echo $k ?>][searches]" value="<?php echo $kw['searches']; ?>" class="ops-change-text" data-field="ops-searches span"/>
                        </div>

                        <?php do_action('ops/keyword_backlinks', $kw['id']); ?>
                    </div>

                    <?php $k++; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($total_active_keywords >= 1 && OPS_PREMIUM == 0) { ?>
                <div class="ops-sad-premium-message">
                    <?php _e('We are sorry, you can only track one keyword in the free version.', 'off-page-seo') ?>
                    <a href="http://www.wpress.me/product/off-page-seo-plugin/" target="_blank"><?php _e('Upgrade.', 'off-page-seo') ?></a>
                </div>
            <?php } else { ?>
                <a href="#" class="button button-primary ops-add-new-keyword <?php echo OPS_PREMIUM == 0 ? 'ops-hide-when-used' : '' ?>"><?php _e('Add a new keyword', 'off-page-seo') ?></a>
                <?php do_action('ops/keyword_backlinks_add_new'); ?>
            <?php } ?>
        </div>
        <?php
    }

    function ops_insert_new_kw_to_metabox()
    {
        $k = $_POST['total_kws'] + 1;
        $pid = sanitize_text_field($_POST['pid']);
        $keyword = sanitize_text_field($_POST['keyword']);
        if (isset($pid) && $pid != '') {
            $url = get_permalink($pid);
        } elseif (isset($_POST['permalink']) && $_POST['permalink'] != '') {
            $url = sanitize_text_field($_POST['permalink']);
        } else {
            $url = '';
        }
        ?>
        <div class="ops-keyword-wrapper">
            <div class="ops-keyword-analyze">
                <div class="ops-total-left">
                    <div class="ops-move-kw">
                        <img src="<?php echo OPS_PLUGIN_PATH ?>/img/ops-move-circle.png" alt="move circle">
                    </div>
                    <a href="#" class="ops-edit-kw"><?php _e('Edit', 'off-page-seo') ?></a>
                    <a href="#" class="ops-delete-kw"><?php _e('Delete', 'off-page-seo') ?></a>
                </div>
                <div class="ops-left">
                    <div class="ops-title">
                        <?php echo $keyword ?>
                    </div>
                    <div class="ops-url">
                        <?php echo $url ?>
                    </div>
                    <div class="ops-searches">
                        <?php _e('Searches per month', 'off-page-seo') ?>
                    </div>
                </div>
                <div class="ops-right">

                </div>
            </div>
            <div class="ops-keyword-setting" style="display:block;">
                <label for="ops[keywords][<?php echo $k ?>][keyword]"><?php _e('Keyword', 'off-page-seo') ?></label>
                <input type="text" name="ops[keywords][<?php echo $k ?>][keyword]" value="<?php echo $keyword ?>" class="ops-change-text" data-field="ops-title"/>
                <br/>
                <label for="ops[keywords][<?php echo $k ?>][url]"><?php _e('URL', 'off-page-seo') ?></label>
                <input type="text" name="ops[keywords][<?php echo $k ?>][url]" value="<?php echo $url ?>" class="ops-change-text" data-field="ops-url"/>
                <br/>
                <label for="ops[keywords][<?php echo $k ?>][searches]"><a href="https://adwords.google.com/ko/KeywordPlanner" target="_blank">
                        <?php _e('Searches per month', 'off-page-seo') ?></a></label>
                <input type="number" name="ops[keywords][<?php echo $k ?>][searches]" placeholder="200" class="ops-change-text" data-field="ops-searches span"/>
            </div>
        </div>
        <?php
        die();
    }


    function ops_render_master_graph($keywords)
    {
        $n = 0;
        foreach ($keywords as $keyword) {
            if (isset($keyword['positions']) && strlen($keyword['positions']) > 10) {
                $positions[$n]['keyword'] = $keyword['keyword'];
                $positions[$n]['positions'] = unserialize($keyword['positions']);
                $n++;
            }
        }
        ?>
        <script src="http://code.highcharts.com/highcharts.js"></script>
        <script src="http://code.highcharts.com/modules/exporting.js"></script>

        <?php if (isset($positions[0]['positions'][0])): ?>
        <div id="ops-master-graph"></div>

        <script>
            <?php $highest_value = array() ?>
            //zzz
            jQuery(document).ready(function ($) {
                $('#ops-master-graph').highcharts({
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        type: 'datetime',
                        dateTimeLabelFormats: {// don't display the dummy year
                            month: '%e. %b',
                            year: '%b'
                        },
                        title: {
                            text: 'Date'
                        }
                    },
                    tooltip: {
                        headerFormat: '{series.name} - {point.x:%e. %b}<br/>',
                        pointFormat: ' <b>{point.y}</b>'
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                enabled: true
                            }
                        }
                    },
                    series: [
                        <?php $r = 0 ?>
                        <?php foreach ($positions as $position) : $r++; ?>
                        <?php $n = 0 ?>
                        {
                            name: '<?php echo $position['keyword'] ?>',
                            data: [
                                <?php foreach ($position['positions'] as $single_position): $n++; ?>
                                [Date.UTC(<?php echo date('Y, m, d, H, i', $single_position['time']) ?>), <?php echo $single_position['position'] ?>],
                                <?php $highest_value[] = $single_position['position']; ?>
                                <?php
                                if ($n > 20) {
                                    break;
                                }
                                ?>
                                <?php endforeach; ?>
                            ]
                        },

                        <?php endforeach; ?>
                    ],
                    yAxis: {
                        title: {
                            text: 'Position'
                        },
                        reversed: true,
                        min: 0,
                        max: <?php echo max($highest_value) != '' ? max($highest_value) : 100; ?>
                    }
                });
            });</script>

    <?php endif; ?>
        <?php
    }

    public function ops_rank_report_email_part()
    {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE active = '1' AND blog_id = '" . get_current_blog_id() . "'", ARRAY_A);
        $total = count($rows);
        ?>
        <div class="block">
            <!-- fulltext -->
            <table width="100%" bgcolor="#f1f1f1" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fulltext">
                <tbody>
                <tr>
                    <td>
                        <table bgcolor="#ffffff" width="680" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" modulebg="edit">
                            <tbody>
                            <!-- Spacing -->
                            <tr>
                                <td width="100%" height="20"></td>
                            </tr>
                            <!-- Spacing -->
                            <tr>
                                <td>
                                    <table width="640" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                        <tbody>
                                        <!-- Title -->
                                        <tr>
                                            <td style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color: #333333; text-align:center;line-height: 20px;" st-title="fulltext-title">
                                                <b><?php echo $total ?></b> <?php _e('positions were checked.', 'off-page-seo') ?>
                                            </td>
                                        </tr>
                                        <!-- End of Title -->
                                        <!-- spacing -->
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width="100%" style="text-align:left; font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #333333; line-height: 14px;" class="ops-report">
                                                    <tr style="background-color:#00a0d2; font-weight: normal; padding: 5px 5px; color:#fff;">
                                                        <th>
                                                            <?php _e('Keyword', 'off-page-seo') ?>
                                                        </th>
                                                        <th>
                                                            <?php _e('URL', 'off-page-seo') ?>
                                                        </th>
                                                        <th>
                                                            <?php _e('Position', 'off-page-seo') ?>
                                                        </th>
                                                        <th>
                                                            <?php _e('Change', 'off-page-seo') ?>
                                                        </th>
                                                    </tr>
                                                    <?php foreach ($rows as $row):
                                                        $positions = unserialize($row['positions']);
                                                        if (isset($positions[1]['position'])) {
                                                            $diff = $positions[1]['position'] - $positions[0]['position'];
                                                        } else {
                                                            $diff = 0;
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?php echo Off_Page_SEO::ops_get_seach_url($row['keyword']) ?>"><?php echo $row['keyword'] ?></a>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo $row['url'] ?>"><?php echo $row['url'] ?></a>
                                                            </td>
                                                            <td>
                                                                <?php echo $positions[0]['position'] ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $diff; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>

                                                </table>
                                            </td>
                                        </tr>
                                        <!-- End of spacing -->
                                        <!-- content -->
                                        <tr>
                                            <td style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #95a5a6; text-align:center;line-height: 30px;" st-content="fulltext-paragraph">

                                            </td>
                                        </tr>
                                        <!-- End of content -->
                                        <!-- Spacing -->
                                        <tr>
                                            <td width="100%" height="20"></td>
                                        </tr>
                                        <!-- Spacing -->
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- end of fulltext -->
        </div>
        <?php
    }

    function ops_display_limit_message($keywords)
    {
        $exec = ini_get('max_execution_time');

        // no limit
        if ($exec == -1 || $exec == 0) {
            return;
        }

        // count max keywords
        $max_allowed = $exec / 2.2; // increase the number to warn user before it happens
        $total_keywords = count($keywords);

        if ($total_keywords > $max_allowed) {
            ?>
            <div class="ops-keyword-warning">
                <?php _e('You are slowly reaching limit of your hosting max_execution_time. Please consider removing couple keywords or raising the limit.', 'off-page-seo'); ?>
                <br>
                <?php _e('Expected time needed for ranking control:', 'off-page-seo'); ?> <?php echo $total_keywords * 2; ?>s
                <br>
                <?php _e('max_execution_time:','off-page-seo');?> <?php echo $exec; ?>s
            </div>
            <?php
        }
        ?>
        <?php
    }

    public function ops_get_diff_class($diff)
    {
        if ($diff > 0) {
            return 'negative';
        } elseif ($diff < 0) {
            return 'positive';
        } else {
            return '';
        }
    }

    public function ops_estimate_visitors($keywords)
    {
        $total_est = 0;
        foreach ($keywords as $keyword) {
            $current_position = isset(unserialize($keyword['positions'])[0]['position']) ? unserialize($keyword['positions'])[0]['position'] : false;
            if (!isset($keyword['searches']) || $current_position == false) {
                continue;
            }
            $est = $this->ops_estimate_keyword_visitors_based_on_position($keyword['searches'], $current_position);
            $total_est = $total_est + $est;
        }
        $total_est = explode('.', $total_est);
        return $total_est[0];
    }


    public function ops_estimate_keyword_visitors_based_on_position($searches, $position)
    {
        $estimation = $searches * $this->ops_click_rate($position);
        return $estimation;
    }

    /*
     * based on this study: http://marketingland.com/new-click-rate-study-google-organic-results-102149
     */
    public function ops_click_rate($position)
    {
        $click_rate = array(
            1 => '31.24',
            2 => '14.04',
            3 => '9.85',
            4 => '6.97',
            5 => '5.50'
        );

        if ($position <= 5) {
            $rate = $click_rate[$position];
        }

        if ($position > 5 && $position <= 10) {
            $rate = '3.73';
        }

        if ($position > 10 && $position <= 20) {
            $rate = '3.99';
        }

        if ($position > 20) {
            $rate = '1.6';
        }

        return $rate / 100;
    }

    function ops_sort_keyword_metabox($a, $b)
    {
        return $a['sort'] - $b['sort'];
    }


    function ops_sort_keyword_dashboard($a, $b)
    {
        return $a['sort_dashboard'] - $b['sort_dashboard'];
    }

}
