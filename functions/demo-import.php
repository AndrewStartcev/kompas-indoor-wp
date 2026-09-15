<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'upload_mimes', function ( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
} );

add_action( 'admin_menu', function () {
	add_theme_page( 'Импорт контента Kompas Indoor', 'Импорт Kompas Indoor', 'manage_options', 'kompas-import', 'kompas_import_page' );
} );

function kompas_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$result = null;
	if ( isset( $_POST['kompas_import'] ) ) {
		check_admin_referer( 'kompas_import_content' );
		$result = kompas_run_import();
	}
	?>
	<div class="wrap">
		<h1>Импорт исходного контента Kompas Indoor</h1>
		<p>Импорт создаёт базовые страницы и услугу, переносит исходные изображения в медиабиблиотеку и записывает их ID в ACF. Шаблоны никогда не обращаются к файлам из папки темы напрямую.</p>
		<?php if ( is_wp_error( $result ) ) : ?><div class="notice notice-error"><p><?php echo esc_html( $result->get_error_message() ); ?></p></div><?php elseif ( $result ) : ?><div class="notice notice-success"><p>Импорт завершён. Изображения записаны в ACF, страницы и форма подготовлены.</p></div><?php endif; ?>
		<form method="post"><?php wp_nonce_field( 'kompas_import_content' ); ?><p><button class="button button-primary" type="submit" name="kompas_import" value="1">Импортировать контент</button></p></form>
	</div>
	<?php
}

function kompas_import_image( $filename ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_kompas_source_file', 'meta_value' => $filename, 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = get_theme_file_path( 'assets/demo-images/' . $filename );
	if ( ! file_exists( $source ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $filename );
	if ( ! $tmp || ! copy( $source, $tmp ) ) {
		return 0;
	}

	$id = media_handle_sideload( array( 'name' => $filename, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp );
		return 0;
	}
	update_post_meta( $id, '_kompas_source_file', $filename );
	return (int) $id;
}

function kompas_find_or_create_page( $title, $slug, $template ) {
	$page = get_page_by_path( $slug );
	$id = $page ? (int) $page->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug ) );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return (int) $id;
}

function kompas_create_cf7_form() {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return '';
	}
	$found = get_page_by_title( 'Расчёт проекта', OBJECT, 'wpcf7_contact_form' );
	if ( $found ) {
		return '[contact-form-7 id="' . (int) $found->ID . '" title="Расчёт проекта"]';
	}
	$form = WPCF7_ContactForm::get_template( array( 'title' => 'Расчёт проекта' ) );
	$form->set_properties( array(
		'form' => '<div class="lead-form lead-form--compact"><label><span>Имя</span>[text* your-name placeholder "Имя"]</label><label><span>Компания</span>[text company placeholder "Компания"]</label><label><span>Телефон или email</span>[text* contact placeholder "Телефон / email"]</label><label><span>Города и тираж</span>[text cities placeholder "Города и тираж"]</label><label class="form-check">[acceptance privacy] Согласен на обработку персональных данных и принимаю политику конфиденциальности. [/acceptance]</label><label class="form-check">[checkbox marketing use_label_element "Хочу получать полезные материалы и предложения компании."]</label>[submit class:button "Получить расчёт"]</div>',
	) );
	$form->save();
	return '[contact-form-7 id="' . (int) $form->id() . '" title="Расчёт проекта"]';
}

