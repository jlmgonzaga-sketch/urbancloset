<?php
/**
 * UrbanCloset — Buy Now + Make an Offer
 *
 * Add this line to functions.php (once) to activate everything in this file:
 *
 *   require get_template_directory() . '/inc/uc-purchase-actions.php';
 *
 * What this file does, and nothing more:
 *   1. BUY NOW      — reuses WooCommerce's own add-to-cart form; adds a second
 *                      submit button inside it, then redirects to checkout
 *                      after WooCommerce adds the item to the cart. No cart
 *                      or checkout logic is reimplemented.
 *   2. MAKE AN OFFER — a small custom post type (`uc_offer`) stores each
 *                      offer, an AJAX endpoint validates + saves it, and two
 *                      plain-text emails (store + shopper) are sent via wp_mail().
 *   3. ASSETS        — enqueues css/uc-purchase-actions.css and
 *                      js/uc-purchase-actions.js, only on single
 *                      product pages.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** =======================================================================
 * 1. BUY NOW
 * ===================================================================== */

/**
 * WooCommerce's own add-to-cart handler (WC_Form_Handler::add_to_cart_action)
 * only looks at $_REQUEST['add-to-cart']. That value normally comes from the
 * *clicked* submit button, so if the shopper clicks our new Buy Now button
 * instead of the native Add to Cart button, the product id needs to travel
 * as a hidden field instead. This fires just before the quantity field /
 * button, still inside <form class="cart">, for every product type
 * (simple, variable, grouped, external).
 */
add_action( 'woocommerce_before_add_to_cart_button', 'uc_inject_add_to_cart_hidden_id' );
function uc_inject_add_to_cart_hidden_id() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	printf( '<input type="hidden" name="add-to-cart" value="%d">', esc_attr( $product->get_id() ) );
}

/**
 * Render the Buy Now button immediately after the native Add to Cart
 * button, inside the same <form class="cart"> — so one form submission
 * carries quantity + variation + add-to-cart id + buy_now flag together.
 */
add_action( 'woocommerce_after_add_to_cart_button', 'uc_render_buy_now_button' );
function uc_render_buy_now_button() {
	global $product;
	if ( ! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}
	printf(
		'<button type="submit" name="buy_now" value="1" class="uc-buy-now-btn">%s</button>',
		esc_html__( 'Buy Now', 'urbancloset' )
	);
}

/**
 * After WooCommerce successfully adds the item to the cart, send Buy Now
 * shoppers straight to checkout instead of back to the product/cart page.
 */
/* Run last so the Buy Now intent cannot be replaced by a generic cart or
 * front-page redirect supplied by WooCommerce extensions. */
