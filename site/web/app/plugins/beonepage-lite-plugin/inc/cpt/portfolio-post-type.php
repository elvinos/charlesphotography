<?php
/**
 * Register Portfolio Post Type
 *
 * @category BeOnePage Plugin
 * @package CPT
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @link    http://jjgrainger.co.uk
 */

// Create Portfolio custom post type.
$portfolio = new CPT(
	array(
		'post_type_name' => 'portfolio',
		'singular'       => 'Portfolio',
		'plural'         => 'Portfolios',
		'slug'           => 'portfolio'
	),

	array(
		'supports'       => array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'      => 'dashicons-portfolio',
		'set_textdomain' => 'beonepage'
	)
);

// Create Portfolio Tag taxonomy.
$portfolio -> register_taxonomy(
	array(
		'taxonomy_name' => 'portfolio_tag',
		'singular'      => 'Portfolio Tag',
		'plural'        => 'Portfolio Tags',
		'slug'          => 'portfolio-tag'
	)
);