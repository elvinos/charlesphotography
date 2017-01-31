<?php

class OPS_Backlinks_UI
{
    public function __construct()
    {
        if (isset($_POST['add_new_backlink']) && $_POST['add_new_backlink'] == true) {
            OPS_Backlinks::ops_add_backlink();
            header('Location: ' . get_home_url() . '/wp-admin/admin.php?page=ops&tab=backlinks&save=true');
            die();
        }


        /*
         * Dasbhoard functions
         */
        add_action('ops/dashboard_tabs', array($this, 'ops_backlinks_dashboard_tab'), 5);
        add_action('ops/dashboard_backlinks', array($this, 'ops_backlinks_dashboard_body'));
        add_action('ops/dashboard_sidebar', array($this, 'ops_backlinks_dashboard_sidebar'));

        add_action('ops/keyword_backlinks', array($this, 'ops_keyword_backlink'));
        add_action('ops/keyword_backlinks_trigger', array($this, 'ops_keyword_backlinks_trigger'));
        add_action('ops/keyword_backlinks_add_new', array($this, 'ops_keyword_backlinks_add_new'));


    }

    function ops_backlinks_dashboard_tab()
    {
        ?>
        <li>
            <a href="admin.php?page=ops&tab=backlinks" class="ops-tab-switcher <?php echo isset($_GET['tab']) && $_GET['tab'] == 'backlinks' ? 'active' : '' ?>">
                <?php _e('Backlinks', 'off-page-seo') ?>
            </a>
        </li>
        <?php
    }

    function ops_backlinks_dashboard_body()
    {
        if (isset($_GET['save']) && $_GET['save'] == true) {
            ?>
            <div class="updated ops-updated" style="padding: 8px 20px;">
                <?php _e('Backlink added.', 'off-page-seo') ?>
            </div>
            <?php
        }
        ?>
        <div class="ops-dashboard-left">
            <a href="#" class="button button-primary ops-add-new-backlink"><?php _e('Add a new backlink', 'off-page-seo') ?></a>

            <div class="postbox ops-add-new-backlink-form <?php echo isset($_GET['new']) && $_GET['new'] == 'true' ? 'active' : ''; ?>">
                <?php $this->ops_new_backlink_form(); ?>
            </div>
            <div class="postbox ops-padding">
                <div class="ops-backlink">
                    <?php
                    $raw_backlinks = Off_Page_SEO::ops_get_all_backlinks();
                    $backlinks = OPS_Backlinks::ops_process_backlinks($raw_backlinks);

                    if ((count($backlinks['assigned']) + count($backlinks['unassigned'])) == 0) {
                        _e("You don't have any backlinks yet. Please add some by clicking the button above this message.", 'off-page-seo');
                    } else {
                        ?>
                        <?php if (count($backlinks['unassigned']) > 0): ?>
                            <h3><?php _e('Unassigned backlinks', 'off-page-seo') ?></h3>
                            <div class="ops-backlinks-wrapper">
                                <?php foreach ($backlinks['unassigned'] as $bid => $backlink):
                                    OPS_Backlinks_UI::ops_backlinks_single($backlink);
                                endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (count($backlinks['assigned']) > 0): ?>
                            <h3><?php _e('Assigned backlinks', 'off-page-seo') ?></h3>
                            <div class="ops-backlinks-wrapper">
                                <?php foreach ($backlinks['assigned'] as $bid => $kw_backlinks): ?>
                                    <?php $bl = Off_Page_SEO::ops_get_keyword($bid); ?>
                                    <div class="ops-backlink-keyword">
                                        <div class="keyword"><?php echo $bl['keyword']; ?></div>
                                    </div>
                                    <div>
                                        <?php
                                        foreach ($kw_backlinks as $backlink):
                                            OPS_Backlinks_UI::ops_backlinks_single($backlink);
                                        endforeach;
                                        ?>
                                    </div>
                                    <?php
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php } ?>

                </div>
            </div>

            <div class="postbox">
                <div class="ops-blue-box">
                    <?php _e('Next reciprocal check will be on ', 'off-page-seo') ?><b><?php
                        $timestamp = wp_next_scheduled('ops_reciprocal_check');
                        echo Off_Page_SEO::ops_format_date($timestamp);
                        ?></b>
                </div>
            </div>
        </div>
        <?php
    }


