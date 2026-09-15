<?php
defined( 'ABSPATH' ) || exit;

function kompas_asset_version( $path ) {
	$file = get_theme_file_path( $path );
	return file_exists( $file ) ? (string) filemtime( $file ) : wp_get_theme()->get( 'Version' );
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'kompas-style', get_theme_file_uri( 'assets/css/style.css' ), array(), kompas_asset_version( 'assets/css/style.css' ) );
	wp_enqueue_style( 'kompas-custom', get_theme_file_uri( 'assets/css/custom.css' ), array( 'kompas-style' ), kompas_asset_version( 'assets/css/custom.css' ) );
	wp_enqueue_script( 'kompas-main', get_theme_file_uri( 'assets/js/main.js' ), array(), kompas_asset_version( 'assets/js/main.js' ), true );
	wp_enqueue_script( 'kompas-custom', get_theme_file_uri( 'assets/js/custom.js' ), array( 'kompas-main' ), kompas_asset_version( 'assets/js/custom.js' ), true );
} );
