<?php
/**
 * UrbanCloset theme functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── CONTACT SETTINGS — edit these to update site-wide ───────────── */
define( 'UC_WHATSAPP_NUMBER', '96590910123' );      // digits only, no + or spaces
define( 'UC_PHONE_DISPLAY',   '+965 9091 0123' );
define( 'UC_EMAIL',           'urbanclosetkuwait@gmail.com' );
define( 'UC_ADDRESS',         'Kuwait, Salmiya, Salem Al Mubarak Street' );

/* ── PURCHASE ACTIONS (Single Product page) ───────────────────────
 * Buy Now redirect + Make an Offer (AJAX form, CPT storage, emails).
 * Lives in its own file so it doesn't get lost in here — see
 * inc/uc-purchase-actions.php for everything it does.
 */
require get_template_directory() . '/inc/uc-purchase-actions.php';

/* ── ACCOUNT MODAL — Sign In / Create Account / Account menu / Sign
 * Out as an overlay modal instead of a page load. AJAX handlers in
 * this file call WordPress/WooCommerce's own wp_signon() /
 * wc_create_new_customer() / wp_logout() — no separate auth system.
 * See inc/uc-account-modal.php for details.
 */
require get_template_directory() . '/inc/uc-account-modal.php';

/* ── CONSIGNMENT FORM ─────────────────────────────────────────────
 * Sends a complete submission, including approved image attachments, to the
 * store contact address. This avoids exposing a recipient address in markup.
 */
add_action( 'admin_post_nopriv_uc_submit_consignment', 'urbancloset_submit_consignment' );
add_action( 'admin_post_uc_submit_consignment', 'urbancloset_submit_consignment' );
/* A cached homepage can contain an expired nonce. Refresh it through the
 * uncached admin-ajax endpoint whenever the visitor opens the form. */
