<?php
get_header();
while ( have_posts() ) : the_post();
$id           = get_the_ID();
$salary       = kompas_vacancy_field( 'vacancy_salary', $id );
$city         = kompas_vacancy_field( 'vacancy_city', $id );
$schedule     = kompas_vacancy_field( 'vacancy_schedule', $id );
$employment   = kompas_vacancy_field( 'vacancy_employment', $id );
$experience   = kompas_vacancy_field( 'vacancy_experience', $id );
$intro        = kompas_vacancy_field( 'vacancy_intro', $id );
$tasks        = kompas_vacancy_field( 'vacancy_tasks', $id );
$requirements = kompas_vacancy_field( 'vacancy_requirements', $id );
$conditions   = kompas_vacancy_field( 'vacancy_conditions', $id );
$status       = isset( $_GET['application'] ) ? sanitize_key( wp_unslash( $_GET['application'] ) ) : '';
?>
<nav class="breadcrumbs" aria-label="Хлебные крошки"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>→</span><a href="<?php echo esc_url( get_post_type_archive_link( 'vacancies' ) ); ?>">Вакансии</a><span>→</span><span><?php the_title(); ?></span></nav>
<section class="hero dark-card vacancy-detail-hero">
	<div class="hero-copy">
		<span class="eyebrow"><?php echo esc_html( $employment ?: 'Карьера в Компас Indoor' ); ?></span>
		<h1><?php the_title(); ?></h1>
		<?php if ( $intro ) : ?><p><?php echo esc_html( $intro ); ?></p><?php endif; ?>
		<div class="vacancy-tags vacancy-tags--on-dark">
			<?php foreach ( array( $city, $schedule, $experience ) as $tag ) : if ( $tag ) : ?><span><?php echo esc_html( $tag ); ?></span><?php endif; endforeach; ?>
		</div>
		<div class="actions"><a class="button button--light" href="#apply">Откликнуться на вакансию ↗</a><a class="button button--outline" href="<?php echo esc_url( get_post_type_archive_link( 'vacancies' ) ); ?>">Все вакансии</a></div>
	</div>
	<?php if ( $salary ) : ?><aside class="vacancy-detail-hero__salary"><span>Доход</span><strong><?php echo esc_html( $salary ); ?></strong><small>Условия уточняются при собеседовании</small></aside><?php endif; ?>
</section>
<section class="vacancy-detail-grid">
	<div class="light-card vacancy-description">
		<?php
		$sections = array(
			'Чем предстоит заниматься' => $tasks,
			'Что нам важно'           => $requirements,
			'Что предлагаем'          => $conditions,
		);
		foreach ( $sections as $heading => $content ) :
			if ( ! $content ) {
				continue;
			}
			?>
			<div class="vacancy-description__section">
				<h2><?php echo esc_html( $heading ); ?></h2>
				<div class="vacancy-prose"><?php echo wp_kses_post( $content ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
	<aside class="light-card vacancy-apply" id="apply">
		<span class="eyebrow">Присоединиться к команде</span>
		<h2>Откликнуться</h2>
		<p>Расскажи немного о себе. Резюме можно приложить, но это не обязательно.</p>
		<?php if ( 'success' === $status ) : ?>
			<div class="vacancy-form-message vacancy-form-message--success" role="status">Спасибо! Отклик отправлен. Если твой опыт подходит, с тобой свяжутся.</div>
		<?php elseif ( in_array( $status, array( 'error', 'invalid', 'file' ), true ) ) : ?>
			<div class="vacancy-form-message vacancy-form-message--error" role="alert"><?php echo 'file' === $status ? 'Не удалось принять резюме. Допустимы PDF, DOC и DOCX до 7 МБ.' : ( 'invalid' === $status ? 'Проверь заполненные поля и согласие на обработку данных.' : 'Не удалось отправить отклик. Попробуй ещё раз позже.' ); ?></div>
		<?php endif; ?>
		<form class="vacancy-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="kompas_vacancy_apply">
			<input type="hidden" name="vacancy_id" value="<?php echo esc_attr( $id ); ?>">
			<?php wp_nonce_field( 'kompas_vacancy_apply_' . $id, 'kompas_vacancy_nonce' ); ?>
			<div class="vacancy-honeypot" aria-hidden="true"><label>Сайт компании <input type="text" name="company_site" tabindex="-1" autocomplete="off"></label></div>
			<label class="vacancy-form__field"><span>Как тебя зовут <b>*</b></span><input name="candidate_name" type="text" autocomplete="name" maxlength="200" placeholder="Имя и фамилия" required></label>
			<label class="vacancy-form__field"><span>Телефон <b>*</b></span><input name="candidate_phone" type="tel" autocomplete="tel" maxlength="60" placeholder="+7 (999) 000-00-00" required></label>
			<label class="vacancy-form__field"><span>Электронная почта</span><input name="candidate_email" type="email" autocomplete="email" maxlength="254" placeholder="you@example.ru"></label>
			<label class="vacancy-form__field"><span>Сообщение</span><textarea name="candidate_message" rows="4" maxlength="5000" placeholder="Расскажи об опыте или задай вопрос"></textarea></label>
			<label class="vacancy-form__upload"><span>Прикрепить резюме</span><input type="file" name="candidate_resume" accept=".pdf,.doc,.docx"><small>PDF, DOC или DOCX, до 7 МБ</small></label>
			<label class="form-check vacancy-form__consent"><input type="checkbox" name="personal_agreement" value="1" required><span>Даю согласие на обработку персональных данных в целях рассмотрения отклика<?php if ( get_privacy_policy_url() ) : ?> и ознакомлен(а) с <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" target="_blank" rel="noopener">политикой конфиденциальности</a><?php endif; ?>.</span></label>
			<button class="button" type="submit">Отправить отклик →</button>
			<small class="vacancy-form__hint">Контакты используются только для связи по вакансии.</small>
		</form>
	</aside>
</section>
<?php
$related = get_posts( array(
	'post_type'      => 'vacancies',
	'post_status'    => 'publish',
	'post__not_in'   => array( $id ),
	'posts_per_page' => 2,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
if ( $related ) :
?>
<section class="light-card vacancy-related"><div class="section-heading"><span class="eyebrow">Другие возможности</span><h2>Также открыты</h2></div><div class="vacancy-list">
<?php foreach ( $related as $post ) : setup_postdata( $post ); ?><a href="<?php the_permalink(); ?>" class="vacancy-related__item"><span><strong><?php the_title(); ?></strong><small><?php echo esc_html( kompas_vacancy_field( 'vacancy_salary', get_the_ID() ) ); ?></small></span><span aria-hidden="true">↗</span></a><?php endforeach; wp_reset_postdata(); ?>
</div></section>
<?php endif; ?>
<?php endwhile; get_footer(); ?>