    function ops_backlinks_dashboard_sidebar()
    {
        $settings = Off_Page_SEO::ops_get_settings();
        ?>
        <h4>
            <?php _e('Backlinks', 'off-page-seo') ?>
            <span>(<?php echo count(Off_Page_SEO::ops_get_all_backlinks()); ?>)</span>
        </h4>
        <?php $costs = OPS_Backlinks::ops_get_total_backlink_costs(); ?>
        <div class="ops-stat">
            <div class="ops-title">
                <?php _e('Total Costs', 'off-page-seo') ?>
            </div>
            <div class="ops-value">
                <?php echo Off_Page_SEO::ops_format_number($costs['fixed']) ?>&nbsp;<?php echo $settings['core']['currency']; ?>
            </div>
        </div>
        <div class="ops-stat">
            <div class="ops-title">
                <?php _e('Total Monthly Costs', 'off-page-seo') ?>
            </div>
            <div class="ops-value">
                <?php echo Off_Page_SEO::ops_format_number($costs['monthly']) ?>&nbsp;<?php echo $settings['core']['currency']; ?>
            </div>
        </div>
        <?php
    }


    function ops_new_backlink_form()
    {
        ?>
        <form method="POST">
            <input type="hidden" name="add_new_backlink" value="true"/>
            <label for="url"><?php _e('URL', 'off-page-seo') ?></label>
            <input type="text" name="url" value=""/>
            <br/>
            <label for="type"><?php _e('Type', 'off-page-seo') ?></label>
            <select name="type">
                <option value="backlink"><?php _e('Backlink', 'off-page-seo') ?></option>
                <option value="article"><?php _e('Article', 'off-page-seo') ?></option>
                <option value="comment"><?php _e('Comment', 'off-page-seo') ?></option>
                <option value="sitewide"><?php _e('Sitewide', 'off-page-seo') ?></option>
            </select>
            <br/>
            <label for="price"><?php _e('Price', 'off-page-seo') ?></label>
            <input type="text" name="price" value="0"/>
            <br/>
            <label for="price"><?php _e('Price monthly', 'off-page-seo') ?></label>
            <input type="text" name="price_monthly" value="0"/>
            <br/>
            <label for="reciprocal_check"><?php _e('Reciprocal Check', 'off-page-seo') ?></label>
            <input type="checkbox" name="reciprocal_check" value="on" checked="checked"/>
            <?php if(!isset(Off_Page_SEO::$settings['backlinks']['reciprocal_check']) || Off_Page_SEO::$settings['backlinks']['reciprocal_check'] != 'on'){
                _e('(Notice: reciprocal check is turned off in the Settings.)', 'off-page-seo');
            } ?>
            <br/>
            <label for="keyword_id"><?php _e('Keyword', 'off-page-seo') ?></label>
            <?php $keywords = Off_Page_SEO::ops_get_all_keywords(); ?>
            <select name="keyword_id">
                <option value="0"><?php _e("Don't assign to keyword", 'off-page-seo') ?></option>
                <?php foreach ($keywords as $keyword): ?>
                    <option value="<?php echo $keyword['id'] ?>"><?php echo $keyword['keyword'] ?></option>
                <?php endforeach; ?>
            </select>
            <br/>
            <label for="comment"><?php _e('Comment', 'off-page-seo') ?></label>
            <input type="text" name="comment" value=""/>
            <br/>
            <label for="comment"><?php _e('Contact', 'off-page-seo') ?></label>
            <input type="text" name="contact" value=""/>
            <br/>
            <label for="start_date"><?php _e('Date', 'off-page-seo') ?></label>
            <input type="text" name="start_date" class="datepicker" value="<?php echo date('m/d/Y', time()); ?>"/>
            <br/>
            <label for="url">&nbsp;</label>
            <input type="submit" value="Save" class="button button-primary"/>
        </form>
        <?php
    }

    /*
     * Add to KEYWORD
     */
    public function ops_keyword_backlinks_add_new()
    {
        ?>
        <a href="<?php echo get_home_url() ?>/wp-admin/admin.php?page=ops&tab=backlinks&new=true" class="button ops-add-new-backlink">
            <?php _e('Add a new backlink', 'off-page-seo') ?></a>
        <?php
    }

    function ops_keyword_backlink($kwid)
    {
        echo '<div class="ops-keyword-backlinks">';
        $backlinks = OPS_Backlinks::ops_get_keyword_backlinks($kwid);
        usort($backlinks, create_function('$a, $b', 'return $b[\'start_date\'] - $a[\'start_date\'];'));
        if (count($backlinks) > 0):
            echo "<div class='ops-backlinks-wrapper'>";
            foreach ($backlinks as $backlink) :
                OPS_Backlinks_UI::ops_backlinks_single($backlink);
            endforeach;
            echo "</div>";
        endif;
        echo "</div>";

    }

    public function ops_keyword_backlinks_trigger($kwid)
    {
        $total = count(OPS_Backlinks::ops_get_keyword_backlinks($kwid));
        if ($total > 0) {
            ?>
            <a href="#" class="button ops-show-keyword-backlinks"><?php _e('Backlinks', 'off-page-seo') ?> (<?php echo $total ?>)</a>
            <?php
        }
    }