add_action( 'wp_ajax_nopriv_uc_consignment_nonce', 'urbancloset_consignment_nonce' );
add_action( 'wp_ajax_uc_consignment_nonce', 'urbancloset_consignment_nonce' );
function urbancloset_consignment_nonce() {
	 nocache_headers();
	 wp_send_json_success( array( 'nonce' => wp_create_nonce( 'uc_submit_consignment' ) ) );
}
function urbancloset_submit_consignment() {
	if ( ! isset( $_POST['uc_consignment_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['uc_consignment_nonce'] ) ), 'uc_submit_consignment' ) ) {
		wp_die( esc_html__( 'Your form session has expired. Please return to the form and try again.', 'urbancloset' ), 403 );
	}

	$name         = isset( $_POST['consignment_name'] ) ? sanitize_text_field( wp_unslash( $_POST['consignment_name'] ) ) : '';
	$email        = isset( $_POST['consignment_email'] ) ? sanitize_email( wp_unslash( $_POST['consignment_email'] ) ) : '';
	$phone        = isset( $_POST['consignment_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['consignment_phone'] ) ) : '';
	$authenticity = isset( $_POST['consignment_authenticity'] ) ? sanitize_text_field( wp_unslash( $_POST['consignment_authenticity'] ) ) : '';
	$original     = isset( $_POST['consignment_original_price'] ) ? sanitize_text_field( wp_unslash( $_POST['consignment_original_price'] ) ) : '';
	$offer        = isset( $_POST['consignment_price_offer'] ) ? sanitize_text_field( wp_unslash( $_POST['consignment_price_offer'] ) ) : '';
	$signature    = isset( $_POST['consignment_signature_data'] ) ? wp_unslash( $_POST['consignment_signature_data'] ) : '';
	$photos       = isset( $_FILES['consignment_photos'] ) ? $_FILES['consignment_photos'] : null;
	$proofs       = isset( $_FILES['consignment_authenticity_proof'] ) ? $_FILES['consignment_authenticity_proof'] : null;

	if ( '' === $name || ! is_email( $email ) || '' === $phone || '' === $authenticity || '' === $offer || ! is_array( $photos ) || empty( $photos['name'][0] ) || 0 !== strpos( $signature, 'data:image/png;base64,' ) || strlen( $signature ) > 1500000 ) {
		urbancloset_consignment_redirect( 'error' );
	}

	$allowed_mimes = array( 'jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' );
	$attachments   = array();
	$photo_count   = min( count( $photos['name'] ), 6 );
	require_once ABSPATH . 'wp-admin/includes/file.php';

	for ( $i = 0; $i < $photo_count; $i++ ) {
		if ( UPLOAD_ERR_OK !== (int) $photos['error'][ $i ] || (int) $photos['size'][ $i ] > 5 * MB_IN_BYTES ) {
			continue;
		}
		$file = array(
			'name'     => sanitize_file_name( $photos['name'][ $i ] ),
			'type'     => $photos['type'][ $i ],
			'tmp_name' => $photos['tmp_name'][ $i ],
			'error'    => $photos['error'][ $i ],
			'size'     => $photos['size'][ $i ],
		);
		$uploaded = wp_handle_upload( $file, array( 'test_form' => false, 'mimes' => $allowed_mimes ) );
		if ( empty( $uploaded['error'] ) && ! empty( $uploaded['file'] ) ) {
			$attachments[] = $uploaded['file'];
		}
	}

	if ( empty( $attachments ) ) {
		urbancloset_consignment_redirect( 'error' );
	}

	/* Add up to three optional receipts, certificates, cards, or other proof. */
	if ( is_array( $proofs ) && ! empty( $proofs['name'][0] ) ) {
		$proof_mimes = $allowed_mimes + array( 'pdf' => 'application/pdf' );
		$proof_count = min( count( $proofs['name'] ), 3 );
		for ( $i = 0; $i < $proof_count; $i++ ) {
			if ( UPLOAD_ERR_OK !== (int) $proofs['error'][ $i ] || (int) $proofs['size'][ $i ] > 5 * MB_IN_BYTES ) continue;
			$proof = array( 'name' => sanitize_file_name( $proofs['name'][ $i ] ), 'type' => $proofs['type'][ $i ], 'tmp_name' => $proofs['tmp_name'][ $i ], 'error' => $proofs['error'][ $i ], 'size' => $proofs['size'][ $i ] );
			$uploaded_proof = wp_handle_upload( $proof, array( 'test_form' => false, 'mimes' => $proof_mimes ) );
			if ( empty( $uploaded_proof['error'] ) && ! empty( $uploaded_proof['file'] ) ) $attachments[] = $uploaded_proof['file'];
		}
	}

	/* The canvas signature is stored as a PNG solely to attach to this email. */
	$signature_binary = base64_decode( substr( $signature, strlen( 'data:image/png;base64,' ) ), true );
	if ( false === $signature_binary || strlen( $signature_binary ) < 100 ) urbancloset_consignment_redirect( 'error' );
	$signature_upload = wp_upload_bits( 'consignment-signature-' . time() . '.png', null, $signature_binary );
	if ( ! empty( $signature_upload['error'] ) || empty( $signature_upload['file'] ) ) urbancloset_consignment_redirect( 'error' );
	$attachments[] = $signature_upload['file'];

	$recipient = apply_filters( 'urbancloset_consignment_recipient', UC_EMAIL );
	$subject   = sprintf( '[%s] New consignment request from %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $name );
	$message   = "A new consignment request was submitted.\n\n";
	$message  .= "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n";
	$message  .= "Authenticity: {$authenticity}\nOriginal price: " . ( $original ? $original : 'Not provided' ) . "\nPrice offer: {$offer}\n";
	$message  .= "Online signature: attached as PNG.\n\nAttached: " . count( $attachments ) . " file(s), including bag photos, any authenticity proof, and signature.\n";
	$headers   = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	if ( ! wp_mail( $recipient, $subject, $message, $headers, $attachments ) ) {
		urbancloset_consignment_redirect( 'error' );
	}

	wp_mail( $email, 'We received your consignment request', "Hi {$name},\n\nThank you for your consignment request. Our team will review your bag and follow up by email.\n\n— " . get_bloginfo( 'name' ) );
	urbancloset_consignment_redirect( 'success' );
}

function urbancloset_consignment_redirect( $status ) {
	$destination = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$destination = remove_query_arg( 'consignment', $destination );
	wp_safe_redirect( add_query_arg( 'consignment', $status, $destination ) . '#consignment-form' );
	exit;
}

/* ── SHIPPING ────────────────────────────────────────────────────
 * Two deliberately separate methods are shown at checkout. WooCommerce
 * selects the applicable one from the shipping address: Kuwait customers
 * see Local Kuwait first; every other country sees International. The fees
 * are editable in WooCommerce → Settings → Shipping.
 */
add_action( 'woocommerce_shipping_init', 'urbancloset_register_shipping_methods' );
function urbancloset_register_shipping_methods() {
	if ( class_exists( 'WC_Shipping_Method' ) ) {
		class UC_Shipping_Local_Kuwait extends WC_Shipping_Method {
			public function __construct() {
				$this->id = 'uc_local_kuwait';
				$this->method_title = __( 'Local Kuwait', 'urbancloset' );
				$this->method_description = __( 'Delivery within Kuwait.', 'urbancloset' );
				$this->supports = array( 'shipping-zones', 'instance-settings' );
				$this->init();
			}
			public function init() {
				$this->init_form_fields();
				$this->init_settings();
				$this->title = $this->get_option( 'title', __( 'Local Kuwait', 'urbancloset' ) );
				$this->cost = (float) $this->get_option( 'cost', '0' );
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			}
			public function init_form_fields() {
				$this->form_fields = array(
					'title' => array( 'title' => __( 'Method title', 'urbancloset' ), 'type' => 'text', 'default' => __( 'Local Kuwait', 'urbancloset' ) ),
					'cost'  => array( 'title' => __( 'Delivery fee', 'urbancloset' ), 'type' => 'price', 'default' => '0', 'description' => __( 'Enter the Kuwait delivery fee.', 'urbancloset' ) ),
				);
			}
			public function is_available( $package ) {
				return isset( $package['destination']['country'] ) && 'KW' === strtoupper( $package['destination']['country'] );
			}
			public function calculate_shipping( $package = array() ) {
				$this->add_rate( array( 'id' => $this->get_rate_id(), 'label' => $this->title, 'cost' => $this->cost, 'package' => $package ) );
			}
		}

		class UC_Shipping_International extends UC_Shipping_Local_Kuwait {
			public function __construct() {
				$this->id = 'uc_international';
				$this->method_title = __( 'International', 'urbancloset' );
				$this->method_description = __( 'Delivery outside Kuwait.', 'urbancloset' );
				$this->supports = array( 'shipping-zones', 'instance-settings' );
				$this->init();
			}
			public function init_form_fields() {
				$this->form_fields = array(
					'title' => array( 'title' => __( 'Method title', 'urbancloset' ), 'type' => 'text', 'default' => __( 'International', 'urbancloset' ) ),
					'cost'  => array( 'title' => __( 'Delivery fee', 'urbancloset' ), 'type' => 'price', 'default' => '0', 'description' => __( 'Enter the international delivery fee.', 'urbancloset' ) ),
				);
			}
			public function is_available( $package ) {
				return ! empty( $package['destination']['country'] ) && 'KW' !== strtoupper( $package['destination']['country'] );
			}
		}
	}
}
add_filter( 'woocommerce_shipping_methods', 'urbancloset_add_shipping_methods' );
function urbancloset_add_shipping_methods( $methods ) {
	$methods['uc_local_kuwait'] = 'UC_Shipping_Local_Kuwait';
	$methods['uc_international'] = 'UC_Shipping_International';
	return $methods;
}

/* ── THEME SETUP ───────────────────────────────────────────────── */
function urbancloset_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	/*
	 * Single Product page: these three enable WooCommerce's own gallery
	 * scripts (zoom / lightbox / thumbnail slider) so the Single Product
	 * template can reuse core gallery behavior instead of custom JS.
	 * They only affect pages that render a WooCommerce product gallery.
	 */
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Drawer Menu', 'urbancloset' ),
	) );
}
add_action( 'after_setup_theme', 'urbancloset_setup' );

/* ── DEFAULT COLLECTIONS & DESIGNER BRANDS ───────────────────────
 * Seed the store's starter terms once, without replacing or deleting
 * existing terms or changing any product assignments. These terms appear
 * in the drawer and Shop All filters, including while they have no products.
 */
function urbancloset_register_brand_taxonomy_if_missing() {
	if ( ! taxonomy_exists( 'product_brand' ) && post_type_exists( 'product' ) ) {
		$labels = array(
			'name'          => __( 'Brands', 'urbancloset' ),
			'singular_name' => __( 'Brand', 'urbancloset' ),
			'menu_name'     => __( 'Brands', 'urbancloset' ),
			'all_items'     => __( 'All Brands', 'urbancloset' ),
			'edit_item'     => __( 'Edit Brand', 'urbancloset' ),
			'add_new_item'  => __( 'Add New Brand', 'urbancloset' ),
		);
		register_taxonomy( 'product_brand', array( 'product' ), array(
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'brand' ),
		) );
	}
}
add_action( 'init', 'urbancloset_register_brand_taxonomy_if_missing', 30 );

function urbancloset_seed_store_terms() {
	if ( ! taxonomy_exists( 'product_cat' ) || ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}

	$top_level_categories = array(
		'Preloved Authentic Bags', 'Preloved Authentic Jewelry', 'Preloved Authentic Shoes', 'Other',
	);
	foreach ( $top_level_categories as $name ) {
		$existing = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'name' => $name, 'number' => 1 ) );
		if ( ( is_wp_error( $existing ) || empty( $existing ) ) ) {
			wp_insert_term( $name, 'product_cat', array( 'parent' => 0 ) );
		}
	}

	$brands = array(
		'HERMES', 'ALEXANDER WANG', 'ARMANI', 'BALENCIAGA', 'BOTTEGA', 'BURBERRY',
		'BVLGARI', 'CAROLINA HERRERA', 'CELINE', 'CHANEL', 'CHLOE', 'CHRISTIAN DIOR',
		'COACH', 'DOLCE & GABBANA', 'FENDI', 'FERRAGAMO', 'GOYARD', 'GUCCI', 'LOEWE',
		'LOUIS VUITTON', 'MCM', 'MIU MIU', 'PRADA', 'VALENTINO', 'YSL',
	);
	foreach ( $brands as $name ) {
		$slug = sanitize_title( $name );
		if ( ! term_exists( $slug, 'product_brand' ) ) {
			wp_insert_term( $name, 'product_brand', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'init', 'urbancloset_seed_store_terms', 40 );

/* ── STYLES & SCRIPTS ──────────────────────────────────────────── */
function urbancloset_assets() {
	wp_enqueue_style( 'uc-fonts', 'https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,ital,wght@6..96,0,400;6..96,0,500;6..96,0,600;6..96,1,400;6..96,1,500&family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500&display=swap', array(), null );
	wp_enqueue_style( 'urbancloset-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
	/*
	 * The product cards are custom theme markup, so explicitly load
	 * WooCommerce's AJAX cart scripts. Without this, the AJAX classes on
	 * custom cards have no WooCommerce click handler on the front page.
	 */
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	wp_enqueue_script( 'urbancloset-main', get_template_directory_uri() . '/js/main.js', array(), filemtime( get_template_directory() . '/js/main.js' ), true );
	wp_enqueue_script( 'urbancloset-currency-switcher', get_template_directory_uri() . '/js/currency-switcher.js', array(), filemtime( get_template_directory() . '/js/currency-switcher.js' ), true );
	if ( is_front_page() ) {
		wp_enqueue_script( 'urbancloset-consignment-form', get_template_directory_uri() . '/js/consignment-form.js', array(), filemtime( get_template_directory() . '/js/consignment-form.js' ), true );
		wp_localize_script( 'urbancloset-consignment-form', 'UCConsignment', array( 'nonceUrl' => admin_url( 'admin-ajax.php?action=uc_consignment_nonce' ) ) );
	}

	wp_localize_script( 'urbancloset-main', 'UC', array(
		'whatsapp'  => UC_WHATSAPP_NUMBER,
		'cartUrl'   => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ),
		'accountUrl'=> function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ),
		'shopUrl'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'urbancloset_assets' );

/* ── WOOCOMMERCE IMAGE / LAYOUT TWEAKS ────────────────────────────
 * We render our own markup in front-page.php, so strip WooCommerce's
 * default wrappers to avoid duplicate titles/breadcrumbs there.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/* ── CART COUNT (for the nav bag icon) ────────────────────────── */
function urbancloset_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return WC()->cart->get_cart_contents_count();
	}
	return 0;
}

/* ── HELPER: get latest products for "New Arrivals" ───────────────
 * Uses the newest published WooCommerce products (max 5, to match design).
 */
function urbancloset_get_new_arrivals( $count = 5 ) {
	if ( ! class_exists( 'WooCommerce' ) ) return array();

	$query = new WP_Query( array(
		'post_type'      => 'product',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	) );
	return $query->posts;
}

/* ── HELPER: condition badge text from a product attribute ───────
 * Reads a custom product attribute "condition" if set (e.g. "Excellent",
 * "Very Good"). Falls back to "Excellent" if not set.
 */
function urbancloset_product_condition( $product ) {
	$condition = $product->get_attribute( 'condition' );
	return $condition ? $condition : 'Excellent';
}

/* ── HELPER: "Buy Now" URL for a product-grid card ────────────────
 * Simple, purchasable, in-stock products use a dedicated Buy Now endpoint
 * that adds the item then sends the shopper to Checkout. The single-product
 * Buy Now form uses the same checkout destination through its redirect
 * filter in inc/uc-purchase-actions.php.
 *
 * Variable / grouped / external products can't skip straight to
 * checkout from a grid card (a variation must be chosen first), so
 * those send the shopper to the product page, where the existing
 * single-product Buy Now button already enforces variation selection.
 */
function urbancloset_buy_now_url( $product ) {
	if ( $product->is_type( 'simple' ) ) {
		/* Use our checkout endpoint rather than WooCommerce's ordinary
		 * add-to-cart redirect. This avoids another redirect (from a plugin or
		 * the current page) replacing the intended Checkout destination. */
		return add_query_arg( 'uc-buy-now', $product->get_id(), home_url( '/' ) );
	}
	return get_permalink( $product->get_id() );
}

/* Handle a Buy Now click from a product grid: add one simple product and
 * always continue to checkout. Variation products still go to their product
 * page so the shopper can select the required options. */
add_action( 'template_redirect', 'urbancloset_handle_grid_buy_now', 1 );
function urbancloset_handle_grid_buy_now() {
	if ( empty( $_GET['uc-buy-now'] ) || ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$product_id = absint( $_GET['uc-buy-now'] );
	$product    = wc_get_product( $product_id );

	/* A repeat Buy Now should still open Checkout. Do this before checking
	 * stock, because a one-of-one item may become unavailable after it was
	 * already placed in this shopper's cart. */
	if ( $product && WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $product_id ) ) ) {
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}

	if ( ! $product || ! $product->is_type( 'simple' ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		wp_safe_redirect( $product ? $product->get_permalink() : home_url( '/' ) );
		exit;
	}

	if ( WC()->cart->add_to_cart( $product->get_id(), 1 ) ) {
		wc_add_to_cart_message( array( $product->get_id() => 1 ), true );
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}

	wp_safe_redirect( $product->get_permalink() );
	exit;
}

/* ── SHOP PAGE: helper to render a WooCommerce product card ──────
 * Shared by front-page.php's New Arrivals, archive-product.php, and
 * single-product.php's related products, so all stay visually identical.
 *
 * Add to Cart now uses WooCommerce's OWN ajax-add-to-cart button markup
 * (class="add_to_cart_button ajax_add_to_cart" + data-product_id) instead
 * of a plain redirect. WooCommerce already enqueues its add-to-cart.js
 * sitewide, so this taps directly into the existing AJAX flow and the
 * existing `added_to_cart` jQuery event — no second add-to-cart system,
 * see the "GOING TO BAG" section of main.js for the animation hook.
 */
function urbancloset_render_product_card( $product ) {
	if ( ! $product ) return;
	$condition = urbancloset_product_condition( $product );
	$rating    = $product->get_average_rating();
	$product_url = get_permalink( $product->get_id() );
	?>
	<div class="product-card">
		<a class="product-card-link" href="<?php echo esc_url( $product_url ); ?>">
			<div class="product-img-wrap">
				<?php echo $product->get_image( 'medium' ); ?>
				<div class="product-condition-badge"><?php echo esc_html( $condition ); ?></div>
			</div>
			<div class="product-info">
				<div class="product-brand"><?php
					$brands = wc_get_product_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) );
					echo $brands ? esc_html( $brands[0] ) : '';
				?></div>
				<div class="product-name"><?php echo esc_html( $product->get_name() ); ?></div>
				<div class="product-stars"><?php echo urbancloset_stars( $rating ); ?></div>
				<div class="product-footer">
					<div class="product-price"><?php echo $product->get_price_html(); ?></div>
					<div class="product-code">#<?php echo esc_html( str_pad( $product->get_id(), 3, '0', STR_PAD_LEFT ) ); ?></div>
				</div>
			</div>
		</a>

		<div class="product-actions">
			<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>
				<a
					href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
					data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
					data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					data-quantity="1"
					class="product-btn product-btn-cart add_to_cart_button ajax_add_to_cart product_type_simple"
					rel="nofollow"
				>Add to Cart</a>
			<?php else : ?>
				<a
					href="<?php echo esc_url( $product_url ); ?>"
					class="product-btn product-btn-cart"
				>Add to Cart</a>
			<?php endif; ?>

			<a
				href="<?php echo esc_url( urbancloset_buy_now_url( $product ) ); ?>"
				class="product-btn product-btn-buy uc-buy-now-link"
				rel="nofollow"
			>Buy Now</a>
		</div>
	</div>
	<?php
}

