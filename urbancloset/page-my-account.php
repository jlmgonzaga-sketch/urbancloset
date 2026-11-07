<?php
/**
 * Dedicated template for the WooCommerce "My Account" page.
 * Full width — no content wrapper, unlike the generic index.php fallback.
 */
get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
  <?php the_content(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>