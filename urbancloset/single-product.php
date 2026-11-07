<?php
/**
 * UrbanCloset — WooCommerce Single Product
 *
 * This is a VISUAL/TEMPLATE redesign only. It does not reimplement
 * WooCommerce functionality — it reuses:
 *   - the existing theme header/footer (get_header / get_footer)
 *   - WooCommerce's native gallery + zoom/lightbox JS (via the
 *     wc-product-gallery-* theme supports added in functions.php)
 *   - WooCommerce's native add-to-cart template (simple / variable /
 *     grouped / stock status all handled by WooCommerce core)
 *   - the existing .product-card / .product-grid system for
 *     "You May Also Like", via the shared urbancloset_render_product_card()
 *     helper already used on Shop All and the homepage.
 *
 * PURCHASE ACTIONS
 * -----------------
 * Buy Now and Make an Offer are added by inc/uc-purchase-actions.php
 * (hooked into WooCommerce's own add-to-cart form via
 * woocommerce_before_add_to_cart_button / woocommerce_after_add_to_cart_button),
 * so no cart/checkout logic is duplicated here. This template only:
 *   - renders the Make an Offer trigger button (Buy Now is injected by the hook)
 *   - renders the Make an Offer modal markup once, for this product
 *
 * PRODUCT DETAILS
 * -----------------
 * The native WooCommerce tabs (Description / Additional Information /
 * Reviews) have been removed. The product description now lives at the
 * top of the "Product Details" box instead, above the spec rows —
 * Description is the only tab content carried over; Additional
 * Information and Reviews are intentionally dropped from this page.
 *
 * No sidebar is called anywhere in this file, which is what removes the
 * default WooCommerce/WordPress sidebar widgets (Search, Pages, Cart,
 * Checkout, My Account, Shop, Archives, Categories) from this page.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

if ( have_posts() ) :
	while ( have_posts() ) : the_post();

		global $product;
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}
		if ( ! $product ) {
			continue;
		}

		// Fires WooCommerce's standard notices (add-to-cart messages, errors).
		do_action( 'woocommerce_before_single_product' );

		$brand_terms = wc_get_product_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) );

		// Make an Offer is only offered on purchasable, price-bearing product
		// types — not grouped products (no single price) or external products
		// (checkout happens off-site).
		$uc_offer_eligible = $product->is_purchasable() && ( $product->is_type( 'simple' ) || $product->is_type( 'variable' ) );
		?>

		<!-- BREADCRUMB -->
		<div class="uc-product-wrap">
			<div class="uc-breadcrumb">
				<?php
				woocommerce_breadcrumb( array(
					'delimiter'   => '&nbsp;/&nbsp;',
					'wrap_before' => '',
					'wrap_after'  => '',
					'before'      => '',
					'after'       => '',
				) );
				?>
			</div>
		</div>

		<!-- GALLERY + SUMMARY -->
		<div class="uc-product-wrap">
			<div class="uc-single-product-main">

				<div class="uc-single-product-gallery">
					<?php
					// Renders WooCommerce's native gallery markup + sale flash.
					// Thumbnail-click / zoom behavior comes from WooCommerce's
					// own gallery scripts — nothing custom to build here.
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>

				<div class="uc-single-product-summary">

					<?php if ( $brand_terms ) : ?>
						<div class="uc-brand"><?php echo esc_html( $brand_terms[0] ); ?></div>
					<?php endif; ?>

					<?php woocommerce_template_single_title(); ?>

					<div class="uc-condition-badge">
						<?php echo esc_html( urbancloset_product_condition( $product ) ); ?>
					</div>

					<?php woocommerce_template_single_rating(); ?>

					<?php woocommerce_template_single_price(); ?>

					<?php if ( $product->get_short_description() ) : ?>
						<div class="uc-short-desc">
							<?php woocommerce_template_single_excerpt(); ?>
						</div>
					<?php endif; ?>

					<!--
						PURCHASE ACTIONS — three-tier hierarchy:
						  1. Buy Now       (solid, highest emphasis)   — injected into
						                    the WooCommerce cart form by
						                    uc_render_buy_now_button() via the
						                    woocommerce_after_add_to_cart_button hook
						  2. Add to Cart   (outline, secondary)        — WooCommerce native
						  3. Make an Offer (text link, lowest emphasis) — opens the modal below
					-->
					<div class="uc-purchase-actions" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
						<?php woocommerce_template_single_add_to_cart(); ?>

						<?php if ( $uc_offer_eligible ) : ?>
							<button type="button" class="uc-make-offer-link" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php esc_html_e( 'Make an Offer', 'urbancloset' ); ?>
							</button>
						<?php endif; ?>
					</div>

					<div class="uc-product-contact">
						<p><?php esc_html_e( 'For any questions about this product or extra support, please contact our Personal Shopping Manager.', 'urbancloset' ); ?></p>
						<a
							class="uc-product-contact-btn"
							href="https://wa.me/<?php echo esc_attr( UC_WHATSAPP_NUMBER ); ?>?text=<?php echo esc_attr( rawurlencode( sprintf( 'Hi, I have a question about %s.', $product->get_name() ) ) ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16 3a13 13 0 0 0-11.2 19.6L3 29l6.6-1.7A13 13 0 1 0 16 3Zm0 23.7a10.7 10.7 0 0 1-5.4-1.5l-.4-.2-3.9 1 1-3.8-.3-.4A10.7 10.7 0 1 1 16 26.7Zm5.9-8c-.3-.2-1.8-.9-2.1-1s-.5-.2-.7.2-.8 1-1 1.2-.4.3-.7.1a8.7 8.7 0 0 1-2.6-1.6 9.8 9.8 0 0 1-1.8-2.3c-.2-.3 0-.5.1-.6l.5-.6c.2-.2.2-.4.3-.6s0-.5-.1-.7l-1-2.3c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.7.1-1 .5s-1.4 1.3-1.4 3.2 1.5 3.7 1.7 4 .1.3.4.5a14.3 14.3 0 0 0 5.5 4.8c.8.3 1.5.6 2 .7.8.3 1.6.2 2.2.1.7-.1 1.8-.7 2-1.4s.2-1.3.2-1.4-.2-.3-.5-.5Z"/></svg>
							<?php esc_html_e( 'Contact Now', 'urbancloset' ); ?>
						</a>
					</div>

					<!--
						PRODUCT DETAILS — description (if any) now sits at the top
						of this box, followed by a divider, then the existing spec
						rows (Condition / Brand / attributes / Category / SKU).
						Replaces the old separate tabs section below.
					-->
					<div class="uc-single-product-details">
						<div class="uc-details-title">Product Details</div>

						<?php if ( $product->get_description() ) : ?>
							<div class="uc-details-description">
								<?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
							</div>
							<div class="uc-details-divider"></div>
						<?php endif; ?>

						<div class="uc-details-row">
							<span class="uc-details-label">Condition</span>
							<span class="uc-details-value"><?php echo esc_html( urbancloset_product_condition( $product ) ); ?></span>
						</div>

						<?php if ( $brand_terms ) : ?>
							<div class="uc-details-row">
								<span class="uc-details-label">Brand</span>
								<span class="uc-details-value"><?php echo esc_html( implode( ', ', $brand_terms ) ); ?></span>
							</div>
						<?php endif; ?>

						<?php
						// Every other product attribute, pulled dynamically — works
						// for any product regardless of which attributes it has.
						$attributes = $product->get_attributes();
						foreach ( $attributes as $attribute ) :
							$attr_name = $attribute->get_name();
							if ( in_array( $attr_name, array( 'condition', 'pa_condition' ), true ) ) {
								continue; // already shown above
							}
							if ( $attribute->is_taxonomy() ) {
								$terms = wc_get_product_terms( $product->get_id(), $attr_name, array( 'fields' => 'names' ) );
								$value = implode( ', ', $terms );
							} else {
								$value = implode( ', ', $attribute->get_options() );
							}
							if ( '' === $value ) {
								continue;
							}
							?>
							<div class="uc-details-row">
								<span class="uc-details-label"><?php echo esc_html( wc_attribute_label( $attr_name ) ); ?></span>
								<span class="uc-details-value"><?php echo esc_html( $value ); ?></span>
							</div>
						<?php endforeach; ?>

						<?php
						$categories = get_the_terms( $product->get_id(), 'product_cat' );
						if ( $categories && ! is_wp_error( $categories ) ) :
							?>
							<div class="uc-details-row">
								<span class="uc-details-label">Category</span>
								<span class="uc-details-value"><?php echo esc_html( implode( ', ', wp_list_pluck( $categories, 'name' ) ) ); ?></span>
							</div>
						<?php endif; ?>

						<?php if ( $product->get_sku() ) : ?>
							<div class="uc-details-row">
								<span class="uc-details-label">SKU</span>
								<span class="uc-details-value"><?php echo esc_html( $product->get_sku() ); ?></span>
							</div>
						<?php endif; ?>
					</div>

					<div class="uc-authenticity">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
						<div>
							<div class="uc-authenticity-title">Authenticity Guaranteed</div>
							<div class="uc-authenticity-text">Every piece is hand-inspected and verified authentic before it reaches you — preloved designer, never compromised.</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<!-- MAKE AN OFFER MODAL
		     Rendered once per page load (single product = single $product).
		     Submission is handled entirely by assets/js/uc-purchase-actions.js
		     posting to admin-ajax.php; see inc/uc-purchase-actions.php. -->
		<?php if ( $uc_offer_eligible ) : ?>
			<div class="uc-offer-modal-overlay" id="uc-offer-modal-overlay">
				<div class="uc-offer-modal" role="dialog" aria-modal="true" aria-labelledby="uc-offer-modal-title">
					<button type="button" class="uc-offer-modal-close" id="uc-offer-modal-close" aria-label="<?php esc_attr_e( 'Close', 'urbancloset' ); ?>">&times;</button>

					<div class="uc-offer-modal-body" id="uc-offer-modal-body">
						<div class="uc-offer-eyebrow"><?php esc_html_e( 'Make an Offer', 'urbancloset' ); ?></div>
						<h3 id="uc-offer-modal-title" class="uc-offer-modal-title"><?php echo esc_html( $product->get_name() ); ?></h3>
						<p class="uc-offer-modal-price">
							<?php
							/* translators: %s: product price HTML */
							printf( esc_html__( 'Listed at %s', 'urbancloset' ), $product->get_price_html() );
							?>
						</p>

						<form id="uc-offer-form" class="uc-offer-form">
							<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">

							<div class="uc-offer-row">
								<label for="uc-offer-amount"><?php esc_html_e( 'Offer Price', 'urbancloset' ); ?></label>
								<div class="uc-offer-amount-wrap">
									<span class="uc-offer-currency"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
									<input type="number" step="0.01" min="1" id="uc-offer-amount" name="offer_amount" required>
								</div>
							</div>

							<div class="uc-offer-row">
								<label for="uc-offer-name"><?php esc_html_e( 'Full Name', 'urbancloset' ); ?></label>
								<input type="text" id="uc-offer-name" name="offer_name"
									value="<?php echo esc_attr( is_user_logged_in() ? wp_get_current_user()->display_name : '' ); ?>" required>
							</div>

							<div class="uc-offer-row">
								<label for="uc-offer-email"><?php esc_html_e( 'Email', 'urbancloset' ); ?></label>
								<input type="email" id="uc-offer-email" name="offer_email"
									value="<?php echo esc_attr( is_user_logged_in() ? wp_get_current_user()->user_email : '' ); ?>" required>
							</div>

							<div class="uc-offer-row">
								<label for="uc-offer-phone"><?php esc_html_e( 'Phone Number', 'urbancloset' ); ?></label>
								<input type="tel" id="uc-offer-phone" name="offer_phone" autocomplete="tel" required>
							</div>

							<div class="uc-offer-row">
								<label for="uc-offer-message"><?php esc_html_e( 'Message', 'urbancloset' ); ?> <span class="uc-optional">(<?php esc_html_e( 'optional', 'urbancloset' ); ?>)</span></label>
								<textarea id="uc-offer-message" name="offer_message" rows="3"></textarea>
							</div>

							<div class="uc-offer-error" id="uc-offer-error" hidden></div>

							<button type="submit" class="uc-offer-submit" id="uc-offer-submit">
								<span class="uc-offer-submit-text"><?php esc_html_e( 'Send Offer', 'urbancloset' ); ?></span>
							</button>

							<p class="uc-offer-disclaimer"><?php esc_html_e( "Submitting an offer does not guarantee acceptance. We'll email you if it's approved.", 'urbancloset' ); ?></p>
						</form>

						<div class="uc-offer-success" id="uc-offer-success" hidden>
							<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6L9 17l-5-5"/></svg>
							<div class="uc-offer-success-title"><?php esc_html_e( 'Offer Sent', 'urbancloset' ); ?></div>
							<p class="uc-offer-success-text"><?php esc_html_e( "We've received your offer and will email you if the seller accepts.", 'urbancloset' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<!-- YOU MAY ALSO LIKE
		     Uses WooCommerce's own related-products query, rendered with
		     the same urbancloset_render_product_card() used on Shop All —
		     no second product-card system. -->
		<?php
		$related_ids = wc_get_related_products( $product->get_id(), 4 );
		if ( $related_ids ) :
			?>
			<section class="section uc-single-product-related">
				<div class="uc-product-wrap">
					<div class="section-head">
						<div class="section-sub">You May Also Like</div>

					</div>
					<div class="product-grid product-grid-shop">
						<?php
						foreach ( $related_ids as $related_id ) {
							$related_product = wc_get_product( $related_id );
							urbancloset_render_product_card( $related_product );
						}
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_single_product' ); ?>

	<?php endwhile;
else :
	?>
	<div class="uc-product-wrap">
		<p><?php esc_html_e( 'This product could not be found.', 'urbancloset' ); ?></p>
	</div>
	<?php
endif;

get_footer();