/* ── SHOP PAGE: build the filtered/sorted WP_Query args ──────────
 * Reads $_GET: brand[], condition[], collection, price_min, price_max, sort, paged.
 * Filters use taxonomies:
 *   - 'product_brand'   → WooCommerce native Brands (Products → Brands)
 *   - 'pa_condition'    → global attribute "Condition" (Products → Attributes)
 *   - 'product_cat'     → Collections
 */
function urbancloset_shop_query_args() {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ) ),
	);

	$tax_query = array( 'relation' => 'AND' );

	$brand_filters = isset( $_GET['brand'] ) ? (array) wp_unslash( $_GET['brand'] ) : array();
	$brand_filters = array_values( array_filter( array_map( 'sanitize_title', $brand_filters ), static function ( $slug ) { return '' !== $slug; } ) );
	if ( ! empty( $brand_filters ) && taxonomy_exists( 'product_brand' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_brand',
			'field'    => 'slug',
			'terms'    => $brand_filters,
		);
	}

	$condition_filters = isset( $_GET['condition'] ) ? (array) wp_unslash( $_GET['condition'] ) : array();
	$condition_filters = array_values( array_filter( array_map( 'sanitize_title', $condition_filters ), static function ( $slug ) { return '' !== $slug; } ) );
	if ( ! empty( $condition_filters ) && taxonomy_exists( 'pa_condition' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'pa_condition',
			'field'    => 'slug',
			'terms'    => $condition_filters,
		);
	}

	if ( ! empty( $_GET['collection'] ) && 'all' !== $_GET['collection'] ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => sanitize_title( $_GET['collection'] ),
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$args['tax_query'] = $tax_query;
	}

	// NOTE: check the RAW submitted text for emptiness before converting to a
	// number. floatval('') returns 0.0, which is not the same as "no filter" —
	// converting first made every page load quietly filter for "price = $0",
	// wiping out every result no matter what else was selected.
	$min = isset( $_GET['price_min'] ) ? trim( $_GET['price_min'] ) : '';
	$max = isset( $_GET['price_max'] ) ? trim( $_GET['price_max'] ) : '';
	if ( '' !== $min || '' !== $max ) {
		$price_query = array( 'key' => '_price', 'type' => 'NUMERIC' );
		if ( '' !== $min && '' !== $max ) {
			$price_query['value']   = array( floatval( $min ), floatval( $max ) );
			$price_query['compare'] = 'BETWEEN';
		} elseif ( '' !== $min ) {
			$price_query['value']   = floatval( $min );
			$price_query['compare'] = '>=';
		} else {
			$price_query['value']   = floatval( $max );
			$price_query['compare'] = '<=';
		}
		$args['meta_query'] = array( $price_query );
	}

	switch ( isset( $_GET['sort'] ) ? $_GET['sort'] : 'newest' ) {
		case 'price_asc':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price';
			$args['order']    = 'ASC';
			break;
		case 'price_desc':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price';
			$args['order']    = 'DESC';
			break;
		case 'rating':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_wc_average_rating';
			$args['order']    = 'DESC';
			break;
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
	}

	return $args;
}

