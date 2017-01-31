<?php
/**
 * Menu Item Custom Fields
 *
 * @link https://github.com/kucrut/wp-menu-item-custom-fields
 *
 * @package BeOnePage
 */

if ( ! class_exists( 'beonepage_Menu_Item_Custom_Fields' ) ) {
	// Menu item custom fields loader.
	class beonepage_Menu_Item_Custom_Fields {
		/**
		* Add filter.
		*
		* @wp_hook action wp_loaded
		*/
		public static function load() {
			add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, '_filter_walker' ), 99 );
		}

		/**
		* Replace default menu editor walker with ours.
		*
		* We don't actually replace the default walker. We're still using it and
		* only injecting some HTMLs.
		*
		* @since   0.1.0
		* @access  private
		* @wp_hook filter wp_edit_nav_menu_walker
		* @param   string $walker Walker class name
		* @return  string Walker class name
		*/
		public static function _filter_walker( $walker ) {
			$walker = 'beonepage_Menu_Item_Custom_Fields_Walker';

			if ( ! class_exists( $walker ) ) {
				require_once 'walker-nav-menu-edit.php';
			}

			return $walker;
		}
	}
	add_action( 'wp_loaded', array( 'beonepage_Menu_Item_Custom_Fields', 'load' ), 9 );
}

/**
 * Add menu item metadata.
 */
class Menu_Item_Custom_Fields_Metadata {
	/**
	 * Holds our custom fields.
	 *
	 * @var    array
	 * @access protected
	 */
	protected static $fields = array();

	/**
	 * Initialize plugin.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin' ), 10, 1 );
		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );

		self::$fields = array(
			array( 'id' => 'menu-icon', 'label' => esc_html__( 'Menu Icon', 'beonepage' ) )
		);
	}

	/**
	 * Save custom field value.
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		foreach ( self::$fields as $_key => $val ) {
			$key = sprintf( 'menu-item-%s', $val['id'] );
			$db_key = sprintf( '_menu_item_%s', str_replace( '-', '_', $val['id'] ) );

			// Sanitize
			if ( !empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				// Do some checks here...
				$value = $_POST[ $key ][ $menu_item_db_id ];
			} else {
				$value = null;
			}

			// Update.
			if ( !is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $db_key, $value );
			} else {
				delete_post_meta( $menu_item_db_id, $db_key );
			}
		}
	}

	/**
	 * Load CSS and JavaScript files for menu item custom fields.
	 *
	 * @param string $hook
	 */
	public static function _admin( $hook ) {
		if ( $hook != 'nav-menus.php' ) {
			return;
		}

		wp_enqueue_style( 'beonepage-admin-font-awesome-style', get_template_directory_uri() . '/layouts/font.awesome.min.css', array(), '4.4.0' );
		wp_enqueue_style( 'beonepage-admin-icon-list-style', plugins_url( 'icon-list.css', __FILE__ ), array(), beonepage_get_version() );
		wp_enqueue_script( 'beonepage-admin-typewatch-script', plugins_url( 'jquery.typewatch.js', __FILE__ ), array( 'jquery' ), '2.2.1', true );
	}

	/**
	 * Print field.
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $id, $item, $depth, $args ) {
		foreach ( self::$fields as $_key => $val ) {
			$key    = sprintf( 'menu-item-%s', $val['id'] );
			$db_key = sprintf( '_menu_item_%s', str_replace( '-', '_', $val['id'] ) );
			$id     = sprintf( 'edit-%s-%s', $key, $item->ID );
			$name   = sprintf( '%s[%s]', $key, $item->ID );
			$value  = get_post_meta( $item->ID, $db_key, true );
			$class  = sprintf( 'field-%s', $val['id'] );
	
			add_thickbox();
?>

		<p class="description description-wide <?php echo esc_attr( $class ) ?>">
			<?php
				printf(
					'<label for="%1$s">%2$s
						<i class="fa fa-%3$s"></i>
						<a href="#TB_inline?width=600&height=480&inlineId=menu-icon-list" title="%4$s" class="thickbox">%5$s</a>
						<input type="hidden" id="%1$s" name="%6$s" value="%3$s" />
						<a href="javascript:void(0);" class="remove-menu-icon">%7$s</a>
					</label>',
					esc_attr( $id ),
					esc_html( $val['label'] ),
					esc_attr( $value ) ? esc_attr( $value ) : 'none',
					esc_html__( 'Select an icon for the menu item', 'beonepage' ),
					esc_html__( 'Choose Icon', 'beonepage' ),
					esc_attr( $name ),
					esc_html__( 'Remove Icon', 'beonepage' )
				)
			?>
		</p>

		<?php $font_icons = beonepage_icon_list(); ?>

		<div id="menu-icon-list" style="display:none;">
			<div class="menu-icon-search">
				<input type="text" id="menu-icon-search" placeholder="<?php esc_html_e( 'Type to search icons', 'beonepage' ); ?>">
				<span id="icons-search-result"></span>
			</div><!-- .icon-search -->

			<ul class="font-icons">
				<?php
					foreach ( $font_icons as $font_icon ) {
						echo '<li id="' . $font_icon . '"><i class="fa fa-' . $font_icon . '"></i></li>';
					}
				?>
			</ul>
		</div><!-- #menu-icon-list -->
<?php
		}
	}

	/**
	 * Add our fields to the screen options toggle.
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		
		$columns = array_merge( $columns, array_column( ( self::$fields ), 'label', 'id' ) );

		return $columns;
	}
}
Menu_Item_Custom_Fields_Metadata::init();

/**
 * Add custom WordPress nav walker class.
 *
 * @link https://github.com/twittem/wp-bootstrap-navwalker
 *
 */
class beonepage_Walker_Nav_Menu extends Walker_Nav_Menu {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	*/
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu clearfix\">\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
 
		$classes = empty( $item->classes ) ? array() : ( array ) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
 
		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = !empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = !empty( $item->target ) ? $item->target : '';
		$atts['rel'] = !empty( $item->xfn ) ? $item->xfn : '';
		$atts['href'] = !empty( $item->url ) ? $item->url : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( !empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';

		/*
		 * Add menu icon.
		 */
		if ( get_post_meta( $item->ID, '_menu_item_menu_icon', true ) != 'none' ) {
			$item_output .= '<i class="fa fa-' . get_post_meta( $item->ID, '_menu_item_menu_icon', true ) . '"></i>' . $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		} else {
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		}

		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Menu Fallback
	 * =============
	 * If this function is assigned to the wp_nav_menu's fallback_cb variable
	 * and a manu has not been assigned to the theme location in the WordPress
	 * menu manager the function with display nothing to a non-logged in user,
	 * and will add a link to the WordPress menu manager if logged in as an admin.
	 *
	 * @param array $args passed from the wp_nav_menu function.
	 *
	 */
	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {
			extract( $args );
			$fb_output = null;

			if ( $container ) {
				$fb_output = '<' . $container;

				if ( $container_id )
					$fb_output .= ' id="' . $container_id . '"';

				if ( $container_class )
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $menu_id ) {
				$fb_output .= ' id="' . $menu_id . '"';
			}

			if ( $menu_class ) {
				$fb_output .= ' class="' . $menu_class . '"';
			}

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">' . esc_html__( 'Add a menu', 'beonepage' ) . '</a></li>';
			$fb_output .= '</ul>';

			if ( $container ) {
				$fb_output .= '</' . $container . '>';
			}

			echo $fb_output;
		}
	}
}