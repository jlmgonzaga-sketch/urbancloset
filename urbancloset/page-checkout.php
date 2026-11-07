<?php
/**
 * Dedicated template for the WooCommerce Checkout page.
 * Full width — no content wrapper, unlike the generic index.php fallback —
 * so the Checkout block can use its own 1300px two-column layout (same
 * pattern as the Cart page) instead of being squeezed into index.php's
 * 760px content column. See the "CHECKOUT PAGE" section in style.css.
 */
get_header();
?>
<div class="uc-checkout-title-wrap">
	<h1 class="uc-checkout-h1">Checkout</h1>
	<p class="uc-checkout-sub">Secure checkout &bull; Fast and simple</p>
	<ol class="uc-checkout-progress" aria-label="Checkout progress">
		<li class="is-current"><span>1</span> Details</li>
		<li><span>2</span> Shipping</li>
		<li><span>3</span> Payment</li>
	</ol>
</div>
<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
<?php endwhile; ?>
<?php get_footer(); ?>
