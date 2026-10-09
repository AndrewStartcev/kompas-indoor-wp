<?php
defined( 'ABSPATH' ) || exit;

/**
 * Add a dynamic submenu for the main services catalog. Editors continue to
 * control all other menu entries in Appearance > Menus. No duplicate top-level
 * "Услуги" is generated when that page is already present in the menu.
 */
function kompas_navigation_item( $id, $title, $url, $parent_id = 0, $current = false ) {
	return (object) array(
		'ID'                    => $id,
		'db_id'                 => 0,
		'menu_item_parent'      => (string) $parent_id,
		'object_id'             => 0,
		'object'                => 'custom',
		'type'                  => 'custom',
		'type_label'            => 'Произвольная ссылка',
		'title'                 => $title,
		'url'                   => $url,
		'target'                => '',
		'attr_title'            => '',
		'description'           => '',
		'xfn'                   => '',
		'classes'               => array_filter( array( 'menu-item', 'menu-item-type-custom', 'menu-item-object-custom', $current ? 'current-menu-item' : '' ) ),
		'current'               => $current,
		'current_item_parent'   => false,
		'current_item_ancestor' => false,
		'menu_order'            => 999,
	);
}

add_filter( 'wp_nav_menu_objects', function ( $items, $args ) {
	if ( 'main' !== ( $args->theme_location ?? '' ) ) {
		return $items;
	}

	$catalog_page = get_page_by_path( 'uslugi', OBJECT, 'page' );
	$catalog_url  = $catalog_page ? get_permalink( $catalog_page ) : home_url( '/uslugi/' );
	$catalog_path = untrailingslashit( wp_parse_url( $catalog_url, PHP_URL_PATH ) ?: '/' );
	$parent_id    = 0;

	foreach ( $items as $item ) {
		if ( (int) ( $item->menu_item_parent ?? 0 ) ) {
			continue;
		}

		$item_path = ! empty( $item->url ) ? wp_parse_url( $item->url, PHP_URL_PATH ) : '';
		$is_catalog = ( $catalog_page && 'page' === ( $item->object ?? '' ) && (int) $item->object_id === (int) $catalog_page->ID )
			|| ( is_string( $item_path ) && untrailingslashit( $item_path ) === $catalog_path )
			|| in_array( trim( wp_strip_all_tags( $item->title ?? '' ) ), array( 'Услуги', 'Наши услуги' ), true );

		if ( $is_catalog ) {
			$parent_id = (int) $item->ID;
			if ( is_singular( 'services' ) ) {
				$item->classes[] = 'current-menu-ancestor';
				$item->current_item_ancestor = true;
			}
			break;
		}
	}

	if ( ! $parent_id ) {
		$parent_id = -9000;
		$parent = kompas_navigation_item( $parent_id, 'Услуги', $catalog_url, 0, is_page( 'uslugi' ) );
		if ( is_singular( 'services' ) ) {
			$parent->classes[] = 'current-menu-ancestor';
			$parent->current_item_ancestor = true;
		}
		$items[] = $parent;
	}

	$services = get_posts( array(
		'post_type'           => 'services',
		'post_status'         => 'publish',
		'posts_per_page'      => -1,
		'orderby'             => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'ignore_sticky_posts' => true,
		'suppress_filters'    => false,
	) );

	$existing_service_ids = array();
	foreach ( $items as $item ) {
		if ( 'services' === ( $item->object ?? '' ) && ! empty( $item->object_id ) ) {
			$existing_service_ids[] = (int) $item->object_id;
		}
	}

	foreach ( $services as $service ) {
		if ( in_array( (int) $service->ID, $existing_service_ids, true ) ) {
			continue; // Manually added service links take precedence.
		}
		$items[] = kompas_navigation_item(
			-100000 - (int) $service->ID,
			get_the_title( $service ),
			get_permalink( $service ),
			$parent_id,
			is_singular( 'services' ) && (int) get_queried_object_id() === (int) $service->ID
		);
	}

	// The dropdown should be discoverable by the standard WP menu walker.
	foreach ( $items as $item ) {
		if ( (int) ( $item->ID ?? 0 ) === $parent_id ) {
			$item->classes = (array) ( $item->classes ?? array() );
			$item->classes[] = 'menu-item-has-children';
			$item->classes[] = 'nav__services';
			$item->classes = array_unique( $item->classes );
			break;
		}
	}

	return $items;
}, 20, 2 );

/** Keep a newly deployed catalog editable in ACF without altering other pages. */
add_action( 'admin_init', function () {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$page = get_page_by_path( 'uslugi', OBJECT, 'page' );
	if ( ! $page ) {
		return; // Normal initial import creates the page.
	}
	$assigned = get_post_meta( $page->ID, '_wp_page_template', true );
	if ( ! $assigned || 'default' === $assigned ) {
		update_post_meta( $page->ID, '_wp_page_template', 'page-uslugi.php' );
	}
} );
