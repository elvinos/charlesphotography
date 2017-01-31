<?php

class OPS_Opportunity_Buy
{

    public function __construct()
    {
        add_action('ops/opportunities_tabs', array($this, 'ops_opportunity_buy'), 2);


    }

    function ops_opportunity_buy()
    {
        ?>
        <li>
            <a href="http://tracking.fiverr.com/aff_c?offer_id=1712&aff_id=6020&url_id=190" target="_blank" class="ops-tab-switcher">
                <?php _e('Buy', 'off-page-seo') ?>
            </a>
        </li>
        <?php
    }
}

new OPS_Opportunity_Buy();