add_filter( 'woocommerce_add_to_cart_redirect', 'uc_buy_now_redirect', PHP_INT_MAX );
function uc_buy_now_redirect( $url ) {
	if ( ! empty( $_REQUEST['buy_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return wc_get_checkout_url();
	}
	return $url;
}

/** =======================================================================
 * 2. MAKE AN OFFER
 * ===================================================================== */

/**
 * Lightweight, non-public CPT used purely as offer storage — visible to
 * admins under a "Product Offers" menu, no front-end templates involved.
 */
add_action( 'init', 'uc_register_offer_cpt' );
function uc_register_offer_cpt() {
	register_post_type(
		'uc_offer',
		array(
			'label'           => __( 'Product Offers', 'urbancloset' ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-tag',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}

add_action( 'wp_ajax_uc_submit_offer', 'uc_handle_offer_submission' );
add_action( 'wp_ajax_nopriv_uc_submit_offer', 'uc_handle_offer_submission' );
function uc_handle_offer_submission() {
	check_ajax_referer( 'uc_submit_offer', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || ! $product->is_purchasable() ) {
		wp_send_json_error( array( 'message' => __( "We couldn't find that product.", 'urbancloset' ) ) );
	}

	$offer_amount = isset( $_POST['offer_amount'] ) ? floatval( $_POST['offer_amount'] ) : 0;
	$name         = isset( $_POST['offer_name'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_name'] ) ) : '';
	$email        = isset( $_POST['offer_email'] ) ? sanitize_email( wp_unslash( $_POST['offer_email'] ) ) : '';
	$phone        = isset( $_POST['offer_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_phone'] ) ) : '';
	$message      = isset( $_POST['offer_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['offer_message'] ) ) : '';

	if ( $offer_amount <= 0 ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid offer amount.', 'urbancloset' ) ) );
	}
	if ( '' === $name ) {
		wp_send_json_error( array( 'message' => __( 'Please enter your name.', 'urbancloset' ) ) );
	}
	if ( '' === $email || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'urbancloset' ) ) );
	}
	if ( '' === $phone ) {
		wp_send_json_error( array( 'message' => __( 'Please enter your phone number.', 'urbancloset' ) ) );
	}

	// Basic throttle: max 5 offers per hour per IP, to deter form spam/abuse.
	$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_key = 'uc_offer_rate_' . md5( $ip );
	$count    = (int) get_transient( $rate_key );
	if ( $count >= 5 ) {
		wp_send_json_error( array( 'message' => __( "You've submitted a few offers already — please try again a little later.", 'urbancloset' ) ) );
	}
	set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );

	$offer_id = wp_insert_post(
		array(
			'post_type'   => 'uc_offer',
			'post_title'  => sprintf( '%1$s — %2$s (%3$s)', $product->get_name(), $name, wp_strip_all_tags( wc_price( $offer_amount ) ) ),
			'post_status' => 'publish',
		)
	);

	if ( is_wp_error( $offer_id ) || ! $offer_id ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong saving your offer. Please try again.', 'urbancloset' ) ) );
	}

	update_post_meta( $offer_id, '_uc_product_id', $product_id );
	update_post_meta( $offer_id, '_uc_offer_amount', $offer_amount );
	update_post_meta( $offer_id, '_uc_offer_name', $name );
	update_post_meta( $offer_id, '_uc_offer_email', $email );
	update_post_meta( $offer_id, '_uc_offer_phone', $phone );
	update_post_meta( $offer_id, '_uc_offer_message', $message );
	update_post_meta( $offer_id, '_uc_offer_status', 'pending' );

	uc_send_offer_notification_emails( $product, $offer_amount, $name, $email, $phone, $message );

	wp_send_json_success( array( 'message' => __( "Offer sent — we'll email you if it's accepted.", 'urbancloset' ) ) );
}

function uc_send_offer_notification_emails( $product, $offer_amount, $name, $email, $phone, $message ) {
	$admin_email = defined( 'UC_EMAIL' ) && is_email( UC_EMAIL )
		? UC_EMAIL
		: get_option( 'admin_email' );
	$site_name   = get_bloginfo( 'name' );
	$product_url = get_permalink( $product->get_id() );
	$offer_price = wp_strip_all_tags( wc_price( $offer_amount ) );
	$list_price  = wp_strip_all_tags( $product->get_price_html() );

	// Notify the store.
	$admin_subject = sprintf( '[%1$s] New offer: %2$s — %3$s', $site_name, $product->get_name(), $offer_price );
	$admin_body    = "A new offer was submitted:\n\n";
	$admin_body   .= 'Product: ' . $product->get_name() . ' (' . $product_url . ")\n";
	$admin_body   .= 'Listed price: ' . $list_price . "\n";
	$admin_body   .= 'Offer: ' . $offer_price . "\n";
	$admin_body   .= 'From: ' . $name . ' <' . $email . ">\n";
	$admin_body   .= 'Phone: ' . $phone . "\n";
	if ( $message ) {
		$admin_body .= "Message:\n" . $message . "\n";
	}
	$admin_headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	wp_mail( $admin_email, $admin_subject, $admin_body, $admin_headers );

	// Confirm to the shopper.
	/* translators: %s: product name */
	$customer_subject = sprintf( __( 'We received your offer on %s', 'urbancloset' ), $product->get_name() );
	$customer_body    = sprintf( "Hi %s,\n\n", $name );
	/* translators: 1: offer amount, 2: product name */
	$customer_body .= sprintf( __( "Thanks for your offer of %1\$s on \"%2\$s\".\n", 'urbancloset' ), $offer_price, $product->get_name() );
	$customer_body .= __( "Our team will review it and follow up by email if it's accepted.\n\n", 'urbancloset' );
	$customer_body .= '— ' . $site_name;
	wp_mail( $email, $customer_subject, $customer_body );
}

/** =======================================================================
 * 3. ASSETS
 * ===================================================================== */
add_action( 'wp_enqueue_scripts', 'uc_enqueue_purchase_action_assets' );
function uc_enqueue_purchase_action_assets() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	wp_enqueue_style(
    'uc-purchase-actions',
    get_template_directory_uri() . '/css/uc-purchase-actions.css',
    array(),
    filemtime( get_template_directory() . '/css/uc-purchase-actions.css' )
);

	wp_enqueue_script(
		'uc-purchase-actions',
		get_template_directory_uri() . '/js/uc-purchase-actions.js',
		array(),
		'1.1',
		true
	);

	wp_localize_script(
		'uc-purchase-actions',
		'ucOfferData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'uc_submit_offer' ),
		)
	);
}
