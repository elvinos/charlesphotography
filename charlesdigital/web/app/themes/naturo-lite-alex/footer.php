<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package SKT Naturolite
 */
?>
<div id="footer-wrapper">
    	<div class="container">
             <div class="cols-4 widget-column-1">   
             <?php if ( '' !== get_theme_mod( 'about_title' ) ){  ?>
             <h5><?php echo esc_attr( get_theme_mod( 'about_title', __('About Us','naturo_lite'))); ?></h5>
             <?php } ?>
             
             <?php if ( '' !== get_theme_mod( 'about_description' ) ){  ?>
			 <?php echo esc_attr( get_theme_mod( 'about_description', __('Sed suscipit mauris nec mauris vulputate, a posuere libero congue. Nam laoreet elit eu erat pulvinar, et efficitur nibh euismod.Nam metus lorem, hendrerit quis ante eget, lobortis elementum neque. Aliquam in ullamcorper quam. Integer euismod ligula in mauris vehic.','naturo_lite')));
			  ?>
              <?php } ?>

              </div>                  
               <div class="cols-4 widget-column-2">
                   <h5><?php esc_attr_e('Recent Posts','naturo_lite'); ?></h5>            	
					<?php $args = array( 'posts_per_page' => 2, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
					$the_query = new WP_Query( $args );
					?>
                    <?php while ( $the_query->have_posts() ) :  $the_query->the_post(); ?>
                        <div class="recent-post">
                         <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                         <a href="<?php the_permalink(); ?>"><h6><?php the_title(); ?></h6></a>  
                         <?php echo wp_kses_post(naturo_lite_short_the_content(apply_filters( 'the_content', get_the_content() ))); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="cols-4 widget-column-3">
                   <h5><?php esc_attr_e('Contact Info','naturo_lite'); ?></h5> 
			    <?php if ( '' !== get_theme_mod( 'contact_add' ) ){  ?>
                 <p><?php echo esc_attr( get_theme_mod( 'contact_add', __('100 King St, Melbourne PIC 4000, Australia','naturo_lite')));
			  ?></p>
			  <?php } ?>
              <div class="phone-no"><strong><?php if ( '' !== get_theme_mod( 'contact_no' ) ){ ?><?php esc_attr_e('Phone:', 'naturo_lite'); } ?></strong> 
			  <?php echo esc_attr( get_theme_mod( 'contact_no', __('+123 456 7890','naturo_lite'))); ?><br  />
           <strong> <?php if ( '' !== get_theme_mod( 'contact_no' ) ){ esc_attr_e('Email:', 'naturo_lite'); } ?></strong> <a href="mailto:<?php echo sanitize_email(get_theme_mod('contact_mail','contact@company.com')); ?>"><?php echo esc_attr(get_theme_mod('contact_mail','contact@company.com')); ?></a></div>
                <div class="clear"></div>                
                <div class="social-icons">
					<?php if ( '' !== get_theme_mod( 'fb_link' ) ) { ?>
                    <a title="facebook" class="fa fa-facebook fa-1x" target="_blank" href="<?php echo esc_url(get_theme_mod('fb_link','#facebook')); ?>"></a> 
                    <?php } ?>
                    
                    <?php if ( '' !== get_theme_mod( 'twitt_link' ) ) { ?>
                    <a title="twitter" class="fa fa-twitter fa-1x" target="_blank" href="<?php echo esc_url(get_theme_mod('twitt_link','#twitter')); ?>"></a>
                    <?php } ?> 
                    
                    <?php if ( '' !== get_theme_mod('gplus_link') ) { ?>
                    <a title="google-plus" class="fa fa-google-plus fa-1x" target="_blank" href="<?php echo esc_url(get_theme_mod('gplus_link','#gplus')); ?>"></a>
                    <?php }?>
                    
                    <?php if ( '' !== get_theme_mod('linked_link') ) { ?> 
                    <a title="linkedin" class="fa fa-linkedin fa-1x" target="_blank" href="<?php echo esc_url(get_theme_mod('linked_link','#linkedin')); ?>"></a>
                    <?php } ?>
                </div>     
                </div><!--end .widget-column-4-->
            <div class="clear"></div>
        </div><!--end .container-->
        
        <div class="copyright-wrapper">
        	<div class="container">
                <div class="design-by"><?php printf( esc_html__( 'Theme %1$s %2$s', 'naturo_lite' ), 'by', '<a href="'.esc_url(SKT_URL).'">SKT Themes</a>' ); ?></div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
<?php wp_footer(); ?>
</body>
</html>