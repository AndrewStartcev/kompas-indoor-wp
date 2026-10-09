<?php
get_header();
$hero = kompas_group( 'glavnyj_ekran' );
$service_slug = get_post_field( 'post_name', get_queried_object_id() );
?>
<nav class="breadcrumbs" aria-label="Хлебные крошки"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span aria-hidden="true">→</span><a href="<?php echo esc_url( home_url( '/uslugi/' ) ); ?>">Услуги</a><span aria-hidden="true">→</span><span aria-current="page"><?php the_title(); ?></span></nav>
<?php if ( $hero ) : ?><section class="hero dark-card hero--service"><div class="hero-copy"><span class="eyebrow"><?php echo esc_html( ( $hero['eyebrow'] ?? '' ) ?: get_the_title() ); ?></span><h1><?php echo kompas_heading( ( $hero['title'] ?? '' ) ?: get_the_title() ); ?></h1><p><?php echo esc_html( $hero['text'] ?? '' ); ?></p><div class="actions"><button class="button" type="button" data-estimate><?php echo esc_html( kompas_cta_label( $hero['button_text'] ?? '' ) ); ?></button><?php if ( '#process' !== ( $hero['second_button']['url'] ?? '' ) ) { kompas_link( $hero['second_button'] ?? array(), 'button button--light' ); } ?></div><div class="stats"><?php foreach ( (array) ( $hero['stats'] ?? array() ) as $item ) : ?><div><b><?php echo esc_html( $item['value'] ?? '' ); ?></b><span><?php echo esc_html( $item['caption'] ?? '' ); ?></span></div><?php endforeach; ?></div></div><?php kompas_image( $hero['image'] ?? null, 'full' ); ?></section><?php endif; ?>

<?php $steps = kompas_group( 'etapy' ); ?>
<?php if ( $steps && 'raskleyka-obyavleniy' !== $service_slug ) : ?><section class="steps-section"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $steps['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $steps['title'] ?? '' ); ?></h2><p><?php echo esc_html( $steps['text'] ?? '' ); ?></p></div><div class="steps"><?php foreach ( (array) ( $steps['items'] ?? array() ) as $item ) : ?><div class="pale-panel"><span class="eyebrow"><?php echo esc_html( $item['number'] ?? '' ); ?></span><b><?php echo esc_html( $item['title'] ?? '' ); ?></b></div><?php endforeach; ?></div></section><?php endif; ?>

<?php $formats = kompas_group( 'formaty' ); ?>
<?php if ( $formats && ! in_array( $service_slug, array( 'raskleyka-obyavleniy', 'rasprostranenie-po-pochtovym-yashchikam' ), true ) ) : ?><section class="light-card formats-section"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $formats['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $formats['title'] ?? '' ); ?></h2><p><?php echo esc_html( $formats['text'] ?? '' ); ?></p></div><div class="formats"><?php foreach ( (array) ( $formats['items'] ?? array() ) as $index => $item ) : ?><div class="<?php echo $index ? 'pale-panel' : 'dark-panel'; ?>"><b><?php echo esc_html( $item['title'] ?? '' ); ?></b><p><?php echo esc_html( $item['text'] ?? '' ); ?></p></div><?php endforeach; ?></div></section><?php endif; ?>

<?php $places = kompas_group( 'mesta' ); ?>
<?php if ( $places ) : ?><section class="light-card places-section"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $places['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $places['title'] ?? '' ); ?></h2><p><?php echo esc_html( $places['text'] ?? '' ); ?></p></div><?php if ( 'rasprostranenie-po-pochtovym-yashchikam' === $service_slug ) : $mailbox_control = kompas_group( 'kontrol' ); $mailbox_text = trim( (string) ( $mailbox_control['text'] ?? '' ) ); $mailbox_sentence = preg_split( '/(?<=[.!?])\\s+/u', $mailbox_text, 2 ); if ( ! empty( $mailbox_sentence[0] ) ) : ?><p class="places-section__summary"><?php echo esc_html( $mailbox_sentence[0] ); ?></p><?php endif; endif; ?><div class="places"><?php foreach ( (array) ( $places['items'] ?? array() ) as $item ) : if ( 'raskleyka-obyavleniy' === $service_slug && 'Конкретный район' === ( $item['title'] ?? '' ) ) { continue; } ?><article class="<?php echo empty( $item['image'] ) ? 'places__item--no-image' : ''; ?>"><?php kompas_image( $item['image'] ?? null, 'full' ); ?><div><h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3><p><?php echo esc_html( $item['text'] ?? '' ); ?></p></div></article><?php endforeach; ?></div><div class="consult"><div><h3><?php echo esc_html( $places['consult_title'] ?? '' ); ?></h3><p><?php echo esc_html( $places['consult_text'] ?? '' ); ?></p></div><?php kompas_link( $places['consult_link'] ?? array(), 'button button--light' ); ?></div><?php if ( 'promoaktsii' === $service_slug ) : $promo_control = kompas_group( 'kontrol' ); if ( ! empty( $promo_control['text'] ) ) : ?><div class="places-section__summary"><h3>Подбор и контроль промоутеров</h3><p><?php echo esc_html( $promo_control['text'] ); ?></p></div><?php endif; endif; ?></section><?php endif; ?>

<?php $control = kompas_group( 'kontrol' ); ?>
<?php if ( $control && 'raskleyka-obyavleniy' === $service_slug ) : ?>
<section class="light-card process-section process-section--examples" id="process">
	<div class="section-heading"><span class="eyebrow">Примеры работ</span><h2>Примеры фотоотчётов</h2><p><?php echo esc_html( $control['text'] ?? '' ); ?></p></div>
	<?php if ( ! empty( $control['examples'] ) ) : ?>
	<div class="report-examples">
		<?php foreach ( (array) $control['examples'] as $example ) : if ( empty( $example['image'] ) ) { continue; } ?>
		<figure class="report-example"><?php kompas_image( $example['image'], 'large', array( 'loading' => 'lazy' ) ); ?><figcaption><?php echo esc_html( $example['title'] ?? '' ); ?></figcaption></figure>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</section>
<?php endif; ?>

<?php $faq = kompas_group( 'faq' ); ?>
<?php if ( $faq && ! in_array( $service_slug, array( 'raskleyka-obyavleniy', 'rasprostranenie-po-pochtovym-yashchikam', 'promoaktsii' ), true ) ) : ?><section class="light-card faq"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $faq['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $faq['title'] ?? '' ); ?></h2></div><?php foreach ( (array) ( $faq['items'] ?? array() ) as $item ) : ?><div class="faq-item"><button type="button" aria-expanded="false"><span><?php echo esc_html( $item['question'] ?? '' ); ?></span><b>+</b></button><p hidden><?php echo wp_kses_post( $item['answer'] ?? '' ); ?></p></div><?php endforeach; ?></section><?php endif; ?>

<?php $related = kompas_group( 'svyazannye_uslugi' ); ?>
<?php if ( $related ) : ?><section class="dark-card related"><div class="section-heading"><span class="eyebrow"><?php echo esc_html( $related['eyebrow'] ?? '' ); ?></span><h2><?php echo kompas_heading( $related['title'] ?? '' ); ?></h2></div><div class="card-grid card-grid--3"><?php foreach ( (array) ( $related['items'] ?? array() ) as $post ) : ?><a class="blue-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>"><h3><?php echo esc_html( get_the_title( $post ) ); ?></h3><span>Перейти к услуге →</span></a><?php endforeach; ?></div></section><?php endif; ?>
<?php get_footer(); ?>
