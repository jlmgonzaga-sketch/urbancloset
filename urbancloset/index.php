<?php
/**
 * Fallback template (used for anything that isn't the homepage,
 * e.g. before you've built dedicated templates for Shop/Product/etc.)
 */
get_header();
?>
<div class="section">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="section-head">
      <h1 class="section-title"><?php the_title(); ?></h1>
    </div>
    <div style="max-width:760px;line-height:1.8;">
      <?php the_content(); ?>
    </div>
  <?php endwhile; else : ?>
    <p>Nothing found.</p>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
