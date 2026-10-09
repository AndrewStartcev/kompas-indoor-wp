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
		'<a class="%1$s" href="%2$s" target="%3$s"%5$s>%4$s</a>',
		esc_attr( $class ),
		esc_url( $link['url'] ),
		esc_attr( ( $link['target'] ?? '' ) ?: '_self' ),
		esc_html( $link['title'] ),
		'_blank' === ( $link['target'] ?? '' ) ? ' rel="noopener noreferrer"' : ''
	);
}

/** Keep legacy ACF buttons compatible with the customer's updated CTA. */
function kompas_cta_label( $label = '' ) {
	$label = trim( wp_strip_all_tags( (string) $label ) );
	return '' === $label || 'Рассчитать проект' === $label ? 'Оставить заявку' : $label;
}


/**
 * ACF WYSIWYG titles may be wrapped in paragraphs by wpautop(). Block-level
 * <p> inside <h1>-<h6> is invalid HTML and can create unexpected margins.
 * Keep safe inline emphasis and intentional <br> line breaks only.
 */
function kompas_heading( $html ) {
	$html = trim( (string) $html );
	$html = preg_replace( '~</p>\\s*<p(?:\\s[^>]*)?>~i', '<br>', $html );
	$html = preg_replace( '~</?p(?:\\s[^>]*)?>~i', '', $html );
	return wp_kses( $html, array(
		'br'     => array(),
		'strong' => array(),
		'em'     => array(),
		'span'   => array( 'class' => true ),
	) );
}
