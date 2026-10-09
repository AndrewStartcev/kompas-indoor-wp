<?php
defined( 'ABSPATH' ) || exit;

/**
 * One-time, non-destructive October 2026 client-copy migration.
 *
 * Updates only text metadata; does not re-save ACF media, galleries or files.
 * Editors' unrelated ACF fields and existing uploaded photography are preserved.
 */
add_action( 'admin_init', function () {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'kompas_october_feedback_migrated_v1' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	$paths = array(
		'home'    => array( 'glavnaya', 'page' ),
		'about'   => array( 'o-kompanii', 'page' ),
		'cases'   => array( 'kejsy', 'page' ),
		'service' => array( 'uslugi', 'page' ),
		'poster'  => array( 'raskleyka-obyavleniy', 'services' ),
		'mail'    => array( 'rasprostranenie-po-pochtovym-yashchikam', 'services' ),
		'promo'   => array( 'promoaktsii', 'services' ),
	);
	$ids = array();
	foreach ( $paths as $key => $path ) {
		$post = get_page_by_path( $path[0], OBJECT, $path[1] );
		$ids[ $key ] = $post ? (int) $post->ID : 0;
	}
	if ( ! $ids['home'] || ! $ids['poster'] || ! $ids['mail'] || ! $ids['promo'] ) {
		return; // Wait for initial text importer to create content.
	}

	$replacements = array(
		'Компас Indoor'       => 'Компас-Индор',
		'Kompas Indoor'       => 'Компас-Индор',
		'Рассчитать проект'   => 'Оставить заявку',
		'Законная расклейка'  => 'Расклейка',
		'законную расклейку'  => 'расклейку',
		'Законное размещение' => 'Размещение',
	);
	// Update text-only post meta values. Do not rewrite serialized ACF arrays,
	// image fields, repeater counts or numeric attachment IDs.
	foreach ( $ids as $id ) {
		if ( ! $id ) {
			continue;
		}
		foreach ( get_post_meta( $id ) as $key => $entries ) {
			if ( 0 === strpos( $key, '_' ) || ! isset( $entries[0] ) || ! is_string( $entries[0] ) || is_serialized( $entries[0] ) ) {
				continue;
			}
			if ( ! preg_match( '/^(glavnyj_ekran|uslugi|preimushhestva|fototchet|forma|model_raboty|istoriya|dlya_agentstv|principy|rekvizity|etapy|formaty|mesta|kontrol|faq)_/', $key ) ) {
				continue;
			}
			$previous = $entries[0];
			$updated = str_replace( array_keys( $replacements ), array_values( $replacements ), $previous );
			if ( $previous !== $updated ) {
				update_post_meta( $id, $key, $updated );
			}
		}
		$post = get_post( $id );
		$title = str_replace( array_keys( $replacements ), array_values( $replacements ), $post->post_title );
		$body  = str_replace( array_keys( $replacements ), array_values( $replacements ), $post->post_content );
		if ( $title !== $post->post_title || $body !== $post->post_content ) {
			wp_update_post( array( 'ID' => $id, 'post_title' => $title, 'post_content' => $body ) );
		}
		foreach ( array( '_kompas_seo_title', '_kompas_seo_description', 'rank_math_title', 'rank_math_description', '_yoast_wpseo_title', '_yoast_wpseo_metadesc' ) as $meta ) {
			$previous = get_post_meta( $id, $meta, true );
			if ( is_string( $previous ) ) {
				$updated = str_replace( array_keys( $replacements ), array_values( $replacements ), $previous );
				if ( $previous !== $updated ) {
					update_post_meta( $id, $meta, $updated );
				}
			}
		}
	}

	// Update only known legacy strings. If an editor has already supplied new
	// wording, do not overwrite their text.
	$changes = array(
		$ids['home'] => array(
			'preimushhestva_items_0_number' => array( 'СВОЯ СЕТЬ', '15 000' ),
			'preimushhestva_items_0_text' => array( 'В Иркутске работаем с собственной сетью рекламных поверхностей у жилых домов и подъездов.', 'В Иркутске охват составляет 15 000 информационных стендов у подъездов жилых домов.' ),
			'fototchet_title' => array( 'Фотоотчёт<br>по выполненным работам', 'Примеры фотоотчётов' ),
			'fototchet_text' => array( 'После выполнения заказчик получает отчётность по размещению или распространению. Это помогает подтвердить факт работ и сопоставить запланированную географию с фактически выполненной.', 'После выполнения работ предоставляем фотографии размещённых листовок.' ),
			'forma_title' => array( 'Нужно разместить рекламу<br>в Иркутске или сразу<br>в нескольких городах?', 'Предпочитаете оперативность и скорость?<br>Оставьте заявку — мы вам перезвоним' ),
			'forma_text' => array( 'Отправьте города, тираж и сроки — подготовим расчёт и предложим подходящий формат кампании.', 'Оставьте номер телефона — свяжемся с вами, уточним задачу и предложим подходящий формат рекламы.' ),
		),
		$ids['poster'] => array(
			'mesta_text' => array( 'В Иркутске используются собственные рекламные поверхности у подъездов и жилых домов. Для проектов в других городах формат размещения подбирается с учётом доступных поверхностей и местных условий.', 'В Иркутске размещение осуществляется на информационных стендах у подъездов жилых домов. Для проектов в других городах формат размещения подбирается с учётом доступных поверхностей и местных условий.' ),
			'mesta_items_0_text' => array( 'Собственная сеть рекламных поверхностей у жилых домов и подъездов.', 'Охват 15 000 информационных стендов у подъездов жилых домов.' ),
			'kontrol_title' => array( 'Фотоотчёт и контроль выполнения', 'Примеры фотоотчётов' ),
			'kontrol_text' => array( 'После завершения работ передаём фотографии размещений. Это позволяет подтвердить факт выполнения и проверить фактическое прохождение адресной программы.', 'После завершения работ передаём фотографии размещённых листовок.' ),
		),
		$ids['promo'] => array(
			'formaty_items_3_title' => array( 'Другая полиграфия', 'Дорхенгеры' ),
			'formaty_items_3_text' => array( 'Формат согласовывается под задачу, тираж и механику промоакции.', 'Рекламные материалы для размещения на дверных ручках. Подходящий тираж и механику распространения согласовываем под проект.' ),
		),
	);
	foreach ( $changes as $id => $fields ) {
		foreach ( $fields as $key => $change ) {
			$previous = get_post_meta( $id, $key, true );
			if ( $previous === $change[0] ) {
				update_post_meta( $id, $key, $change[1] );
			}
		}
	}
	if ( ! get_post_meta( $ids['home'], 'forma_manager_name', true ) ) {
		update_post_meta( $ids['home'], 'forma_manager_name', 'Татьяна Вовк' );
		update_post_meta( $ids['home'], '_forma_manager_name', 'field_ki_home_manager_name' );
	}
	if ( ! get_post_meta( $ids['home'], 'forma_manager_message', true ) ) {
		update_post_meta( $ids['home'], 'forma_manager_message', 'Здравствуйте! Напишите или оставьте номер — помогу с выбором рекламы.' );
		update_post_meta( $ids['home'], '_forma_manager_message', 'field_ki_home_manager_message' );
	}

	// Change the company name / button label in global ACF options only when
	// the old values are still present.
	foreach ( array( 'options_header_brand_name', 'options_header_button_text', 'options_footer_tagline', 'options_footer_legal', 'options_forms_title', 'options_forms_text' ) as $key ) {
		$previous = get_option( $key );
		if ( ! is_string( $previous ) ) {
			continue;
		}
		$updated = str_replace( array_keys( $replacements ), array_values( $replacements ), $previous );
		if ( $updated !== $previous ) {
			update_option( $key, $updated );
		}
	}

	if ( $ids['cases'] ) {
		update_post_meta( $ids['cases'], '_wp_page_template', 'page-kejsy.php' );
		if ( ! get_field( 'case_stories', $ids['cases'] ) ) {
			update_field( 'field_ki_case_stories', array(
				array( 'client' => 'Иркутскэнергосбыт', 'title' => 'Оперативное информирование жителей', 'text' => 'Размещение информационных объявлений в жилых микрорайонах Иркутска помогло донести важные сведения до жителей. Клиент отмечает оперативность выполнения.', 'image' => '' ),
				array( 'client' => 'Суши-шоп', 'title' => 'Распространение меню по почтовым ящикам', 'text' => 'Меню распространяли по жилым домам и новостройкам. Представитель сети отмечает отклики от жителей выбранных районов.', 'image' => '' ),
				array( 'client' => 'Дом.ru', 'title' => 'Долгосрочное сотрудничество', 'text' => 'Представитель компании отмечает стабильное выполнение работ в сроки, предоставление отчётности и оперативное решение рабочих вопросов.', 'image' => '' ),
				array( 'client' => 'Пересвет-Недвижимость', 'title' => 'От объявлений к размещению мини-баннеров', 'text' => 'Работа начиналась с расклейки на подъездных стендах, после чего сотрудничество расширилось до продолжительного размещения мини-баннеров.', 'image' => '' ),
			), $ids['cases'] );
		}
		if ( ! get_field( 'case_reviews', $ids['cases'] ) ) {
			update_field( 'field_ki_case_reviews', array(
				array( 'client' => 'Woman Gym', 'person' => 'Дарья Бровкина', 'role' => 'Руководитель отдела маркетинга', 'quote' => 'Отмечает оперативную работу и помощь команды в подборе инструментов продвижения.', 'logo' => '' ),
				array( 'client' => 'ШВТ «Нейла»', 'person' => 'Жанна Казакова', 'role' => 'Руководитель', 'quote' => 'Команда помогла не только с размещением рекламы, но и с разработкой макетов листовок.', 'logo' => '' ),
				array( 'client' => 'Суши-шоп', 'person' => 'Анастасия Курилова', 'role' => 'Управляющая сети', 'quote' => 'Распространение меню по почтовым ящикам помогает привлекать клиентов из новостроек.', 'logo' => '' ),
				array( 'client' => 'Пересвет-Недвижимость', 'person' => 'Елена Крюкова', 'role' => 'Руководитель представительства', 'quote' => 'После успешной расклейки объявлений компания расширила сотрудничество до регулярного размещения мини-баннеров.', 'logo' => '' ),
				array( 'client' => 'Дом.ru', 'person' => 'Екатерина Филонова', 'role' => 'Ведущий менеджер по маркетингу', 'quote' => 'Отмечает соблюдение сроков, полную отчётность и умение оперативно решать вопросы.', 'logo' => '' ),
				array( 'client' => 'Иркутскэнергосбыт', 'person' => 'Татьяна Распопова', 'role' => 'Специалист по связям с общественностью', 'quote' => 'Объявления на подъездных стендах помогают информировать жителей микрорайонов по важным вопросам.', 'logo' => '' ),
			), $ids['cases'] );
		}
	}

	update_option( 'kompas_october_feedback_migrated_v1', '1', false );
}, 50 );