/* ── HELPER: star rating markup (matches the original design) ───── */
function urbancloset_stars( $rating ) {
	$rating = round( $rating ? $rating : 5 );
	$out = '';
	for ( $i = 1; $i <= 5; $i++ ) {
		if ( $i <= $rating ) {
			$out .= '<svg width="10" height="10" viewBox="0 0 24 24" fill="#8B5E4A" style="display:inline-block;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
		} else {
			$out .= '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#8B5E4A" stroke-width="1.5" style="display:inline-block;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
		}
	}
	return $out;
}

/* ── MINI-CART FRAGMENT ────────────────────────────────────────── */
function urbancloset_minicart_fragment( $fragments ) {
	ob_start();
	if ( WC()->cart && ! WC()->cart->is_empty() ) {
		?>
		<div class="uc-minicart-items">
			<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
				$product = $cart_item['data'];
				if ( ! $product ) continue;
				?>
				<div class="uc-minicart-item">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php echo $product->get_image( 'thumbnail' ); ?>
					</a>
					<div>
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="uc-minicart-item-name">
							<?php echo esc_html( $product->get_name() ); ?>
						</a>
						<div class="uc-minicart-item-meta">
							<?php echo esc_html( $cart_item['quantity'] ); ?> &times; <?php echo $product->get_price_html(); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="uc-minicart-foot">
			<div class="uc-minicart-subtotal">
				<span>Subtotal</span>
				<strong><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
			</div>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="uc-minicart-btn uc-minicart-btn-view">View Cart</a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="uc-minicart-btn uc-minicart-btn-checkout">Checkout</a>
		</div>
		<?php
	} else {
		?>
		<div class="uc-minicart-empty">
			<p>Your bag is currently empty.</p>
			<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">Explore Collections</a>
		</div>
		<?php
	}
	$fragments['.uc-minicart-body'] = '<div class="uc-minicart-body">' . ob_get_clean() . '</div>';
	$fragments['.cart-count']       = '<span class="cart-count">' . urbancloset_cart_count() . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'urbancloset_minicart_fragment' );

