<?php
defined( 'ABSPATH' ) || exit;
$review_page_id = isset( $args['page_id'] ) ? (int) $args['page_id'] : 0;
if ( ! $review_page_id ) {
	$review_page = get_page_by_path( 'kejsy' );
	$review_page_id = $review_page ? (int) $review_page->ID : 0;
}
$reviews = $review_page_id && function_exists( 'get_field' ) ? get_field( 'case_reviews', $review_page_id ) : array();
if ( ! is_array( $reviews ) || ! $reviews ) {
	return;
}
?>
<section class="light-card client-reviews" aria-label="Отзывы клиентов">
	<div class="section-heading"><span class="eyebrow">Нам доверяют</span><h2>Что говорят клиенты</h2><p>Отзывы компаний, с которыми мы работали над офлайн-рекламой.</p></div>
	<div class="client-reviews__viewport">
		<div class="client-reviews__track">
			<?php foreach ( array( 0, 1 ) as $copy ) : ?>
				<div class="client-reviews__set"<?php if ( $copy ) : ?> aria-hidden="true"<?php endif; ?>>
				<?php foreach ( $reviews as $review ) : ?>
					<blockquote class="client-review">
						<div class="client-review__client">
							<?php if ( ! empty( $review['logo'] ) ) : ?><?php kompas_image( $review['logo'], 'medium', array( 'loading' => 'lazy' ) ); ?><?php endif; ?>
							<strong><?php echo esc_html( $review['client'] ?? '' ); ?></strong>
						</div>
						<p><?php echo esc_html( $review['quote'] ?? '' ); ?></p>
						<footer><b><?php echo esc_html( $review['person'] ?? '' ); ?></b><small><?php echo esc_html( $review['role'] ?? '' ); ?></small></footer>
					</blockquote>
				<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
