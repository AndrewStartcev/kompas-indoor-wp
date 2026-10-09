<?php
defined( 'ABSPATH' ) || exit;

/**
 * Vacancies, editable fields and a secure, plugin-free application form.
 * Records are managed in WordPress; CV files are mailed from PHP's temporary
 * directory and are not retained in public uploads.
 */
add_action( 'init', function () {
	register_post_type( 'vacancies', array(
		'labels' => array(
			'name'               => 'Вакансии',
			'singular_name'      => 'Вакансия',
			'add_new_item'       => 'Добавить вакансию',
			'edit_item'          => 'Редактировать вакансию',
			'all_items'          => 'Все вакансии',
			'not_found'          => 'Вакансии не найдены',
			'archives'           => 'Каталог вакансий',
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_in_rest'       => true,
		'has_archive'        => 'vakansii',
		'rewrite'            => array( 'slug' => 'vakansii', 'with_front' => false ),
		'menu_icon'          => 'dashicons-businessperson',
		'menu_position'      => 21,
		'supports'           => array( 'title', 'page-attributes', 'revisions' ),
	) );
} );

function kompas_vacancy_field( $name, $post_id = false ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, $post_id );
		if ( null !== $value && false !== $value && '' !== $value ) {
			return $value;
		}
	}
	return $post_id ? get_post_meta( $post_id, $name, true ) : '';
}

/**
 * Add a link to the public archive even when the custom menu has already been set.
 * Avoid adding it twice if the editor inserted the archive manually.
 */
add_filter( 'wp_nav_menu_items', function ( $items, $args ) {
	if ( 'main' !== ( $args->theme_location ?? '' ) ) {
		return $items;
	}
	$url = get_post_type_archive_link( 'vacancies' );
	if ( $url && false === strpos( $items, esc_url( $url ) ) ) {
		$current = is_post_type_archive( 'vacancies' ) || is_singular( 'vacancies' );
		$items  .= '<li class="menu-item nav__vacancies-item"><a href="' . esc_url( $url ) . '"' . ( $current ? ' aria-current="page"' : '' ) . '>Вакансии</a></li>';
	}
	return $items;
}, 20, 2 );

add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'vacancies' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		$query->set( 'posts_per_page', 9 );
	}
} );

