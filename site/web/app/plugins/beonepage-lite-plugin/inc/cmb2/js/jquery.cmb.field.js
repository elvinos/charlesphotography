/**
 * jquery.cmb.field.js
 *
 * Contain all handlers for CMB2 fields.
 */

( function( $ ) {
	$( document ).ready( function() {
		// Icon for CMB field.
		var	selected_icon,
			icon_field_object;

		// Get selected icon.
		$( '.icon-control' ).each( function() {
			selected_icon = $( this ).parent().find( 'input[id*="_icon"]' ).val();

			if ( selected_icon ) {
				$( this ).parent().find( '.selected-icon i' ).removeClass().addClass( 'fa fa-' + selected_icon );
			} else {
				$( this ).parent().find( '.selected-icon i' ).removeClass().addClass( 'icon-none' );
			}
		} );

		// Trigger events when click "Choose Icon" link.
		$( 'body' ).on( 'click', 'a.add-cmb-icon', function() {
			$( this ).closest( 'div' ).find( '#cmb-icon-list' ).show();

			// Determine when a user has stopped typing in a text field.
			$( this ).closest( 'div' ).find( '#cmb-icon-search' ).typeWatch( {
				wait: 500,
				highlight: false,
				captureLength: 0,
				callback: function() {
					var count = 0,
						val = $( this ).val().replace( ' ', '-' );

					// Icon search filter.
					$( this ).closest( 'div' ).parent().find( 'ul.font-icons li' ).each( function() {
						if ( $( this ).attr( 'id' ).search( new RegExp( val, 'i' ) ) < 0 ) {
							$( this ).hide();
						} else {
							$( this ).show();
							count++;
						}
					} );

					// Show text for icon search results.
					$( this ).parent().find( 'span#icons-search-result' ).show();

					if ( count == 1 ) {
						$( 'span#icons-search-result' ).html( '<strong>' + count + '</strong> ' + admin_vars.s_icon_found );
					} else if ( count > 1 ) {
						$( 'span#icons-search-result' ).html( '<strong>' + count + '</strong> ' + admin_vars.p_icons_found );
					} else {
						$( 'span#icons-search-result' ).html( admin_vars.no_icons_found );
					}
				}
			} );
		} );

		// Trigger events when click "Remove Icon" link.
		$( 'body' ).on( 'click', 'a.remove-cmb-icon', function() {
			$( this ).closest( 'div' ).find( '#cmb-icon-list' ).hide();
		} );

		// Trigger events when click "Choose Icon" and "Remove Icon" links.
		$( 'body' ).on( 'click', 'a.add-cmb-icon, a.remove-cmb-icon', function() {
			icon_field_object = $( this );

			// Add class for selected icon.
			if ( icon_field_object.closest( 'div' ).find( 'input[id*="_icon"]' ).val() ) {
				selected_icon = icon_field_object.closest( 'div' ).find( 'input[id*="_icon"]' ).val();

				$( 'ul.font-icons li i.fa-' + selected_icon ).addClass( 'active' );
			}
		} );

		// Trigger events when select an icon.
		$( 'body' ).on( 'click', 'ul.font-icons li', function() {
			selected_icon = $( this ).attr( 'id' );

			$( this ).closest( 'div' ).parent().find( 'input[id*="_icon"]' ).val( selected_icon );
			$( this ).closest( 'div' ).parent().find( '.selected-icon i' ).removeClass().addClass( 'fa fa-' + selected_icon );
			$( 'ul.font-icons li i' ).removeClass( 'active' );

			$( this ).closest( 'div', '#cmb-icon-list' ).hide();

			$( this ).closest( 'div', '#cmb-icon-list' ).find( '#cmb-icon-search' ).val( '' );
			$( this ).closest( 'div', '#cmb-icon-list' ).find( 'ul.font-icons li' ).show();
			$( this ).closest( 'div', '#cmb-icon-list' ).find( 'span#icons-search-result' ).hide();
		} );

		// Trigger events when click "Remove Icon" link.
		$( 'body' ).on( 'click', 'a.remove-cmb-icon', function() {
			icon_field_object = $( this );

			icon_field_object.closest( 'div' ).find( 'input[id*="_icon"]' ).val( '' );
			icon_field_object.closest( 'div' ).find( '.selected-icon i' ).removeClass().addClass( 'icon-none' );
			icon_field_object.closest( 'div' ).find( 'ul.font-icons li i' ).removeClass( 'active' );
		} );

		// Get title for service box.
		$( '#_beonepage_option_icon_service_box_repeat' ).find( '.cmb-repeatable-grouping' ).each( function() {
			var field_title = $( this ).find( 'textarea[id*="_title"]' ).val();

			if ( field_title ) {
				$( this ).find( '.cmb-group-title span' ).html( field_title );
			}
		} );

		// Get title for contact box.
		$( '#_beonepage_option_contact_box_repeat' ).find( '.cmb-repeatable-grouping' ).each( function() {
			var field_title = $( this ).find( 'input[id*="_label"]' ).val();

			if ( field_title ) {
				$( this ).find( '.cmb-group-title span' ).html( field_title );
			}
		} );
	} );
} )( jQuery );