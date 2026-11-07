<?php
/**
 * Account Modal — Sign In / Create Account / Account menu / Sign Out,
 * all as an overlay modal instead of separate page loads.
 *
 * IMPORTANT: this file does NOT reimplement authentication. Every
 * handler below is a thin AJAX wrapper around the exact functions
 * WordPress/WooCommerce already use on the native /my-account/ page:
 *   - Login:    wp_signon()               (same call WC's own
 *                                           WC_Form_Handler::process_login()
 *                                           wraps, plus the same
 *                                           'woocommerce_login_credentials'
 *                                           / 'woocommerce_process_login_errors'
 *                                           filters WooCommerce fires)
 *   - Register: wc_create_new_customer()   (the exact function
 *                                           WC_Form_Handler::process_registration()
 *                                           calls internally)
 *   - Auto-login after register: wc_set_customer_auth_cookie()
 *                                           (WooCommerce's own helper for
 *                                           this, used by core registration)
 *   - Logout:   wp_logout()
 * No separate user table, no separate password hashing, no separate
 * session handling — same accounts, same sessions, same admin screens.
 *
 * The existing /my-account/ page (page-myaccount.php) and its forms
 * are left untouched and still work as a no-JS fallback.
 */

defined( 'ABSPATH' ) || exit;

/* ── ASSETS: ajax url + nonces for the modal's JS ─────────────────
 * Runs after urbancloset_assets() (which registers 'urbancloset-main')
 * so the script handle already exists to attach data to.
 */
function urbancloset_account_modal_assets() {
	if ( ! wp_script_is( 'urbancloset-main', 'registered' ) && ! wp_script_is( 'urbancloset-main', 'enqueued' ) ) {
		return;
	}
	wp_localize_script( 'urbancloset-main', 'UC_ACCOUNT', array(
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'loginNonce'    => wp_create_nonce( 'uc-account-login' ),
		'registerNonce' => wp_create_nonce( 'uc-account-register' ),
		'logoutNonce'   => wp_create_nonce( 'uc-account-logout' ),
		'isLoggedIn'    => is_user_logged_in(),
		'myAccountUrl'  => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'urbancloset_account_modal_assets', 20 );

/* ── LOGIN ─────────────────────────────────────────────────────── */
function urbancloset_ajax_login() {
	check_ajax_referer( 'uc-account-login', 'nonce' );

	$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
	$password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
	$remember = ! empty( $_POST['remember'] );

	if ( empty( $username ) || empty( $password ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter your email/username and password.', 'urbancloset' ) ) );
	}

	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => $remember,
	);

	// Same filter WooCommerce's native login form fires, so any
	// site-wide login customizations (2FA plugins, etc.) still apply.
	if ( has_filter( 'woocommerce_login_credentials' ) ) {
		$creds = apply_filters( 'woocommerce_login_credentials', $creds );
	}

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		$message = wp_strip_all_tags( $user->get_error_message() );
		if ( has_filter( 'woocommerce_process_login_errors' ) ) {
			$message = wp_strip_all_tags( apply_filters( 'woocommerce_process_login_errors', $message, $username, $password ) );
		}
		wp_send_json_error( array( 'message' => $message ) );
	}

	do_action( 'wp_login', $user->user_login, $user );

	wp_send_json_success( array(
		'name'     => $user->first_name ? $user->first_name : $user->display_name,
		'redirect' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ),
	) );
}
add_action( 'wp_ajax_nopriv_uc_account_login', 'urbancloset_ajax_login' );
add_action( 'wp_ajax_uc_account_login', 'urbancloset_ajax_login' );

