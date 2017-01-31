<?php

class OPS_Opportunities
{
    public function __construct()
    {
        $this->ops_render_opportunities();
    }


    function ops_render_opportunities()
    {
        ?>
        <div class="wrap ops-wrapper" id="ops-dashboard">
            <h2><?php _e('Opportunities', 'off-page-seo') ?></h2>

            <div class="postbox ops-padding" id="ops-dashboard-tabs">
                <ul>
                    <?php do_action('ops/opportunities_tabs'); ?>
                </ul>
            </div>
            <?php
            $tab = sanitize_text_field(isset($_GET['tab']) ? $_GET['tab'] : '');
            if ($tab == '') {
                do_action('ops/opportunities_comment');
            }
            ?>
<!--            <div class="ops-dashboard-right">-->
<!--                <div class="postbox ops-padding">-->
<!--                    --><?php //do_action('ops/dashboard_sidebar'); ?>
<!--                </div>-->
<!--                --><?php //do_action('ops/dashboard_sidebar_ads'); ?>
<!--            </div>-->
            <div class="ops-clearfix"></div>
        </div>
        <?php
    }


}