function kompas_run_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'acf_required', 'Для импорта необходимо активировать ACF Pro.' );
	}

	$img = function ( $name ) { return kompas_import_image( $name ); };
	$home_id = kompas_find_or_create_page( 'Главная', 'glavnaya', 'default' );
	$about_id = kompas_find_or_create_page( 'О компании', 'o-kompanii', 'page-o-kompanii.php' );
	$contacts_id = kompas_find_or_create_page( 'Контакты', 'kontakty', 'page-kontakty.php' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );

	$service = get_posts( array( 'post_type' => 'services', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	$service_id = $service ? (int) $service[0] : (int) wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'publish', 'post_title' => 'Расклейка объявлений', 'post_name' => 'raskleyka-obyavleniy' ) );
	$shortcode = kompas_create_cf7_form();
	$menu = wp_get_nav_menu_object( 'Основное меню' );
	$menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( 'Основное меню' );
	if ( $menu_id && ! wp_get_nav_menu_items( $menu_id ) ) {
		wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Услуги', 'menu-item-url' => get_permalink( $service_id ), 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'О компании', 'menu-item-object' => 'page', 'menu-item-object-id' => $about_id, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Кейсы', 'menu-item-url' => home_url( '/#cases' ), 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Контакты', 'menu-item-object' => 'page', 'menu-item-object-id' => $contacts_id, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
	}
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations['main'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	update_field( 'header', array( 'logo_mark' => $img( 'logo-mark.svg' ), 'brand_name' => 'КОМПАС — INDOOR', 'brand_caption' => 'Рекламная группа | Иркутск · Россия', 'phone' => '+7 3952 48-88-68', 'email' => 'info@kompas-indoor.ru', 'city' => 'Иркутск', 'button_text' => 'Рассчитать проект' ), 'option' );
	update_field( 'forms', array( 'eyebrow' => 'Расчёт проекта', 'title' => 'Расскажите о задаче', 'text' => 'Укажите город, формат рекламы и тираж.', 'popup_shortcode' => $shortcode, 'success_eyebrow' => 'Готово', 'success_title' => 'Спасибо!', 'success_text' => 'Мы свяжемся с вами для уточнения проекта.' ), 'option' );
	update_field( 'cookies', array( 'title' => 'Мы используем cookie', 'text' => 'Они помогают сайту работать корректно и анализировать посещаемость.', 'necessary_text' => 'Только необходимые', 'accept_text' => 'Принять' ), 'option' );

	update_field( 'footer', array( 'tagline' => 'Офлайн-реклама там,<br>где находятся ваши клиенты.', 'copyright' => '© 2026 ООО «Компас-Индор»', 'legal' => 'Политика конфиденциальности · Согласие на обработку данных' ), 'option' );

	update_field( 'glavnyj_ekran', array(
		'title' => 'Расклейка объявлений,<br>листовки и промоакции<br>в Иркутске и по России',
		'text' => 'Компас Indoor организует законное размещение рекламы, распространение по почтовым ящикам и работу промоутеров. Собственные рекламные поверхности в Иркутске и запуск кампаний более чем в 1000 городах России.',
		'button_text' => 'Рассчитать проект', 'second_button' => array( 'url' => '#services', 'title' => 'Смотреть услуги', 'target' => '' ),
		'stats' => array( array( 'value' => '1117', 'caption' => 'городов в географии проектов' ), array( 'value' => '16+', 'caption' => 'лет в офлайн-рекламе' ), array( 'value' => '100%', 'caption' => 'фотоотчёт по размещению' ) ),
		'image' => $img( 'hero-main.webp' ),
	), $home_id );
	update_field( 'uslugi', array( 'items' => array(
		array( 'image' => $img( 'service-pin.png' ), 'number' => '01', 'title' => 'Расклейка объявлений', 'text' => 'Законное размещение на рекламных поверхностях, подъездах и согласованных точках.', 'link' => array( 'url' => get_permalink( $service_id ), 'title' => 'Расклейка объявлений', 'target' => '' ) ),
		array( 'image' => $img( 'service-mail.png' ), 'number' => '02', 'title' => 'Распространение<br>по ящикам', 'text' => 'Адресные программы, нужные тиражи и контроль выполнения.' ),
		array( 'image' => $img( 'service-promo.png' ), 'number' => '03', 'title' => 'Промоакции', 'text' => 'Промоутеры, раздача, работа в местах трафика и отчётность.' ),
	), 'eyebrow' => 'Что делаем', 'title' => 'Услуги офлайн-рекламы для бизнеса<br>и рекламных агентств', 'text' => 'Расклеиваем объявления, распространяем листовки по почтовым ящикам и проводим промоакции. Берём на себя тиражи, адресную программу, исполнителей, контроль и фотоотчёт.' ), $home_id );
	update_field( 'fototchet', array( 'eyebrow' => 'Фотоотчёт', 'title' => 'Фото с размещений<br>и работы исполнителей', 'text' => 'На сайте показаны сцены с рекламными стендами, распространением по ящикам, работой промоутеров и контролем размещения.', 'images' => array( $img( 'gallery-1.webp' ), $img( 'gallery-2.webp' ), $img( 'gallery-3.webp' ) ) ), $home_id );
	update_field( 'doverie', array( 'eyebrow' => 'Доверие', 'title' => 'Нам доверяют компании из Иркутска<br>и федеральные бренды', 'text' => 'В портфолио — регулярные размещения для Дом.ru, Иркутскэнергосбыта, МедСтандарта и других компаний.', 'logos' => array( array( 'image' => $img( 'client-domru.png' ) ), array( 'image' => $img( 'client-irkutskenergosbyt.png' ) ), array( 'image' => $img( 'client-medstandart.png' ) ), array( 'image' => $img( 'client-mntk.png' ) ) ) ), $home_id );
	update_field( 'forma', array( 'title' => 'Нужно разместить рекламу<br>в Иркутске<br>или нескольких городах?', 'text' => 'Отправьте города, тираж и сроки. Рассчитаем стоимость и предложим подходящий формат.', 'shortcode' => $shortcode ), $home_id );
	update_field( 'preimushhestva', array( 'eyebrow' => 'География', 'title' => 'Рекламные поверхности<br>в Иркутске и размещение<br>по городам России', 'text' => 'В Иркутске работаем на собственной сети рекламных поверхностей. Для региональных и федеральных кампаний подключаем исполнителей в других городах.', 'items' => array( array( 'number' => '1117 ГОРОДОВ', 'text' => 'Москва · Санкт-Петербург · Екатеринбург<br>Казань · Новосибирск · Красноярск · Владивосток<br>Иркутск · Ангарск · Братск · ещё 1000+ городов' ), array( 'title' => 'Иркутск', 'number' => '11 688', 'text' => 'рекламных поверхностей<br>у подъездов и жилых домов' ) ) ), $home_id );

	update_field( 'glavnyj_ekran', array( 'eyebrow' => 'Компас Indoor', 'title' => 'Компас Indoor —<br>рекламная группа<br>из Иркутска,<br>работающая по России', 'text' => 'Основные направления — законная расклейка объявлений, распространение полиграфии по почтовым ящикам и промоакции.', 'stats' => array( array( 'value' => '2010', 'caption' => 'начало работы бренда' ), array( 'value' => '1117', 'caption' => 'городов' ), array( 'value' => '4000+', 'caption' => 'исполнителей в сети' ) ), 'image' => $img( 'team.webp' ) ), $about_id );
	update_field( 'rekvizity', array( 'eyebrow' => 'Реквизиты', 'title' => 'Реквизиты<br>ООО «Компас-Индор»', 'text' => 'Юридические данные, адрес и документы компании должны быть доступны на сайте до заключения договора.', 'details' => '<p><strong>ООО «Компас-Индор»</strong><br>ИНН / ОГРН — заполните актуальными данными<br>Юридический адрес — заполните актуальными данными</p>', 'image' => $img( 'office.webp' ) ), $about_id );
	update_field( 'glavnyj_ekran', array( 'eyebrow' => 'Контакты', 'title' => 'Контакты рекламной<br>группы Компас Indoor', 'text' => 'Свяжитесь с нами по телефону или отправьте задачу через форму. Для расчёта укажите город, услугу, тираж и желаемые сроки запуска.', 'button_text' => 'Рассчитать проект', 'image' => $img( 'contact-map.webp' ) ), $contacts_id );
	update_field( 'kontakty', array( 'eyebrow' => 'Связаться', 'title' => 'Связаться с Компас Indoor', 'schedule' => 'Иркутск, Россия<br>Пн–Пт, 09:00–18:00', 'shortcode' => $shortcode ), $contacts_id );
	update_field( 'ofis', array( 'eyebrow' => 'Офис', 'title' => 'Офис Компас Indoor<br>в Иркутске', 'map_label' => 'Map / Irkutsk', 'address' => 'Коммунистическая, 44', 'image' => $img( 'office.webp' ) ), $contacts_id );

	update_field( 'glavnyj_ekran', array( 'eyebrow' => 'Расклейка объявлений', 'title' => 'Законная расклейка<br>объявлений в Иркутске<br>и других городах России', 'text' => 'Размещаем объявления на собственных и согласованных рекламных поверхностях. Подбираем районы и адреса, распределяем тираж, контролируем выполнение и передаём фотоотчёт.', 'button_text' => 'Рассчитать стоимость', 'second_button' => array( 'url' => '#process', 'title' => 'Как проходит контроль', 'target' => '' ), 'stats' => array( array( 'value' => 'от 1', 'caption' => 'города' ), array( 'value' => '100%', 'caption' => 'фотоотчёт' ), array( 'value' => '1117', 'caption' => 'городов' ) ), 'image' => $img( 'hero-service-collage.png' ) ), $service_id );
	update_field( 'mesta', array( 'items' => array(
		array( 'image' => $img( 'place-1-design.png' ), 'title' => 'Подъезды жилых домов', 'text' => 'Для локальных услуг, медицины, ремонта, доставки, фитнеса и других предложений рядом с домом.' ),
		array( 'image' => $img( 'place-2-design.png' ), 'title' => 'Рекламные стенды у входа', 'text' => 'Хорошо работают на постоянный локальный охват и повторные контакты с жителями.' ),
		array( 'image' => $img( 'place-3-design.png' ), 'title' => 'Остановки и места трафика', 'text' => 'Подходят для массовых предложений, акций и услуг с широкой аудиторией.' ),
		array( 'image' => $img( 'place-4-design.png' ), 'title' => 'Районы рядом с точкой продаж', 'text' => 'Охватывают жителей нужных кварталов, офиса или нового объекта.' ),
	), 'eyebrow' => 'Эффективные примеры размещения', 'title' => 'Эффективные места<br>для размещения', 'text' => 'Подбираем точки под задачу бизнеса, район и целевую аудиторию.', 'consult_title' => 'Не знаете, где разместиться?', 'consult_text' => 'Подберём районы, типы поверхностей и составим план размещения под вашу задачу и бюджет.' ), $service_id );
	update_field( 'kontrol', array( 'eyebrow' => 'Контроль', 'title' => 'Фотоотчёт по выполненной расклейке', 'text' => 'После завершения работ передаём фотографии размещений.', 'panel_label' => 'Расклейка / Иркутск', 'panel_value' => '142 / 158 ТОЧЕК', 'panel_text' => '89,9% маршрута подтверждено', 'tags' => array( array( 'text' => 'Фото получено' ), array( 'text' => 'Геометка совпала' ), array( 'text' => 'Контроль подтверждён' ) ), 'image' => $img( 'control-map.webp' ) ), $service_id );

	update_field( 'model_raboty', array( 'eyebrow' => 'Модель', 'title' => 'Как устроена работа Компас Indoor', 'text' => 'Клиент ставит задачу и передаёт тираж. Команда подбирает адресную программу, распределяет материалы, назначает исполнителей, контролирует размещение и готовит отчёт.', 'items' => array( array( 'number' => '01', 'title' => 'Клиент', 'text' => 'задача / города / тираж' ), array( 'number' => '02', 'title' => 'Компас', 'text' => 'единый менеджер и схема запуска', 'dark' => 1 ), array( 'number' => '03', 'title' => 'Сеть', 'text' => 'координаторы и исполнители' ), array( 'number' => '04', 'title' => 'Контроль', 'text' => 'фото, проверка, отчёт' ) ) ), $about_id );
	update_field( 'istoriya', array( 'eyebrow' => 'История', 'title' => 'От локальной расклейки в Иркутске<br>к проектам по всей России', 'items' => array( array( 'year' => '2010', 'text' => 'Старт в Иркутске' ), array( 'year' => '2016', 'text' => 'ООО «Компас-Индор»' ), array( 'year' => '2020', 'text' => 'Рост региональной сети' ), array( 'year' => '2026', 'text' => '1117 городов РФ', 'active' => 1 ) ) ), $about_id );
	update_field( 'dlya_agentstv', array( 'eyebrow' => 'Для агентств', 'title' => 'Работаем с рекламными агентствами<br>как подрядчик по размещению', 'text' => 'Можем выполнить расклейку, распространение и промоакции в рамках проекта агентства, в том числе без прямого контакта с конечным клиентом.', 'items' => array( array( 'title' => 'NDA<br>и конфиденциальность' ), array( 'title' => 'Единая смета на города' ), array( 'title' => 'Контроль исполнения' ), array( 'title' => 'Отчётность под клиента' ) ) ), $about_id );
	update_field( 'principy', array( 'eyebrow' => 'Принципы', 'title' => 'Принципы работы', 'items' => array( array( 'number' => '01', 'title' => 'Законность', 'text' => 'Используем собственные или согласованные поверхности.' ), array( 'number' => '02', 'title' => 'Контроль', 'text' => 'Фиксируем выполнение и передаём фотоотчёт.' ), array( 'number' => '03', 'title' => 'Масштаб', 'text' => 'Одна команда организует проект в Иркутске или одновременно в нескольких городах.' ) ) ), $about_id );

	update_field( 'rekvizity', array( 'eyebrow' => 'Компания', 'title' => 'Реквизиты ООО «Компас-Индор»', 'items' => array( array( 'title' => 'ООО «Компас-Индор»', 'text' => 'Действующее юридическое лицо, Иркутск.' ), array( 'title' => 'ИНН / ОГРН', 'text' => 'Данные заполняются после финальной сверки.' ), array( 'title' => 'Договор и документы', 'text' => 'Данные заполняются после финальной сверки.' ) ) ), $contacts_id );
	update_field( 'prizyv', array( 'title' => 'Нужно запустить рекламу сразу<br>в нескольких городах?', 'text' => 'Пришлите список городов, услугу, тираж и сроки. Подготовим единый расчёт и организуем размещение без поиска отдельных подрядчиков.', 'button_text' => 'Обсудить федеральный запуск' ), $contacts_id );

	update_field( 'etapy', array( 'eyebrow' => 'Что входит', 'title' => 'Расклейка объявлений под ключ:<br>от адресной программы до отчёта', 'text' => 'Подходит для локальной рекламы, массового информирования жителей и регулярного продвижения услуг.', 'items' => array( array( 'number' => '01', 'title' => 'Адресная программа' ), array( 'number' => '02', 'title' => 'Получение тиража' ), array( 'number' => '03', 'title' => 'Подбор исполнителей' ), array( 'number' => '04', 'title' => 'Размещение' ), array( 'number' => '05', 'title' => 'Контроль' ), array( 'number' => '06', 'title' => 'Итоговый отчёт' ) ) ), $service_id );
	update_field( 'formaty', array( 'eyebrow' => 'Форматы', 'title' => 'Где можно размещать объявления', 'text' => 'В Иркутске используем собственные рекламные поверхности у подъездов. В других городах формат размещения согласовывается под конкретную задачу и местные возможности.', 'items' => array( array( 'title' => 'Приподъездные стенды', 'text' => 'Контролируем точки размещения с привязкой к маршруту.' ), array( 'title' => 'Разрешённые доски', 'text' => 'Контролируем точки размещения с привязкой к маршруту.' ), array( 'title' => 'Жилые дома', 'text' => 'Контролируем точки размещения с привязкой к маршруту.' ), array( 'title' => 'Частный сектор', 'text' => 'Контролируем точки размещения с привязкой к маршруту.' ) ) ), $service_id );
	update_field( 'faq', array( 'eyebrow' => 'FAQ', 'title' => 'Частые вопросы о расклейке<br>объявлений', 'items' => array(
		array( 'question' => 'Какой минимальный объём?', 'answer' => 'Условия зависят от города и формата. Мы уточним задачу, предложим подходящий объём и включим контроль в расчёт.' ),
		array( 'question' => 'Можно запустить несколько городов одновременно?', 'answer' => 'Да. Сформируем единую смету, распределим тираж и соберём общий отчёт.' ),
		array( 'question' => 'Предоставляете фотоотчёт?', 'answer' => 'Да, после завершения работ передаём фотографии размещений.' ),
		array( 'question' => 'Можно забрать тираж из типографии?', 'answer' => 'Уточним адрес типографии и включим получение тиража в организацию проекта.' ),
		array( 'question' => 'Работаете с рекламными агентствами?', 'answer' => 'Да, в том числе по NDA и без прямого контакта с конечным клиентом.' ),
	) ), $service_id );
	return true;
}
