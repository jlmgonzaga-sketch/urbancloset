<?php
/**
 * My Account → single order = the customer's receipt.
 *
 * $order is provided by WooCommerce (WC_Order object) before this
 * template loads. Shows the order number, the Customer ID this order
 * is tied to, status, dates, payment method, itemized products,
 * totals, and billing/shipping — everything a customer would expect
 * on a receipt/proof of purchase, plus a print button.
 */
defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	echo '<p>' . esc_html__( 'Invalid order.', 'woocommerce' ) . '</p>';
	return;
}

do_action( 'woocommerce_before_view_order', $order->get_id() );

$customer_id       = $order->get_customer_id();
$show_customer_id  = $customer_id > 0; // 0 means the order was placed as a guest.
$status_slug       = $order->get_status();
$item_count        = $order->get_item_count() - $order->get_item_count_refunded();
$needs_payment     = $order->needs_payment();
?>

<div class="uc-receipt" id="uc-receipt-print">

  <div class="uc-receipt-head">
    <div>
      <div class="uc-receipt-eyebrow">Receipt</div>
      <h2 class="uc-receipt-order-num">
        <?php echo esc_html( _x( 'Order #', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
      </h2>
    </div>
    <div class="uc-receipt-head-right">
      <span class="uc-order-status uc-order-status--<?php echo esc_attr( $status_slug ); ?>">
        <?php echo esc_html( wc_get_order_status_name( $status_slug ) ); ?>
      </span>
      <button type="button" class="uc-receipt-print-btn" onclick="window.print()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print
      </button>
    </div>
  </div>

  <?php if ( $needs_payment ) : ?>
    <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info uc-receipt-pay-note">
      <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="woocommerce-Button button">Pay for this order</a>
      This order is awaiting payment.
    </div>
  <?php endif; ?>

  <div class="uc-receipt-meta-grid">

    <div class="uc-receipt-meta-item">
      <span class="uc-receipt-meta-label">Order Date</span>
      <span class="uc-receipt-meta-value"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></span>
    </div>

    <?php if ( $show_customer_id ) : ?>
      <div class="uc-receipt-meta-item">
        <span class="uc-receipt-meta-label">Customer ID</span>
        <span class="uc-receipt-meta-value">#<?php echo esc_html( $customer_id ); ?></span>
      </div>
    <?php else : ?>
      <div class="uc-receipt-meta-item">
        <span class="uc-receipt-meta-label">Customer</span>
        <span class="uc-receipt-meta-value">Guest checkout</span>
      </div>
    <?php endif; ?>

    <div class="uc-receipt-meta-item">
      <span class="uc-receipt-meta-label">Email</span>
      <span class="uc-receipt-meta-value"><?php echo esc_html( $order->get_billing_email() ); ?></span>
    </div>

    <div class="uc-receipt-meta-item">
      <span class="uc-receipt-meta-label">Payment Method</span>
      <span class="uc-receipt-meta-value"><?php echo esc_html( $order->get_payment_method_title() ?: '—' ); ?></span>
    </div>

  </div>

  <?php
  /**
   * Product line items — mirrors WooCommerce's order-details table
   * structure/hooks so plugins (subscriptions, bundles, etc.) that
   * hook into these actions still work, just restyled.
   */
  ?>
  <table class="uc-receipt-table shop_table order_details">
    <thead>
      <tr>
        <th class="uc-receipt-th-product">Item</th>
        <th class="uc-receipt-th-qty">Qty</th>
        <th class="uc-receipt-th-total">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php
      do_action( 'woocommerce_order_details_before_order_table_items', $order );

      foreach ( $order->get_items() as $item_id => $item ) {
        $product = $item->get_product();
        ?>
        <tr class="uc-receipt-row">
          <td class="uc-receipt-td-product">
            <div class="uc-receipt-product">
              <?php if ( $product ) : ?>
                <div class="uc-receipt-thumb">
                  <?php echo wp_kses_post( $product->get_image( 'thumbnail' ) ); ?>
                </div>
              <?php endif; ?>
              <div class="uc-receipt-product-info">
                <span class="uc-receipt-product-name">
                  <?php
                  echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
                  ?>
                </span>
                <?php echo wc_get_formatted_order_item_meta_html( $item, $order ); ?>
              </div>
            </div>
          </td>
          <td class="uc-receipt-td-qty">&times; <?php echo esc_html( $item->get_quantity() ); ?></td>
          <td class="uc-receipt-td-total"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
        </tr>
        <?php
      }

      do_action( 'woocommerce_order_details_after_order_table_items', $order );
      ?>
    </tbody>
    <tfoot>
      <?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
        <tr class="uc-receipt-total-row <?php echo 'order_total' === $key ? 'uc-receipt-grand-total' : ''; ?>">
          <th colspan="2"><?php echo esc_html( $total['label'] ); ?></th>
          <td><?php echo wp_kses_post( $total['value'] ); ?></td>
        </tr>
      <?php endforeach; ?>
    </tfoot>
  </table>

  <div class="uc-receipt-addresses">

    <?php if ( $order->get_formatted_billing_address() ) : ?>
      <div class="uc-receipt-address-card">
        <div class="uc-receipt-address-label">Billed To</div>
        <address><?php echo wp_kses_post( $order->get_formatted_billing_address() ); ?></address>
        <?php if ( $order->get_billing_phone() ) : ?>
          <p class="uc-receipt-address-phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ( $order->needs_shipping_address() && $order->get_formatted_shipping_address() ) : ?>
      <div class="uc-receipt-address-card">
        <div class="uc-receipt-address-label">Shipped To</div>
        <address><?php echo wp_kses_post( $order->get_formatted_shipping_address() ); ?></address>
      </div>
    <?php endif; ?>

  </div>

  <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

</div>

<a class="account-shop-cta uc-receipt-back" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
  <div>
    <div class="account-shop-cta-label">Order History</div>
    <div class="account-shop-cta-sub">Back to all your receipts</div>
  </div>
</a>

<?php do_action( 'woocommerce_after_view_order', $order->get_id() ); ?>
