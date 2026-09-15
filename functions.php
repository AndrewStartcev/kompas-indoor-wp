<?php
defined( 'ABSPATH' ) || exit;

$functions_files = glob( get_template_directory() . '/functions/*.php' );

foreach ( $functions_files as $file ) {
	require_once $file;
}
