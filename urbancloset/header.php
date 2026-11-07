  <!DOCTYPE html>
  <html <?php language_attributes(); ?>>
  <head>
  <meta charset="<?php bloginfo( 'charset' ); ?>"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <!-- DRAWER OVERLAY -->
  <div class="drawer-overlay" id="drawerOverlay"></div>

  <!-- SIDE DRAWER -->
  <aside class="side-drawer" id="sideDrawer">
    <div class="drawer-head">
      <div class="drawer-logo"><?php bloginfo( 'name' ); ?></div>
      <div class="drawer-close" id="drawerClose"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div>
    </div>
    <nav class="drawer-nav">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Home
      </a>
      <a href="<?php echo esc_url( home_url( '/#featured-collections' ) ); ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>Featured Collections
      </a>
      <a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'shop' ) ) : '#'; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>Shop All
      </a>
      <div class="drawer-divider"></div>
      <div class="drawer-section-label">Authentic Preloved Collections</div>
      <?php
      $uc_collection_slugs = array( 'preloved-authentic-bags', 'preloved-authentic-jewelry', 'preloved-authentic-shoes', 'other' );
      if ( class_exists( 'WooCommerce' ) ) {
        foreach ( $uc_collection_slugs as $uc_category_slug ) {
          $uc_term = get_term_by( 'slug', $uc_category_slug, 'product_cat' );
          if ( ! $uc_term || is_wp_error( $uc_term ) ) continue;
          $uc_link = get_term_link( $uc_term );
          if ( ! is_wp_error( $uc_link ) ) {
            echo '<a href="' . esc_url( $uc_link ) . '"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>' . esc_html( $uc_term->name ) . '</a>';
          }
        }
      }
      ?>
      <div class="drawer-divider"></div>
      <a href="<?php echo function_exists( 'wc_get_cart_url' ) ? esc_url( wc_get_cart_url() ) : '#'; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>Cart <span class="drawer-badge"><?php echo esc_html( urbancloset_cart_count() ); ?></span>
      </a>
      <a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'myaccount' ) ) : '#'; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>My Orders
      </a>
      <div class="drawer-divider"></div>
      <a href="<?php echo esc_url( home_url( '/#how-to-order' ) ); ?>"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>How to Order</a>
      <a href="<?php echo esc_url( home_url( '/#authenticity' ) ); ?>"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Authenticity</a>
      <a href="<?php echo esc_url( home_url( '/#consignment-form' ) ); ?>" class="drawer-consignment"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.24l7.78-7.79 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>Consignment <span class="drawer-consignment-tag">Start a request</span></a>
      <a href="https://wa.me/<?php echo esc_attr( UC_WHATSAPP_NUMBER ); ?>" target="_blank"><svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM11.999 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2.024 22l4.975-1.306A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 11.999 2z"/></svg>Contact Us</a>
    </nav>
    <div class="drawer-foot">
      <div style="display:flex;align-items:flex-start;gap:7px;margin-bottom:5px;"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg><?php echo esc_html( UC_ADDRESS ); ?></div>
      <div style="display:flex;align-items:flex-start;gap:7px;margin-bottom:5px;"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg><?php echo esc_html( UC_PHONE_DISPLAY ); ?></div>
      <div style="display:flex;align-items:flex-start;gap:7px;"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg><?php echo esc_html( UC_EMAIL ); ?></div>
    </div>
  </aside>

  <!-- NAVBAR -->
  <nav class="navbar">
    <button class="nav-ham" id="navHam" type="button" aria-label="Open menu"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>

      <div class="nav-actions">
      <div class="nav-icons">
        <label class="uc-currency-switch" aria-label="Display currency">
          <select data-uc-currency>
            <option value="KWD">KD</option>
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
          </select>
        </label>
        <a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'myaccount' ) ) : '#'; ?>" id="ucAccountTrigger" data-uc-account-trigger aria-haspopup="dialog" style="display:flex;align-items:center;color:inherit;">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </a>
        <div class="uc-minicart-wrap" id="ucMinicartWrap">
          <a href="<?php echo function_exists( 'wc_get_cart_url' ) ? esc_url( wc_get_cart_url() ) : '#'; ?>" id="ucMinicartTrigger" aria-label="<?php esc_attr_e( 'View your bag', 'urbancloset' ); ?>" style="cursor:pointer;position:relative;display:inline-flex;color:inherit;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            <span class="cart-count"><?php echo esc_html( urbancloset_cart_count() ); ?></span>
          </a>

          <!-- CART PREVIEW / MINI CART
               Populated entirely from WooCommerce's own cart fragments
               (urbancloset_minicart_fragment() in functions.php), so it
               always reflects the real cart — nothing here is hard-coded.
               Desktop: opens on hover (CSS) or click of the bag icon.
               Mobile: opens on tap (main.js toggles .open); closes on
               outside tap or the close button below. -->
          <div class="uc-minicart-panel" id="ucMinicartPanel">
            <div class="uc-minicart-head">
              <span>Your Bag</span>
              <button type="button" class="uc-minicart-close" id="ucMinicartClose" aria-label="<?php esc_attr_e( 'Close', 'urbancloset' ); ?>">&times;</button>
            </div>
            <div class="uc-minicart-body">
              <div class="uc-minicart-empty">
                <p>Your bag is currently empty.</p>
                <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">Explore Collections</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
