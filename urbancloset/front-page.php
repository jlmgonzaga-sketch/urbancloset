<?php
/**
 * UrbanCloset homepage
 */
get_header();
?>

<!-- HERO -->
<section class="hero">
  <img class="hero-img" src="<?php echo esc_url( get_template_directory_uri() . '/images/banners/herologo.webp' ); ?>" alt="<?php bloginfo( 'name' ); ?>"/>
  <div class="hero-grad"></div>
  <div class="hero-content">
    <div class="hero-eyebrow">New Collection 2026</div>
    <h1 class="hero-h1">Rare pieces.<br/><em>Lasting presence.</em></h1>
    <div class="hero-badges">
      <span class="hbadge"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polyline points="20 6 9 17 4 12"/></svg>Authenticated</span>
      <span class="hbadge"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polyline points="20 6 9 17 4 12"/></svg>Ships to Gulf</span>
      <span class="hbadge"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polyline points="20 6 9 17 4 12"/></svg>Preloved Luxury</span>
    </div>
    <p class="hero-sub">Authentic preloved designer bags, curated for the bold &amp; intentional.</p>
    <div class="hero-btns">
      <a class="btn-gold" href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'shop' ) ) : '#'; ?>">Shop Now</a>
      <a class="btn-outline" href="#featured-collections">Featured Collections</a>
    </div>
  </div>
</section>

<!-- MARQUEE -->
<div class="marquee-bar">
  <span class="marquee-inner">
    FREE SHIPPING TO KUWAIT &middot; UAE &middot; BAHRAIN &middot; QATAR &nbsp;&bull;&nbsp;
    NEW DROPS EVERY FRIDAY &nbsp;&bull;&nbsp;
    FOLLOW US @URBANCLOSET &nbsp;&bull;&nbsp;
    ALL BAGS 100% AUTHENTICATED &nbsp;&bull;&nbsp;
    FREE SHIPPING TO KUWAIT &middot; UAE &middot; BAHRAIN &middot; QATAR &nbsp;&bull;&nbsp;
    NEW DROPS EVERY FRIDAY &nbsp;&bull;&nbsp;
    FOLLOW US @URBANCLOSET &nbsp;&bull;&nbsp;
    ALL BAGS 100% AUTHENTICATED &nbsp;&bull;&nbsp;
  </span>
</div>

