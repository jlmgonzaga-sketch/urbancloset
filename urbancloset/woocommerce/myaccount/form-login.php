<?php
defined( 'ABSPATH' ) || exit;

$uc_show_register = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>

<section class="account-hero">
  <div class="account-hero-inner">
    <div class="account-hero-eyebrow">Welcome</div>
    <h1 class="account-hero-h1">Sign <em>In.</em></h1>
    <p class="account-hero-sub">Track orders, save addresses, and check out faster next time.</p>
  </div>
  <div class="account-hero-deco" aria-hidden="true">
    <svg width="220" height="220" viewBox="0 0 220 220" fill="none">
      <circle cx="110" cy="110" r="100" stroke="currentColor" stroke-width="0.6"/>
      <text x="110" y="132" text-anchor="middle" font-family="Georgia, serif" font-size="80" fill="currentColor">UC</text>
    </svg>
  </div>
</section>

<div class="account-shell">
<div class="auth-layout auth-layout-solo" id="ucAuthLayout">

  <?php do_action( 'woocommerce_before_customer_login_form' ); ?>

  <div class="auth-card">
    <h2 class="auth-card-title">Returning Customer</h2>
    <p class="auth-card-sub">Sign in with your account details below.</p>

    <form class="auth-form woocommerce-form woocommerce-form-login login" method="post">

      <?php do_action( 'woocommerce_login_form_start' ); ?>

      <p class="form-row form-row-wide">
        <label for="username" class="auth-label"><?php esc_html_e( 'Email or Username', 'woocommerce' ); ?></label>
        <input type="text" class="auth-input" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
      </p>
      <p class="form-row form-row-wide">
        <label for="password" class="auth-label"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
        <input class="auth-input" type="password" name="password" id="password" autocomplete="current-password" />
      </p>

      <?php do_action( 'woocommerce_login_form' ); ?>

      <p class="auth-row-between">
        <label class="auth-remember">
          <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e( 'Remember me', 'woocommerce' ); ?>
        </label>
        <?php if ( wc_get_page_permalink( 'myaccount' ) ) : ?>
          <a class="auth-forgot" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
        <?php endif; ?>
      </p>

      <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

      <button type="submit" class="filter-apply-btn auth-submit" name="login" value="<?php esc_attr_e( 'Sign In', 'woocommerce' ); ?>"><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></button>

      <input type="hidden" name="redirect" value="<?php echo esc_url( isset( $_SERVER['REQUEST_URI'] ) ? home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''  ); ?>" />

      <?php do_action( 'woocommerce_login_form_end' ); ?>

    </form>

    <?php if ( $uc_show_register ) : ?>
    <div class="auth-signup-prompt">
      <span>Don't have an account?</span>
      <button type="button" class="auth-signup-btn" id="ucSignupToggle" aria-expanded="false" aria-controls="ucRegisterCard">Create Account</button>
    </div>
    <?php endif; ?>
  </div>

  <?php if ( $uc_show_register ) : ?>
  <div class="auth-card auth-card-register" id="ucRegisterCard" hidden>
    <h2 class="auth-card-title">New Here?</h2>
    <p class="auth-card-sub">Create an account to start shopping.</p>

    <form method="post" class="auth-form woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

      <?php do_action( 'woocommerce_register_form_start' ); ?>

      <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
        <p class="form-row form-row-wide">
          <label for="reg_username" class="auth-label"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
          <input type="text" class="auth-input" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
        </p>
      <?php endif; ?>

      <p class="form-row form-row-wide">
        <label for="reg_email" class="auth-label"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="email" class="auth-input" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
      </p>

      <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
        <p class="form-row form-row-wide">
          <label for="reg_password" class="auth-label"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
          <input type="password" class="auth-input" name="password" id="reg_password" autocomplete="new-password" />
        </p>
      <?php else : ?>
        <p class="auth-hint"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
      <?php endif; ?>

      <?php do_action( 'woocommerce_register_form' ); ?>

      <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

      <button type="submit" class="btn-outline auth-submit auth-submit-outline" name="register" value="<?php esc_attr_e( 'Create Account', 'woocommerce' ); ?>"><?php esc_html_e( 'Create Account', 'woocommerce' ); ?></button>

      <?php do_action( 'woocommerce_register_form_end' ); ?>

    </form>
  </div>
  <?php endif; ?>

  <?php do_action( 'woocommerce_after_customer_login_form' ); ?>

</div>
</div>