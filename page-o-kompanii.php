<?php
/* Template Name: О компании */
get_header();
$hero = kompas_group( 'glavnyj_ekran' );
?>
<?php if ( $hero ) : ?><section class="hero dark-card hero--about"><div class="hero-copy"><span class="eyebrow"><?php echo esc_html( $hero['eyebrow'] ?? '' ); ?></span><h1><?php echo wp_kses_post( $hero['title'] ?? '' ); ?></h1><p><?php echo esc_html( $hero['text'] ?? '' ); ?></p><div class="stats"><?php foreach ( (array) ( $hero['stats'] ?? array() ) as $item ) : ?><div><b><?php echo esc_html( $item['value'] ?? '' ); ?></b><span><?php echo esc_html( $item['caption'] ?? '' ); ?></span></div><?php endforeach; ?></div></div><?php kompas_image( $hero['image'] ?? null, 'full' ); ?></section><?php endif; ?>

<?php $model = kompas_group( 'model_raboty' ); ?>
<?php if ( $model ) : ?><section class="light-card about-model-section"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $model['eyebrow'] ?? '' ); ?></span><h2><?php echo esc_html( $model['title'] ?? '' ); ?></h2><p><?php echo esc_html( $model['text'] ?? '' ); ?></p></div><div class="formats model"><?php foreach ( (array) ( $model['items'] ?? array() ) as $item ) : ?><div class="<?php echo ! empty( $item['dark'] ) ? 'dark-panel' : 'pale-panel'; ?>"><span class="eyebrow"><?php echo esc_html( $item['number'] ?? '' ); ?></span><b><?php echo esc_html( $item['title'] ?? '' ); ?></b><p><?php echo esc_html( $item['text'] ?? '' ); ?></p></div><?php endforeach; ?></div></section><?php endif; ?>

<?php $history = kompas_group( 'istoriya' ); ?>
<?php if ( $history ) : ?><section class="dark-card history"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $history['eyebrow'] ?? '' ); ?></span><h2><?php echo wp_kses_post( $history['title'] ?? '' ); ?></h2></div><div class="timeline"><?php foreach ( (array) ( $history['items'] ?? array() ) as $item ) : ?><div class="<?php echo ! empty( $item['active'] ) ? 'active' : ''; ?>"><b><?php echo esc_html( $item['year'] ?? '' ); ?></b><span><?php echo esc_html( $item['text'] ?? '' ); ?></span></div><?php endforeach; ?></div></section><?php endif; ?>

<?php $agencies = kompas_group( 'dlya_agentstv' ); ?>
<?php if ( $agencies ) : ?><section class="light-card agency-section"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $agencies['eyebrow'] ?? '' ); ?></span><h2><?php echo wp_kses_post( $agencies['title'] ?? '' ); ?></h2><p><?php echo esc_html( $agencies['text'] ?? '' ); ?></p></div><div class="formats"><?php foreach ( (array) ( $agencies['items'] ?? array() ) as $item ) : ?><div class="pale-panel"><b><?php echo wp_kses_post( $item['title'] ?? '' ); ?></b></div><?php endforeach; ?></div></section><?php endif; ?>

<?php $principles = kompas_group( 'principy' ); ?>
<?php if ( $principles ) : ?><section class="dark-card principles"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $principles['eyebrow'] ?? '' ); ?></span><h2><?php echo esc_html( $principles['title'] ?? '' ); ?></h2></div><div class="card-grid card-grid--3"><?php foreach ( (array) ( $principles['items'] ?? array() ) as $item ) : ?><div class="blue-card"><span class="eyebrow"><?php echo esc_html( $item['number'] ?? '' ); ?></span><h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3><p><?php echo esc_html( $item['text'] ?? '' ); ?></p></div><?php endforeach; ?></div></section><?php endif; ?>

<?php $details = kompas_group( 'rekvizity' ); ?>
<?php if ( $details ) : ?><section class="light-card requisites"><div><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $details['eyebrow'] ?? '' ); ?></span><h2><?php echo wp_kses_post( $details['title'] ?? '' ); ?></h2><p><?php echo esc_html( $details['text'] ?? '' ); ?></p></div><div><?php echo wp_kses_post( $details['details'] ?? '' ); ?></div></div><?php kompas_image( $details['image'] ?? null, 'full' ); ?></section><?php endif; ?>
<?php get_footer(); ?>