<!-- BRANDS: paste each direct image address in that brand's image field below. -->
<?php
$uc_brands = array(
  array( 'name' => 'Louis Vuitton',    'label' => 'لويس فويتون',       'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/LV.jpg' ),
  array( 'name' => 'Chanel',           'label' => 'شانيل',              'image' => get_stylesheet_directory_uri() . '/images/brands/chanel.jpg', 'fit' => 'contain' ),
  array( 'name' => 'Gucci',            'label' => 'غوتشي',              'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/GUCCI.jpg' ),
  array( 'name' => 'Hermes',           'display_name' => 'Hermès',       'label' => 'هيرميس',             'image' => get_stylesheet_directory_uri() . '/images/brands/hermes.jpg', 'fit' => 'contain' ),
  array( 'name' => 'Prada',            'label' => 'برادا',              'image' => get_stylesheet_directory_uri() . '/images/brands/prada.jpg', 'fit' => 'contain' ),
  array( 'name' => 'Miu Miu',          'label' => 'Miu Miu',             'image' => get_stylesheet_directory_uri() . '/images/brands/miu-miu.jpg', 'fit' => 'contain' ),
  array( 'name' => 'Fendi',            'label' => 'فندي',               'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/FENDI.jpg' ),
  array( 'name' => 'YSL',              'label' => 'إيف سان لوران',      'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/YSL.jpg' ),
  array( 'name' => 'Goyard',           'label' => 'غويار',              'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/GOYARD.jpg' ),
  array( 'name' => 'Christian Dior',   'label' => 'كريستيان ديور',      'image' => get_stylesheet_directory_uri() . '/images/brands/christian-dior.jpg', 'fit' => 'contain' ),
  array( 'name' => 'Bottega Veneta',   'label' => 'بوتيغا فينيتا',      'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BOTTEGA.jpg' ),
  array( 'name' => 'Burberry',         'label' => 'بربري',              'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BURBERRY.jpg' ),
  array( 'name' => 'Bvlgari',          'label' => 'بولغري',             'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BVLGARI.jpg' ),
  array( 'name' => 'Armani',           'label' => 'أرماني',             'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/ARMANI.jpg' ),
  array( 'name' => 'Celine',           'label' => 'سيلين',              'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/CELINE.jpg' ),
  array( 'name' => 'Chloe',            'display_name' => 'Chloé',        'label' => 'Chloé',               'image' => get_stylesheet_directory_uri() . '/images/brands/chloe.jpg', 'fit' => 'contain' ),
  array( 'name' => 'Valentino',        'label' => 'فالنتينو',           'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/VALENTINO.jpg' ),
  array( 'name' => 'Coach',            'label' => 'كوتش',               'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/COACH.jpg' ),
  array( 'name' => 'Dolce & Gabbana',  'label' => 'دولتشي آند غابانا', 'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/DOLCE-AND-GABBANA.jpg' ),
  array( 'name' => 'Ferragamo',        'label' => 'فيراغامو',           'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/FERRAGAMO.jpg' ),
  array( 'name' => 'Loewe',            'label' => 'لويفي',              'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/LOEWE.jpg' ),
  array( 'name' => 'MCM',              'label' => 'إم سي إم',           'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/MCM.jpg' ),
  array( 'name' => 'Balenciaga',       'label' => 'بالنسياغا',          'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BALENCIAGA.jpg' ),
  array( 'name' => 'Carolina Herrera', 'label' => 'كارولينا هيريرا',    'image' => 'https://urbanclosetkw.com/wp-content/uploads/2026/10/CAROLINA.jpg' ),
  array( 'name' => 'Alexander Wang',   'label' => 'Alexander Wang',      'image' => get_stylesheet_directory_uri() . '/images/brands/alexander-wang.jpg', 'fit' => 'contain' ),
);
?>
<section class="brands-section" aria-labelledby="brands-title">
  <div class="section-head">
    <h2 class="section-title" id="brands-title">Explore by Brand</h2>
    <span class="section-sub">Iconic houses, thoughtfully curated</span>
  </div>
  <div class="brands-grid" id="brandsGrid">
    <?php foreach ( $uc_brands as $uc_brand ) :
      $uc_brand_name = $uc_brand['name'];
      $uc_brand_image = $uc_brand['image'];
      $uc_has_image = ( '' !== $uc_brand_image && 'PASTE_IMAGE_ADDRESS_HERE' !== $uc_brand_image );
      $uc_brand_display = $uc_brand['display_name'] ?? $uc_brand_name;
      $uc_brand_term = false;
      if ( taxonomy_exists( 'product_brand' ) ) {
        foreach ( array_unique( array( $uc_brand_name, $uc_brand_display ) ) as $uc_brand_term_name ) {
          $uc_brand_term = get_term_by( 'name', $uc_brand_term_name, 'product_brand' );
          if ( $uc_brand_term ) {
            break;
          }
        }
      }
      $uc_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
      // Homepage tiles open a brand-only view. Shop All filters continue to
      // use brand[] and never trigger this dedicated view.
      $uc_brand_url = add_query_arg( 'brand_page', $uc_brand_term ? $uc_brand_term->slug : sanitize_title( $uc_brand_name ), $uc_shop_url );
      ?>
      <a class="brand-card" href="<?php echo esc_url( $uc_brand_url ); ?>" aria-label="Shop <?php echo esc_attr( $uc_brand_display ); ?>">
        <?php if ( $uc_has_image ) : ?>
          <img class="<?php echo ! empty( $uc_brand['fit'] ) ? 'brand-image-' . esc_attr( $uc_brand['fit'] ) : ''; ?>" src="<?php echo esc_url( $uc_brand_image ); ?>" alt="" loading="lazy" />
        <?php else : ?>
          <span class="brand-card-monogram" aria-hidden="true"><?php echo esc_html( strtoupper( preg_replace( '/[^A-Za-z]/', '', $uc_brand_name ) ) ); ?></span>
        <?php endif; ?>
        <span class="brand-card-overlay"></span>
        <span class="brand-card-label" lang="en"><?php echo esc_html( $uc_brand_display ); ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- FEATURED COLLECTIONS -->
