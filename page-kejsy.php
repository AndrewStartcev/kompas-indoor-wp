<?php
/* Template Name: Кейсы */
get_header();
while ( have_posts() ) : the_post();
$stories = function_exists( 'get_field' ) ? (array) get_field( 'case_stories' ) : array();
?>
<nav class="breadcrumbs" aria-label="Хлебные крошки"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>→</span><span>Кейсы</span></nav>
<section class="hero dark-card hero--cases">
	<div class="hero-copy"><span class="eyebrow">Реальные проекты</span><h1>Результаты, о которых рассказывают наши клиенты</h1><p>Расклейка, распространение полиграфии и промоакции для компаний Иркутска и других городов России. Рассказываем только о проектах, подтверждённых клиентами.</p><div class="actions"><button class="button button--light" type="button" data-estimate>Оставить заявку →</button></div></div>
	<div class="case-hero__decoration" aria-hidden="true"><span>КОМПАС</span><strong>↗</strong><small>Офлайн-реклама с результатом</small></div>
</section>
<section class="light-card case-stories"><div class="section-heading"><span class="eyebrow">Наш опыт</span><h2>Задачи клиентов</h2><p>Каждый проект — это конкретная задача, территория и формат рекламной кампании.</p></div>
<?php if ( $stories ) : ?>
<div class="case-stories__grid">
	<?php foreach ( $stories as $story ) : if ( empty( $story['client'] ) && empty( $story['title'] ) ) { continue; } ?>
		<article class="case-story">
			<?php if ( ! empty( $story['image'] ) ) : ?><div class="case-story__media"><?php kompas_image( $story['image'], 'large', array( 'loading' => 'lazy' ) ); ?></div><?php endif; ?>
			<div class="case-story__copy"><span class="eyebrow"><?php echo esc_html( $story['client'] ?? '' ); ?></span><h3><?php echo esc_html( $story['title'] ?? '' ); ?></h3><p><?php echo esc_html( $story['text'] ?? '' ); ?></p></div>
		</article>
	<?php endforeach; ?>
</div>
<?php else : ?><div class="case-content"><?php the_content(); ?></div><?php endif; ?>
</section>
<?php
get_template_part( 'template-parts/common/reviews', null, array( 'page_id' => get_the_ID() ) );
endwhile;
?>
<section class="cta case-callout"><div><span class="eyebrow">Следующий проект — ваш</span><h2>Обсудим вашу рекламную кампанию?</h2><p>Оставьте заявку — уточним задачу, географию и предложим формат размещения.</p></div><button class="button button--light" type="button" data-estimate>Оставить заявку →</button></section>
<?php get_footer(); ?>