/* ── REGISTER ──────────────────────────────────────────────────── */
function urbancloset_ajax_register() {
	check_ajax_referer( 'uc-account-register', 'nonce' );

	if ( ! function_exists( 'wc_create_new_customer' ) ) {
		wp_send_json_error( array( 'message' => __( 'Registration is currently unavailable.', 'urbancloset' ) ) );
	}

	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$password   = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
	$password2  = isset( $_POST['password_confirm'] ) ? (string) wp_unslash( $_POST['password_confirm'] ) : '';
	$terms_ok   = ! empty( $_POST['terms'] );

	$errors = array();
	if ( '' === $first_name ) {
		$errors[] = __( 'Please enter your first name.', 'urbancloset' );
	}
	if ( '' === $last_name ) {
		$errors[] = __( 'Please enter your last name.', 'urbancloset' );
	}
	if ( ! is_email( $email ) ) {
		$errors[] = __( 'Please enter a valid email address.', 'urbancloset' );
	} elseif ( email_exists( $email ) ) {
		$errors[] = __( 'An account already exists with this email address.', 'urbancloset' );
	}
	if ( '' === $password ) {
		$errors[] = __( 'Please enter a password.', 'urbancloset' );
	} elseif ( $password !== $password2 ) {
		$errors[] = __( 'Passwords do not match.', 'urbancloset' );
	}
	if ( ! $terms_ok ) {
		$errors[] = __( 'Please agree to the Terms & Privacy Policy.', 'urbancloset' );
	}

	if ( $errors ) {
		wp_send_json_error( array( 'message' => implode( ' ', $errors ) ) );
	}

	// Respects the same "auto-generate username" setting the native
	// registration form checks (see page-myaccount.php / uc_show_register).
	$username = '';
	if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
		$username = isset( $_POST['username'] ) && '' !== trim( wp_unslash( $_POST['username'] ) )
			? sanitize_user( wp_unslash( $_POST['username'] ) )
			: sanitize_user( current( explode( '@', $email ) ) );
	}

	$new_customer_id = wc_create_new_customer(
		$email,
		$username,
		$password,
		array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
		)
	);

	if ( is_wp_error( $new_customer_id ) ) {
		wp_send_json_error( array( 'message' => wp_strip_all_tags( $new_customer_id->get_error_message() ) ) );
	}

	if ( $phone ) {
		update_user_meta( $new_customer_id, 'billing_phone', $phone );
	}

	// The exact helper WooCommerce's own registration flow uses to log
	// a shopper in immediately after their account is created.
	wc_set_customer_auth_cookie( $new_customer_id );

	do_action( 'woocommerce_created_customer', $new_customer_id, array(), false );

	$user = get_user_by( 'id', $new_customer_id );

	wp_send_json_success( array(
		'name'     => $first_name,
		'redirect' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ),
	) );
}
add_action( 'wp_ajax_nopriv_uc_account_register', 'urbancloset_ajax_register' );
add_action( 'wp_ajax_uc_account_register', 'urbancloset_ajax_register' );

/* ── LOGOUT ────────────────────────────────────────────────────── */
function urbancloset_ajax_logout() {
	check_ajax_referer( 'uc-account-logout', 'nonce' );
	wp_logout();
	wp_send_json_success( array( 'redirect' => home_url( '/' ) ) );
}
add_action( 'wp_ajax_uc_account_logout', 'urbancloset_ajax_logout' );

/* ── MODAL MARKUP ──────────────────────────────────────────────── *
 * Printed once in the footer on every page (cheap — it's just hidden
 * markup until JS opens it). Whichever icon/link you use to open it
 * just needs data-uc-account-trigger — see INTEGRATION.md.
 */
