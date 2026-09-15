<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	register_post_type( 'services', array(
		'labels' => array(
			'name'          => 'Услуги',
			'singular_name' => 'Услуга',
			'add_new_item'  => 'Добавить услугу',
			'edit_item'     => 'Редактировать услугу',
		),
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'uslugi', 'with_front' => false ),
		'menu_icon'    => 'dashicons-megaphone',
		'supports'     => array( 'title', 'page-attributes' ),
	) );
} );

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title' => 'Настройки сайта',
		'menu_title' => 'Настройки сайта',
		'menu_slug'  => 'site-settings',
		'icon_url'   => 'dashicons-admin-settings',
		'position'   => 2,
		'redirect'   => false,
	) );
} );
