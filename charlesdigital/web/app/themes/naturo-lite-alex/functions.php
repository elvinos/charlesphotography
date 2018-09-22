<?php
/**
 * SKT Naturo functions and definitions
 *
 * @package SKT Naturolite
 */

// Set the word limit of post content 

add_action('the_content','naturo_lite_limit_the_content');

function naturo_lite_limit_the_content($content){
$word_limit =40;
$words = explode(' ', $content);
if (!is_single() && !is_page()) {
return implode(' ', array_slice($words, 0, $word_limit));
} else {
return $content;
}
}

function naturo_lite_short_the_content($content){
  $word_limit = 30;
  $words = explode(' ', $content);
  return implode(' ', array_slice($words, 0, $word_limit));
}

/**
 * Set the content width based on the theme's design and stylesheet.
 */

if ( ! function_exists( 'naturo_lite_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function naturo_lite_setup() {
	if ( ! isset( $content_width ) )
		$content_width = 640; /* pixels */

	load_theme_textdomain( 'naturo_lite', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support('woocommerce');
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-header' );
	add_theme_support( 'title-tag' );
	add_image_size('naturo-homepage-thumb',240,145,true);
	register_nav_menu( 'primary', esc_attr__( 'Primary Menu', 'naturo_lite' ) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff'
	) );
	
	add_editor_style( 'editor-style.css' );
}
endif; // naturo_lite_setup
add_action( 'after_setup_theme', 'naturo_lite_setup' );


function naturo_lite_widgets_init() {	
	
	register_sidebar( array(
		'name'          => esc_attr__( 'Blog Sidebar', 'naturo_lite' ),
		'description'   => esc_attr__( 'Appears on blog page sidebar', 'naturo_lite' ),
		'id'            => 'sidebar-1',
		'before_widget' => '',		
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3><aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
	) );	
	
}
add_action( 'widgets_init', 'naturo_lite_widgets_init' );


function naturo_lite_font_url(){
		$font_url = '';		
		
		/* Translators: If there are any character that are not
		* supported by Oswald, trsnalate this to off, do not
		* translate into your own language.
		*/
		$roboto = _x('on','Roboto:on or off','naturo_lite');
		
		/* Translators: If there has any character that are not supported 
		*  by Scada, translate this to off, do not translate
		*  into your own language.
		*/		
		
		if('off' !== $roboto){
			$font_family = array();
			
			if('off' !== $roboto){
				$font_family[] = 'Roboto:300,400,600,700,800,900';
			}			
			$query_args = array(
				'family'	=> urlencode(implode('|',$font_family)),
			);
			
			$font_url = add_query_arg($query_args,'//fonts.googleapis.com/css');
		}
		
	return $font_url;
	}


function naturo_lite_scripts() {
	wp_enqueue_style('naturo_lite-font', naturo_lite_font_url(), array());
	wp_enqueue_style( 'naturo_lite-basic-style', get_stylesheet_uri() );
	wp_enqueue_style( 'naturo_lite-editor-style', get_template_directory_uri().'/editor-style.css' );
	wp_enqueue_style( 'naturo_lite-nivoslider-style', get_template_directory_uri().'/css/nivo-slider.css' );
	wp_enqueue_style( 'naturo_lite-main-style', get_template_directory_uri().'/css/responsive.css' );		
	wp_enqueue_style( 'naturo_lite-base-style', get_template_directory_uri().'/css/style_base.css' );
	wp_enqueue_script( 'naturo_lite-nivo-script', get_template_directory_uri() . '/js/jquery.nivo.slider.js', array('jquery') );
	wp_enqueue_script( 'naturo_lite-custom_js', get_template_directory_uri() . '/js/custom.js' );
	wp_enqueue_style( 'naturo_lite-font-awesome-style', get_template_directory_uri().'/css/font-awesome.css' );
	wp_enqueue_style( 'naturo_lite-animation-style', get_template_directory_uri().'/css/animation.css' );	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'naturo_lite_scripts' );

function naturo_lite_ie_stylesheet(){
	global $wp_styles;
	
	/** Load our IE-only stylesheet for all versions of IE.
	*   <!--[if lt IE 9]> ... <![endif]-->
	*
	*  Note: It is also possible to just check and see if the $is_IE global in WordPress is set to true before
	*  calling the wp_enqueue_style() function. If you are trying to load a stylesheet for all browsers
	*  EXCEPT for IE, then you would HAVE to check the $is_IE global since WordPress doesn't have a way to
	*  properly handle non-IE conditional comments.
	*/
	wp_enqueue_style('naturolite-ie', get_template_directory_uri().'/css/ie.css', array('naturo-style'));
	$wp_styles->add_data('naturolite-ie','conditional','IE');
	}
add_action('wp_enqueue_scripts','naturo_lite_ie_stylesheet');

define('SKT_URL','http://www.sktthemes.net');
define('SKT_THEME_URL','http://www.sktthemes.net/themes');
define('SKT_THEME_DOC','http://sktthemesdemo.net/documentation/skt-naturo-doc/');
define('SKT_PRO_THEME_URL','http://www.sktthemes.net/shop/clean-minimal-wordpress-theme/');
define('SKT_THEME_FEATURED_SET_VIDEO_URL','https://www.youtube.com/watch?v=310YGYtGLIM');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

// get slug by id
function naturo_lite_get_slug_by_id($id) {
	$post_data = get_post($id, ARRAY_A);
	$slug = $post_data['post_name'];
	return $slug; 
}