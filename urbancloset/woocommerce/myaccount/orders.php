<?php
/**
 * My Account → Orders (order history).
 *
 * Lists every order the logged-in customer has placed, newest first,
 * as a set of receipt-style cards linking through to view-order.php
 * (the actual receipt). Based on WooCommerce core's
 * myaccount/orders.php but restyled to match the theme.
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>

<div class="account-overview-head">
  <div class="account-overview-eyebrow">Order History</div>
  <p class="account-overview-sub">Every receipt for orders placed with this account.</p>
</div>

<?php if ( $has_orders ) : ?>

  <div class="uc-orders-list">
    <?php foreach ( $customer_orders->orders as $customer_order ) :
      $order       = wc_get_order( $customer_order );
      $item_count  = $order->get_item_count() - $order->get_item_count_refunded();
      $status_slug = $order->get_status();
    ?>
      <a class="uc-order-card" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">

        <div class="uc-order-card-main">
          <div class="uc-order-card-num">
            <?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
          </div>
          <div class="uc-order-card-meta">
            <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
              <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
            </time>
            <span class="uc-order-card-dot">&bull;</span>
            <span>
              <?php
              /* translators: %d: number of items in the order */
              echo esc_html( sprintf( _n( '%d item', '%d items', $item_count, 'woocommerce' ), $item_count ) );
              ?>
            </span>
          </div>
        </div>

        <div class="uc-order-card-status">
          <span class="uc-order-status uc-order-status--<?php echo esc_attr( $status_slug ); ?>">
            <?php echo esc_html( wc_get_order_status_name( $status_slug ) ); ?>
          </span>
        </div>

        <div class="uc-order-card-total">
          <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
        </div>


      </a>
    <?php endforeach; ?>
  </div>

  <?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>
  <?php if ( 1 < $customer_orders->max_num_pages ) : ?>
    <div class="woocommerce-pagination woocommerce-Pagination uc-orders-pagination">
      <?php if ( 1 !== $current_page ) : ?>
        <a class="woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">Previous</a>
      <?php endif; ?>
      <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
        <a class="woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">Next</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

<?php else : ?>

  <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
    <a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">Browse products</a>
    No order has been made yet.
  </div>

<?php endif;

do_action( 'woocommerce_after_account_orders', $has_orders );