/* ── CHECKOUT: make required fields actually required ─────────────
 * Applies to WooCommerce's core address-field definitions, which the
 * Checkout block (Store API) also reads for its required/optional
 * flags. Phone is optional by default in WooCommerce — this makes it
 * required to match the "Customer Information" section (name, email,
 * phone) requested for the checkout redesign. Note: also confirm the
 * "Phone" field is set to required under
 * WooCommerce → Settings → Accounts & Privacy / Checkout Blocks
 * settings, since that admin toggle can override this filter.
 */
add_filter( 'woocommerce_checkout_fields', 'urbancloset_require_checkout_fields', 999 );
add_filter( 'woocommerce_default_address_fields', 'urbancloset_require_address_fields', 999 );
function urbancloset_require_checkout_fields( $fields ) {
	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['required'] = true;
	}
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['required'] = true;
	}

	/* UrbanCloset does not collect postcode / ZIP details. */
	unset( $fields['billing']['billing_postcode'] );
	unset( $fields['shipping']['shipping_postcode'] );
	unset( $fields['billing']['billing_state'] );
	unset( $fields['shipping']['shipping_state'] );

	return $fields;
}
/* Remove postcode / ZIP from classic WooCommerce field sets too. */
add_filter( 'woocommerce_billing_fields', 'urbancloset_remove_billing_postcode', 999 );
function urbancloset_remove_billing_postcode( $fields ) {
	unset( $fields['billing_postcode'] );
	unset( $fields['billing_state'] );
	return $fields;
}

