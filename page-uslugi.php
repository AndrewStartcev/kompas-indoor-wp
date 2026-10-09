<?php
/**
 * Template Name: Услуги
 * Description: Каталог направлений Компас-Индор.
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();

	$catalog = function_exists( 'kompas_group' ) ? kompas_group( 'services_catalog' ) : array();
	$title = $catalog['title'] ?? '';
	$intro = $catalog['intro'] ?? '';
	$list_title = $catalog['list_title'] ?? '';
	$list_intro = $catalog['list_intro'] ?? '';
	$closing_title = $catalog['closing_title'] ?? '';
	$closing_text = $catalog['closing_text'] ?? '';

	$title = $title ?: 'Офлайн-реклама, которая работает рядом с клиентом';
	$intro = $intro ?: 'Расклейка объявлений, распространение полиграфии по почтовым ящикам и промоакции. Организуем рекламные кампании в Иркутске и других городах России — от адресной программы до фотоотчёта.';
	$list_title = $list_title ?: 'Выберите формат продвижения';
	$list_intro = $list_intro ?: 'Каждое направление можно заказать отдельно или объединить несколько форматов в одну рекламную кампанию.';
	$closing_title = $closing_title ?: 'Нужно подобрать подходящий формат?';
	$closing_text = $closing_text ?: 'Опишите задачу, географию и сроки — предложим решение и рассчитаем стоимость проекта.';
?>
<nav class="breadcrumbs" aria-label="Хлебные крошки">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a>
	<span aria-hidden="true">→</span>
	<span aria-current="page">Услуги</span>
</nav>
<section class="hero dark-card hero--services-catalog">
	<div class="hero-copy">
		<span class="eyebrow"><?php echo esc_html( $catalog['eyebrow'] ?: 'Услуги Компас-Индор' ); ?></span>
		<h1><?php echo esc_html( $title ); ?></h1>
		<p><?php echo esc_html( $intro ); ?></p>
		<div class="actions">
			<a class="button button--light" href="#catalog">Смотреть услуги <span aria-hidden="true">↗</span></a>
			<button class="button button--catalog-ghost" type="button" data-estimate>Оставить заявку</button>
		</div>
	</div>
	<div class="services-catalog__visual" aria-hidden="true">
		<div class="services-catalog__visual-core">К</div>
		<div class="services-catalog__visual-note services-catalog__visual-note--first"><span>01</span> Расклейка</div>
		<div class="services-catalog__visual-note services-catalog__visual-note--second"><span>02</span> Распространение</div>
		<div class="services-catalog__visual-note services-catalog__visual-note--third"><span>03</span> Промоакции</div>
	</div>
</section>

<section class="light-card services-catalog" id="catalog">
	<div class="section-heading">
		<span class="eyebrow">Направления работы</span>
		<h2><?php echo esc_html( $list_title ); ?></h2>
		<p><?php echo esc_html( $list_intro ); ?></p>
	</div>
	<?php
	$services_query = new WP_Query( array(
		'post_type'           => 'services',
		'post_status'         => 'publish',
		'posts_per_page'      => -1,
		'orderby'             => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
	if ( $services_query->have_posts() ) :
		$number = 0;
	?>
	<div class="services-catalog__grid">
		<?php while ( $services_query->have_posts() ) : $services_query->the_post();
			++$number;
			$service_id = get_the_ID();
			$service_hero = function_exists( 'kompas_group' ) ? kompas_group( 'glavnyj_ekran', $service_id ) : array();
			$summary = trim( wp_strip_all_tags( (string) ( $service_hero['text'] ?? '' ) ) );
			$image = $service_hero['image'] ?? null;
			$number_label = str_pad( (string) $number, 2, '0', STR_PAD_LEFT );
		?>
		<a class="services-catalog__card" href="<?php the_permalink(); ?>">
			<div class="services-catalog__card-media<?php echo $image ? '' : ' services-catalog__card-media--empty'; ?>">
				<?php if ( $image ) : ?>
					<?php kompas_image( $image, 'large', array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<span class="services-catalog__card-monogram" aria-hidden="true"><?php echo esc_html( $number_label ); ?></span>
				<?php endif; ?>
				<span class="services-catalog__card-arrow" aria-hidden="true">↗</span>
			</div>
			<div class="services-catalog__card-copy">
				<span class="eyebrow">Услуга <?php echo esc_html( $number_label ); ?></span>
				<h3><?php the_title(); ?></h3>
				<?php if ( $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?>
				<span class="services-catalog__card-link">Подробнее об услуге <span aria-hidden="true">→</span></span>
			</div>
		</a>
		<?php endwhile; ?>
	</div>
	<?php else : ?>
	<div class="services-catalog__empty"><h3>Скоро здесь появятся услуги</h3><p>А пока расскажите о задаче — мы подберём подходящий формат рекламы.</p><button class="button" type="button" data-estimate>Оставить заявку</button></div>
	<?php endif; wp_reset_postdata(); ?>
</section>

<section class="services-catalog__benefits">
	<div class="section-heading">
		<span class="eyebrow">Один подрядчик</span>
		<h2>От задачи до результата</h2>
		<p>Помогаем запускать рекламные кампании в одном городе или сразу в нескольких регионах.</p>
	</div>
	<div class="services-catalog__benefits-grid">
		<div class="pale-panel"><span class="services-catalog__benefit-icon" aria-hidden="true">↗</span><h3>Подбираем географию</h3><p>Формируем адресные программы, точки или маршруты под цели кампании.</p></div>
		<div class="pale-panel"><span class="services-catalog__benefit-icon" aria-hidden="true">◎</span><h3>Организуем исполнение</h3><p>Координируем тиражи, промоутеров и исполнителей по согласованному плану.</p></div>
		<div class="pale-panel"><span class="services-catalog__benefit-icon" aria-hidden="true">✓</span><h3>Предоставляем отчёт</h3><p>Собираем подтверждения выполненных работ и передаём итоговую отчётность.</p></div>
	</div>
</section>

<section class="cta services-catalog__cta">
	<div>
		<span class="eyebrow">Обсудим задачу</span>
		<h2><?php echo esc_html( $closing_title ); ?></h2>
		<p><?php echo esc_html( $closing_text ); ?></p>
	</div>
	<button class="button button--light" type="button" data-estimate>Оставить заявку ↗</button>
</section>
<?php
endwhile;
get_footer();
