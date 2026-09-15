<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'acf/settings/save_json', function () {
	return get_template_directory() . '/acf-json';
} );

add_filter( 'acf/settings/load_json', function ( $paths ) {
	$paths[] = get_template_directory() . '/acf-json';
	return array_unique( $paths );
} );
