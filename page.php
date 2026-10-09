<?php
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
?>
<nav class="breadcrumbs" aria-label="Хлебные крошки">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a>
	<span aria-hidden="true">→</span>
	<span aria-current="page"><?php the_title(); ?></span>
</nav>
<article <?php post_class( 'light-card content-page' ); ?>>
	<header class="content-page__header"><h1><?php the_title(); ?></h1></header>
	<div class="content-page__body"><?php the_content(); ?></div>
</article>
<?php
endwhile;
get_footer();
