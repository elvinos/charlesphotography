<?php
/*
Template Name: New Home
*/

?>
<div id="loader-wrapper">
  <div id="load-text">
     <div class="containerNH u-inline-block">
        <div class="row">
          <div class="mainNH item">

            <span class="loaderText"><?php bloginfo('name') ?></span>

          </div>
        </div>
      </div>
  </div>
  <div id="cube1"></div>
  <div id="cube2"></div>
  <div class="loader-section section-left"></div>
  <div class="loader-section section-right"></div>
</div>

<div class="wrapperNH" id="page">

<section class="section landing home slider fullscreen" data-slider-animation-speed="2000" data-slider-speed="">


  <div class="slides fade kenburns">

    <?php

          $vargs = array(
            'post_type' => 'post',
            'tag'=> 'Carousel'
          );

          $the_query1 = new WP_Query( $vargs );

      ?>

      <?php if ( have_posts() ) : while ( $the_query1->have_posts() ) : $the_query1->the_post(); ?>
        <?php
  $thumbnail_id = get_post_thumbnail_id();
  $thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'full', true );
  $thumbnail_meta = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true);
?>
    <div class="slide">

      <div class="background fill image overlay-dark" data-url="<?php echo $thumbnail_url[0]; ?>"></div>

      <!-- <div class="border"></div> -->
      <div class="containerNH vertical-center u-inline-block">
        <div class="row">
          <div class="mainNH item">


            <span class="text"><?php bloginfo('name') ?></span>

            <span class="explore">Explore</span>


          </div>
        </div>
      </div>
    </div>
  <?php endwhile; endif; ?>
  </div>


</section>


</div>
