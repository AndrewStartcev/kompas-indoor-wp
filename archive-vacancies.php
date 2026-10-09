<?php
get_header();
$vacancy_count = (int) wp_count_posts( 'vacancies' )->publish;
$archive_headline = function_exists( 'get_field' ) ? get_field( 'vacancy_archive_headline', 'option' ) : '';
$archive_intro = function_exists( 'get_field' ) ? get_field( 'vacancy_archive_intro', 'option' ) : '';
$list_title = function_exists( 'get_field' ) ? get_field( 'vacancy_archive_list_title', 'option' ) : '';
$list_intro = function_exists( 'get_field' ) ? get_field( 'vacancy_archive_list_intro', 'option' ) : '';
$archive_headline = $archive_headline ?: 'Работа, в которой виден результат';
$archive_intro = $archive_intro ?: 'Мы организуем офлайн-рекламу в Иркутске и по всей России. За каждым проектом стоят люди: координаторы, операторы и исполнители. Присоединяйтесь к нашей команде.';
$list_title = $list_title ?: 'Открытые вакансии';
$list_intro = $list_intro ?: 'Выберите направление и посмотрите подробности: задачи, требования, график и условия. На странице каждой вакансии можно оставить отклик.';
?>
<nav class="breadcrumbs" aria-label="Хлебные крошки">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>→</span><span>Вакансии</span>
</nav>
<section class="hero dark-card hero--vacancies">
	<div class="hero-copy">
		<span class="eyebrow">Карьера в Компас Indoor</span>
		<h1><?php echo esc_html( $archive_headline ); ?></h1>
		<p><?php echo esc_html( $archive_intro ); ?></p>
		<div class="actions"><a class="button button--light" href="#open-vacancies">Смотреть вакансии ↗</a></div>
		<div class="vacancy-hero__facts"><span>Иркутск и проекты по России</span><span>Обучение и поддержка команды</span></div>
	</div>
	<div class="vacancy-hero__visual" aria-hidden="true"><span class="vacancy-hero__orbit"></span><span class="vacancy-hero__symbol">К</span><div class="vacancy-hero__badge"><strong><?php echo esc_html( $vacancy_count ); ?></strong><span><?php echo esc_html( _n( 'открытая вакансия', 'открытых вакансий', $vacancy_count, 'kompas-indoor' ) ); ?></span></div></div>
</section>
<section class="light-card vacancy-catalog" id="open-vacancies">
	<div class="section-heading">
		<span class="eyebrow">Возможности</span>
		<h2><?php echo esc_html( $list_title ); ?></h2>
		<p><?php echo esc_html( $list_intro ); ?></p>
	</div>
	<?php if ( have_posts() ) : ?>
	<div class="vacancy-list">
		<?php while ( have_posts() ) : the_post();
			$id = get_the_ID();
			$salary = kompas_vacancy_field( 'vacancy_salary', $id );
			$city = kompas_vacancy_field( 'vacancy_city', $id );
			$schedule = kompas_vacancy_field( 'vacancy_schedule', $id );
			$employment = kompas_vacancy_field( 'vacancy_employment', $id );
			$intro = kompas_vacancy_field( 'vacancy_intro', $id );
			?>
			<article class="vacancy-card">
				<div class="vacancy-card__top"><span class="vacancy-card__eyebrow"><?php echo esc_html( $employment ?: 'Работа в команде' ); ?></span><span class="vacancy-card__arrow" aria-hidden="true">↗</span></div>
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<?php if ( $salary ) : ?><strong class="vacancy-card__salary"><?php echo esc_html( $salary ); ?></strong><?php endif; ?>
				<?php if ( $intro ) : ?><p><?php echo esc_html( $intro ); ?></p><?php endif; ?>
				<div class="vacancy-tags">
					<?php if ( $city ) : ?><span><?php echo esc_html( $city ); ?></span><?php endif; ?>
					<?php if ( $schedule ) : ?><span><?php echo esc_html( $schedule ); ?></span><?php endif; ?>
				</div>
				<a class="vacancy-card__link" href="<?php the_permalink(); ?>">Подробнее о вакансии <span aria-hidden="true">→</span></a>
			</article>
		<?php endwhile; ?>
	</div>
	<?php the_posts_pagination( array( 'prev_text' => '← Назад', 'next_text' => 'Вперёд →' ) ); ?>
	<?php else : ?>
	<div class="vacancy-empty"><h3>Сейчас нет открытых вакансий</h3><p>Мы обновляем этот раздел. Загляните сюда позже или свяжитесь с нами через страницу контактов.</p><a class="button" href="<?php echo esc_url( home_url( '/kontakty/' ) ); ?>">Связаться с нами</a></div>
	<?php endif; ?>
</section>
<section class="cta vacancy-cta">
	<div><span class="eyebrow">Компас Indoor</span><h2>Давайте делать большие проекты вместе</h2><p>Работать с рекламными проектами можно научиться. Главное — внимательность, ответственность и желание развиваться.</p></div>
	<a class="button button--light" href="<?php echo esc_url( home_url( '/o-kompanii/' ) ); ?>">О компании ↗</a>
</section>
<?php get_footer(); ?>
