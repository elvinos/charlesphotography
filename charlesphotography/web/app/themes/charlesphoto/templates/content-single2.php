<?php
$category_id = get_the_category();
$category_link = get_category_link($category_id[0]->cat_ID);
?>

  <div id="site-wrapper">

<div class="container-fluid" id="centrebuttons">
  <span class= "button">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php /* loop post navigation */?>

    <a href="<?php echo esc_url($category_link) ?>"> <span class="glyphicon glyphicon-th" id="catBut"></span></a>

  </span>
  <!-- <a class="toggle-nav btn btn-default" id="single-toggle" ><i class="glyphicon glyphicon-info-sign"></i> </a> -->
  <a href="#" class="toggle-nav button-plus" id="single-toggle"></a>
    </div>

          <div id="site-canvas">

  <?php
    $thumbnail_id = get_post_thumbnail_id();
    $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'thumbnail-size', large);
    $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));
?>

<div class="container-fluid" id="main-canvas">

   <div class="imgBox" id="imgBoxid">



<?php c2c_previous_or_loop_post_link('%link', '<img class="lazy" data-original=" ' . $thumbnail_url[0] . ' " id="singleImage">'); ?>


<div class="imgBoxOL leftIB">
<?php c2c_next_or_loop_post_link('%link', '<nav class="mfp-arrow mfp-arrow-left glyphicon glyphicon-menu-left"> </nav>', true); ?>
</div>
<div class="imgBoxOL rightIB">
<?php c2c_previous_or_loop_post_link('%link', '<nav class="mfp-arrow mfp-arrow-right glyphicon glyphicon-menu-right"> </nav>',true); ?>
</div>
</div>
<div class="container-fluid" id="site-menu">
<section class="wrapper">
<ul class="tabs">
<li class="active">Details</li>
<li id="CommentTab">Comments</li>
</ul>

<ul class="tab__content">
<li class="active">
<div class="content__wrapper">
<h1><?php the_title(); ?></h1>
 <?php the_content(); ?>
 <?php echo '<dl class="table-display">';

 echo wp_strip_all_tags( get_the_term_list($thumbnail_id, 'photos_keywords', '<DT>Keywords: </DT><DD>', ', ', '</DD>'));
 echo wp_strip_all_tags( get_the_term_list($thumbnail_id, 'photos_camera', '<DT>Camera: </DT><DD>', ', ', '</DD>'));
 echo wp_strip_all_tags( get_the_term_list($thumbnail_id, 'photos_lens', '<DT>Lens: </DT><DD>', ', ', '</DD>'));
 echo wp_strip_all_tags(get_the_term_list($thumbnail_id, 'photos_city', '<DT>City: </DT><DD>', ', ', '</DD>'));
 echo wp_strip_all_tags(get_the_term_list($thumbnail_id, 'photos_state', '<DT>State: </DT><DD>', ', ', '</DD>'));
 echo wp_strip_all_tags(get_the_term_list($thumbnail_id, 'photos_country', '<DT>Country: </DT><DD>', ', ', '</DD>'));
 echo wp_strip_all_tags(get_the_term_list($thumbnail_id, 'photos_people', '<DT>People: </DT><DD>', ', ', '</DD>'));
 echo '<dl>';?>
 </div>
</li>
<li>
 <div class="content__wrapper" id="commentsbox">
   <section id="comments" class="comments">
<?php comments_template(); ?>
   </section>
 </div>
</li>
</ul>
</section>
</div>

</div><!-- #site-canvas -->


<?php endwhile; else:?>

<div class="page-header">
  <h1>Oh no!</h1>
</div>

<p>No content is appearing in this space</p>

<?php endif; ?>
</div><!-- #site-wrapper> -->
