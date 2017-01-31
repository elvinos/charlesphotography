  <?php get_template_part('templates/page', 'header'); ?>

  <div id="wrapper">

         <!-- Sidebar -->
         <div id="sidebar-wrapper">
           <h1 class="featurette-heading"><?php wp_title(''); ?> </h1>
           <p class="lead"><?php echo category_description() ?></p>
         </div>
         <!-- /#sidebar-wrapper -->

         <!-- Page Content -->
         <div id="page-content-wrapper">
             <div class="container-fluid">
                 <div class="row">
                     <div class="col-lg-12">
                     </div>
                         <a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><i class="glyphicon glyphicon-info-sign"></i></a>
                     </div>
                       <ul id="categories" class="clr">

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
                       <li>
                         <div class="Hex">
                         <a class="imageHex" href="<?php the_permalink(); ?>"><img src="<?php echo $thumbnail_url[0]; ?>"></a>
                       </div>
                       </li>

                     <?php endwhile; else:?>

                     <div class="page-header">
                       <h1>Oh no!</h1>
                     </div>

                     <p>No content is appearing in this space</p>

                     <?php endif; ?>


                 </div>
             </div>
         </div>