<?php
$uc_featured_products = new WP_Query( class_exists( 'WooCommerce' )
  ? array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 8, 'orderby' => 'date', 'order' => 'DESC' )
  : array( 'post__in' => array( 0 ) )
);
$uc_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<section class="section home-featured" id="featured-collections">
  <div class="section-head">
    <span class="section-kicker">THE URBAN CLOSET EDIT</span>
    <h2 class="section-title">Featured Collections</h2>
    <span class="section-sub">Explore all of our curated designer bags</span>
  </div>
  <div class="product-grid featured-product-grid">
    <?php if ( $uc_featured_products->have_posts() ) : while ( $uc_featured_products->have_posts() ) : $uc_featured_products->the_post();
      $uc_product = wc_get_product( get_the_ID() );
      if ( $uc_product ) urbancloset_render_product_card( $uc_product );
    endwhile; wp_reset_postdata(); else : ?>
      <p class="grid-loading">Our collection is being refreshed. Please check back soon.</p>
    <?php endif; ?>
  </div>
  <div class="home-shop-all"><a href="<?php echo esc_url( $uc_shop_url ); ?>">View All <span aria-hidden="true">→</span></a></div>
</section>

<!-- EDITORIAL -->
<div class="editorial">
  <svg class="editorial-icon" viewBox="0 0 40 70" fill="none">
    <ellipse cx="20" cy="12" rx="8" ry="9" fill="rgba(255,255,255,0.3)"/>
    <rect x="10" y="22" width="20" height="35" rx="4" fill="rgba(255,255,255,0.2)"/>
    <rect x="6" y="45" width="10" height="22" rx="3" fill="rgba(255,255,255,0.2)"/>
    <rect x="24" y="45" width="10" height="22" rx="3" fill="rgba(255,255,255,0.2)"/>
  </svg>
  <div>
    <span class="editorial-eyebrow">Curated With Intention</span>
    <p class="editorial-quote">&ldquo;Each piece is thoughtfully selected &mdash; beauty, condition, and provenance considered in every detail.&rdquo;</p>
  </div>
</div>

