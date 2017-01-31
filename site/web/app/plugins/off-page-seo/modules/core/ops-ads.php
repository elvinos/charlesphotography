<?php

/**
 * Main plugin class
 * */
class OPS_Ads
{
    function __construct()
    {
        // skip ads completely if premium
        if (OPS_PREMIUM == 1) {
            return;
        }

        // if no admin, skip
        if (!is_admin()) {
            return;
        }

        // cache it alllllll!
        $ads = Off_Page_SEO::ops_get_master_helper();
        if (!isset($ads['ads_last_cache']) || (time() - $ads['ads_last_cache']) > 3000) {
            $this->ops_cache_ads();
        }

        // insert ads
        add_action('ops/dashboard_sidebar_ads', array($this, 'ops_fiverr_gigs'));
    }

    public function ops_fiverr_gigs()
    {
        $helper = Off_Page_SEO::ops_get_master_helper();
        if (isset($helper['ads']) && count($helper['ads']) > 0) {
            ?>
            <div class="postbox ops-padding">
                <h4>
                    <?php _e('SEO services', 'off-page-seo') ?>
                </h4>
                <ul class="ops-ads">
                    <?php foreach ($helper['ads'] as $ad): ?>
                        <li>
                            <a href="<?php echo $ad['url'] ?>" target="_blank"><?php echo $ad['title'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        }
    }

    public function ops_cache_ads()
    {
        $helper = Off_Page_SEO::ops_get_master_helper();
        $response = ops_curl(Off_Page_SEO::$mother . '/api2/cache-ads/?site_url=' . urlencode(get_home_url()), 0);
        $helper['ads'] = array();
        $helper['ads_last_cache'] = time();

        if (count(unserialize($response)) > 0) {
            $helper['ads'] = unserialize($response);
        } else {
            Off_Page_SEO::ops_create_log_entry('seo_tips', 'alert', __('We could not fetch SEO tips from our server.', 'off-page-seo'));
        }

        Off_Page_SEO::ops_update_master_helper($helper);
    }

}
