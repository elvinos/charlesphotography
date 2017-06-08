<?php
// $category_id = get_the_category();
// $category_link = get_category_link($category_id[0]->cat_ID);
global $post;
$post_slug=$post->post_name;
$post_id=$post->ID;
$uri = $_SERVER['REQUEST_URI'];
// echo get_permalink($post->ID);
// echo $uri;
$newuri = str_replace( $post_slug , "", $uri );
$newuri = str_replace( '/' , "", $newuri );
$catIDObj= get_category_by_slug( $newuri );
$catID = $catIDObj->term_id;
// echo $newuri;
// echo $catID;
$category_link = get_category_link($catID);
// echo $category_link;
// echo $post_id;
?>

  <div id="site-wrapper">

    <div class="container-fluid" id="centrebuttons">
      <!-- <a class="toggle-nav btn btn-default" id="single-toggle" ><i class="glyphicon glyphicon-info-sign"></i> </a> -->
      <m-button href="<?php echo esc_url($category_link) ?>" id="single-toggle" shape="round" ripple role="primary" size="big" icon="menu" class="selected">
         <icon>
           <line></line>
           <line></line>
           <line></line>
         </icon>
         <ripples></ripples>
       </m-button>
        </div>


    <?php
    // $categories = get_the_category();
    // $category_id = $categories[0]->cat_ID;
       $Cargs = array(
                'order'          => 'ASC',
                'cat'           => $catID,
                'posts_per_page' => -1
        );
        $queryCat = new WP_Query( $Cargs );

        $post_ids = wp_list_pluck( $queryCat->posts, 'ID' );
        // var_dump($post_ids);
        $key = array_search($post_id, $post_ids); // $key = 2;
        $output1 = array_slice($post_ids, $key);
        $output2 = array_slice($post_ids, 0, $key);
        $result = array_merge($output1, $output2);

        $Cargs2 = array(
                'post__in' => $result,
                'orderby' => 'post__in',
                 'cat'           => $catID,
                 'posts_per_page' => -1
         );
         $queryCat2 = new WP_Query( $Cargs2 );
        // var_dump($result);
        if ( $queryCat2->have_posts() ) : while ( $queryCat2->have_posts() ) : $queryCat2->the_post();
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