add_filter( 'woocommerce_shipping_fields', 'urbancloset_remove_shipping_postcode', 999 );
function urbancloset_remove_shipping_postcode( $fields ) {
	unset( $fields['shipping_postcode'] );
	unset( $fields['shipping_state'] );
	return $fields;
}

function urbancloset_require_address_fields( $fields ) {
	foreach ( array( 'first_name', 'last_name', 'address_1', 'city', 'country' ) as $key ) {
		if ( isset( $fields[ $key ] ) ) {
			$fields[ $key ]['required'] = true;
		}
	}
	unset( $fields['postcode'] );
	unset( $fields['state'] );
	return $fields;
}

/* Remove postcode / ZIP and state / county from Checkout Blocks and country locales. */
add_filter( 'woocommerce_get_country_locale', 'urbancloset_hide_postcode_field', 999 );
function urbancloset_hide_postcode_field( $locales ) {
	foreach ( $locales as $country => $locale ) {
		unset( $locales[ $country ]['postcode'] );
		unset( $locales[ $country ]['state'] );
	}
	return $locales;
}

/* The Checkout Block also uses WooCommerce's default address locale. */
add_filter( 'woocommerce_get_country_locale_default', 'urbancloset_remove_default_address_fields', 999 );
function urbancloset_remove_default_address_fields( $locale ) {
	unset( $locale['postcode'] );
	unset( $locale['state'] );
	return $locale;
}

