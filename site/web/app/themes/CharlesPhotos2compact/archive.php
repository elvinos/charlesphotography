<?php get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<div id="wrapper">

       <!-- Sidebar -->
       <div id="sidebar-wrapper">
         <h1 class="featurette-heading"><?php single_cat_title( '', true ); ?> </h1>
         <p class="lead"><?php echo category_description() ?></p>
       </div>
       <!-- /#sidebar-wrapper -->

       <!-- Page Content -->
       <div id="page-content-wrapper">
         <div class="container-fluid">
               <div class="row">
                   <div class="col-lg-12">
                   </div>
                       <!-- <a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><i class="glyphicon glyphicon-info-sign"></i></a> -->
                       <m-button href="#menu-toggle" id="menu-toggle" shape="round" ripple role="primary" size="big" icon="menu" class="selected">
                          <icon>
                            <line></line>
                            <line></line>
                            <line></line>
                          </icon>
                          <ripples></ripples>
                        </m-button>
                   </div>

                   <ul id="categories" class="clr">

            <?php if( is_category() ) :
$the_query = new WP_Query('showposts=-1&orderby=post_date&order=desc&cat='.get_query_var('cat'));
else : $the_query = new WP_Query('showposts=-1&orderby=post_date&order=desc');
endif; ?>
<?php if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post(); ?>
<?php
$thumbnail_id = get_post_thumbnail_id();
$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'thumbnail-size', false);

$thumb_attr = array(
  'src' => '',
  'data-original'   => $thumbnail_url[0],
  'class' => "lazy",
  'alt'   => $wp_postmeta->_wp_attachment_image_alt,
  'title' =>  $attachment->post_title
); ?>

<?php
$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "medium" );?>

 <li>
<!-- ADD src to remove square-->
      <div class="Hex">
      <a class="imageHex" href="<?php the_permalink(); ?>"><?php printf( '<img src="" class="lazy" data-original="%s"/>', esc_url( $thumbnail_src[0] ) ); ?></a>
    </div>
    </li>
 <?php endwhile; endif; ?>





           </div>
       </div>
       <!-- /#page-content-wrapper -->

   </div>
   <!-- /#wrapper -->
