<?php
defined( 'ABSPATH' ) || exit;

function kompas_group( $name, $post_id = false ) {
	$value = get_field( $name, $post_id );
	return is_array( $value ) ? $value : array();
}

function kompas_image( $image, $size = 'full', $attributes = array() ) {
	if ( empty( $image ) ) {
		return;
	}

	$id = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;
	if ( ! $id ) {
		return;
	}

	echo wp_get_attachment_image( $id, $size, false, $attributes );
}

function kompas_phone_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

function kompas_link( $link, $class = '' ) {
	if ( empty( $link['url'] ) || empty( $link['title'] ) ) {
		return;
	}
	printf(
		'<a class="%1$s" href="%2$s" target="%3$s">%4$s</a>',
		esc_attr( $class ),
		esc_url( $link['url'] ),
		esc_attr( $link['target'] ?: '_self' ),
		esc_html( $link['title'] )
	);
}

/** Keep legacy ACF buttons compatible with the customer's updated CTA. */
function kompas_cta_label( $label = '' ) {
	$label = trim( wp_strip_all_tags( (string) $label ) );
	return '' === $label || 'Рассчитать проект' === $label ? 'Оставить заявку' : $label;
}
