# UrbanCloset WordPress Theme — Homepage + Shop All

This is a custom WordPress theme that recreates your UrbanCloset design
using real WordPress/WooCommerce data instead of the static HTML/JS demo.

**Pages included so far:**
- Homepage (hero, marquee, New Arrivals, Collections, As Worn, Why Trust
  UrbanCloset, How to Order, footer)
- **Shop All** — hero + product count, sidebar filters (Brand, Condition,
  Collections, Price), sort dropdown, 4-column product grid, pagination.
  This is a real, working template (`archive-product.php`) — filters submit
  a normal form and reload the page with matching products, no page-builder
  involved.

## 1. Requirements
- WordPress installed
- **WooCommerce plugin** installed & activated (Plugins → Add New → search
  "WooCommerce" → Install → Activate). Run its setup wizard, set your
  currency to KWD, and add a payment method.

## 2. Install the theme
1. Zip the `urbancloset` folder (if not already zipped).
2. In wp-admin: **Appearance → Themes → Add New → Upload Theme**.
3. Upload `urbancloset.zip`, then **Activate**.

## 3. Set up your shop content
- **Products** → Add New: add your bags. Set price, upload a photo
  (this becomes the product image on the homepage cards).
- **Product categories** (Products → Categories) power the "Collections"
  section — name them "Series 1", "Series 2", etc. (or whatever you like),
  add a category thumbnail image if you want the photo background shown
  on the card.
- The **New Arrivals** grid on the homepage automatically shows your 5
  most recently added products.
- **Condition badge & filter** ("Excellent" / "Very Good" / "Good"): this
  needs to be a **global** attribute so it can be filtered, not just a
  per-product custom one:
  1. Go to **Products → Attributes → Add New**, name it `Condition`
     (WooCommerce will create it as `pa_condition`).
  2. Click **Configure terms** on it and add values: Excellent, Very Good, Good.
  3. On each product, go to **Product data → Attributes**, add the
     `Condition` attribute, tick the value that applies.
  If this attribute doesn't exist yet, the Condition filter simply won't
  show in the sidebar — everything else still works.
- **Brand filter**: uses WooCommerce's built-in Brands taxonomy. If you
  don't see a "Brand" field on the product edit screen, go to
  **WooCommerce → Settings → Products** (or **Features**, depending on
  your WooCommerce version) and enable **Product Brands**, then set a
  brand per product. If Brands isn't enabled, the Brand filter just won't
  show — everything else still works.
- **Collections filter** uses your existing Product Categories (Series 1,
  Series 2, etc.) — nothing extra to set up.

## 4. Update contact details
Open `functions.php` and edit the constants near the top:
```php
define( 'UC_WHATSAPP_NUMBER', '96590910123' );
define( 'UC_PHONE_DISPLAY',   '+965 9091 0123' );
define( 'UC_EMAIL',           'admin@urbancloset.kw' );
define( 'UC_ADDRESS',         'Kuwait, Salmiya, Salem Al Mubarak Street' );
```

## 5. "As Worn" images
These 3 images currently come from the theme's own
`images/banners/` folder and are hardcoded in `front-page.php`
(search for `worn-card`). Swap in your own by uploading new images
to that folder (or via Media Library + updating the `src` paths),
and update the brand/name captions.

## 6. Site title & tagline
**Settings → General** — Site Title becomes the logo text in the top
nav and footer; Tagline becomes the footer description line.

## 7. Menus (drawer links)
The side drawer's "Collections" links are generated automatically from
your Product Categories — no menu setup needed for those.

## What's included
- `style.css` — all design tokens & styling from your original HTML
- `functions.php` — theme setup, WooCommerce hook-in, helper functions
- `header.php` — topbar, sticky nav, side drawer
- `front-page.php` — the homepage sections
- `archive-product.php` — the Shop All page (hero, filters, product grid)
- `footer.php` — footer
- `index.php` — fallback template for other pages until you build more
- `js/main.js` — drawer open/close, collections carousel, accordions

## Next steps
We can build out: the single Product page, Cart/Checkout styling
(WooCommerce handles the logic, we just need to skin it), and the
admin-side pieces — just say the word.
