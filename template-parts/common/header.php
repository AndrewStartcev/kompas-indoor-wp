<?php
$header = kompas_group( 'header', 'option' );
$phone = (string) ( $header['phone'] ?? '' );
?>
<header class="header shell">
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — главная">
		<?php kompas_image( $header['logo_mark'] ?? null, 'full', array( 'class' => 'brand__mark', 'alt' => '' ) ); ?>
		<span class="brand__text">
			<strong><?php echo kompas_heading( $header['brand_name'] ?? '' ); ?></strong>
			<small><?php echo esc_html( $header['brand_caption'] ?? '' ); ?></small>
		</span>
	</a>
	<button class="menu-button" type="button" aria-expanded="false" aria-controls="main-navigation" aria-label="Открыть меню"><span></span><span></span><span></span></button>
	<nav class="nav" id="main-navigation" aria-label="Основная навигация">
		<?php wp_nav_menu( array(
			'theme_location' => 'main',
			'container'      => false,
			'menu_class'     => 'nav__list',
			'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'depth'          => 2,
			'fallback_cb'    => false,
		) ); ?>
	</nav>
	<?php if ( $phone ) : ?><a class="header-phone" href="<?php echo esc_url( kompas_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
	<button class="button button--small" type="button" data-estimate><?php echo esc_html( kompas_cta_label( $header['button_text'] ?? '' ) ); ?></button>
</header>