/*
 * UPayments validates billing_postcode even though UrbanCloset does not ask
 * shoppers for it. Keep it completely out of the form, while supplying a
 * technical value only to WooCommerce and the gateway.
 */
add_filter( 'woocommerce_checkout_posted_data', 'urbancloset_supply_checkout_postcode', 999 );
function urbancloset_supply_checkout_postcode( $data ) {
	$data['billing_postcode']  = empty( $data['billing_postcode'] ) ? '00000' : $data['billing_postcode'];
	$data['shipping_postcode'] = empty( $data['shipping_postcode'] ) ? '00000' : $data['shipping_postcode'];
	return $data;
}

/* Checkout Block requests bypass woocommerce_checkout_posted_data. */
function urbancloset_supply_store_api_postcode( $customer ) {
	if ( ! $customer->get_billing_postcode() ) {
		$customer->set_billing_postcode( '00000' );
	}
	if ( ! $customer->get_shipping_postcode() ) {
		$customer->set_shipping_postcode( '00000' );
	}
}
add_action( 'woocommerce_store_api_cart_update_customer_from_request', 'urbancloset_supply_store_api_postcode', 999 );
add_action( 'woocommerce_store_api_checkout_update_customer_from_request', 'urbancloset_supply_store_api_postcode', 999 );

/* Paste this at the very bottom of your active theme's functions.php file. */
add_filter( 'woocommerce_cart_item_name', 'urbancloset_checkout_order_product_image', 20, 3 );
function urbancloset_checkout_order_product_image( $name, $cart_item, $cart_item_key ) {
	/* Add the image only to the classic checkout order summary. */
	if ( ! is_checkout() || is_order_received_page() || empty( $cart_item['data'] ) || ! is_a( $cart_item['data'], 'WC_Product' ) ) {
		return $name;
	}

	$product = $cart_item['data'];
	$image   = $product->get_image(
		'woocommerce_thumbnail',
		array(
			'class' => 'uc-order-item__image',
			'alt'   => esc_attr( $product->get_name() ),
		)
	);

	$remove_url = wp_nonce_url(
		add_query_arg(
			array( 'uc_remove_checkout_item' => rawurlencode( $cart_item_key ) ),
			wc_get_checkout_url()
		),
		'uc_remove_checkout_item_' . $cart_item_key
	);
	$remove = '<a class="uc-checkout-image-remove" href="' . esc_url( $remove_url ) . '" aria-label="' . esc_attr( sprintf( __( 'Remove %s from order', 'urbancloset' ), wp_strip_all_tags( $product->get_name() ) ) ) . '" title="' . esc_attr__( 'Remove item', 'urbancloset' ) . '"><span aria-hidden="true">&times;</span></a>';

	return '<span class="uc-order-item"><span class="uc-order-item__image-wrap">' . $image . $remove . '</span><span class="uc-order-item__name">' . $name . '</span></span>';
}

/* Remove a checkout item safely, then return to the checkout page. */
add_action( 'template_redirect', 'urbancloset_remove_checkout_item' );
function urbancloset_remove_checkout_item() {
	if ( ! is_checkout() || is_order_received_page() || empty( $_GET['uc_remove_checkout_item'] ) ) {
		return;
	}

	$key = wc_clean( wp_unslash( $_GET['uc_remove_checkout_item'] ) );
	if ( ! wp_verify_nonce( isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '', 'uc_remove_checkout_item_' . $key ) ) {
		wc_add_notice( __( 'This remove link has expired. Please try again.', 'urbancloset' ), 'error' );
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}

	if ( WC()->cart && WC()->cart->remove_cart_item( $key ) ) {
		wc_add_notice( __( 'Item removed from your order.', 'urbancloset' ), 'success' );
	}
	wp_safe_redirect( wc_get_checkout_url() );
	exit;
}

