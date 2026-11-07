<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_dashboard' );

$order_count = wc_get_customer_order_count( get_current_user_id() );
?>

<div class="account-overview-head">
  <div class="account-overview-eyebrow">Account Overview</div>
  <p class="account-overview-sub">Here's a quick look at your UrbanCloset account.</p>
</div>

<div class="account-dash-grid">

  <a class="account-dash-card" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
    <div class="account-dash-num"><?php echo esc_html( $order_count ); ?></div>
    <div class="account-dash-label">Orders</div>
    <div class="account-dash-desc">View your order history</div>
  </a>

  <a class="account-dash-card" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">
    <div class="account-dash-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
    </div>
    <div class="account-dash-label">Addresses</div>
    <div class="account-dash-desc">Manage your delivery information</div>
  </a>

  <a class="account-dash-card" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>">
    <div class="account-dash-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
    <div class="account-dash-label">Account Details</div>
    <div class="account-dash-desc">Update your personal information</div>
  </a>

</div>

<a class="account-shop-cta" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>">
  <div>
    <div class="account-shop-cta-label">Continue Shopping</div>
    <div class="account-shop-cta-sub">Discover your next designer piece.</div>
  </div>
</a>

<?php do_action( 'woocommerce_after_account_dashboard' ); ?>
