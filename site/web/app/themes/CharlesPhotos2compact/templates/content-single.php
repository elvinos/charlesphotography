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

     <div class="imgBox2">
          		<div class="image">
          			<img class="lazy" src="<?php echo $thumbnail_url[0]; ?>" id="singleImage">
                <span class="Infobut glyphicon glyphicon-info-sign" aria-hidden="true"></span>
          		</div>
          		<div class="info">
          			<h3><?php the_title(); ?></h3>
                            <?php  the_content(); ?>
                            <?php   echo '<dl class="table-display">';
                            echo wp_strip_all_tags( get_the_term_list($thumbnail_id, 'photos_camera', '<DT>Camera: </DT><DD>', ', ', '</DD>'));
                            echo wp_strip_all_tags( get_the_term_list($thumbnail_id, 'photos_lens', '<DT>Lens: </DT><DD>', ', ', '</DD>'));
                            echo wp_strip_all_tags(get_the_term_list($thumbnail_id, 'photos_city', '<DT>City: </DT><DD>', ', ', '</DD>'));
                            echo wp_strip_all_tags(get_the_term_list($thumbnail_id, 'photos_state', '<DT>State: </DT><DD>', ', ', '</DD>'));
                            echo wp_strip_all_tags(get_the_term_list($thumbnail_id, 'photos_country', '<DT>Country: </DT><DD>', ', ', '</DD>'));
                            echo '<dl>'; ?>
          		</div>
          	</div>


<?php endwhile;?>
<?php endif; ?>
</div><!-- #site-wrapper> -->