/* Visible checkout controls above the order table. This separate hook avoids
 * any theme CSS that clips content inside the individual product-title cell. */
add_action( 'woocommerce_checkout_before_order_review', 'urbancloset_checkout_remove_controls', 5 );
function urbancloset_checkout_remove_controls() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}

	echo '<div class="uc-checkout-edit-order"><span class="uc-checkout-edit-order__title">Need to change your order?</span><div class="uc-checkout-edit-order__items">';
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$product = isset( $cart_item['data'] ) ? $cart_item['data'] : false;
		if ( ! $product || ! $product->exists() ) {
			continue;
		}
		$remove_url = wp_nonce_url( add_query_arg( array( 'uc_remove_checkout_item' => rawurlencode( $cart_item_key ) ), wc_get_checkout_url() ), 'uc_remove_checkout_item_' . $cart_item_key );
		echo '<a class="uc-checkout-edit-order__remove" href="' . esc_url( $remove_url ) . '" aria-label="' . esc_attr( sprintf( __( 'Remove %s from order', 'urbancloset' ), $product->get_name() ) ) . '"><span aria-hidden="true">&times;</span> ' . esc_html__( 'Remove', 'urbancloset' ) . ': ' . esc_html( $product->get_name() ) . '</a>';
	}
	echo '</div></div>';
}
/* The image-corner × is the only checkout remove control. */
remove_action( 'woocommerce_checkout_before_order_review', 'urbancloset_checkout_remove_controls', 5 );

/* Checkout Block compatibility: WooCommerce's block summary bypasses the
 * classic woocommerce_cart_item_name hook above. Render a small Store API
 * client inline so the same remove action is available on that summary. */
add_action( 'wp_footer', 'urbancloset_block_checkout_remove_buttons', 99 );
function urbancloset_block_checkout_remove_buttons() {
	if ( ! is_checkout() || is_order_received_page() ) {
		return;
	}
	$config = wp_json_encode( array(
		'cart'  => rest_url( 'wc/store/v1/cart' ),
		'nonce' => wp_create_nonce( 'wc_store_api' ),
		'error' => __( 'Unable to remove this item. Please refresh and try again.', 'urbancloset' ),
	) );
	?>
	<script>
	(function () {
		'use strict';
		var config = <?php echo $config; ?>;
		var loading = false;
		function clean(value) { return (value || '').replace(/\s+/g, ' ').trim(); }
		function itemTitle(row) {
			return clean((row.querySelector('.wc-block-components-product-name, .wc-block-components-order-summary-item__description a') || {}).textContent);
		}
		function install(cart) {
			var used = {};
			document.querySelectorAll('.wc-block-components-order-summary-item').forEach(function (row) {
				if (row.querySelector('.uc-checkout-remove')) return;
				var title = itemTitle(row);
				var item = (cart.items || []).find(function (candidate) { return !used[candidate.key] && clean(candidate.name) === title; });
				if (!item) return;
				used[item.key] = true;
				var button = document.createElement('button');
				button.type = 'button';
				button.className = 'uc-checkout-remove';
				button.innerHTML = '<span aria-hidden="true">&times;</span> Remove';
				button.setAttribute('aria-label', 'Remove ' + item.name + ' from order');
				button.addEventListener('click', function () { remove(item.key, button); });
				(row.querySelector('.wc-block-components-order-summary-item__description') || row).appendChild(button);
			});
		}
		function load() {
			if (loading) return;
			loading = true;
			fetch(config.cart, { credentials: 'same-origin', headers: { 'Nonce': config.nonce } })
				.then(function (response) { if (!response.ok) throw new Error(); return response.json(); })
				.then(install).catch(function () {}).finally(function () { loading = false; });
		}
		function remove(key, button) {
			button.disabled = true; button.textContent = 'Removing…';
			fetch(config.cart + '/remove-item?key=' + encodeURIComponent(key), { method: 'POST', credentials: 'same-origin', headers: { 'Nonce': config.nonce } })
				.then(function (response) { if (!response.ok) throw new Error(); window.location.reload(); })
				.catch(function () { button.disabled = false; button.innerHTML = '<span aria-hidden="true">&times;</span> Remove'; alert(config.error); });
		}
		document.addEventListener('DOMContentLoaded', load);
		var observer = new MutationObserver(function () { window.requestAnimationFrame(load); });
		observer.observe(document.documentElement, { childList: true, subtree: true });
	}());
	</script>
	<?php
}

/* The coupon form uses WooCommerce's default, stable checkout placement. */
