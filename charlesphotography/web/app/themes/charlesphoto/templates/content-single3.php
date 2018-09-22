<?php
$category_id = get_the_category();
$category_link = get_category_link($category_id[0]->cat_ID);
?>

  <div id="site-wrapper">

    <div class="container-fluid" id="centrebuttons">
      <!-- <a class="toggle-nav btn btn-default" id="single-toggle" ><i class="glyphicon glyphicon-info-sign"></i> </a> -->
      <a href="<?php echo esc_url($category_link) ?>" class="toggle-nav button-plus" id="single-toggle"></a>
        </div>


    <?php
    $categories = get_the_category();
    $category_id = $categories[0]->cat_ID;
       $Cargs = array(
                'order'          => 'ASC',
                'cat'           => $category_id,
                'posts_per_page' => -1
        );
        $queryCat = new WP_Query( $Cargs );
        if ( $queryCat->have_posts() ) : while ( $queryCat->have_posts() ) : $queryCat->the_post();
        // if ( have_posts() ) : while (have_posts() ) : the_post();
         $thumbnail_id = get_post_thumbnail_id();
         $thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'large', true );
     ?>

     <div class="imgBox" id="imgBoxid">

          <img class="lazy" src="<?php echo $thumbnail_url[0]; ?>" id="singleImage">
     </div>
     <div class="info">
       <h3>Image title</h3>
       <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nesciunt, aut. Ratione libero cupiditate distinctio corporis accusantium tempore quod a dignissimos excepturi ut magnam aut, quaerat itaque esse vel temporibus. Eos.</p>
 </div>


<?php endwhile;?>
<?php endif; ?>
</div><!-- #site-wrapper> -->
