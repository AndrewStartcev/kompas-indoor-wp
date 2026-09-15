<?php get_header(); ?>
<section class="light-card"><h1><?php the_title(); ?></h1><?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?></section>
<?php get_footer(); ?>
