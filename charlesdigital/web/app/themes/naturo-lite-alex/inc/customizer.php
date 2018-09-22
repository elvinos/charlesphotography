<?php
/**
 * SKT Naturo Theme Customizer
 *
 * @package SKT Naturolite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function naturo_lite_customize_register( $wp_customize ) {
	
	//Add a class for titles
    class Naturo_Lite_Info extends WP_Customize_Control {
        public $type = 'info';
        public $label = '';
        public function render_content() {
        ?>
			<h3 style="text-decoration: underline; color: #DA4141; text-transform: uppercase;"><?php echo esc_html( $this->label ); ?></h3>
        <?php
        }
    }
	
	class WP_Customize_Textarea_Control extends WP_Customize_Control {
    public $type = 'textarea';
 
    public function render_content() {
        ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
        <?php
    }
}
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->remove_control('header_textcolor');
	$wp_customize->remove_control('display_header_text');
	
	$wp_customize->add_section(
        'logo_sec',
        array(
            'title' => __('Logo (PRO Version)', 'naturo_lite'),
            'priority' => 1,
 			'description' => sprintf( __( 'Logo Settings available in<br /> %s.', 'naturo_lite' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( '"'.SKT_PRO_THEME_URL.'"' ), __( 'PRO Version', 'naturo_lite' )
						)
					),			
        )
    );  
    $wp_customize->add_setting('naturo_lite_options[logo-info]',array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control( new Naturo_Lite_Info( $wp_customize, 'logo_section', array(
        'section' => 'logo_sec',
        'settings' => 'naturo_lite_options[logo-info]',
        'priority' => null
        ) )
    );
	$wp_customize->add_setting('color_scheme',array(
			'default'	=> '#e75300',
			'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','naturo_lite'),
 			'description' => sprintf( __( 'More color options in <br /> %s.', 'naturo_lite' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( '"'.SKT_PRO_THEME_URL.'"' ), __( 'PRO Version', 'naturo_lite' )
						)
					),				
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);

// Home Boxes 	
	$wp_customize->add_section('page_boxes',array(
		'title'	=> __('Homepage Boxes','naturo_lite'),
 			'description' => sprintf( __( 'Featured Image Dimensions : ( 61 X 46 )<br/> Select Featured Image for these pages <br /> How to set featured image %s', 'naturo_lite' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( '"'.SKT_THEME_FEATURED_SET_VIDEO_URL.'"' ), __( 'Click Here ?', 'naturo_lite' )
						)
					),
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('page-setting1',array(
			'sanitize_callback'	=> 'naturo_lite_sanitize_integer'
	));
	
	$wp_customize->add_control('page-setting1',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for box one:','naturo_lite'),
			'section'	=> 'page_boxes'	
	));
	
	$wp_customize->add_setting('page-setting2',array(
			'sanitize_callback'	=> 'naturo_lite_sanitize_integer'
	));
	
	$wp_customize->add_control('page-setting2',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for box two:','naturo_lite'),
			'section'	=> 'page_boxes'
	));
	
	$wp_customize->add_setting('page-setting3',array(
			'sanitize_callback'	=> 'naturo_lite_sanitize_integer'
	));
	
	$wp_customize->add_control('page-setting3',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for box three:','naturo_lite'),
			'section'	=> 'page_boxes'
	));	
	
	$wp_customize->add_setting('page-setting4',array(
			'sanitize_callback'	=> 'naturo_lite_sanitize_integer'
	));
	
	$wp_customize->add_control('page-setting4',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for box four:','naturo_lite'),
			'section'	=> 'page_boxes'
	));	
 
// Home Boxes
	$wp_customize->add_section('slider_section',array(
		'title'	=> __('Slider Settings','naturo_lite'),
		'description' => sprintf( __( 'Slider Image Dimensions ( 1400 X 544 )<br/> Select Featured Image for these pages <br /> How to set featured image <a href="%1$s" target="_blank">Click Here ?</a><br/><br/> More slider settings available in <a href="%2$s" target="_blank">PRO Version</a>', 'naturo_lite' ),
			esc_url( '"'.SKT_THEME_FEATURED_SET_VIDEO_URL.'"' ),
			esc_url( '"'.SKT_PRO_THEME_URL.'"' )
		),			   	
		'priority'		=> null
	));
// Slider Section	
	
	
	// Slide Image 1
	$wp_customize->add_setting('slide_image1',array(
		'default'	=> get_template_directory_uri().'/images/slides/slider1.jpg',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	$wp_customize->add_control(   new WP_Customize_Image_Control( $wp_customize, 'slide_image1', array(
            'label' => __('Slide Image 1 (1400x544)','naturo_lite'),
            'section' => 'slider_section',
            'settings' => 'slide_image1'
       		)
     	 )
	);	
	$wp_customize->add_setting('slide_title1',array(
			'default'	=> __('We Create Wordpress Theme','naturo_lite'),
			'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control( 'slide_title1', array(	
			'label'	=> __('Slide title 1','naturo_lite'),
			'section'	=> 'slider_section',
			'setting'	=> 'slide_title1'
	));
	$wp_customize->add_setting('slide_desc1',array(
		'default'	=> __('We are a group of experienced designers and developers. We set new standards in user experience & make future happen.','naturo_lite'),
		'sanitize_callback'	=> 'sanitize_text_field'	
	));
	$wp_customize->add_control(	new WP_Customize_Textarea_Control( $wp_customize,'slide_desc1', array(
				'label'	=> __('Slider description  1','naturo_lite'),
				'section'	=> 'slider_section',
				'setting'	=> 'slide_desc1'
			)
		)
	);
	$wp_customize->add_setting('slide_link1',array(
			'default'	=> '#link1',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('slide_link1',array(
			'label'	=> __('Slide link 1','naturo_lite'),
			'section'	=> 'slider_section',
			'setting'	=> 'slide_link1'
	));	
	// Slide Image 2
	$wp_customize->add_setting('slide_image2',array(
			'default'	=> get_template_directory_uri().'/images/slides/slider2.jpg',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control(	new WP_Customize_Image_Control(	$wp_customize, 'slide_image2', array(
				'label'	=> __('Slide image 2','naturo_lite'),
				'section'	=> 'slider_section',
				'setting'	=> 'slide_image2'
			)
		)
	);	
	$wp_customize->add_setting('slide_title2',array(
			'default'	=> __('We Create Wordpress Responsive Theme','naturo_lite'),
			'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control( 'slide_title2', array(	
			'label'	=> __('Slide title 2','naturo_lite'),
			'section'	=> 'slider_section',
			'setting'	=> 'slide_title2'
	));	
	$wp_customize->add_setting('slide_desc2',array(
			'default'	=> __('We are a group of experienced designers and developers. We set new standards in user experience & make future happen.','naturo_lite'),
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(	new WP_Customize_Textarea_Control( $wp_customize,'slide_desc2', array(
				'label'	=> __('Slide description 2','naturo_lite'),
				'section'	=> 'slider_section',
				'setting'	=> 'slide_desc2'
			)
		)
	);	
	$wp_customize->add_setting('slide_link2',array(
			'default'	=> '#link2',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('slide_link2',array(
		'label'	=> __('Slide link 2','naturo_lite'),
		'section'	=> 'slider_section',
		'setting'	=> 'slide_link2'
	));	
	// Slide Image 3
	$wp_customize->add_setting('slide_image3',array(
			'default'	=> '',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control( new WP_Customize_Image_Control(	$wp_customize,'slide_image3', array(
				'label'	=> __('Slide Image 3','naturo_lite'),
				'section'	=> 'slider_section',
				'setting'	=> 'slide_image3'				
			)
		)
	);	
	$wp_customize->add_setting('slide_title3',array(
			'default'	=> __('','naturo_lite'),
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('slide_title3', array(		
			'label'	=> __('Slide title 3','naturo_lite'),
			'section'	=> 'slider_section',
			'setting'	=> 'slide_title3'			
	));	
	$wp_customize->add_setting('slide_desc3',array(
			'default'	=> __('','naturo_lite'),
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(	new WP_Customize_Textarea_Control($wp_customize,'slide_desc3', array(
				'label'	=> __('Slide Description 3','naturo_lite'),
				'section'	=> 'slider_section',
				'setting'	=> 'slide_desc3'		
			)
		)
	);	
	$wp_customize->add_setting('slide_link3',array(
			'default'	=> '',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('slide_link3',array(
			'label'	=> __('Slide link 3','naturo_lite'),
			'section'	=> 'slider_section',
			'setting'	=> 'slide_link3'
	));	
 
	$wp_customize->add_section('social_sec',array(
			'title'	=> __('Social Settings','naturo_lite'),
 			'description' => sprintf( __( 'Add social icons link here.<br />More icon available in %s.', 'naturo_lite' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( '"'.SKT_PRO_THEME_URL.'"' ), __( 'PRO Version', 'naturo_lite' )
						)
					),			
			'priority'		=> null
	));
	$wp_customize->add_setting('fb_link',array(
			'default'	=> '#facebook',
			'sanitize_callback'	=> 'esc_url_raw'	
	));
	
	$wp_customize->add_control('fb_link',array(
			'label'	=> __('Add facebook link here','naturo_lite'),
			'section'	=> 'social_sec',
			'setting'	=> 'fb_link'
	));	
	$wp_customize->add_setting('twitt_link',array(
			'default'	=> '#twitter',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('twitt_link',array(
			'label'	=> __('Add twitter link here','naturo_lite'),
			'section'	=> 'social_sec',
			'setting'	=> 'twitt_link'
	));
	$wp_customize->add_setting('gplus_link',array(
			'default'	=> '#gplus',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('gplus_link',array(
			'label'	=> __('Add google plus link here','naturo_lite'),
			'section'	=> 'social_sec',
			'setting'	=> 'gplus_link'
	));
	$wp_customize->add_setting('linked_link',array(
			'default'	=> '#linkedin',
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('linked_link',array(
			'label'	=> __('Add linkedin link here','naturo_lite'),
			'section'	=> 'social_sec',
			'setting'	=> 'linked_link'
	));
	$wp_customize->add_section('footer_text',array(
			'title'	=> __('Footer Area','naturo_lite'),
			'priority'	=> null,
			'description'	=> __('Add footer copyright text','naturo_lite')
	));
	$wp_customize->add_setting('naturo_lite_options[credit-info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control( new Naturo_Lite_Info( $wp_customize, 'cred_section', array(
		'label'	=> __('To modify footer copyright notice without changing your themes code, please upgrade to pro.','naturo_lite'),
        'section' => 'footer_text',
        'settings' => 'naturo_lite_options[credit-info]'
        ) )
    );
	$wp_customize->add_setting('about_title',array(
			'default'	=> __('About Us','naturo_lite'),
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('about_title',array(
			'label'	=> __('Add about title here','naturo_lite'),
			'section'	=> 'footer_text',
			'setting'	=> 'about_title'
	));
	
	$wp_customize->add_setting('about_description', array(
			'default'	=> __('Sed suscipit mauris nec mauris vulputate, a posuere libero congue. Nam laoreet elit eu erat pulvinar, et efficitur nibh euismod.Nam metus lorem, hendrerit quis ante eget, lobortis elementum neque. Aliquam in ullamcorper quam. Integer euismod ligula in mauris vehic.','naturo_lite'),
			'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(	new WP_Customize_Textarea_Control( $wp_customize,'about_description', array(
				'label'	=> __('Add about description for footer','naturo_lite'),
				'section'	=> 'footer_text',
				'setting'	=> 'about_description'
			)
		)
	);
	
	$wp_customize->add_section('contact_sec',array(
			'title'	=> __('Contact Details','naturo_lite'),
			'description'	=> __('Add you contact details here','naturo_lite'),
			'priority'	=> null
	));
	
	$wp_customize->add_setting('contact_add',array(
			'default'	=> __('100 King St, Melbourne PIC 4000, Australia','naturo_lite'),
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control(	new WP_Customize_Textarea_Control( $wp_customize, 'contact_add', array(
				'label'	=> __('Add contact address here','naturo_lite'),
				'section'	=> 'contact_sec',
				'setting'	=> 'contact_add'
			)
		)
	);
	$wp_customize->add_setting('contact_no',array(
			'default'	=> __('+123 456 7890','naturo_lite'),
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('contact_no',array(
			'label'	=> __('Add contact number here.','naturo_lite'),
			'section'	=> 'contact_sec',
			'setting'	=> 'contact_no'
	));
	$wp_customize->add_setting('contact_mail',array(
			'default'	=> 'contact@company.com',
			'sanitize_callback'	=> 'sanitize_email'
	));
	
	$wp_customize->add_control('contact_mail',array(
			'label'	=> __('Add you email here','naturo_lite'),
			'section'	=> 'contact_sec',
			'setting'	=> 'contact_mail'
	));
	
	$wp_customize->add_section(
        'theme_layout_sec',
        array(
            'title' => __('Layout Settings (PRO Version)', 'naturo_lite'),
            'priority' => null,
 			'description' => sprintf( __( 'Layout Settings available in <br />%s.', 'naturo_lite' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( '"'.SKT_PRO_THEME_URL.'"' ), __( 'PRO Version', 'naturo_lite' )
						)
					),			
        )
    );  
    $wp_customize->add_setting('naturo_lite_options[layout-info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control( new Naturo_Lite_Info( $wp_customize, 'layout_section', array(
        'section' => 'theme_layout_sec',
        'settings' => 'naturo_lite_options[layout-info]',
        'priority' => null
        ) )
    );
	
	$wp_customize->add_section(
        'theme_font_sec',
        array(
            'title' => __('Fonts Settings (PRO Version)', 'naturo_lite'),
            'priority' => null,
 			'description' => sprintf( __( 'Font Settings available in <br />%s.', 'naturo_lite' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( '"'.SKT_PRO_THEME_URL.'"' ), __( 'PRO Version', 'naturo_lite' )
						)
					),			
        )
    );  
    $wp_customize->add_setting('naturo_lite_options[font-info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control( new Naturo_Lite_Info( $wp_customize, 'font_section', array(
        'section' => 'theme_font_sec',
        'settings' => 'naturo_lite_options[font-info]',
        'priority' => null
        ) )
    );
	
    $wp_customize->add_section(
        'theme_doc_sec',
        array(
            'title' => __('Documentation &amp; Support', 'naturo_lite'),
            'priority' => null,
 			'description' => sprintf( __( 'For documentation and support check this link : <br />%s.', 'naturo_lite' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( '"'.SKT_THEME_DOC.'"' ), __( 'Naturo Documentation', 'naturo_lite' )
						)
					),							
        )
    );  
    $wp_customize->add_setting('naturo_lite_options[info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control( new Naturo_Lite_Info( $wp_customize, 'doc_section', array(
        'section' => 'theme_doc_sec',
        'settings' => 'naturo_lite_options[info]',
        'priority' => 10
        ) )
    );		
}
add_action( 'customize_register', 'naturo_lite_customize_register' );

//Integer
function naturo_lite_sanitize_integer( $input ) {
    if( is_numeric( $input ) ) {
        return intval( $input );
    }
}

function naturo_lite_custom_css(){
		?>
        	<style type="text/css">
					
					a, .header .header-inner .nav ul li a:hover, 
					.signin_wrap a:hover,
					.header .header-inner .nav ul li.current_page_item a,					
					.services-wrap .one_fourth:hover .ReadMore,
					.services-wrap .one_fourth:hover h3,
					.services-wrap .one_fourth:hover .fa,
					.blog_lists h2 a:hover,
					#sidebar ul li a:hover,
					.recent-post h6:hover,
					.MoreLink:hover,
					.ReadMore a:hover,
					.services-wrap .one_fourth:hover .ReadMore a,
					.cols-3 ul li a:hover, .cols-3 ul li.current_page_item a
					{ color:<?php echo esc_attr(get_theme_mod('color_scheme','#e75300') ); ?>;}
					
					.social-icons a:hover, 
					.pagination ul li .current, .pagination ul li a:hover, 
					#commentform input#submit:hover,
					.nivo-controlNav a.active
					{ background-color:<?php echo esc_attr(get_theme_mod('color_scheme','#e75300')); ?>;}
					
					.services-wrap .one_fourth:hover .ReadMore,
					.services-wrap .one_fourth:hover .fa,
					.MoreLink:hover
					{ border-color:<?php echo esc_attr(get_theme_mod('color_scheme','#e75300')); ?>;}
					
			</style>
<?php      
}
         
add_action('wp_head','naturo_lite_custom_css');	

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function naturo_lite_customize_preview_js() {
	wp_enqueue_script( 'naturo_lite_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'naturo_lite_customize_preview_js' );


function naturo_lite_custom_customize_enqueue() {
	wp_enqueue_script( 'naturo-custom-customize', get_template_directory_uri() . '/js/custom.customize.js', array( 'jquery', 'customize-controls' ), false, true );
}
add_action( 'customize_controls_enqueue_scripts', 'naturo_lite_custom_customize_enqueue' );