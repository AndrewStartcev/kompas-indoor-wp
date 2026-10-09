<?php
$header = kompas_group( 'header', 'option' );
$footer = kompas_group( 'footer', 'option' );
$phone = (string) ( $header['phone'] ?? '' );
$email = (string) ( $header['email'] ?? '' );
?>
<footer class="footer shell dark-card">
	<div class="footer-brand">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php kompas_image( $header['logo_mark'] ?? null, 'full', array( 'class' => 'brand__mark', 'alt' => '' ) ); ?>
			<span class="brand__text"><strong><?php echo kompas_heading( $header['brand_name'] ?? '' ); ?></strong><small><?php echo esc_html( $header['brand_caption'] ?? '' ); ?></small></span>
		</a>
		<strong class="footer-tagline"><?php echo kompas_heading( $footer['tagline'] ?? '' ); ?></strong>
	</div>
	<?php foreach ( (array) ( $footer['columns'] ?? array() ) as $column ) : ?>
		<div><span class="eyebrow"><?php echo esc_html( $column['title'] ?? '' ); ?></span>
		<?php foreach ( (array) ( $column['links'] ?? array() ) as $item ) { kompas_link( $item['link'] ?? array() ); } ?>
		</div>
	<?php endforeach; ?>
	<div><span class="eyebrow">Связаться</span>
		<?php if ( $phone ) : ?><a href="<?php echo esc_url( kompas_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
		<?php if ( $email ) : ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a><?php endif; ?>
		<span><?php echo esc_html( $header['city'] ?? '' ); ?></span>
		<?php $social_vk = $header['social_vk_url'] ?? ''; $social_max = $header['social_max_url'] ?? ''; ?>
		<?php if ( $social_vk || $social_max ) : ?><div class="footer-social" aria-label="Социальные сети">
			<?php if ( $social_vk ) : ?><a href="<?php echo esc_url( $social_vk ); ?>" rel="noopener noreferrer" target="_blank" aria-label="ВКонтакте">ВКонтакте ↗</a><?php endif; ?>
			<?php if ( $social_max ) : ?><a href="<?php echo esc_url( $social_max ); ?>" rel="noopener noreferrer" target="_blank" aria-label="MAX">MAX ↗</a><?php endif; ?>
		</div><?php endif; ?>
	</div>
	<div class="footer-bottom"><span><?php echo esc_html( $footer['copyright'] ?? '' ); ?></span><span><?php echo wp_kses_post( $footer['legal'] ?? '' ); ?></span></div>
</footer>