/** Only create the two positions verified in public company listings once. */
add_action( 'admin_init', function () {
	if ( get_option( 'kompas_vacancies_initialized_v1' ) || ! post_type_exists( 'vacancies' ) ) {
		return;
	}

	$jobs = array(
		array(
			'title' => 'Офис-менеджер / Координатор',
			'slug'  => 'ofis-menedzher-koordinator',
			'order' => 10,
			'fields' => array(
				'vacancy_salary'       => '55 000–80 000 ₽/мес.',
				'vacancy_city'         => 'Иркутск, ул. Коммунистическая, 44',
				'vacancy_schedule'     => '5/2, примерно 12:00–20:30',
				'vacancy_employment'   => 'Полная занятость',
				'vacancy_experience'   => 'Можно без опыта',
				'vacancy_intro'        => 'Координируй рекламные проекты по России из офиса в Иркутске. Компания обучает работе с маршрутами, исполнителями и отчётами.',
				'vacancy_tasks'        => '<ul><li>Координировать работу промоутеров и расклейщиков в разных городах.</li><li>Подбирать и распределять маршруты, согласовывать сроки выполнения.</li><li>Проверять фотоотчёты, контролировать качество и вести таблицы по проектам.</li><li>Поддерживать связь с исполнителями и помогать решать рабочие вопросы.</li></ul>',
				'vacancy_requirements' => '<ul><li>Уверенно работать на компьютере, с таблицами и картами.</li><li>Грамотно общаться и уметь договариваться с людьми.</li><li>Быть внимательным к срокам, деталям и отчётности.</li><li>Опыт не обязателен — предусмотрено обучение.</li></ul>',
				'vacancy_conditions'   => '<ul><li>Работа в офисе в Иркутске, пятидневная неделя.</li><li>График со смещением на вторую половину дня из-за работы с западными регионами.</li><li>Заработная плата дважды в месяц, возможность профессионального роста.</li><li>Рабочее оборудование и связь предоставляет компания.</li></ul>',
				'vacancy_source_url'   => 'https://nesiditsa.ru/vacancy/137654191',
			),
		),
		array(
			'title' => 'Оператор ПК в офисе',
			'slug'  => 'operator-pk-v-ofise',
			'order' => 20,
			'fields' => array(
				'vacancy_salary'       => '1 000–1 500 ₽/смена',
				'vacancy_city'         => 'Иркутск, ул. Коммунистическая, 44',
				'vacancy_schedule'     => 'Гибкий график',
				'vacancy_employment'   => 'Подработка',
				'vacancy_experience'   => 'Можно без опыта',
				'vacancy_intro'        => 'Подработка за компьютером: помогай команде готовить фотоотчёты и приводить данные по рекламным кампаниям в порядок.',
				'vacancy_tasks'        => '<ul><li>Обрабатывать и сортировать фотографии, поступающие от исполнителей.</li><li>Готовить изображения для клиентских фотоотчётов.</li><li>Работать с данными в Excel и Google Таблицах.</li><li>При необходимости связываться с промоутерами по готовым инструкциям.</li></ul>',
				'vacancy_requirements' => '<ul><li>Уверенное владение ПК, быстрая работа с файлами и папками.</li><li>Знание офисных программ и полезных горячих клавиш.</li><li>Внимательность, аккуратность, готовность разбираться в рабочих задачах.</li><li>Опыт работы не обязателен.</li></ul>',
				'vacancy_conditions'   => '<ul><li>Подработка в офисе компании в Иркутске.</li><li>График согласовывается с командой.</li><li>Оплата за смену; точные условия обсуждаются на собеседовании.</li></ul>',
				'vacancy_source_url'   => 'https://finder.work/vacancies/36086227',
			),
		),
	);

	foreach ( $jobs as $job ) {
		$post = get_page_by_path( $job['slug'], OBJECT, 'vacancies' );
		if ( $post ) {
			continue; // Never overwrite an existing job or an editor's changes.
		}
		$id = wp_insert_post( array(
			'post_type'   => 'vacancies',
			'post_status' => 'publish',
			'post_title'  => $job['title'],
			'post_name'   => $job['slug'],
			'menu_order'  => $job['order'],
		), true );
		if ( is_wp_error( $id ) ) {
			continue;
		}
		foreach ( $job['fields'] as $name => $value ) {
			$key = 'field_ki_' . $name;
			if ( function_exists( 'update_field' ) ) {
				update_field( $key, $value, $id );
			} else {
				update_post_meta( $id, $name, $value );
			}
		}
	}

	flush_rewrite_rules( false );
	update_option( 'kompas_vacancies_initialized_v1', '1', false );
} );

/**
 * POST-redirect-GET; no personal details go into query parameters.
 * The form intentionally requires no third-party plugin.
 */
add_action( 'admin_post_nopriv_kompas_vacancy_apply', 'kompas_handle_vacancy_application' );
add_action( 'admin_post_kompas_vacancy_apply', 'kompas_handle_vacancy_application' );

function kompas_vacancy_redirect( $post_id, $status ) {
	$url = $post_id && 'publish' === get_post_status( $post_id )
		? get_permalink( $post_id )
		: get_post_type_archive_link( 'vacancies' );
	wp_safe_redirect( add_query_arg( 'application', $status, $url ) . '#apply', 303 );
	exit;
}

