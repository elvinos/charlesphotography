<div class="container-fluid">

      <nav id="hexNav">
        <div id="menuBtn" class="menuBtnPos menuBtnStyle">
            <svg viewbox="0 0 100 100">
                <polygon points="50 2 7 26 7 74 50 98 93 74 93 26" fill="transparent" stroke-width="2" stroke-dasharray="0,0,300"/>
    </svg>
    <div class="bars"></div>
  </div>
<div class="overlay">
  <div class="grid">
    <div class="container-fluid" id="Gallery-Nav">
      <h2 class="featurette-heading" id="menuTitle"> Collections </h2>
      <a href="<?php echo home_url() ?>"  ><span id="Home-Button" class="glyphicon glyphicon-home"></span> </a>

    </div>

      <?php
        $args = array(
        'orderby' => 'name',
        'order' => 'ASC'
        );
        $categories = get_categories($args);
        foreach($categories as $category) {
          $term_id = $category->term_id;
          $image   = category_image_src( array('term_id'=>$term_id, 'size' => 'medium'), $icon = false );
          $name = $category->name;
          $category_link = get_category_link( $term_id );
        ?>

          <div class="block full medium-half large-one-third type-image" data-order="" data-order-medium="" data-order-large="">
               <div class="block-inner ">
                 <a href="<?php echo $category_link ?>" class="block-content">
                   <div class="content-rollover no-caption">

                     <div class="background">

                       <img src="<?php echo $image; ?>">
                     </div>

                     <div class="caption">
                       <span><?php echo $name?></span>
                     </div>
                   </div>
                 </a>
                 </div>
               </div>


        <?php } ?>

  </div>


  </div>
  </nav>
</div>
