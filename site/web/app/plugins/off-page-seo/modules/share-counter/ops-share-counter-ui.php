<?php

class OPS_Share_Counter_UI
{
    public function __construct()
    {

        /*
         * Dasbhoard functions
         */
        add_action('ops/dashboard_tabs', array($this, 'ops_share_counter_dashboard_tab'), 10);
        add_action('ops/dashboard_share_counter', array($this, 'ops_share_counter_dashboard_body'));

    }

    function ops_share_counter_dashboard_tab()
    {
        ?>
        <li>
            <a href="admin.php?page=ops&tab=share_counter" class="ops-tab-switcher <?php echo isset($_GET['tab']) && $_GET['tab'] == 'share_counter' ? 'active' : '' ?>" data-tab="core">
                <?php _e('Share Counter', 'off-page-seo') ?>
            </a>
        </li>
        <?php
    }


    function ops_share_counter_dashboard_body()
    {
        ?>
        <div class="ops-dashboard-left" id="ops-dashboard-shares">

            <div class="postbox ops-padding">
                <div class="ops-posts-from">
                    <?php $date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : ''; ?>
                    <?php _e('Display data for posts from:', 'off-page-seo') ?>
                    <a href="admin.php?page=ops&tab=share_counter" class="<?php echo $date == '' ? 'active' : '' ?>"><?php _e('all time', 'off-page-seo') ?></a>&nbsp;-
                    <a href="admin.php?page=ops&tab=share_counter&date=month" class="<?php echo $date == 'month' ? 'active' : '' ?>"><?php _e('this month', 'off-page-seo') ?></a>&nbsp;-
                    <a href="admin.php?page=ops&tab=share_counter&date=week" class="<?php echo $date == 'week' ? 'active' : '' ?>"><?php _e('this week', 'off-page-seo') ?></a>
                </div>
                <?php
                $pt = Off_Page_SEO::ops_get_post_types();
                $args = array(
                    'post_type' => $pt,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'ops_shares',
                            'compare' => 'EXISTS'
                        ),
                    ),
                );

                if ('week' == $date) {
                    $args['date_query'] = array(
                        array(
                            'after' => '1 week ago'
                        )
                    );
                }

                if ('month' == $date) {
                    $args['date_query'] = array(
                        array(
                            'after' => '1 month ago'
                        )
                    );
                }

                $wp_query = new WP_Query($args);

                if ($wp_query->have_posts()) :
                    ?>
                    <table class="ops-table">
                        <thead>
                        <tr>
                            <th><?php _e('Article', 'off-page-seo') ?></th>
                            <th>Facebook</th>
                            <th>Google+</th>
                            <th>Pinterest</th>
                            <th>Linkedin</th>
                            <th>Stumbleupon</th>
                            <th><?php _e('Total', 'off-page-seo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($wp_query->have_posts()) : $wp_query->the_post();
                            $shares = get_post_meta(get_the_ID(), 'ops_shares');
                            if(!isset($shares[0]['shares'])){
                                continue;
                            }
                            $shares_uns = $shares[0]['shares'];
                            $sh_fb = isset($shares_uns['facebook']) ? $shares_uns['facebook'] : 0;
                            $sh_go = isset($shares_uns['googleplus']) ? $shares_uns['googleplus'] : 0;
                            $sh_pi = isset($shares_uns['pinterest']) ? $shares_uns['pinterest'] : 0;
                            $sh_li = isset($shares_uns['linkedin']) ? $shares_uns['linkedin'] : 0;
                            $sh_st = isset($shares_uns['stumbleupon']) ? $shares_uns['stumbleupon'] : 0;
                            $total = $sh_fb + $sh_go + $sh_pi + $sh_li + $sh_st;
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </td>
                                <td>
                                    <?php echo $sh_fb ?>
                                </td>
                                <td>
                                    <?php echo $sh_go ?>
                                </td>
                                <td>
                                    <?php echo $sh_pi ?>
                                </td>
                                <td>
                                    <?php echo $sh_li ?>
                                </td>
                                <td>
                                    <?php echo $sh_st ?>
                                </td>
                                <td>
                                    <?php echo $total ?>
                                </td>
                            </tr>
                            <?php
                        endwhile;
                        ?>
                        </tbody>
                    </table>
                    <?php
                endif;
                ?>
            </div>

        </div>
        <?php
    }
}
