<?php

/**
 * Main plugin class
 * */
class Off_Page_SEO
{

    public static $settings;
    public static $total_active_keywords;
    public static $mother = "http://www.offpageseoplugin.com";

    /**
     * Initialization of main class
     * */
    public function __construct()
    {


//        add_action('wp_dashboard_setup', array($this, ''ops_add_dashboard_widgets));

        add_action('add_meta_boxes', array($this, 'ops_main_metabox'));

        add_action('admin_menu', array($this, 'init'));

        if(OPS_PREMIUM == true) {
            add_filter('plugin_action_links_off-page-seo-premium/off-page-seo.php', array($this, 'ops_add_settings_link'));
        } else {
            add_filter('plugin_action_links_off-page-seo/off-page-seo.php', array($this, 'ops_add_settings_link'));
        }

        // inserts ajax url to frontend
        add_action('wp_footer', array($this, 'ops_insert_ajax_url'));

        // add three days interval
        add_filter('cron_schedules', array($this, 'ops_three_days_interval'), 10, 1);

        add_action('plugins_loaded', array($this, 'ops_load_languages'));

        // hook icon to admin bar
        add_action('admin_bar_menu', array($this, 'ops_add_admin_bar_item'), 999);

        // add styles to footer
        add_action('wp_footer', array($this, 'ops_style_admin_bar_icon'));

        self::$settings = self::ops_get_settings();

        // we have no settings yet
        if (!isset(self::$settings['module'])) {
            OPS_Install::ops_initiate_settings();
        }

    }

