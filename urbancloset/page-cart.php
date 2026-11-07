<?php
/**
 * Dedicated template for the WooCommerce Cart page.
 * Full width — no content wrapper, same pattern as page-checkout.php —
 * so the Cart block can use its own 1280px two-column layout instead
 * of being squeezed into index.php's 760px content column. Adds the
 * editorial "Your Bag" header above the block; styled in the
 * "CART PAGE" section of style.css.
 */
get_header();

$uc_cart_count = function_exists( 'urbancloset_cart_count' ) ? urbancloset_cart_count() : 0;
?>

<div class="uc-cart-title-wrap">
	<div class="uc-cart-eyebrow"><?php esc_html_e( 'Your Bag', 'urbancloset' ); ?></div>
	<h1 class="uc-cart-h1">Your <em>Bag.</em></h1>
	<div class="uc-cart-item-count">
		<?php
		printf(
			/* translators: %s: zero-padded item count */
			esc_html( _n( '%s Item In Your Collection', '%s Items In Your Collection', max( 1, $uc_cart_count ), 'urbancloset' ) ),
			esc_html( str_pad( $uc_cart_count, 2, '0', STR_PAD_LEFT ) )
		);
		?>
	</div>
</div>

<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
