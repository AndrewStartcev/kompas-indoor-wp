<?php get_header(); ?>
<?php $hero = kompas_group( 'glavnyj_ekran' ); ?>
<?php if ( $hero ) : ?>
<section class="hero dark-card hero--home">
	<div class="hero-copy">
		<h1><?php echo kompas_heading( $hero['title'] ?? '' ); ?></h1>
		<p><?php echo wp_kses_post( $hero['text'] ?? '' ); ?></p>
		<div class="actions"><button class="button" type="button" data-estimate><?php echo esc_html( kompas_cta_label( $hero['button_text'] ?? '' ) ); ?></button><?php kompas_link( $hero['second_button'] ?? array(), 'button button--light' ); ?></div>
		<div class="stats"><?php foreach ( (array) ( $hero['stats'] ?? array() ) as $item ) : ?><div><b><?php echo esc_html( $item['value'] ?? '' ); ?></b><span><?php echo esc_html( $item['caption'] ?? '' ); ?></span></div><?php endforeach; ?></div>
	</div>
	<?php kompas_image( $hero['image'] ?? null, 'full' ); ?>
</section>
<?php endif; ?>

<?php $services = kompas_group( 'uslugi' ); ?>
<?php if ( $services ) : ?>
<section id="services"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $services['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $services['title'] ?? '' ); ?></h2><p><?php echo wp_kses_post( $services['text'] ?? '' ); ?></p></div>
	<div class="card-grid card-grid--3">
	<?php foreach ( (array) ( $services['items'] ?? array() ) as $item ) : ?>
		<a class="service-card light-card" href="<?php echo esc_url( $item['link']['url'] ?? '#' ); ?>"><div class="service-card__top"><span class="service-card__meta"><?php kompas_image( $item['image'] ?? null, 'full', array( 'alt' => '' ) ); ?><span><?php echo esc_html( $item['number'] ?? '' ); ?></span></span><span class="arrow">↗</span></div><h3><?php echo kompas_heading( $item['title'] ?? '' ); ?></h3><p><?php echo esc_html( $item['text'] ?? '' ); ?></p></a>
	<?php endforeach; ?>
	</div>
	<div class="services-home__all"><a href="<?php echo esc_url( home_url( '/uslugi/' ) ); ?>">Все услуги <span aria-hidden="true">→</span></a></div>
</section>
<?php endif; ?>

<?php $advantages = kompas_group( 'preimushhestva' ); ?>
<?php if ( $advantages ) : $geo_items = (array) ( $advantages['items'] ?? array() ); ?>
<section class="light-card geography"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $advantages['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $advantages['title'] ?? '' ); ?></h2><p><?php echo esc_html( $advantages['text'] ?? '' ); ?></p></div><div class="geography-grid"><?php foreach ( $geo_items as $index => $item ) : ?><div class="<?php echo $index ? 'dark-panel' : 'pale-panel'; ?>"><?php if ( $index ) : ?><span class="eyebrow"><?php echo esc_html( $item['title'] ?? '' ); ?></span><?php endif; ?><b><?php echo esc_html( $item['number'] ?? '' ); ?></b><p><?php echo wp_kses_post( $item['text'] ?? '' ); ?></p></div><?php endforeach; ?></div></section>
<?php endif; ?>

<?php $gallery = kompas_group( 'fototchet' ); ?>
<?php if ( $gallery ) : ?>
<section class="dark-card gallery" id="cases"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $gallery['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $gallery['title'] ?? '' ); ?></h2><p><?php echo esc_html( $gallery['text'] ?? '' ); ?></p></div><div class="gallery-grid gallery-grid--reports">
<?php $report_examples = array_filter( (array) ( $gallery['examples'] ?? array() ), static function ( $example ) { return ! empty( $example['image'] ); } ); ?>
<?php if ( $report_examples ) : ?>
	<?php foreach ( $report_examples as $example ) : ?><figure class="gallery-report"><?php kompas_image( $example['image'], 'large', array( 'loading' => 'lazy' ) ); ?><figcaption><?php echo esc_html( $example['title'] ?? '' ); ?></figcaption></figure><?php endforeach; ?>
<?php else : foreach ( (array) ( $gallery['images'] ?? array() ) as $image ) : ?><figure class="gallery-report"><?php kompas_image( $image, 'large', array( 'loading' => 'lazy' ) ); ?></figure><?php endforeach; endif; ?>
</div></section>
<?php endif; ?>

<?php $trust = kompas_group( 'doverie' ); ?>
<?php if ( $trust ) : ?>
<section class="light-card trust"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $trust['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $trust['title'] ?? '' ); ?></h2><p><?php echo esc_html( $trust['text'] ?? '' ); ?></p></div><div class="logos"><?php foreach ( (array) ( $trust['logos'] ?? array() ) as $item ) { kompas_image( $item['image'] ?? null, 'full' ); } ?></div></section>
<?php endif; ?>

<?php get_template_part( 'template-parts/common/reviews' ); ?>

<?php $cta = kompas_group( 'forma' ); ?>
<?php if ( $cta ) : ?>
<section class="cta <?php echo ! empty( $cta['shortcode'] ) ? 'cta--form' : 'home-cta--simple'; ?>"><div class="home-cta__copy"><h2><?php echo kompas_heading( $cta['title'] ?? '' ); ?></h2><p><?php echo esc_html( $cta['text'] ?? '' ); ?></p><div class="home-cta__dialog"><?php if ( ! empty( $cta['manager_avatar'] ) ) : ?><div class="home-cta__avatar"><?php kompas_image( $cta['manager_avatar'], 'thumbnail', array( 'loading' => 'lazy' ) ); ?></div><?php endif; ?><div class="home-cta__bubble"><span><?php echo esc_html( ( $cta['manager_name'] ?? '' ) ?: 'Татьяна Вовк' ); ?></span><p><?php echo esc_html( ( $cta['manager_message'] ?? '' ) ?: 'Расскажите о задаче — поможем выбрать формат и быстро подготовим предложение.' ); ?></p></div></div></div><div class="home-cta__form"><?php if ( ! empty( $cta['shortcode'] ) ) : echo do_shortcode( $cta['shortcode'] ); else : ?><button class="button button--light" type="button" data-estimate>Оставить заявку →</button><?php endif; ?></div></section>
<?php endif; ?>
<?php get_footer(); ?>