<!-- TRUST -->
<section class="trust-section" id="authenticity">
  <div class="section-head">
   
    <span class="section-sub">Authenticity &middot; Transparency &middot; Care</span>
  </div>

  <div class="trust-cards">
    <div class="trust-card">
      <div class="trust-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
      <div class="trust-card-title">Expert Curation</div>
      <p class="trust-card-text">Every bag hand-inspected for hardware, stitching, and provenance before listing.</p>
    </div>
    <div class="trust-card">
      <div class="trust-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
      <div class="trust-card-title">Condition Reports</div>
      <p class="trust-card-text">Detailed ratings, written notes, and photos for every item &mdash; no surprises.</p>
    </div>
    <div class="trust-card">
      <div class="trust-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg></div>
      <div class="trust-card-title">3rd-Party Option</div>
      <p class="trust-card-text">Independent authentication available on request for full peace of mind.</p>
    </div>
  </div>

  <div class="trust-accordions">
    <div class="accord" id="accordTPA">
      <div class="accord-head">
        <div class="accord-head-left">
          <div>
            <div class="accord-label">Optional Service</div>
            <div class="accord-title">Third-Party Authentication</div>
          </div>
          <span class="accord-tag">$20&ndash;30 USD</span>
        </div>
        <span class="accord-arrow">&#8964;</span>
      </div>
      <div class="accord-body">
        <div class="accord-inner">
          <div class="accord-row"><div class="accord-num">01</div><div class="accord-row-text"><strong>Request anytime.</strong> Before or after purchase &mdash; message us on WhatsApp and we arrange it for you.</div></div>
          <div class="accord-row"><div class="accord-num">02</div><div class="accord-row-text"><strong>Fee: $20&ndash;30 USD</strong> (&asymp; KD 6&ndash;9), paid to the authentication service. Non-refundable.</div></div>
          <div class="accord-row"><div class="accord-num">03</div><div class="accord-row-text"><strong>Turnaround: 2&ndash;5 business days.</strong> Heritage houses (Herm&egrave;s, Chanel) may take slightly longer.</div></div>
          <div class="accord-row"><div class="accord-num">04</div><div class="accord-row-text"><strong>Digital certificate issued</strong> on success. Transfers with the bag and adds resale value.</div></div>
          <div class="accord-row"><div class="accord-num">05</div><div class="accord-row-text"><strong>Available on all items,</strong> including during checkout &mdash; just mention it in your order message.</div></div>
        </div>
      </div>
    </div>

    <div class="accord" id="accordConsign">
      <div class="accord-head">
        <div class="accord-head-left">
          <div>
            <div class="accord-label">Earn From Your Wardrobe</div>
            <div class="accord-title">Consign Your Pieces With Us</div>
          </div>
          <span class="accord-tag">10% Commission</span>
        </div>
        <span class="accord-arrow">&#8964;</span>
      </div>
      <div class="accord-body">
        <div class="accord-inner">
          <div class="accord-row"><div class="accord-num">01</div><div class="accord-row-text"><strong>Send us photos.</strong> WhatsApp clear shots of your bag &mdash; front, back, hardware, interior, and accessories.</div></div>
          <div class="accord-row"><div class="accord-num">02</div><div class="accord-row-text"><strong>We assess &amp; price.</strong> Our team proposes a fair listing price. You approve before anything goes live.</div></div>
          <div class="accord-row"><div class="accord-num">03</div><div class="accord-row-text"><strong>We list &amp; sell.</strong> We handle photography, listing, buyer enquiries, and negotiations.</div></div>
          <div class="accord-row"><div class="accord-num">04</div><div class="accord-row-text"><strong>You get paid.</strong> 90% of the final sale price transferred to you directly &mdash; fast and hassle-free.</div></div>
          
        </div>
      </div>
    </div>
  </div>

  <div class="consignment-cta-wrap">
    <button class="consignment-cta" type="button" data-consignment-open>Consign a Bag</button>
    <p>Share your piece with us for a private review and price proposal.</p>
  </div>

</section>

