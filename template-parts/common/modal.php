<?php
$forms = kompas_group( 'forms', 'option' );
$cookies = kompas_group( 'cookies', 'option' );
?>
<div class="modal" role="presentation" hidden><div class="modal__dialog" role="dialog" aria-modal="true" aria-labelledby="estimate-title">
	<button class="modal__close" type="button" aria-label="Закрыть окно">×</button>
	<span class="eyebrow"><?php echo esc_html( $forms['eyebrow'] ?? '' ); ?></span>
	<h2 id="estimate-title"><?php echo esc_html( $forms['title'] ?? '' ); ?></h2>
	<p><?php echo esc_html( $forms['text'] ?? '' ); ?></p>
	<?php if ( ! empty( $forms['popup_shortcode'] ) ) { echo do_shortcode( $forms['popup_shortcode'] ); } ?>
</div></div>
<div class="success-modal" role="presentation" hidden><div class="modal__dialog" role="dialog" aria-modal="true"><button class="modal__close" type="button" aria-label="Закрыть окно">×</button><span class="eyebrow"><?php echo esc_html( $forms['success_eyebrow'] ?? 'Готово' ); ?></span><h2><?php echo esc_html( $forms['success_title'] ?? 'Спасибо!' ); ?></h2><p><?php echo esc_html( $forms['success_text'] ?? '' ); ?></p></div></div>
<aside class="cookie-banner" aria-label="Настройки файлов cookie"><div><b><?php echo esc_html( $cookies['title'] ?? '' ); ?></b><p><?php echo esc_html( $cookies['text'] ?? '' ); ?></p></div><div class="cookie-banner__actions"><button type="button" data-cookie="necessary"><?php echo esc_html( $cookies['necessary_text'] ?? '' ); ?></button><button class="button button--small" type="button" data-cookie="all"><?php echo esc_html( $cookies['accept_text'] ?? '' ); ?></button></div></aside>
<button class="back-to-top" type="button" aria-label="Наверх" title="Наверх">↑</button>