    function ops_style_admin_bar_icon()
    {
        if (!is_user_logged_in()) {
            return;
        }
        ?>
        <style>
            #wpadminbar .ops-icon:before {
                position: relative;
                float: left;
                font: 400 20px/1 dashicons;
                speak: none;
                padding: 4px 0;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                background-image: none !important;
                margin-right: 6px;
                content: "\f307";
                top: 3px;
            }
        </style>
        <?php
    }

    function ops_add_admin_bar_item($wp_admin_bar)
    {
        $args = array(
            'id' => 'off_page_seo',
            'title' => '<span class="ops-icon"></span>OPS',
            'href' => get_admin_url() . 'admin.php?page=ops',
            'meta' => array('class' => 'ops-admin-bar-icon'),
            'parent' => false
        );
        $wp_admin_bar->add_node($args);

        if (self::ops_is_module_on('backlinks')) {
            $args_bl = array(
                'id' => 'off_page_se_add_backlink',
                'title' => '<a href="' . get_admin_url() . 'admin.php?page=ops&tab=backlinks&new=true" class="ab-item" style="padding-left:0;">' . __('Record Backlink', 'off-page-seo') . '</a>',
                'parent' => 'off_page_seo',
            );
            $wp_admin_bar->add_node($args_bl);
        }

    }

    function ops_load_languages()
    {
        load_plugin_textdomain('off-page-seo', false, dirname(plugin_basename(__FILE__)) . '/../../languages/');
    }


    public function ops_add_settings_link($links)
    {
        $mylinks = array(
            '<a href="' . admin_url('admin.php?page=ops_settings') . '">Settings</a>',
        );
        return array_merge($links, $mylinks);
    }

    public static function ops_start_session()
    {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Add administration menu and styles
     * */
    public function init()
    {
        $display_rights = isset(self::$settings['core']['permission']) && self::$settings['core']['permission'] != '' ? self::$settings['core']['permission'] : 'read';

        // analyze competitors
        add_menu_page('Off Page SEO', 'Off Page SEO', $display_rights, 'ops', array($this, 'ops_dashboard'), 'dashicons-groups', '2.01981816');

        // hook menus
        do_action('ops/add_menu');

        // opportunities
        add_submenu_page('ops', __('Opportunities', 'off-page-seo'), __('Opportunities', 'off-page-seo'), 'activate_plugins', 'ops_opportunities', array($this, 'ops_opportunities'));

        // settings
        add_submenu_page('ops', __('Settings', 'off-page-seo'), __('Settings', 'off-page-seo'), 'activate_plugins', 'ops_settings', array($this, 'ops_settings'));

        wp_enqueue_style('off_page_seo_css', OPS_PLUGIN_PATH .'/less/ops-style.css');

        wp_enqueue_script('off_page_seo_js', OPS_PLUGIN_PATH . '/js/ops-main.js');

        wp_enqueue_style('ops_select_2_css', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-beta.3/css/select2.min.css');

        wp_enqueue_script('ops_select_2_js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-beta.3/js/select2.min.js');

        wp_enqueue_script('ops_sortable', '//code.jquery.com/ui/1.11.4/jquery-ui.js');

        wp_enqueue_script('ops_tablesorter', OPS_PLUGIN_PATH . '/js/jquery.tablesorter.min.js');
    }

    /**
     * Dashboard
     */
    public function ops_dashboard()
    {
        new OPS_Dashboard();
    }

    /**
     * Opportunities Page Call
     */
    public function ops_opportunities()
    {
        new OPS_Opportunities();
    }

    /**
     * Settings Page Call
     */
    public function ops_settings()
    {
        new OPS_Settings();
    }

    /**
     * Adds meta boxes with shares
     */
    public function ops_main_metabox()
    {
        $settings = self::ops_get_settings();
        if (!isset($settings['core']['post_types'])) {
            return;
        }
        foreach ($settings['core']['post_types'] as $post_type) {
            add_meta_box(
                'ops-main-meta-box', esc_html__('Off Page SEO', 'ops'), array($this, 'ops_main_metabox_callback'), $post_type,
                'normal',
                'default'
            );
        }

    }

    public function ops_main_metabox_callback()
    {

        ?>
        <div id="ops-metabox">
            <?php do_action('ops/main_metabox_tabs'); ?>
        </div>
        <?php
    }

    /**
     * call dashboard
     * DEPRECATED - MIGHT BE ACTIVATED IN FUTURE
     */
    public function ops_add_dashboard_widgets()
    {
        wp_add_dashboard_widget('off_page_seo_wp_dashboard_reporter', 'Off Page SEO Rank Reporter', array($this, 'ops_render_dashboard_widget_reporter'));
        wp_add_dashboard_widget('off_page_seo_wp_dashboard_backlinks', 'Off Page SEO Rank Backlinks', array($this, 'ops_render_dashboard_widget_backlinks'));
    }

    /**
     * Render Dashboard Widget
     */
    public function ops_render_dashboard_widget_reporter()
    {
        new OPS_Dashboard_Widget_Reporter;
    }

    /**
     * Render Dashboard Widget
     */
    public function ops_render_dashboard_widget_backlinks()
    {
        new OPS_Dashboard_Widget_Backlinks;
    }


    public static function ops_format_date($timestamp)
    {
        $settings = Off_Page_SEO::ops_get_settings();
        if (isset($settings['core']['date_format'])) {
            $format = $settings['core']['date_format'] . ' H:i:s';
        } else {
            $format = 'F d, Y H:i:s';
        }
        return date($format, $timestamp);
    }


    public static function ops_get_total_active_keywords()
    {
        global $wpdb;
        $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_rank_report WHERE active = 1 AND blog_id = "' . get_current_blog_id() . '"', ARRAY_A);
        return count($db_results);
    }

    public static function ops_get_all_keywords()
    {
        global $wpdb;
        $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_rank_report WHERE active = 1 AND blog_id = "' . get_current_blog_id() . '"', ARRAY_A);
        return $db_results;
    }

    public static function ops_get_post_keywords($pid)
    {
        global $wpdb;
        $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_rank_report WHERE post_id = "' . $pid . '" AND active = 1 AND blog_id = "' . get_current_blog_id() . '"', ARRAY_A);
        return $db_results;
    }

    public static function ops_get_keyword($kwid)
    {
        global $wpdb;
        $db_result = $wpdb->get_row('SELECT * FROM ' . $wpdb->base_prefix . 'ops_rank_report WHERE id = "' . $kwid . '"', ARRAY_A);
        return $db_result;
    }

    public static function ops_deactivate_keyword($kwid)
    {
        global $wpdb;
        $wpdb->query('UPDATE ' . $wpdb->base_prefix . 'ops_rank_report SET active = 0 WHERE id = ' . $kwid);
    }

    public static function ops_delete_backlink($blid)
    {
        global $wpdb;
        $wpdb->delete($wpdb->base_prefix . 'ops_backlinks', array('ID' => $blid));
    }

    public static function ops_get_all_backlinks()
    {
        global $wpdb;
        $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_backlinks WHERE blog_id=' . get_current_blog_id(), ARRAY_A);
        return $db_results;
    }

    public static function ops_get_backlink($blid)
    {
        global $wpdb;
        $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_backlinks WHERE id=' . $blid . ' AND blog_id=' . get_current_blog_id(), ARRAY_A);
        return $db_results;
    }

    public static function ops_get_all_backlinks_with_keywords()
    {
        global $wpdb;
        $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_backlinks WHERE keyword_id != 0 AND blog_id=' . get_current_blog_id(), ARRAY_A);
        return $db_results;
    }

    /**
     * Recognize if the site is multisite. It returns either blog settings or page settings.
     * returns: array
     */
    public static function ops_get_settings()
    {
        if (is_multisite()) {
            $settings = get_blog_option(get_current_blog_id(), 'ops_settings');
        } else {
            $settings = get_option('ops_settings');
        }

        // bug fix form old version
        if (is_array($settings)) {
            $settings = serialize($settings);
        }

        return unserialize($settings);
    }


    public static function ops_get_request_url($keyword)
    {
        $settings = Off_Page_SEO::ops_get_settings();
        $request = 'http://www.google.' . $settings['core']['google_domain'] . '/search?hl=' . $settings['core']['language'] . '&start=0&q=' . urlencode($keyword) . '&num=100&pws=0&adtest=off';
        return $request;
    }

    public static function ops_get_seach_url($keyword)
    {
        $settings = self::ops_get_settings();
        $url = 'http://www.google.' . $settings['core']['google_domain'] . '/search?hl=' . $settings['core']['language'] . '&q=' . urlencode($keyword);
        return $url;
    }

    /**
     * Recognize if its multisite and saves either blog option or site option
     * @param type $option
     * @param type $value
     */
    public static function ops_update_option($option, $value)
    {
        if (is_multisite()) {
            update_blog_option(get_current_blog_id(), $option, $value);
        } else {
            update_site_option($option, $value);
        }
    }

    /**
     * GET Option based on multistite / or not
     * @param type $option
     * @param type $value
     */
    public static function ops_get_option($option)
    {
        if (is_multisite()) {
            $return = get_blog_option(get_current_blog_id(), $option);
        } else {
            $return = get_site_option($option);
        }
        return $return;
    }

    /**
     * Get settings of the Moove plugin from blog with ID 1
     * @return array
     */
    public static function ops_get_master_helper()
    {
        $switch_back = false;
        // if we are on multisite, get settings from the blog ID 1
        if (is_multisite() && get_current_blog_id() != 1) {
            switch_to_blog(1);
            $switch_back = true;
        }

        $master_helper = get_option('ops_master_helper');

        if ($switch_back == true) {
            restore_current_blog();
        }

        return $master_helper;
    }

    /**
     * Updates moove settings to blog with ID 1
     */
    public static function ops_update_master_helper($master_helper)
    {
        $switch_back = false;
        // if we are on multisite, update settings from the blog id 1
        if (is_multisite() && get_current_blog_id() != 1) {
            switch_to_blog(1);
            $switch_back = true;
        }

        update_option('ops_master_helper', $master_helper);

        if ($switch_back == true) {
            restore_current_blog();
        }

    }


    /**
     * Returns nice language
     */
    public static function ops_get_language($id)
    {
        $languages = Off_Page_SEO::ops_lang_array();
        return $languages[$id];
    }

    /**
     * Returns current language ID
     * @return type current language ID
     */
    public static function ops_get_lang()
    {
        $settings = Off_Page_SEO::ops_get_settings();
        return $settings['lang'];
    }

    /**
     * Get post types
     * @return boolean
     */
    public static function ops_get_post_types()
    {
        $settings = self::ops_get_settings();
        if (isset($settings['core']['post_types'])) {
            return $settings['core']['post_types'];
        } else {
            return false;
        }
    }


    /**
     * Allowed post types
     * @return type
     */
    public static function ops_get_allowed_post_types()
    {
        $types = get_post_types();
        $banned = array('wpseo_crawl_issue', 'wpcf7_contact_form', 'nav_menu_item', 'revision', 'attachment', 'acf', 'acf-field', 'acf-field-group');
        foreach ($types as $type) {
            if (!in_array($type, $banned))
                $data[] = $type;
        }

        return $data;
    }

    public static function ops_is_module_on($name)
    {
        $settings = self::ops_get_settings();
        if (isset($settings['module'][$name]) && $settings['module'][$name] == 1) {
            return true;
        }
        return false;
    }

    public static function ops_is_any_module_on()
    {
        $settings = self::ops_get_settings();
        if (isset($settings['module'])) {
            foreach ($settings['module'] as $module) {
                if ($module == 1) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function ops_create_log_entry($action, $type = 'info', $message)
    {
        $log = unserialize(self::ops_get_option('ops_log'));
        if ($log == '' || count($log) == 0) {
            $log = array();
        }
        $new_entry = array(
            'time' => time(),
            'action' => $action,
            'type' => $type,
            'message' => $message
        );
        array_unshift($log, $new_entry);
        $new_log = array_slice($log, 0, 100);
        self::ops_update_option('ops_log', serialize($new_log));
    }

    public function ops_insert_ajax_url()
    {
        ?>
        <script type="text/javascript">
            var opsajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    }

    function ops_three_days_interval($schedules)
    {
        $schedules['ops_three_days'] = array(
            'interval' => 259200,
            'display' => 'Once Every 3 Days'
        );

        $schedules['ops_six_days'] = array(
            'interval' => 518400,
            'display' => 'Once Every 6 Days'
        );

        return (array)$schedules;
    }

    public static function ops_format_number($number)
    {
        $number = str_replace(' ', '', $number);
        return number_format($number, 0, ',', ' ');
    }

    public static function ops_request_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = $_SERVER['SERVER_ADDR'];
        return $ipaddress;
    }

    /**
     * Array of all languages
     * @return array
     */
    public static function ops_lang_array()
    {
        $languages = array(
            'aa' => 'Afar',
            'ab' => 'Abkhaz',
            'ae' => 'Avestan',
            'af' => 'Afrikaans',
            'ak' => 'Akan',
            'am' => 'Amharic',
            'an' => 'Aragonese',
            'ar' => 'Arabic',
            'as' => 'Assamese',
            'av' => 'Avaric',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
            'ba' => 'Bashkir',
            'be' => 'Belarusian',
            'bg' => 'Bulgarian',
            'bh' => 'Bihari',
            'bi' => 'Bislama',
            'bm' => 'Bambara',
            'bn' => 'Bengali',
            'bo' => 'Tibetan Standard, Tibetan, Central',
            'br' => 'Breton',
            'bs' => 'Bosnian',
            'ca' => 'Catalan; Valencian',
            'ce' => 'Chechen',
            'ch' => 'Chamorro',
            'co' => 'Corsican',
            'cr' => 'Cree',
            'cs' => 'Czech',
            'cv' => 'Chuvash',
            'cy' => 'Welsh',
            'da' => 'Danish',
            'de' => 'German',
            'dv' => 'Divehi; Dhivehi; Maldivian;',
            'dz' => 'Dzongkha',
            'ee' => 'Ewe',
            'el' => 'Greek, Modern',
            'en' => 'English',
            'es' => 'Spanish; Castilian',
            'et' => 'Estonian',
            'eu' => 'Basque',
            'fa' => 'Persian',
            'ff' => 'Fula; Fulah; Pulaar; Pular',
            'fi' => 'Finnish',
            'fj' => 'Fijian',
            'fo' => 'Faroese',
            'fr' => 'French',
            'fy' => 'Western Frisian',
            'ga' => 'Irish',
            'gd' => 'Scottish Gaelic; Gaelic',
            'gl' => 'Galician',
            'gu' => 'Gujarati',
            'gv' => 'Manx',
            'ha' => 'Hausa',
            'he' => 'Hebrew (modern)',
            'hi' => 'Hindi',
            'ho' => 'Hiri Motu',
            'hr' => 'Croatian',
            'ht' => 'Haitian; Haitian Creole',
            'hu' => 'Hungarian',
            'hy' => 'Armenian',
            'hz' => 'Herero',
            'ia' => 'Interlingua',
            'id' => 'Indonesian',
            'ie' => 'Interlingue',
            'ig' => 'Igbo',
            'ii' => 'Nuosu',
            'ik' => 'Inupiaq',
            'io' => 'Ido',
            'is' => 'Icelandic',
            'it' => 'Italian',
            'iu' => 'Inuktitut',
            'ja' => 'Japanese (ja)',
            'jv' => 'Javanese (jv)',
            'ka' => 'Georgian',
            'kg' => 'Kongo',
            'ki' => 'Kikuyu, Gikuyu',
            'kj' => 'Kwanyama, Kuanyama',
            'kk' => 'Kazakh',
            'kl' => 'Kalaallisut, Greenlandic',
            'km' => 'Khmer',
            'kn' => 'Kannada',
            'ko' => 'Korean',
            'kr' => 'Kanuri',
            'ks' => 'Kashmiri',
            'ku' => 'Kurdish',
            'kv' => 'Komi',
            'kw' => 'Cornish',
            'ky' => 'Kirghiz, Kyrgyz',
            'la' => 'Latin',
            'lb' => 'Luxembourgish, Letzeburgesch',
            'lg' => 'Luganda',
            'li' => 'Limburgish, Limburgan, Limburger',
            'ln' => 'Lingala',
            'lo' => 'Lao',
            'lt' => 'Lithuanian',
            'lu' => 'Luba-Katanga',
            'lv' => 'Latvian',
            'mg' => 'Malagasy',
            'mh' => 'Marshallese',
            'mi' => 'Maori',
            'mk' => 'Macedonian',
            'ml' => 'Malayalam',
            'mn' => 'Mongolian',
            'mr' => 'Marathi (Mara?hi)',
            'ms' => 'Malay',
            'mt' => 'Maltese',
            'my' => 'Burmese',
            'na' => 'Nauru',
            'nb' => 'Norwegian BokmÃ¥l',
            'nd' => 'North Ndebele',
            'ne' => 'Nepali',
            'ng' => 'Ndonga',
            'nl' => 'Dutch',
            'nn' => 'Norwegian Nynorsk',
            'no' => 'Norwegian',
            'nr' => 'South Ndebele',
            'nv' => 'Navajo, Navaho',
            'ny' => 'Chichewa; Chewa; Nyanja',
            'oc' => 'Occitan',
            'oj' => 'Ojibwe, Ojibwa',
            'om' => 'Oromo',
            'or' => 'Oriya',
            'os' => 'Ossetian, Ossetic',
            'pa' => 'Panjabi, Punjabi',
            'pi' => 'Pali',
            'pl' => 'Polish',
            'ps' => 'Pashto, Pushto',
            'pt' => 'Portuguese',
            'qu' => 'Quechua',
            'rm' => 'Romansh',
            'rn' => 'Kirundi',
            'ro' => 'Romanian, Moldavian, Moldovan',
            'ru' => 'Russian',
            'rw' => 'Kinyarwanda',
            'sa' => 'Sanskrit',
            'sc' => 'Sardinian',
            'sd' => 'Sindhi',
            'se' => 'Northern Sami',
            'sg' => 'Sango',
            'si' => 'Sinhala, Sinhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovene',
            'sm' => 'Samoan',
            'sn' => 'Shona',
            'so' => 'Somali',
            'sq' => 'Albanian',
            'sr' => 'Serbian',
            'ss' => 'Swati',
            'st' => 'Southern Sotho',
            'su' => 'Sundanese',
            'sv' => 'Swedish',
            'sw' => 'Swahili',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'tg' => 'Tajik',
            'th' => 'Thai',
            'ti' => 'Tigrinya',
            'tk' => 'Turkmen',
            'tl' => 'Tagalog',
            'tn' => 'Tswana',
            'to' => 'Tonga (Tonga Islands)',
            'tr' => 'Turkish',
            'ts' => 'Tsonga',
            'tt' => 'Tatar',
            'tw' => 'Twi',
            'ty' => 'Tahitian',
            'ug' => 'Uighur, Uyghur',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            've' => 'Venda',
            'vi' => 'Vietnamese',
            'wa' => 'Walloon',
            'wo' => 'Wolof',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'yo' => 'Yoruba',
            'za' => 'Zhuang, Chuang',
            'zh' => 'Chinese',
            'zu' => 'Zulu',
        );
        return $languages;
    }

    public static function ops_google_domains_array()
    {
        $google_domains = array(
            "com" => "Default - google.com",
            "as" => "American Samoa - google.as",
            "off.ai" => "Anguilla - google.off.ai",
            "com.ag" => "Antigua and Barbuda - google.com.ag",
            "com.ar" => "Argentina - google.com.ar",
            "com.au" => "Australia - google.com.au",
            "at" => "Austria - google.at",
            "az" => "Azerbaijan - google.az",
            "be" => "Belgium - google.be",
            "com.br" => "Brazil - google.com.br",
            "vg" => "British Virgin Islands - google.vg",
            "bi" => "Burundi - google.bi",
            "ca" => "Canada - google.ca",
            "td" => "Chad - google.td",
            "cl" => "Chile - google.cl",
            "com.co" => "Colombia - google.com.co",
            "co.cr" => "Costa Rica - google.co.cr",
            "ci" => "Côte dIvoire - google.ci",
            "com.cu" => "Cuba - google.com.cu",
            "cz" => "Czech Republic - google.cz",
            "cd" => "Dem. Rep. of the Congo - google.cd",
            "dk" => "Denmark - google.dk",
            "dj" => "Djibouti - google.dj",
            "com.do" => "Dominican Republic - google.com.do",
            "com.ec" => "Ecuador - google.com.ec",
            "com.sv" => "El Salvador - google.com.sv",
            "fm" => "Federated States of Micronesia - google.fm",
            "com.fj" => "Fiji - google.com.fj",
            "fi" => "Finland - google.fi",
            "fr" => "France - google.fr",
            "gm" => "The Gambia - google.gm",
            "ge" => "Georgia - google.ge",
            "de" => "Germany - google.de",
            "com.gi" => "Gibraltar - google.com.gi",
            "com.gr" => "Greece - google.com.gr",
            "gl" => "Greenland - google.gl",
            "gg" => "Guernsey - google.gg",
            "hn" => "Honduras - google.hn",
            "com.hk" => "Hong Kong - google.com.hk",
            "co.hu" => "Hungary - google.co.hu",
            "co.in" => "India - google.co.in",
            "ie" => "Ireland - google.ie",
            "co.im" => "Isle of Man - google.co.im",
            "co.il" => "Israel - google.co.il",
            "it" => "Italy - google.it",
            "com.jm" => "Jamaica - google.com.jm",
            "co.jp" => "Japan - google.co.jp",
            "co.je" => "Jersey - google.co.je",
            "kz" => "Kazakhstan - google.kz",
            "co.kr" => "Korea - google.co.kr",
            "lv" => "Latvia - google.lv",
            "co.ls" => "Lesotho - google.co.ls",
            "li" => "Liechtenstein - google.li",
            "lt" => "Lithuania - google.lt",
            "lu" => "Luxembourg - google.lu",
            "mw" => "Malawi - google.mw",
            "com.my" => "Malaysia - google.com.my",
            "com.mt" => "Malta - google.com.mt",
            "mu" => "Mauritius - google.mu",
            "com.mx" => "México - google.com.mx",
            "ms" => "Montserrat - google.ms",
            "com.na" => "Namibia - google.com.na",
            "com.np" => "Nepal - google.com.np",
            "nl" => "Netherlands - google.nl",
            "co.nz" => "New Zealand - google.co.nz",
            "com.ni" => "Nicaragua - google.com.ni",
            "com.nf" => "Norfolk Island - google.com.nf",
            "com.pk" => "Pakistan - google.com.pk",
            "com.pa" => "Panamá - google.com.pa",
            "com.py" => "Paraguay - google.com.py",
            "com.pe" => "Perú - google.com.pe",
            "com.ph" => "Philippines - google.com.ph",
            "pn" => "Pitcairn Islands - google.pn",
            "pl" => "Poland - google.pl",
            "pt" => "Portugal - google.pt",
            "com.pr" => "Puerto Rico - google.com.pr",
            "cg" => "Rep. of the Congo - google.cg",
            "ro" => "Romania - google.ro",
            "ru" => "Russia - google.ru",
            "rw" => "Rwanda - google.rw",
            "sh" => "Saint Helena - google.sh",
            "sm" => "San Marino - google.sm",
            "com.sg" => "Singapore - google.com.sg",
            "sk" => "Slovakia - google.sk",
            "co.za" => "South Africa - google.co.za",
            "es" => "Spain - google.es",
            "se" => "Sweden - google.se",
            "ch" => "Switzerland - google.ch",
            "com.tw" => "Taiwan - google.com.tw",
            "co.th" => "Thailand - google.co.th",
            "tt" => "Trinidad and Tobago - google.tt",
            "com.tr" => "Turkey - google.com.tr",
            "com.ua" => "Ukraine - google.com.ua",
            "ae" => "United Arab Emirates - google.ae",
            "co.uk" => "United Kingdom - google.co.uk",
            "com.uy" => "Uruguay - google.com.uy",
            "uz" => "Uzbekistan - google.uz",
            "vu" => "Vanuatu - google.vu",
            "co.ve" => "Venezuela - google.co.ve"
        );
        return $google_domains;
    }
}


function ops_is_the_plugin_premium()
{
    return false;
}