<div class="consignment-modal" id="consignment-form" hidden>
  <div class="consignment-modal__backdrop" data-consignment-close></div>
  <section class="consignment-modal__panel" role="dialog" aria-modal="true" aria-labelledby="consignment-form-title" tabindex="-1">
    <button class="consignment-modal__close" type="button" data-consignment-close aria-label="Close consignment form">&times;</button>
    <div class="consignment-form-intro">
      <span class="accord-label">Private submission</span>
      <h3 id="consignment-form-title">Start your consignment</h3>
      <p>Tell us about your bag, add clear photos and proof of authenticity, then sign below.</p>
    </div>

    <?php if ( isset( $_GET['consignment'] ) && 'success' === sanitize_key( wp_unslash( $_GET['consignment'] ) ) ) : ?>
      <p class="consignment-notice success" role="status">Thank you. Your consignment request has been sent.</p>
    <?php elseif ( isset( $_GET['consignment'] ) && 'error' === sanitize_key( wp_unslash( $_GET['consignment'] ) ) ) : ?>
      <p class="consignment-notice error" role="alert">We could not send your request. Please check the required fields and try again.</p>
    <?php endif; ?>

    <form class="consignment-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="uc_submit_consignment">
      <input type="hidden" name="consignment_signature_data" id="uc-consign-signature-data">
      <?php wp_nonce_field( 'uc_submit_consignment', 'uc_consignment_nonce' ); ?>
      <div class="consignment-form-grid">
        <p><label for="uc-consign-name">Name <span aria-hidden="true">*</span></label><input id="uc-consign-name" name="consignment_name" type="text" required autocomplete="name"></p>
        <p><label for="uc-consign-email">Email <span aria-hidden="true">*</span></label><input id="uc-consign-email" name="consignment_email" type="email" required autocomplete="email"></p>
        <p><label for="uc-consign-phone">Phone number <span aria-hidden="true">*</span></label><input id="uc-consign-phone" name="consignment_phone" type="tel" required autocomplete="tel"></p>
        <p><label for="uc-consign-authenticity">Authenticity <span aria-hidden="true">*</span></label><select id="uc-consign-authenticity" name="consignment_authenticity" required><option value="">Select one</option><option>Original receipt available</option><option>Certificate / authentication card available</option><option>No document available</option><option>Other proof available</option></select></p>
        <p><label for="uc-consign-original-price">Original price</label><input id="uc-consign-original-price" name="consignment_original_price" type="text" inputmode="decimal" placeholder="e.g. KD 1,250"></p>
        <p><label for="uc-consign-offer">Your price offer <span aria-hidden="true">*</span></label><input id="uc-consign-offer" name="consignment_price_offer" type="text" inputmode="decimal" required placeholder="e.g. KD 450"></p>
      </div>
      <div class="consignment-upload-grid">
        <p class="consignment-upload"><label for="uc-consign-photos">Bag photos <span aria-hidden="true">*</span></label><input id="uc-consign-photos" name="consignment_photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple required><small>Up to 6 JPG, PNG, or WebP photos. Include the receipt or certificate in these photos if available.</small></p>
      </div>
      <div class="consignment-signature-wrap">
        <div><label for="uc-consign-signature">Draw your signature <span aria-hidden="true">*</span></label><p>By signing, you confirm the information is accurate and that you own the bag.</p></div>
        <canvas id="uc-consign-signature" class="consignment-signature" width="720" height="180" aria-label="Signature drawing area"></canvas>
        <button class="consignment-signature-clear" type="button" data-signature-clear>Clear signature</button>
      </div>
      <div class="consignment-actions">
        <button class="consignment-submit" type="submit">Send Consignment Request</button>
        <a class="consignment-whatsapp" href="https://wa.me/<?php echo esc_attr( UC_WHATSAPP_NUMBER ); ?>?text=<?php echo rawurlencode( 'Hello Urban Closet, I have a question about consigning a bag.' ); ?>" target="_blank" rel="noopener">Ask on WhatsApp</a>
      </div>
    </form>
  </section>
</div>

<!-- HOW TO ORDER -->
<section class="hto-section" id="how-to-order">
  <div class="section-head">
    <h2 class="section-title">How to Order</h2>
    <span class="section-sub">Secure checkout &middot; Multiple payment options</span>
  </div>
  <div class="hto-pay-row">
    <span class="hto-pay-badge">KNET</span>
    <span class="hto-pay-badge">Credit Card</span>
    <span class="hto-pay-badge">Apple Pay</span>
    <span class="hto-pay-badge">Deema Installments</span>
    <span class="hto-pay-badge">UPayments</span>
  </div>
  <div class="hto-grid">
    <div class="hto-card">
      <div class="hto-card-num">01</div>
      <div class="hto-card-title">Browse &amp; Select</div>
      <p class="hto-card-text">Explore Featured Collections or browse by brand. View full details, condition report, and pricing on each bag.</p>
    </div>
    <div class="hto-card">
      <div class="hto-card-num">02</div>
      <div class="hto-card-title">Add to Cart &amp; Checkout</div>
      <p class="hto-card-text">Add your item and proceed to checkout via our secure payment page.</p>
    </div>
    <div class="hto-card">
      <div class="hto-card-num">03</div>
      <div class="hto-card-title">Pay Your Way</div>
      <p class="hto-card-text">Choose KNET, credit card, or Apple Pay. Prefer installments? Select Deema at checkout &mdash; available on eligible orders.</p>
    </div>
    <div class="hto-card">
      <div class="hto-card-num">04</div>
      <div class="hto-card-title">Delivered to Your Door</div>
      <p class="hto-card-text">Kuwait: 1&ndash;2 business days. Gulf region (UAE, Bahrain, Qatar, KSA): 3&ndash;5 days. International shipping on request.</p>
    </div>
  </div>
  <a class="hto-cta" href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'shop' ) ) : '#'; ?>">Shop Now</a>
</section>

<?php get_footer(); ?>

