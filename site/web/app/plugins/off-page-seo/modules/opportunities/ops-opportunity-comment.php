<?php

class OPS_Opportunity_Comment
{

    public function __construct()
    {
        add_action('ops/opportunities_tabs', array($this, 'ops_opportunity_comment_tab'), 2);

        // edit keyword
        add_action('ops/opportunities_comment', array($this, 'ops_opportunity_comment_body'));

        // ajax for comment ideas
        add_action('wp_ajax_ops_comment_ideas', array($this, 'ops_comment_ideas'));
        add_action('wp_ajax_nopriv_ops_comment_ideas', array($this, 'ops_comment_ideas'));

    }

    function ops_opportunity_comment_tab()
    {
        ?>
        <li>
            <a href="admin.php?page=ops_opportunities" class="ops-tab-switcher <?php echo !isset($_GET['tab']) ? 'active' : '' ?>">
                <?php _e('Comment', 'off-page-seo') ?>
            </a>
        </li>
        <?php
    }


    function ops_opportunity_comment_body()
    {
        $settings = Off_Page_SEO::$settings;
        ?>
        <div class="ops-dashboard-left" id="ops-opportunity-comment">
            <div class="postbox ops-padding">
                <?php if ($this->ops_is_comment_supported($settings['core']['language']) == false): ?>
                    <div class="ops-warning">
                        <?php _e('Your language is not supported, please','off-page-seo') ?>
                        <a href="mailto:info@offpageseoplugin.com"><?php _e('contact us','off-page-seo') ?></a> <?php _e('to improve this plugin.','off-page-seo') ?>
                    </div>
                <?php endif; ?>
                <form action="#" data-preloader="<?php echo OPS_PLUGIN_PATH ?>/img/preloader.gif">
                    <input type="hidden" name="language" id="language" value="<?php echo $settings['core']['language'] ?>">
                    <input type="text" name="keyword" id="keyword" placeholder="<?php _e('Keyword','off-page-seo') ?>">
                    <input type="submit" value="<?php _e('Get ideas','off-page-seo') ?>" class="button button-primary">
                </form>
                <div id="ops-comment-output">
                    <p>
                        <?php _e('Please type in your keyword and generate ideas.','off-page-seo') ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    public function ops_comment_ideas()
    {

        $kw = sanitize_text_field($_POST['keyword']);
        $lang = sanitize_text_field($_POST['language']);
        $language = $this->ops_is_comment_supported($lang) ? $lang : 'en';
        $ideas = $this->ops_get_comment_queries($language);

        // check if we have some ideas
        if (count($ideas) > 0) {

            echo "<ul>";
            foreach ($ideas as $idea) {
                // get query
                if (stristr($idea['tail'], '%keyword%')) {
                    $query = str_replace('%keyword%', $kw, $idea['tail']);
                } else {
                    $query = $kw . ' ' . $idea['tail'];
                }
                ?>
                <li>
                    <div class="ops-name">
                        <?php echo $idea['name']; ?>
                    </div>
                    <div class="ops-google">
                        <a href="<?php echo Off_Page_SEO::ops_get_seach_url($query); ?>" class="button" target="_blank"><?php _e('Browse','off-page-seo') ?></a>
                    </div>
                </li>
                <?php
            }
            echo "</ul>";
        }

        die();
    }

    public function ops_is_comment_supported($lang)
    {
        $supported = array('cs', 'en');
        if (in_array($lang, $supported) == true) {
            return true;
        } else {
            return false;
        }
    }

    public function ops_get_comment_queries($lang)
    {
        if ($lang == 'cs') {
            $output = array(
                'domain-kw' => array(
                    'name' => 'Klíčové slovo v URL',
                    'tail' => '"přidat komentář" inurl:%keyword%'
                ),
                'title-kw' => array(
                    'name' => 'Klíčové slovo v titulku',
                    'tail' => '"přidat komentář" intitle:%keyword%'
                ),
                'blog-cz' => array(
                    'name' => 'Blog.cz komentáře',
                    'tail' => 'site:blog.cz'
                ),
                'forum-kw' => array(
                    'name' => 'Forum s klíčovým slovem',
                    'tail' => '"forum" site:.cz'
                ),
                'forum-phpbb' => array(
                    'name' => 'Forum phpBB s klíčovým slovem',
                    'tail' => '"phpBB"'
                ),
                'forum-bbpress' => array(
                    'name' => 'Forum BBpress s klíčovým slovem',
                    'tail' => '"powered by BBpress"'
                ),
                'discuss-kw' => array(
                    'name' => 'Diskuze s klíčovým slovem',
                    'tail' => '"diskuze" site:.cz'
                ),
                'commentluv' => array(
                    'name' => 'CommentLuv',
                    'tail' => 'CommentLuv'
                ),
            );
        } else {
            $output = array(
                'edu-blogs' => array(
                    'name' => '.edu Blogs',
                    'tail' => 'site:.edu inurl:blog "post a comment" -"you must be logged in"'
                ),
                'gov-blogs' => array(
                    'name' => '.gov Blogs',
                    'tail' => 'site:.gov inurl:blog "post a comment" -"you must be logged in"'
                ),
                'html-comments' => array(
                    'name' => 'Anchor Text In Comment Blogs',
                    'tail' => '"Allowed HTML tags:"'
                ),
                'comment-luv-premium' => array(
                    'name' => 'CommentLuv Premium Blogs',
                    'tail' => '"This blog uses premium CommentLuv" -"The version of CommentLuv on this site is no longer supported."'
                ),
                'do-follow-comments' => array(
                    'name' => 'Do Follow Comment Blogs',
                    'tail' => '"Notify me of follow-up comments?" "Submit the word you see below:"'
                ),
                'expression-engine' => array(
                    'name' => 'Expression Engine Forums',
                    'tail' => '"powered by expressionengine"'
                ),
                'hubpages' => array(
                    'name' => 'Hubpages - Hot Hubs',
                    'tail' => 'site:hubpages.com "hot hubs"'
                ),
                'keywordluv' => array(
                    'name' => 'KeywordLuv Blogs',
                    'tail' => '"Enter YourName@YourKeywords"'
                ),
                'livefyre' => array(
                    'name' => 'LiveFyre Blogs',
                    'tail' => '"get livefyre" "comment help" -"Comments have been disabled for this post"'
                ),
                'intensedebate' => array(
                    'name' => 'Intense Debate Blogs',
                    'tail' => '"if you have a website, link to it here" "post a new comment"'
                ),
                'squidoo-addtolist' => array(
                    'name' => 'Squidoo lenses - Add To List',
                    'tail' => 'site:squidoo.com "add to this list"'
                )
            );
        }

        return $output;
    }
}

new OPS_Opportunity_Comment();