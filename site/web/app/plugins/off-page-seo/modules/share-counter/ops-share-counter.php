<?php

class OPS_Share_Counter
{
    public function __construct()
    {
        // count shares
        add_action('wp_footer', array($this, 'ops_insert_ajax_update_shares'));

        // register ajax
        add_action('wp_ajax_ops_update_shares', array($this, 'ops_update_shares'));
        add_action('wp_ajax_nopriv_ops_update_shares', array($this, 'ops_update_shares'));
    }

    public function ops_insert_ajax_update_shares()
    {

        // reset query to default in case template is not well written
        wp_reset_query();

        $ops_timer = get_post_meta(get_the_ID(), 'ops_timer');
        // don't update shares if we updated in last 24 hours
        if (isset($ops_timer[0]) && $ops_timer[0] > time()) {
            return;
        }
        ?>
        <script>
            jQuery(document).ready(function ($) {
                $.ajax({
                    url: opsajaxurl,
                    type: "POST",
                    data: {
                        action: 'ops_update_shares',
                        pid: <?php echo get_the_ID() ?>
                    },
                    success: function (data) {
//                        $('footer').html(data);
                    },
                    error: function () {
                    }
                });
            });
        </script>
        <?php


    }

    /*
     * Callback on ajax udpate shares
     */
    public function ops_update_shares()
    {
        $pid = sanitize_text_field($_POST['pid']);
        $settings = Off_Page_SEO::ops_get_settings();
        // get number of shares
        $shares = $this->ops_api_multi_request($pid);

        // update timer now + 1 day
        update_post_meta($pid, 'ops_timer', time() + $settings['share_counter']['perform_every']);

        update_post_meta($pid, 'ops_shares', $shares);

        die();
    }

    /**
     * @param type $url
     * @return array
     * Twitter removed.
     */
    public static function ops_api_request_urls($pid)
    {
        $url = get_permalink($pid);
        $urls = array(
            'facebook' => 'https://api.facebook.com/method/links.getStats?urls=' . rawurlencode($url) . '&format=json',
//            'twitter' => 'http://urls.api.twitter.com/1/urls/count.json?url=' . rawurlencode($url) . '&callback=?',
            'googleplus' => 'https://apis.google.com/u/0/_/+1/fastbutton?usegapi=1&hl=en-GB&url=' . rawurlencode($url),
            'pocket' => 'https://widgets.getpocket.com/v1/button?label=pocket&count=horizontal&v=1&url=' . rawurlencode($url),
            'pinterest' => 'http://api.pinterest.com/v1/urls/count.json?url=' . rawurlencode($url) . '&callback=receiveCount',
            'linkedin' => 'http://www.linkedin.com/countserv/count/share?url=' . rawurlencode($url) . '&format=json',
            'stumbleupon' => 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . rawurlencode($url)
        );

        return $urls;
    }

    /*
     * Mulit Request
     */
    public function ops_api_multi_request($post_id)
    {
        $apilist = self::ops_api_request_urls($post_id);

        $counts = array();

        foreach ($apilist as $key => $url) :

            $response = wp_remote_get($url);
            $getcontent = $response['body'];

            if ('facebook' == $key) {
                $content = json_decode($getcontent);
                $counts['shares']['facebook'] = (int)$content[0]->total_count;
            } elseif ('twitter' == $key) {
                $content = json_decode($getcontent);
                if (isset($content->count)) {
                    $counts['shares']['twitter'] = (int)$content->count;
                } else {
                    $counts['shares']['twitter'] = (int)0;
                }
            } elseif ('googleplus' == $key) {
                $content = preg_match('/<div\sid=\"aggregateCount\"\sclass=\"Oy\">([0-9]+)<\/div>/i', $getcontent, $matches);
                if (!isset($matches[1]))
                    $matches[1] = 0;
                $counts['shares']['googleplus'] = (int)$matches[1];
            } elseif ('pinterest' == $key) {
                $content = json_decode(preg_replace('/\AreceiveCount\((.*)\)$/', "\\1", $getcontent));
                if (isset($content->count)) {
                    $counts['shares']['pinterest'] = (int)$content->count;
                } else {
                    $counts['shares']['pinterest'] = (int)0;
                }
            } elseif ('linkedin' == $key) {
                $content = json_decode($getcontent);
                $counts['shares']['linkedin'] = isset($content->count) && (int)$content->count != '' ? (int)$content->count : 0;
            } elseif ('stumbleupon' == $key) {
                $content = json_decode($getcontent);
                $counts['shares']['stumbleupon'] = isset($content->result->views) && $content->result->views != '' ? (int)$content->result->views : 0;
            }

        endforeach;

        return $counts;
    }


}
