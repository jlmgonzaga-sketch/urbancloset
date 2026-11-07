<?php
/**
 * UrbanCloset — Shop All (WooCommerce product archive)
 */
get_header();

$has_brand_tax     = taxonomy_exists( 'product_brand' );
$has_condition_tax = taxonomy_exists( 'pa_condition' );

$selected_brands     = isset( $_GET['brand'] ) ? array_values( array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['brand'] ) ), static function ( $slug ) { return '' !== $slug; } ) ) : array();
$brand_page          = isset( $_GET['brand_page'] ) ? sanitize_title( wp_unslash( $_GET['brand_page'] ) ) : '';
$selected_conditions = isset( $_GET['condition'] ) ? array_values( array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['condition'] ) ), static function ( $slug ) { return '' !== $slug; } ) ) : array();
$selected_collection = isset( $_GET['collection'] ) ? sanitize_title( $_GET['collection'] ) : 'all';
$selected_sort       = isset( $_GET['sort'] ) ? sanitize_key( $_GET['sort'] ) : 'newest';
$price_min           = isset( $_GET['price_min'] ) ? esc_attr( $_GET['price_min'] ) : '';
$price_max           = isset( $_GET['price_max'] ) ? esc_attr( $_GET['price_max'] ) : '';

$brand_focus = '' !== $brand_page && $has_brand_tax;
$requested_brands = $brand_focus ? array( $brand_page ) : $selected_brands;
$focused_term = $brand_focus ? get_term_by( 'slug', $brand_page, 'product_brand' ) : false;
$brand_cards = array(
  'louis-vuitton' => array( 'Louis Vuitton', 'لويس فويتون', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/LV.jpg' ),
  'chanel' => array( 'Chanel', 'شانيل', get_stylesheet_directory_uri() . '/images/brands/chanel.jpg' ),
  'gucci' => array( 'Gucci', 'غوتشي', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/GUCCI.jpg' ),
  'hermes' => array( 'Hermes', 'هيرميس', get_stylesheet_directory_uri() . '/images/brands/hermes.jpg' ),
  'prada' => array( 'Prada', 'برادا', get_stylesheet_directory_uri() . '/images/brands/prada.jpg' ),
  'miu-miu' => array( 'Miu Miu', 'Miu Miu', get_stylesheet_directory_uri() . '/images/brands/miu-miu.jpg' ),
  'fendi' => array( 'Fendi', 'فندي', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/FENDI.jpg' ),
  'ysl' => array( 'YSL', 'إيف سان لوران', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/YSL.jpg' ),
  'goyard' => array( 'Goyard', 'غويار', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/GOYARD.jpg' ),
  'christian-dior' => array( 'Christian Dior', 'كريستيان ديور', get_stylesheet_directory_uri() . '/images/brands/christian-dior.jpg' ),
  'bottega-veneta' => array( 'Bottega Veneta', 'بوتيغا فينيتا', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BOTTEGA.jpg' ),
  'burberry' => array( 'Burberry', 'بربري', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BURBERRY.jpg' ),
  'bvlgari' => array( 'Bvlgari', 'بولغري', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BVLGARI.jpg' ),
  'armani' => array( 'Armani', 'أرماني', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/ARMANI.jpg' ),
  'celine' => array( 'Celine', 'سيلين', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/CELINE.jpg' ),
  'chloe' => array( 'Chloe', 'Chloé', get_stylesheet_directory_uri() . '/images/brands/chloe.jpg' ),
  'valentino' => array( 'Valentino', 'فالنتينو', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/VALENTINO.jpg' ),
  'coach' => array( 'Coach', 'كوتش', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/COACH.jpg' ),
  'dolce-gabbana' => array( 'Dolce & Gabbana', 'دولتشي آند غابانا', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/DOLCE-AND-GABBANA.jpg' ),
  'ferragamo' => array( 'Ferragamo', 'فيراغامو', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/FERRAGAMO.jpg' ),
  'loewe' => array( 'Loewe', 'لويفي', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/LOEWE.jpg' ),
  'mcm' => array( 'MCM', 'إم سي إم', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/MCM.jpg' ),
  'balenciaga' => array( 'Balenciaga', 'بالنسياغا', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/BALENCIAGA.jpg' ),
  'carolina-herrera' => array( 'Carolina Herrera', 'كارولينا هيريرا', 'https://urbanclosetkw.com/wp-content/uploads/2026/10/CAROLINA.jpg' ),
  'alexander-wang' => array( 'Alexander Wang', 'Alexander Wang', get_stylesheet_directory_uri() . '/images/brands/alexander-wang.jpg' ),
);
$requested_brand_card = $brand_focus ? ( $brand_cards[ $requested_brands[0] ] ?? false ) : false;
if ( $brand_focus && ! $focused_term && $requested_brand_card ) {
  $focused_term = get_term_by( 'name', $requested_brand_card[0], 'product_brand' );
}
if ( $brand_focus ) {
  // A homepage brand view is isolated from any Shop All filter query string.
  $_GET['brand'] = array( $focused_term ? $focused_term->slug : $brand_page );
}
if ( $focused_term ) {
  // Use the actual taxonomy slug if it differs from the card's URL slug.
  $_GET['brand'] = array( $focused_term->slug );
  $selected_brands = array( $focused_term->slug );
}
$shop_query_args = urbancloset_shop_query_args();
if ( is_product_category() && 'all' === $selected_collection ) {
  $archive_category = get_queried_object();
  if ( $archive_category instanceof WP_Term && 'product_cat' === $archive_category->taxonomy ) {
    if ( empty( $shop_query_args['tax_query'] ) ) {
      $shop_query_args['tax_query'] = array( 'relation' => 'AND' );
    }
    $shop_query_args['tax_query'][] = array(
      'taxonomy'         => 'product_cat',
      'field'            => 'term_id',
      'terms'            => array( (int) $archive_category->term_id ),
      'include_children' => true,
    );
  }
}
$query = new WP_Query( $shop_query_args );
// Older products may have a brand saved as a product tag, or only in the
// product title, instead of the Brands taxonomy. Keep the selected category
// and other filters, but try those legacy brand locations before showing 0.
if ( ! $brand_focus && 0 === (int) $query->found_posts && 1 === count( $selected_brands ) ) {
  $brand_slug = $selected_brands[0];
  $brand_term = get_term_by( 'slug', $brand_slug, 'product_brand' );
  $brand_name = $brand_term && ! is_wp_error( $brand_term ) ? $brand_term->name : ucwords( str_replace( '-', ' ', $brand_slug ) );
  $fallback_args = $shop_query_args;
  if ( ! empty( $fallback_args['tax_query'] ) ) {
    foreach ( $fallback_args['tax_query'] as $tax_key => $tax_clause ) {
      if ( is_array( $tax_clause ) && isset( $tax_clause['taxonomy'] ) && 'product_brand' === $tax_clause['taxonomy'] ) {
        unset( $fallback_args['tax_query'][ $tax_key ] );
      }
    }
    $fallback_args['tax_query'] = array_values( $fallback_args['tax_query'] );
    if ( count( $fallback_args['tax_query'] ) <= 1 ) unset( $fallback_args['tax_query'] );
  }
  if ( taxonomy_exists( 'product_tag' ) ) {
    $tag_args = $fallback_args;
    $tag_args['tax_query'] = isset( $tag_args['tax_query'] ) ? $tag_args['tax_query'] : array( 'relation' => 'AND' );
    $tag_args['tax_query'][] = array( 'taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => array( $brand_slug ) );
    $tag_query = new WP_Query( $tag_args );
    if ( $tag_query->found_posts > 0 ) $query = $tag_query;
  }
  if ( 0 === (int) $query->found_posts ) {
    $fallback_args['s'] = $brand_name;
    $title_query = new WP_Query( $fallback_args );
    if ( $title_query->found_posts > 0 ) $query = $title_query;
  }
}
if ( $brand_focus && 0 === (int) $query->found_posts && $requested_brand_card ) {
  // Fallback for manually entered products that mention the brand in the title
  // but have not been assigned a WooCommerce brand term.
  $original_brand_filter = $_GET['brand'] ?? null;
  unset( $_GET['brand'] );
  $manual_brand_args = urbancloset_shop_query_args();
  $manual_brand_args['s'] = $requested_brand_card[0];
  $manual_brand_query = new WP_Query( $manual_brand_args );
  if ( $manual_brand_query->found_posts > 0 ) {
    $query = $manual_brand_query;
  }
  if ( null !== $original_brand_filter ) {
    $_GET['brand'] = $original_brand_filter;
  }
}
$total_bags = $query->found_posts;
$focused_card = $brand_focus
  ? ( $requested_brand_card ?: ( $focused_term ? array( $focused_term->name, $focused_term->name, '' ) : array( ucwords( str_replace( '-', ' ', $requested_brands[0] ) ), '', '' ) ) )
  : array( 'Brand', 'الماركة', '' );
?>

<?php if ( $brand_focus ) : ?>
<section class="brand-focus" aria-labelledby="brand-focus-title">
  <a class="brand-focus-back" href="<?php echo esc_url( home_url( '/#brands-title' ) ); ?>" aria-label="Back to all brands"><span aria-hidden="true">←</span> Back to all brands</a>
  <div class="brand-focus-card">
    <div class="brand-focus-image">
      <?php if ( ! empty( $focused_card[2] ) ) : ?><img src="<?php echo esc_url( $focused_card[2] ); ?>" alt="" /><?php endif; ?>
      <span class="brand-card-overlay"></span>
      <span class="brand-focus-image-note">AUTHENTICATED · PRELOVED</span>
    </div>
    <div class="brand-focus-info">
      <span class="brand-focus-kicker">THE DESIGNER EDIT</span>
      <h1 id="brand-focus-title" lang="en"><?php echo esc_html( $focused_card[0] ); ?></h1>
      <p class="brand-focus-count"><?php echo esc_html( $total_bags ); ?> bag<?php echo 1 === (int) $total_bags ? '' : 's'; ?> available</p>
      <a class="brand-focus-cta" href="#brand-products">VIEW THE COLLECTION <span aria-hidden="true">↓</span></a>
    </div>
  </div>
</section>
<?php else : ?>
<!-- SHOP HERO -->
<section class="shop-hero">
  <div class="shop-hero-eyebrow">The Full Collection</div>
  <h1 class="shop-hero-h1">Shop <em>All.</em></h1>
  <p class="shop-hero-sub">Every authenticated preloved bag, in one place. Filter by brand, condition, or price to find your next piece.</p>
  <div class="shop-hero-count"><?php echo esc_html( $total_bags ); ?> authenticated bag<?php echo 1 === (int) $total_bags ? '' : 's'; ?></div>
</section>

<!-- MARQUEE -->
<div class="marquee-bar">
  <span class="marquee-inner">
    100% AUTHENTICATED &nbsp;&bull;&nbsp; NEW DROPS EVERY FRIDAY &nbsp;&bull;&nbsp; FOLLOW US @URBANCLOSET &nbsp;&bull;&nbsp;
    FREE SHIPPING TO KUWAIT &middot; UAE &middot; BAHRAIN &middot; QATAR &nbsp;&bull;&nbsp; ALL BAGS 100% AUTHENTICATED &nbsp;&bull;&nbsp;
    100% AUTHENTICATED &nbsp;&bull;&nbsp; NEW DROPS EVERY FRIDAY &nbsp;&bull;&nbsp; FOLLOW US @URBANCLOSET &nbsp;&bull;&nbsp;
    FREE SHIPPING TO KUWAIT &middot; UAE &middot; BAHRAIN &middot; QATAR &nbsp;&bull;&nbsp; ALL BAGS 100% AUTHENTICATED &nbsp;&bull;&nbsp;
  </span>
</div>
<?php endif; ?>

<div id="<?php echo $brand_focus ? 'brand-products' : 'shop-products'; ?>" class="shop-layout<?php echo $brand_focus ? ' shop-layout-brand-focus' : ''; ?>">

  <!-- FILTER SIDEBAR -->
  <?php if ( ! $brand_focus ) : ?><aside class="filter-sidebar">
    <details class="shop-filter-panel" id="shopFilterPanel">
      <summary><span>Filter &amp; refine</span><span aria-hidden="true">+</span></summary>
    <form method="get" id="shopFilterForm">

      <div class="filter-head"><a class="filter-clear" href="<?php echo esc_url( strtok( $_SERVER['REQUEST_URI'], '?' ) ); ?>">Clear all filters</a></div>

      <?php
        $cat_terms = array();
        foreach ( array( 'preloved-authentic-bags', 'preloved-authentic-jewelry', 'preloved-authentic-shoes', 'other' ) as $category_slug ) {
          $category_term = get_term_by( 'slug', $category_slug, 'product_cat' );
          if ( ! $category_term || is_wp_error( $category_term ) ) continue;
          $cat_terms[] = $category_term;
        }
        if ( ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ) : ?>
      <div class="filter-group">
        <div class="filter-group-head"><span class="filter-label">Category</span></div>
        <select name="collection" aria-label="Choose a category" onchange="document.getElementById('shopFilterForm').submit();">
          <option value="all" <?php selected( 'all' === $selected_collection ); ?>>All categories</option>
          <?php foreach ( $cat_terms as $term ) : if ( 'uncategorized' === $term->slug ) continue; ?>
            <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $term->slug === $selected_collection ); ?>><?php echo esc_html( $term->name ); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <?php if ( $has_brand_tax ) :
        $brand_terms = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
        if ( ! empty( $brand_terms ) && ! is_wp_error( $brand_terms ) ) : ?>
      <div class="filter-group">
        <div class="filter-group-head"><span class="filter-label">Brand</span></div>
        <select name="brand[]" aria-label="Choose a brand" onchange="document.getElementById('shopFilterForm').submit();">
          <option value="">All brands</option>
          <?php foreach ( $brand_terms as $term ) : ?>
            <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( in_array( $term->slug, $selected_brands, true ) ); ?>><?php echo esc_html( strtoupper( $term->name ) ); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; endif; ?>

      <div class="filter-group">
        <div class="filter-group-head"><span class="filter-label">Price (<?php echo esc_html( get_woocommerce_currency() ); ?>)</span></div>
        <div class="filter-price-row">
          <input type="number" name="price_min" placeholder="Min" aria-label="Minimum price" value="<?php echo $price_min; ?>" min="0" onchange="document.getElementById('shopFilterForm').submit();" />
          <span class="filter-price-dash">&mdash;</span>
          <input type="number" name="price_max" placeholder="Max" aria-label="Maximum price" value="<?php echo $price_max; ?>" min="0" onchange="document.getElementById('shopFilterForm').submit();" />
        </div>
      </div>

      <?php if ( 'newest' !== $selected_sort ) : ?>
        <input type="hidden" name="sort" value="<?php echo esc_attr( $selected_sort ); ?>" />
      <?php endif; ?>

    </form>
    </details>
    <script>
      (function () {
        var panel = document.getElementById('shopFilterPanel');
        if (!panel) return;
        var mobile = window.matchMedia('(max-width: 640px)');
        var setInitialState = function () { panel.open = !mobile.matches; };
        var updateToggle = function () { panel.querySelector('summary span:last-child').textContent = panel.open ? '−' : '+'; };
        setInitialState();
        updateToggle();
        panel.addEventListener('toggle', updateToggle);
        if (mobile.addEventListener) mobile.addEventListener('change', setInitialState);
        else if (mobile.addListener) mobile.addListener(setInitialState);
      })();
    </script>
  </aside><?php endif; ?>

  <!-- MAIN -->
  <div class="shop-main">
    <?php if ( ! $brand_focus ) : ?><div class="shop-toolbar">
      <div class="shop-count"><?php echo esc_html( $total_bags ); ?> bag<?php echo 1 === (int) $total_bags ? '' : 's'; ?></div>
      <div class="shop-sort">
        <label for="shopSort">Sort</label>
        <select id="shopSort" onchange="
          var url = new URL(window.location.href);
          url.searchParams.set('sort', this.value);
          url.searchParams.delete('paged');
          window.location.href = url.toString();
        ">
          <option value="newest" <?php selected( $selected_sort, 'newest' ); ?>>Newest First</option>
          <option value="price_asc" <?php selected( $selected_sort, 'price_asc' ); ?>>Price: Low to High</option>
          <option value="price_desc" <?php selected( $selected_sort, 'price_desc' ); ?>>Price: High to Low</option>
          <option value="rating" <?php selected( $selected_sort, 'rating' ); ?>>Top Rated</option>
        </select>
      </div>
    </div><?php endif; ?>

    <?php if ( $brand_focus ) : ?>
    <div class="brand-results-head"><span>THE COLLECTION</span><span><?php echo esc_html( $total_bags ); ?> PIECES</span></div>
    <?php endif; ?>
    <div class="product-grid product-grid-shop">
      <?php if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
          global $product;
          if ( ! $product instanceof WC_Product ) $product = wc_get_product( get_the_ID() );
          urbancloset_render_product_card( $product );
        endwhile;
        wp_reset_postdata();
      else : ?>
        <div class="grid-loading"><?php echo $brand_focus ? 'No bags from this brand yet.' : 'No bags match those filters — try clearing a few.'; ?></div>
      <?php endif; ?>
    </div>

    <?php if ( $query->max_num_pages > 1 ) : ?>
    <div class="shop-pagination">
      <?php
        echo paginate_links( array(
          'base'      => add_query_arg( 'paged', '%#%' ),
          'format'    => '',
          'current'   => max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ) ),
          'total'     => $query->max_num_pages,
          'prev_text' => 'Previous',
          'next_text' => 'Next',
        ) );
      ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php get_footer(); ?>