    public static function ops_backlinks_single($backlink)
    {
        ?>

        <div class="ops-single-backlink-wrapper">
            <div class="ops-backlink-data">
                <div class="ops-reciprocal-status <?php echo OPS_Backlinks::ops_get_nice_reciprocal_status($backlink) ?>"></div>
                <a href="<?php echo $backlink['url'] ?>" target="_blank">
                    <?php echo mb_substr(strip_tags($backlink['url']), 0, 50, 'UTF-8'); ?><?php echo (strlen($backlink['url']) > 50) ? "..." : ""; ?>
                </a>&nbsp;
                <?php if ($backlink['comment'] != ''): ?>
                    <span class="ops-hint right"><i><?php echo $backlink['comment'] ?></i></span>
                <?php endif; ?>
                <div class="time-ago">
                    <b><?php echo human_time_diff($backlink['start_date'], time()); ?></b> <?php _e('ago', 'off-page-seo') ?>
                </div>

                <?php if ($backlink['price'] != ''): ?>
                    <div class="costs">
                        <?php echo $backlink['price'] ?>&nbsp;<?php echo Off_Page_SEO::$settings['core']['currency'] ?>
                    </div>
                <?php endif; ?>
                <div class="edit">
                    <a href="#" class="ops-edit-single-backlink"><?php _e('Edit', 'off-page-seo') ?></a>
                    <a href="#" class="ops-edit-single-delete" data-blid="<?php echo $backlink['id'] ?>"><?php _e('Delete', 'off-page-seo') ?></a>
                </div>
            </div>
            <div class="ops-edit-single-backlink-box">
                <form method="POST">
                    <input type="hidden" name="blid" value="<?php echo $backlink['id'] ?>">
                    <label for="url"><?php _e('URL', 'off-page-seo') ?></label>
                    <input type="text" name="url" value="<?php echo $backlink['url'] ?>"/>
                    <br/>
                    <label for="type"><?php _e('Type', 'off-page-seo') ?></label>
                    <select name="type">
                        <option value="backlink" <?php echo $backlink['type'] == 'backlink' ? 'selected="selected"' : ''; ?>>
                            <?php _e('Backlink', 'off-page-seo') ?>
                        </option>
                        <option value="article" <?php echo $backlink['type'] == 'article' ? 'selected="selected"' : ''; ?>>
                            <?php _e('Article', 'off-page-seo') ?>
                        </option>
                        <option value="comment" <?php echo $backlink['type'] == 'comment' ? 'selected="selected"' : ''; ?>>
                            <?php _e('Comment', 'off-page-seo') ?>
                        </option>
                        <option value="sitewide" <?php echo $backlink['type'] == 'sitewide' ? 'selected="selected"' : ''; ?>>
                            <?php _e('Sitewide', 'off-page-seo') ?>
                        </option>
                    </select>
                    <br/>
                    <label for="price"><?php _e('Price', 'off-page-seo') ?></label>
                    <input type="text" name="price" value="<?php echo $backlink['price'] ?>"/>
                    <br/>
                    <label for="price"><?php _e('Price monthly', 'off-page-seo') ?></label>
                    <input type="text" name="price_monthly" value="<?php echo $backlink['price_monthly'] ?>"/>
                    <br/>
                    <label for="reciprocal_check"><?php _e('Reciprocal Check', 'off-page-seo') ?></label>
                    <input type="checkbox" name="reciprocal_check" value="checked" <?php echo $backlink['reciprocal_check'] == 1 ? 'checked="checked"' : '' ?>/>
                    <br/>
                    <label for="keyword_id"><?php _e('Keyword', 'off-page-seo') ?></label>
                    <?php $keywords = Off_Page_SEO::ops_get_all_keywords(); ?>
                    <select name="keyword_id">
                        <option value="0" <?php echo (0 == $backlink['keyword_id']) ? 'selected="selected"' : ''; ?>>
                            <?php _e('Not assigned', 'off-page-seo') ?>
                        </option>
                        <?php foreach ($keywords as $keyword): ?>
                            <option value="<?php echo $keyword['id'] ?>" <?php echo ($keyword['id'] == $backlink['keyword_id']) ? 'selected="selected"' : ''; ?> ><?php echo $keyword['keyword'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br/>
                    <label for="comment"><?php _e('Comment', 'off-page-seo') ?></label>
                    <input type="text" name="comment" value="<?php echo $backlink['comment'] ?>"/>
                    <br/>
                    <label for="comment"><?php _e('Contact', 'off-page-seo') ?></label>
                    <input type="text" name="contact" value="<?php echo $backlink['contact'] ?>"/>
                    <br/>
                    <label for="start_date"><?php _e('Start Date', 'off-page-seo') ?></label>
                    <input type="text" name="start_date" class="datepicker" value="<?php echo date('m/d/Y', $backlink['start_date']); ?>"/>
                    <br/>
                    <label for="url">&nbsp;</label>
                    <input type="submit" value="Save" class="button button-primary"/>
                </form>
            </div>
        </div>
        <?php
    }


}
