<?php
/*
Template Name: Home
*/

?>

<?php get_template_part('templates/page', 'header'); ?>

<div id="loader-wrapper">
  <div id="load-text">
    <h1><?php bloginfo('name') ?></h1>
  </div>
  <div id="cube1"></div>
  <div id="cube2"></div>
  <div class="loader-section section-left"></div>
  <div class="loader-section section-right"></div>

</div>


<div class="container-fluid" id="carousel">


<?php

          $vargs = array(
            'post_type' => 'post',
            'tag'=> 'Carousel'
          );

          $the_query1 = new WP_Query( $vargs );

      ?>

      <div id="carousel-example" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <?php if ( have_posts() ) : while ( $the_query1->have_posts() ) : $the_query1->the_post(); ?>
    <li data-target="#carousel-example" data-slide-to="<?php echo $the_query1->current_post; ?>" class="<?php if( $the_query1->current_post == 0 ):?>active<?php endif; ?>"></li>
    <?php endwhile; endif; ?>
  </ol>
<?php rewind_posts(); ?>
  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <?php if ( have_posts() ) : while ( $the_query1->have_posts() ) : $the_query1->the_post(); ?>

    <div class="item <?php if( $the_query1->current_post == 0 ):?>active<?php endif; ?>">

      <?php
        $thumbnail_id = get_post_thumbnail_id();
        $thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'full', true );
        $thumbnail_meta = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true);
      ?>
      <a href="<?php the_permalink(); ?>"><div style="background:url(<?php echo $thumbnail_url[0]; ?>) center center;
          background-size:cover;" class="slider-size" id="loadimage">
          </div></a>
</div>

<?php endwhile; endif; ?>
  </div>

      <!-- Controls -->
      <a class="left carousel-control" href="javascript:void(0)"
           data-slide="prev" data-target="#carousel-example">
      <span class="glyphicon glyphicon-chevron-left"></span>
      </a>
      <a class="right carousel-control" href="javascript:void(0)"
           data-slide="next" data-target="#carousel-example">
      <span class="glyphicon glyphicon-chevron-right"></span>
      </a>

    </div>
      <span id="arrow" class="glyphicon glyphicon-chevron-left homeArrow"></span> </span><h2 class="featurette-heading homeCollections" id="arrowtext">Browse collections</h2>
      <h2 class="featurette-heading homeheader" id="SiteHomeTitle"> <?php bloginfo('name') ?></h2>
</div>
<div class="container-fluid" id="spacer">

</div>
    <!-- </div> -->