function kompas_handle_vacancy_application() {
	$id = isset( $_POST['vacancy_id'] ) ? absint( wp_unslash( $_POST['vacancy_id'] ) ) : 0;
	if ( ! $id || 'vacancies' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
		kompas_vacancy_redirect( 0, 'invalid' );
	}

	$nonce = isset( $_POST['kompas_vacancy_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['kompas_vacancy_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'kompas_vacancy_apply_' . $id ) ) {
		kompas_vacancy_redirect( $id, 'invalid' );
	}

	// Silent rejection protects against basic automated submissions.
	if ( ! empty( $_POST['company_site'] ) ) {
		kompas_vacancy_redirect( $id, 'success' );
	}

	$name    = isset( $_POST['candidate_name'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_name'] ) ) : '';
	$phone   = isset( $_POST['candidate_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_phone'] ) ) : '';
	$email   = isset( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
	$message = isset( $_POST['candidate_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['candidate_message'] ) ) : '';

	if ( ! $name || strlen( $name ) > 200 || ( ! $phone && ! $email ) || strlen( $phone ) > 60
		|| ( $email && ! is_email( $email ) ) || strlen( $message ) > 5000 || empty( $_POST['personal_agreement'] ) ) {
		kompas_vacancy_redirect( $id, 'invalid' );
	}

	$attachment = '';
	$original_filename = '';
	if ( isset( $_FILES['candidate_resume'] ) && UPLOAD_ERR_NO_FILE !== (int) $_FILES['candidate_resume']['error'] ) {
		$file = $_FILES['candidate_resume'];
		if ( UPLOAD_ERR_OK !== (int) $file['error'] || (int) $file['size'] > 7 * MB_IN_BYTES
			|| (int) $file['size'] < 1 || ! is_uploaded_file( $file['tmp_name'] ) ) {
			kompas_vacancy_redirect( $id, 'file' );
		}

		$original_filename = sanitize_file_name( wp_unslash( $file['name'] ) );
		$extension = strtolower( pathinfo( $original_filename, PATHINFO_EXTENSION ) );
		$allowed = array(
			'pdf'  => array( 'application/pdf' ),
			'doc'  => array( 'application/msword', 'application/vnd.ms-office', 'application/x-ole-storage', 'application/octet-stream' ),
			'docx' => array( 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/x-zip-compressed' ),
		);
		$mime = function_exists( 'mime_content_type' ) ? mime_content_type( $file['tmp_name'] ) : '';
		if ( ! isset( $allowed[ $extension ] ) || ( $mime && ! in_array( $mime, $allowed[ $extension ], true ) ) ) {
			kompas_vacancy_redirect( $id, 'file' );
		}

		$tmp = tempnam( get_temp_dir(), 'kompas_cv_' );
		if ( ! $tmp ) {
			kompas_vacancy_redirect( $id, 'error' );
		}
		$attachment = $tmp . '.' . $extension;
		if ( ! rename( $tmp, $attachment ) || ! move_uploaded_file( $file['tmp_name'], $attachment ) ) {
			@unlink( $tmp );
			@unlink( $attachment );
			kompas_vacancy_redirect( $id, 'error' );
		}
	}

	$recipient = function_exists( 'get_field' ) ? get_field( 'vacancy_recipient_email', 'option' ) : '';
	$recipient = is_email( $recipient ) ? $recipient : get_option( 'admin_email' );
	$subject = sprintf( 'Отклик на вакансию: %s', get_the_title( $id ) );
	$body = implode( "\n", array(
		'Новый отклик с сайта ' . home_url( '/' ),
		'Вакансия: ' . get_the_title( $id ),
		'Ссылка: ' . get_permalink( $id ),
		'Имя: ' . $name,
		'Телефон: ' . $phone,
		'Email: ' . $email,
		'Сообщение: ' . $message,
		'Файл резюме: ' . ( $original_filename ?: 'не приложен' ),
		'Согласие на обработку персональных данных подтверждено.',
	) );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . $email;
	}
	$sent = wp_mail( $recipient, $subject, $body, $headers, $attachment ? array( $attachment ) : array() );
	if ( $attachment ) {
		@unlink( $attachment ); // CV is never left in a public directory.
	}
	kompas_vacancy_redirect( $id, $sent ? 'success' : 'error' );
}
