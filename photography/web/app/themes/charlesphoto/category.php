<?php get_template_part('templates/page', 'header'); ?>
<?php wp_reset_postdata();?>

<?php
$queried_category = get_term( get_query_var('cat'), 'category' );
$category_id = get_cat_ID( $queried_category->name );
$category = get_the_category();
$link = get_category_link($category_id);

// echo $queried_category->term_id; // The category ID
// echo $queried_category->slug; // The category slug
// echo $link;
// echo $queried_category->name;
// echo $category_id; // The category name
// echo $queried_category->description; // The category description
?>


<div id="wrapper">

       <!-- Sidebar -->
       <div id="sidebar-wrapper">
         <h1 class="featurette-heading"><?php single_cat_title('', true); ?> </h1>
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

        <?php if (is_category()) : $the_query = new WP_Query('showposts=-1&orderby=post_date&order=desc&cat='.get_query_var('cat'));
                else : $the_query = new WP_Query('showposts=-1&orderby=post_date&order=desc');
              endif; ?>

                <?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
<?php
$thumbnail_id = get_post_thumbnail_id();
$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'thumbnail-size', false);

$thumb_attr = array(
  'src' => '',
  'data-original' => $thumbnail_url[0],
  'class' => 'lazy',
  'alt' => $wp_postmeta->_wp_attachment_image_alt,
  'title' => $attachment->post_title,
); ?>

<?php
$thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
  $post_slug=$post->post_name;
?>

 <li>
<!-- ADD src to remove square-->
      <div class="Hex">
      <a class="imageHex" href="<?php echo $link; echo $post_slug?>">
        <?php printf('<img src="" class="lazy" data-original="%s"/>', esc_url($thumbnail_src[0])); ?></a>
    </div>
    </li>
 <?php endwhile; endif; ?>





           </div>
       </div>
       <!-- /#page-content-wrapper -->

   </div>
   <!-- /#wrapper -->
