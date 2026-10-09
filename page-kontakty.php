<?php
/* Template Name: Контакты */
get_header();
$hero = kompas_group( 'glavnyj_ekran' );
?>
<?php if ( $hero ) : ?><section class="hero dark-card hero--contacts"><div class="hero-copy"><span class="eyebrow"><?php echo esc_html( $hero['eyebrow'] ?? '' ); ?></span><h1><?php echo kompas_heading( $hero['title'] ?? '' ); ?></h1><p><?php echo esc_html( $hero['text'] ?? '' ); ?></p><div class="actions"><button class="button" type="button" data-estimate><?php echo esc_html( kompas_cta_label( $hero['button_text'] ?? '' ) ); ?></button></div></div><?php kompas_image( $hero['image'] ?? null, 'full' ); ?></section><?php endif; ?>

<?php $contacts = kompas_group( 'kontakty' ); $global = kompas_group( 'header', 'option' ); ?>
<?php if ( $contacts ) : ?><section class="light-card contacts-grid" id="form"><div><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $contacts['eyebrow'] ?? '' ); ?></span><h2><?php echo esc_html( $contacts['title'] ?? '' ); ?></h2></div><a class="contact-phone" href="<?php echo esc_url( kompas_phone_href( $global['phone'] ?? '' ) ); ?>"><?php echo esc_html( $global['phone'] ?? '' ); ?></a><a class="contact-mail" href="mailto:<?php echo esc_attr( $global['email'] ?? '' ); ?>"><?php echo esc_html( $global['email'] ?? '' ); ?></a><p><?php echo wp_kses_post( $contacts['schedule'] ?? '' ); ?></p></div><div><?php if ( ! empty( $contacts['shortcode'] ) ) { echo do_shortcode( $contacts['shortcode'] ); } ?></div></section><?php endif; ?>

<?php $office = kompas_group( 'ofis' ); ?>
<?php if ( $office ) : ?><section class="dark-card office-grid"><div><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $office['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $office['title'] ?? '' ); ?></h2></div><div class="blue-card address-card"><span class="eyebrow"><?php echo esc_html( $office['map_label'] ?? '' ); ?></span><b><?php echo esc_html( $office['address'] ?? '' ); ?></b></div></div><?php kompas_image( $office['image'] ?? null, 'full' ); ?></section><?php endif; ?>

<?php $details = kompas_group( 'rekvizity' ); ?>
<?php if ( $details ) : ?><section class="light-card" id="details"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $details['eyebrow'] ?? '' ); ?></span><h2><?php echo esc_html( $details['title'] ?? '' ); ?></h2></div><div class="card-grid card-grid--3 details"><?php foreach ( (array) ( $details['items'] ?? array() ) as $item ) : ?><div class="pale-panel"><b><?php echo esc_html( $item['title'] ?? '' ); ?></b><p><?php echo esc_html( $item['text'] ?? '' ); ?></p></div><?php endforeach; ?></div></section><?php endif; ?>

<?php $cta = kompas_group( 'prizyv' ); ?>
<?php if ( $cta ) : ?><section class="cta contacts-cta"><div><h2><?php echo kompas_heading( $cta['title'] ?? '' ); ?></h2><p><?php echo esc_html( $cta['text'] ?? '' ); ?></p><button class="button button--light" type="button" data-estimate><?php echo esc_html( kompas_cta_label( $cta['button_text'] ?? '' ) ); ?></button></div></section><?php endif; ?>
<?php get_footer(); ?>
