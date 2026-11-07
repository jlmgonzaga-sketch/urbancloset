<?php
defined( 'ABSPATH' ) || exit;

$uc_nav_icons = array(
	'dashboard'       => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>',
	'orders'          => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>',
	'downloads'       => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
	'edit-address'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
	'payment-methods' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
	'edit-account'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
	'customer-logout' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
);

$uc_nav_groups = array(
	'overview' => array( 'label' => 'Overview', 'endpoints' => array( 'dashboard' ) ),
	'shopping' => array( 'label' => 'Shopping',  'endpoints' => array( 'orders', 'downloads' ) ),
	'personal' => array( 'label' => 'Personal',  'endpoints' => array( 'edit-address', 'payment-methods', 'edit-account' ) ),
);

$uc_menu_items = wc_get_account_menu_items();
?>
<nav class="account-nav woocommerce-MyAccount-navigation">

  <?php foreach ( $uc_nav_groups as $group ) :
    $has_items = false;
    foreach ( $group['endpoints'] as $ep ) { if ( isset( $uc_menu_items[ $ep ] ) ) { $has_items = true; break; } }
    if ( ! $has_items ) continue;
  ?>
    <div class="account-nav-group">
      <div class="account-nav-group-label"><?php echo esc_html( $group['label'] ); ?></div>
      <ul>
        <?php foreach ( $group['endpoints'] as $endpoint ) :
          if ( ! isset( $uc_menu_items[ $endpoint ] ) ) continue;
        ?>
          <li class="account-nav-item <?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
              <span class="account-nav-icon"><?php echo isset( $uc_nav_icons[ $endpoint ] ) ? $uc_nav_icons[ $endpoint ] : ''; ?></span>
              <span class="account-nav-label"><?php echo esc_html( $uc_menu_items[ $endpoint ] ); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endforeach; ?>

  <div class="account-nav-divider"></div>

  <ul class="account-nav-logout">
    <?php if ( isset( $uc_menu_items['customer-logout'] ) ) : ?>
      <li class="account-nav-item">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>">
          <span class="account-nav-icon"><?php echo $uc_nav_icons['customer-logout']; ?></span>
          <span class="account-nav-label"><?php echo esc_html( $uc_menu_items['customer-logout'] ); ?></span>
        </a>
      </li>
    <?php endif; ?>
  </ul>
</nav>
