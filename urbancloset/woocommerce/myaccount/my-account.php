<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_my_account' );

if ( ! is_user_logged_in() ) {
	wc_get_template( 'myaccount/form-login.php' );
	return;
}

$current_user = wp_get_current_user();
?>

<section class="account-hero">
  <div class="account-hero-inner">
    <div class="account-hero-eyebrow">Your Account</div>
    <h1 class="account-hero-h1">Welcome back, <em><?php echo esc_html( $current_user->display_name ); ?></em></h1>
    <p class="account-hero-sub">Manage your orders, saved addresses, and account details in one place.</p>
  </div>
  <div class="account-hero-deco" aria-hidden="true">
    <svg width="220" height="220" viewBox="0 0 220 220" fill="none">
      <circle cx="110" cy="110" r="100" stroke="currentColor" stroke-width="0.6"/>
      <text x="110" y="132" text-anchor="middle" font-family="Georgia, serif" font-size="80" fill="currentColor">UC</text>
    </svg>
  </div>
</section>

<div class="account-shell">
  <div class="account-layout">
    <aside class="account-nav-wrap">
      <?php do_action( 'woocommerce_account_navigation' ); ?>
    </aside>

    <div class="account-content-wrap">
      <div class="woocommerce-MyAccount-content">
        <?php do_action( 'woocommerce_account_content' ); ?>
      </div>
    </div>
  </div>
</div>

<?php do_action( 'woocommerce_after_my_account' ); ?>