function urbancloset_render_account_modal() {
	?>
	<div class="uc-account-overlay" id="ucAccountOverlay" aria-hidden="true"></div>
	<div class="uc-account-modal" id="ucAccountModal" role="dialog" aria-modal="true" aria-labelledby="ucAccountModalTitle" aria-hidden="true">
		<button type="button" class="uc-account-modal-close" id="ucAccountModalClose" aria-label="<?php esc_attr_e( 'Close', 'urbancloset' ); ?>">&times;</button>

		<?php if ( is_user_logged_in() ) :
			$current_user = wp_get_current_user();
			$display_name = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
			?>
			<!-- ACCOUNT PANEL -->
			<div class="uc-account-panel" data-uc-panel="account">
				<div class="uc-account-modal-eyebrow"><?php esc_html_e( 'Welcome back', 'urbancloset' ); ?></div>
				<h2 class="uc-account-modal-title" id="ucAccountModalTitle">
					<?php printf( esc_html__( 'Hello, %s', 'urbancloset' ), esc_html( $display_name ) ); ?>
				</h2>
				<div class="uc-account-menu">
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="uc-account-menu-link">
						<span><?php esc_html_e( 'My Account', 'urbancloset' ); ?></span>
					</a>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="uc-account-menu-link">
						<span><?php esc_html_e( 'My Orders', 'urbancloset' ); ?></span>
					</a>
					<button type="button" class="uc-account-menu-link uc-account-menu-signout" data-uc-open-panel="signout">
						<span><?php esc_html_e( 'Sign Out', 'urbancloset' ); ?></span>
					</button>
				</div>
			</div>

			<!-- SIGN OUT CONFIRM PANEL -->
			<div class="uc-account-panel" data-uc-panel="signout" hidden>
				<h2 class="uc-account-modal-title"><?php esc_html_e( 'Sign out?', 'urbancloset' ); ?></h2>
				<p class="uc-account-modal-sub"><?php esc_html_e( 'You can sign back in anytime.', 'urbancloset' ); ?></p>
				<div class="uc-account-form-error" data-uc-form-error hidden></div>
				<div class="uc-account-confirm-actions">
					<button type="button" class="uc-account-btn-outline" data-uc-open-panel="account"><?php esc_html_e( 'Cancel', 'urbancloset' ); ?></button>
					<button type="button" class="uc-account-btn-solid" id="ucAccountSignoutConfirm">
						<span class="uc-btn-label"><?php esc_html_e( 'Sign Out', 'urbancloset' ); ?></span>
						<span class="uc-btn-spinner" hidden></span>
					</button>
				</div>
			</div>

		<?php else : ?>

			<!-- LOGIN PANEL -->
			<div class="uc-account-panel" data-uc-panel="login">
				<div class="uc-account-modal-eyebrow"><?php esc_html_e( 'Welcome back', 'urbancloset' ); ?></div>
				<h2 class="uc-account-modal-title" id="ucAccountModalTitle"><?php esc_html_e( 'Sign In', 'urbancloset' ); ?></h2>
				<p class="uc-account-modal-sub"><?php esc_html_e( 'Sign in to your Urban Closet account.', 'urbancloset' ); ?></p>

				<div class="uc-account-form-error" data-uc-form-error hidden></div>

				<form class="uc-account-form" id="ucLoginForm" novalidate>
					<p class="form-row">
						<label class="auth-label" for="uc_login_username"><?php esc_html_e( 'Email Address', 'urbancloset' ); ?></label>
						<input class="auth-input" type="text" id="uc_login_username" name="username" autocomplete="username" required>
					</p>
					<p class="form-row uc-password-row">
						<label class="auth-label" for="uc_login_password"><?php esc_html_e( 'Password', 'urbancloset' ); ?></label>
						<input class="auth-input" type="password" id="uc_login_password" name="password" autocomplete="current-password" required>
						<button type="button" class="uc-password-toggle" data-uc-toggle-pass="uc_login_password" aria-label="<?php esc_attr_e( 'Show password', 'urbancloset' ); ?>">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
						</button>
					</p>
					<p class="uc-account-row-between">
						<label class="auth-remember">
							<input type="checkbox" name="remember" value="1"> <?php esc_html_e( 'Remember me', 'urbancloset' ); ?>
						</label>
						<a class="auth-forgot" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot Password?', 'urbancloset' ); ?></a>
					</p>
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'uc-account-login' ) ); ?>">
					<button type="submit" class="filter-apply-btn auth-submit uc-account-submit">
						<span class="uc-btn-label"><?php esc_html_e( 'Sign In', 'urbancloset' ); ?></span>
						<span class="uc-btn-spinner" hidden></span>
					</button>
				</form>

				<div class="uc-account-switch">
					<span><?php esc_html_e( "Don't have an account?", 'urbancloset' ); ?></span>
					<button type="button" class="auth-signup-btn" data-uc-open-panel="register"><?php esc_html_e( 'Create an Account', 'urbancloset' ); ?></button>
				</div>
			</div>

			<!-- REGISTER PANEL -->
			<div class="uc-account-panel" data-uc-panel="register" hidden>
				<div class="uc-account-modal-eyebrow"><?php esc_html_e( 'New here', 'urbancloset' ); ?></div>
				<h2 class="uc-account-modal-title"><?php esc_html_e( 'Create Your Account', 'urbancloset' ); ?></h2>
				<p class="uc-account-modal-sub"><?php esc_html_e( 'Create an account to start shopping.', 'urbancloset' ); ?></p>

				<div class="uc-account-form-error" data-uc-form-error hidden></div>

				<form class="uc-account-form" id="ucRegisterForm" novalidate>
					<div class="uc-form-2col">
						<p class="form-row">
							<label class="auth-label" for="uc_reg_first"><?php esc_html_e( 'First Name', 'urbancloset' ); ?></label>
							<input class="auth-input" type="text" id="uc_reg_first" name="first_name" autocomplete="given-name" required>
						</p>
						<p class="form-row">
							<label class="auth-label" for="uc_reg_last"><?php esc_html_e( 'Last Name', 'urbancloset' ); ?></label>
							<input class="auth-input" type="text" id="uc_reg_last" name="last_name" autocomplete="family-name" required>
						</p>
					</div>
					<p class="form-row">
						<label class="auth-label" for="uc_reg_email"><?php esc_html_e( 'Email Address', 'urbancloset' ); ?></label>
						<input class="auth-input" type="email" id="uc_reg_email" name="email" autocomplete="email" required>
					</p>
					<p class="form-row">
						<label class="auth-label" for="uc_reg_phone"><?php esc_html_e( 'Mobile Number', 'urbancloset' ); ?></label>
						<input class="auth-input" type="tel" id="uc_reg_phone" name="phone" autocomplete="tel">
					</p>
					<p class="form-row uc-password-row">
						<label class="auth-label" for="uc_reg_pass"><?php esc_html_e( 'Password', 'urbancloset' ); ?></label>
						<input class="auth-input" type="password" id="uc_reg_pass" name="password" autocomplete="new-password" required>
						<button type="button" class="uc-password-toggle" data-uc-toggle-pass="uc_reg_pass" aria-label="<?php esc_attr_e( 'Show password', 'urbancloset' ); ?>">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
						</button>
					</p>
					<p class="form-row uc-password-row">
						<label class="auth-label" for="uc_reg_pass2"><?php esc_html_e( 'Confirm Password', 'urbancloset' ); ?></label>
						<input class="auth-input" type="password" id="uc_reg_pass2" name="password_confirm" autocomplete="new-password" required>
						<button type="button" class="uc-password-toggle" data-uc-toggle-pass="uc_reg_pass2" aria-label="<?php esc_attr_e( 'Show password', 'urbancloset' ); ?>">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
						</button>
					</p>
					<label class="uc-terms-row">
						<input type="checkbox" name="terms" value="1" required>
						<span><?php printf( wp_kses( __( 'I agree to the <a href="%s" target="_blank" rel="noopener">Terms &amp; Privacy Policy</a>', 'urbancloset' ), array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) ) ), esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ) ); ?></span>
					</label>
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'uc-account-register' ) ); ?>">
					<button type="submit" class="btn-outline auth-submit-outline uc-account-submit">
						<span class="uc-btn-label"><?php esc_html_e( 'Create Account', 'urbancloset' ); ?></span>
						<span class="uc-btn-spinner" hidden></span>
					</button>
				</form>

				<div class="uc-account-switch">
					<span><?php esc_html_e( 'Already have an account?', 'urbancloset' ); ?></span>
					<button type="button" class="auth-signup-btn" data-uc-open-panel="login"><?php esc_html_e( 'Sign In', 'urbancloset' ); ?></button>
				</div>
			</div>

		<?php endif; ?>

		<div class="uc-account-success" data-uc-success hidden>
			<div class="uc-account-success-icon">&#10003;</div>
			<p class="uc-account-success-msg"></p>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'urbancloset_render_account_modal', 5 );
