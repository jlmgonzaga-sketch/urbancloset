document.addEventListener('DOMContentLoaded', function () {
  /* ── DRAWER ─── */
  var drawer   = document.getElementById('sideDrawer');
  var overlay  = document.getElementById('drawerOverlay');
  var ham      = document.getElementById('navHam');
  var closeBtn = document.getElementById('drawerClose');

  function openDrawer() {
    if (!drawer) return;
    drawer.classList.add('open');
    if (overlay) overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    if (!drawer) return;
    drawer.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (ham) ham.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  if (overlay) overlay.addEventListener('click', closeDrawer);


  /* ── SERIES CAROUSEL ─── */
  var track = document.getElementById('seriesTrack');

  if (track) {
    var seriesPos = 0;
    var seriesCardW = 174;
    var seriesVisible = 5;
    var cards = track.querySelectorAll('.series-card');
    var seriesTotal = cards.length;
    var prevBtn = document.getElementById('seriesPrev');
    var nextBtn = document.getElementById('seriesNext');

    function scrollSeries(dir) {
      var max = Math.max(0, seriesTotal - seriesVisible);

      seriesPos = Math.max(
        0,
        Math.min(seriesPos + dir, max)
      );

      track.style.transform =
        'translateX(-' + (seriesPos * seriesCardW) + 'px)';

      if (prevBtn) prevBtn.disabled = seriesPos === 0;
      if (nextBtn) nextBtn.disabled = seriesPos >= max;
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        scrollSeries(-1);
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        scrollSeries(1);
      });
    }

    scrollSeries(0);
  }


  /* ── SIGN IN PAGE ─── */
  var signupToggle = document.getElementById('ucSignupToggle');
  var registerCard = document.getElementById('ucRegisterCard');

  if (signupToggle && registerCard) {
    signupToggle.addEventListener('click', function () {

      var willShow =
        registerCard.hasAttribute('hidden');

      if (willShow) {

        registerCard.hidden = false;

        var authLayout =
          document.getElementById('ucAuthLayout');

        if (authLayout) {
          authLayout.classList.add(
            'auth-layout-expanded'
          );
        }

        signupToggle.setAttribute(
          'aria-expanded',
          'true'
        );

        registerCard.scrollIntoView({
          behavior: 'smooth',
          block: 'nearest'
        });

      } else {

        registerCard.hidden = true;

        signupToggle.setAttribute(
          'aria-expanded',
          'false'
        );

      }

    });
  }


  /* ── ACCORDIONS ─── */
  document.querySelectorAll('.accord-head').forEach(
    function (head) {

      head.addEventListener('click', function () {

        var accord =
          head.closest('.accord');

        if (accord) {
          accord.classList.toggle('open');
        }

      });

    }
  );


  /* ── MINI CART ─── */
  var minicartWrap =
    document.getElementById('ucMinicartWrap');

  var minicartTrigger =
    document.getElementById('ucMinicartTrigger');

  var minicartClose =
    document.getElementById('ucMinicartClose');

  function ucIsTouch() {
    return window.matchMedia(
      '(max-width: 900px)'
    ).matches;
  }

  if (minicartWrap && minicartTrigger) {

    minicartTrigger.addEventListener(
      'click',
      function (e) {

        if (
          !minicartWrap.classList.contains('open')
        ) {

          e.preventDefault();

          minicartWrap.classList.add('open');

        }

      }
    );


    if (minicartClose) {

      minicartClose.addEventListener(
        'click',
        function () {

          minicartWrap.classList.remove(
            'open'
          );

        }
      );

    }


    document.addEventListener(
      'click',
      function (e) {

        if (
          !minicartWrap.contains(e.target)
        ) {

          minicartWrap.classList.remove(
            'open'
          );

        }

      }
    );

  }


  /* ── REFRESH MINI CART ─── */
  if (typeof jQuery !== 'undefined') {

    jQuery(document.body).trigger(
      'wc_fragment_refresh'
    );

  }


  /* ── ADD TO CART ANIMATION ─── */
  (function () {

    var cartIcon =
      document.getElementById(
        'ucMinicartTrigger'
      );


    function flyToBag(sourceEl) {

      if (!cartIcon || !sourceEl) return;


      var imgEl =
        sourceEl.closest(
          '.product-card, .uc-single-product-gallery'
        );


      var img =
        imgEl
          ? imgEl.querySelector('img')
          : null;


      if (!img) return;


      var startRect =
        img.getBoundingClientRect();

      var endRect =
        cartIcon.getBoundingClientRect();


      img.classList.remove(
        'uc-img-lift'
      );

      void img.offsetWidth;

      img.classList.add(
        'uc-img-lift'
      );


      window.setTimeout(
        function () {

          img.classList.remove(
            'uc-img-lift'
          );

        },
        260
      );


      var clone =
        img.cloneNode(true);

      clone.classList.add(
        'uc-fly-thumb'
      );


      clone.style.width =
        startRect.width + 'px';

      clone.style.height =
        startRect.height + 'px';

      clone.style.top =
        startRect.top + 'px';

      clone.style.left =
        startRect.left + 'px';


      document.body.appendChild(
        clone
      );


      var endX =
        endRect.left +
        (endRect.width / 2) -
        (
          startRect.left +
          startRect.width / 2
        );


      var endY =
        endRect.top +
        (endRect.height / 2) -
        (
          startRect.top +
          startRect.height / 2
        );


      requestAnimationFrame(
        function () {

          clone.style.transform =
            'translate(' +
            endX +
            'px, ' +
            endY +
            'px) scale(0.15)';

          clone.style.opacity =
            '0.2';

        }
      );


      window.setTimeout(
        function () {

          if (
            clone &&
            clone.parentNode
          ) {
            clone.remove();
          }

        },
        650
      );

    }


    function showAddedToast() {

      var existing =
        document.querySelector(
          '.uc-added-toast'
        );


      if (existing) {
        existing.remove();
      }


      var toast =
        document.createElement('div');


      toast.className =
        'uc-added-toast';


      toast.textContent =
        'Added to Bag';


      document.body.appendChild(
        toast
      );


      requestAnimationFrame(
        function () {

          toast.classList.add(
            'show'
          );

        }
      );


      window.setTimeout(
        function () {

          toast.classList.remove(
            'show'
          );


          window.setTimeout(
            function () {

              if (
                toast &&
                toast.parentNode
              ) {
                toast.remove();
              }

            },
            250
          );

        },
        1600
      );

    }


    function bounceCartIcon() {

      if (!cartIcon) return;


      cartIcon.classList.remove(
        'uc-cart-bounce'
      );


      void cartIcon.offsetWidth;


      cartIcon.classList.add(
        'uc-cart-bounce'
      );

    }


    /*
     * ADD TO CART
     *
     * IMPORTANT:
     * This handler does NOT call preventDefault().
     *
     * WooCommerce's own add-to-cart.js
     * handles the actual AJAX request.
     */
    document.addEventListener(
      'click',
      function (e) {

        var btn =
          e.target.closest
            ? e.target.closest(
                '.add_to_cart_button'
              )
            : null;


        if (!btn) return;


        /*
         * Variable and grouped products
         * must open the product page.
         */
        if (
          btn.classList.contains(
            'product_type_variable'
          ) ||
          btn.classList.contains(
            'product_type_grouped'
          )
        ) {
          return;
        }


        /*
         * Do NOT preventDefault().
         *
         * WooCommerce needs the original
         * click event.
         */
        flyToBag(btn);

      }
    );


    /*
     * This event only fires when WooCommerce
     * successfully adds the product.
     */
    if (typeof jQuery !== 'undefined') {

      jQuery(document.body).on(
        'added_to_cart',
        function () {

          bounceCartIcon();

          showAddedToast();


          if (minicartWrap) {

            minicartWrap.classList.add(
              'open'
            );


            window.clearTimeout(
              minicartWrap._ucAutoCloseTimer
            );


            minicartWrap._ucAutoCloseTimer =
              window.setTimeout(
                function () {

                  minicartWrap.classList.remove(
                    'open'
                  );

                },
                3500
              );

          }

        }
      );

    }

  })();


  /* ── CART PAGE ─── */

  if (
    document.body.classList.contains(
      'woocommerce-cart'
    )
  ) {

    /*
     * WooCommerce handles the actual
     * cart functionality.
     */

  }


  /* ── CHECKOUT CUSTOMIZATION ─── */

  if (
    document.body.classList.contains(
      'woocommerce-checkout'
    )
  ) {


    function ucBuildCheckoutTrustList() {

      /* Checkout's root is WooCommerce's own grid layout now (see
         style.css). Adding a message as a direct child of that grid
         would become an unplaced fourth column, so the reassurance
         message lives in page-checkout.php instead, above the grid. */
      return;

    }


    function ucBuildCheckoutBanner() {

      return;

    }


    function ucCheckoutInject() {

      ucBuildCheckoutTrustList();

      ucBuildCheckoutBanner();

      ucArrangeCheckoutSteps();

      /* Postcode/ZIP and state/county are not collected for this store. The
         PHP locale filters remove them from new Checkout Block renders; this
         also removes a field retained by an already-mounted block. */
      document.querySelectorAll(
        'input[autocomplete="postal-code"], input[name*="postcode"], input[name*="postal_code"], input[autocomplete="address-level1"], input[name*="state"], select[name*="state"]'
      ).forEach(function (field) {
        field.required = false;
        field.value = '';
        var row = field.closest('.wc-block-components-text-input, .wc-block-components-address-form__postcode, .form-row');
        if (row) row.style.display = 'none';
      });

      ucUpdateCheckoutButtonState();

    }


    /*
     * Assigns each rendered checkout step to one of the three visual
     * columns (see style.css: 1 = address, 2 = shipping & payment,
     * 3 = order summary sidebar) by reading its own visible heading.
     *
     * This is the ONLY place layout decisions are made in JS — it
     * never moves DOM nodes, it only labels them with a
     * data-uc-col attribute. CSS grid-column placement in style.css
     * does the rest. WooCommerce can restructure its internal wrapper
     * classnames between releases; the visible step heading text is
     * far more stable, so this keeps working without edits here.
     */
    function ucArrangeCheckoutSteps() {

      var steps = document.querySelectorAll(
        '.wc-block-components-checkout-step'
      );

      steps.forEach(function (step) {

        /* Checkout Block releases use different heading elements. The full
           step text is stable, and avoids depending on a particular tag. */
        var title = step.textContent.trim().toLowerCase();

        var col = 'full'; // order note, terms, actions, or anything unrecognized

        if (
          title.indexOf('contact information') !== -1 ||
          title.indexOf('shipping address') !== -1 ||
          title.indexOf('shipping options') !== -1
        ) {
          col = 'address';
        } else if (
          title.indexOf('payment options') !== -1
        ) {
          col = 'middle';
        }

        step.setAttribute('data-uc-col', col);

      });

    }

    /* WooCommerce Blocks can leave its button technically enabled while the
       address is incomplete. Evaluate the real required inputs instead, so
       green always means the shopper is actually ready to place an order. */
    function ucUpdateCheckoutButtonState() {
      var checkout = document.querySelector('.wp-block-woocommerce-checkout');
      if (!checkout) return;

      var requiredFields = Array.prototype.slice.call(checkout.querySelectorAll(
        'input[required], select[required], textarea[required], ' +
        'input[aria-required="true"], select[aria-required="true"], textarea[aria-required="true"]'
      )).filter(function (field) {
        return field.offsetParent !== null && field.getAttribute('autocomplete') !== 'postal-code';
      });

      var fieldsComplete = requiredFields.length > 0 && requiredFields.every(function (field) {
        if (field.type === 'checkbox' || field.type === 'radio') return field.checked;
        return String(field.value || '').trim().length > 0;
      });
      var hasInvalidValue = requiredFields.some(function (field) {
        var hasValue = (field.type === 'checkbox' || field.type === 'radio') ? field.checked : String(field.value || '').trim().length > 0;
        return hasValue && typeof field.checkValidity === 'function' && !field.checkValidity();
      });
      var hasCheckoutErrors = hasInvalidValue || !!checkout.querySelector(
        '[aria-invalid="true"], .wc-block-components-validation-error:not([hidden]), .wc-block-components-notice-banner.is-error'
      );

      checkout.querySelectorAll('.wc-block-components-checkout-place-order-button').forEach(function (button) {
        button.classList.toggle('uc-checkout-ready', fieldsComplete && !hasCheckoutErrors);
        button.classList.toggle('uc-checkout-invalid', hasCheckoutErrors);
      });
    }


    // Run once immediately in case the block has already mounted its
    // steps (e.g. fast reload / cached render) — avoids a flash of an
    // unarranged layout while we wait for the observer below.
    ucCheckoutInject();

    document.addEventListener('input', ucUpdateCheckoutButtonState, true);
    document.addEventListener('change', ucUpdateCheckoutButtonState, true);
    document.addEventListener('blur', ucUpdateCheckoutButtonState, true);


    /*
     * FIX: The WooCommerce Checkout Block renders its steps
     * client-side (via the Store API) *after* DOMContentLoaded, and
     * can swap its own subtree wholesale during hydration rather than
     * patching it in place. Observing .wp-block-woocommerce-checkout
     * directly is unreliable — at the moment this script runs, that
     * node may not exist yet, or may get fully replaced later in a
     * way that isn't guaranteed to fire mutations against a stale
     * reference. Observing document.body instead guarantees the
     * observer is attached from the start and keeps working through
     * any internal re-renders. The callback is debounced because the
     * block can emit many mutations per render pass.
     */
    var ucCheckoutDebounce = null;

    var ucCheckoutObserver =
      new MutationObserver(
        function () {

          window.clearTimeout(ucCheckoutDebounce);

          ucCheckoutDebounce = window.setTimeout(
            ucCheckoutInject,
            50
          );

        }
      );


    ucCheckoutObserver.observe(
      document.body,
      {
        childList: true,
        subtree: true
      }
    );

  }

});


/* ═══════════════════════════════════════════════════════════════
   ACCOUNT MODAL
   ═══════════════════════════════════════════════════════════════ */

document.addEventListener(
  'DOMContentLoaded',
  function () {

    var overlay =
      document.getElementById(
        'ucAccountOverlay'
      );


    var modal =
      document.getElementById(
        'ucAccountModal'
      );


    if (
      !overlay ||
      !modal ||
      typeof UC_ACCOUNT === 'undefined'
    ) {
      return;
    }


    var closeBtn =
      document.getElementById(
        'ucAccountModalClose'
      );


    var defaultPanel =
      UC_ACCOUNT.isLoggedIn
        ? 'account'
        : 'login';


    function panels() {

      return Array.prototype.slice.call(
        modal.querySelectorAll(
          '[data-uc-panel]'
        )
      );

    }


    function clearErrors() {

      panels().forEach(
        function (p) {

          var err =
            p.querySelector(
              '[data-uc-form-error]'
            );


          if (err) {

            err.hidden = true;

            err.textContent = '';

          }

        }
      );

    }


    function showPanel(name) {

      panels().forEach(
        function (p) {

          p.hidden =
            p.getAttribute(
              'data-uc-panel'
            ) !== name;

        }
      );


      var successBox =
        modal.querySelector(
          '[data-uc-success]'
        );


      if (successBox) {
        successBox.hidden = true;
      }


      clearErrors();


      var active =
        modal.querySelector(
          '[data-uc-panel="' +
          name +
          '"]'
        );


      var firstField =
        active
          ? active.querySelector(
              'input:not([type="hidden"])'
            )
          : null;


      if (firstField) {

        window.setTimeout(
          function () {

            firstField.focus();

          },
          150
        );

      }

    }


    function showError(
      panelEl,
      message
    ) {

      var err =
        panelEl.querySelector(
          '[data-uc-form-error]'
        );


      if (!err) return;


      err.textContent =
        message;


      err.hidden =
        false;

    }


    function openModal() {

      showPanel(
        defaultPanel
      );


      overlay.classList.add(
        'open'
      );


      modal.classList.add(
        'open'
      );


      overlay.setAttribute(
        'aria-hidden',
        'false'
      );


      modal.setAttribute(
        'aria-hidden',
        'false'
      );


      document.body.style.overflow =
        'hidden';

    }


    function closeModal() {

      overlay.classList.remove(
        'open'
      );


      modal.classList.remove(
        'open'
      );


      overlay.setAttribute(
        'aria-hidden',
        'true'
      );


      modal.setAttribute(
        'aria-hidden',
        'true'
      );


      document.body.style.overflow =
        '';

    }


    /* ACCOUNT TRIGGER */

    document.addEventListener(
      'click',
      function (e) {

        var trigger =
          e.target.closest
            ? e.target.closest(
                '[data-uc-account-trigger]'
              )
            : null;


        if (trigger) {

          e.preventDefault();

          openModal();

        }

      }
    );


    if (closeBtn) {

      closeBtn.addEventListener(
        'click',
        closeModal
      );

    }


    overlay.addEventListener(
      'click',
      closeModal
    );


    document.addEventListener(
      'keydown',
      function (e) {

        if (
          e.key === 'Escape' &&
          modal.classList.contains(
            'open'
          )
        ) {

          closeModal();

        }

      }
    );


    /* PANEL SWITCHING */

    modal.addEventListener(
      'click',
      function (e) {

        var btn =
          e.target.closest
            ? e.target.closest(
                '[data-uc-open-panel]'
              )
            : null;


        if (!btn) return;


        showPanel(
          btn.getAttribute(
            'data-uc-open-panel'
          )
        );

      }
    );


    /* PASSWORD SHOW / HIDE */

    modal.addEventListener(
      'click',
      function (e) {

        var toggle =
          e.target.closest
            ? e.target.closest(
                '[data-uc-toggle-pass]'
              )
            : null;


        if (!toggle) return;


        var input =
          document.getElementById(
            toggle.getAttribute(
              'data-uc-toggle-pass'
            )
          );


        if (!input) return;


        input.type =
          input.type === 'password'
            ? 'text'
            : 'password';


        toggle.classList.toggle(
          'is-visible',
          input.type === 'text'
        );

      }
    );


    /* LOADING STATE */

    function setLoading(
      form,
      loading
    ) {

      var btn =
        form.querySelector(
          '.uc-account-submit'
        );


      if (!btn) return;


      btn.disabled =
        loading;


      var label =
        btn.querySelector(
          '.uc-btn-label'
        );


      var spinner =
        btn.querySelector(
          '.uc-btn-spinner'
        );


      if (label) {

        label.style.visibility =
          loading
            ? 'hidden'
            : 'visible';

      }


      if (spinner) {

        spinner.hidden =
          !loading;

      }

    }


    /* SUCCESS */

    function showSuccess(
      panelEl,
      message,
      redirectUrl
    ) {

      panelEl.hidden =
        true;


      var successBox =
        modal.querySelector(
          '[data-uc-success]'
        );


      if (!successBox) return;


      var messageEl =
        successBox.querySelector(
          '.uc-account-success-msg'
        );


      if (messageEl) {

        messageEl.textContent =
          message;

      }


      successBox.hidden =
        false;


      window.setTimeout(
        function () {

          if (redirectUrl) {

            window.location.href =
              redirectUrl;

          } else {

            window.location.reload();

          }

        },
        1100
      );

    }


    /* AJAX FORM POST */

    function postForm(
      action,
      formEl
    ) {

      var data =
        new FormData(
          formEl
        );


      data.append(
        'action',
        action
      );


      return fetch(
        UC_ACCOUNT.ajaxUrl,
        {
          method: 'POST',
          credentials: 'same-origin',
          body: data
        }
      ).then(
        function (res) {

          return res.json();

        }
      );

    }


    /* LOGIN */

    var loginForm =
      document.getElementById(
        'ucLoginForm'
      );


    if (loginForm) {

      loginForm.addEventListener(
        'submit',
        function (e) {

          e.preventDefault();


          var panelEl =
            loginForm.closest(
              '[data-uc-panel]'
            );


          clearErrors();


          setLoading(
            loginForm,
            true
          );


          postForm(
            'uc_account_login',
            loginForm
          )
            .then(
              function (res) {

                setLoading(
                  loginForm,
                  false
                );


                if (res.success) {

                  showSuccess(
                    panelEl,
                    'Welcome back, ' +
                      res.data.name +
                      '.',
                    null
                  );

                } else {

                  showError(
                    panelEl,
                    res.data &&
                    res.data.message
                      ? res.data.message
                      : 'Something went wrong. Please try again.'
                  );

                }

              }
            )
            .catch(
              function () {

                setLoading(
                  loginForm,
                  false
                );


                showError(
                  panelEl,
                  'Something went wrong. Please try again.'
                );

              }
            );

        }
      );

    }


    /* REGISTER */

    var registerForm =
      document.getElementById(
        'ucRegisterForm'
      );


    if (registerForm) {

      registerForm.addEventListener(
        'submit',
        function (e) {

          e.preventDefault();


          var panelEl =
            registerForm.closest(
              '[data-uc-panel]'
            );


          clearErrors();


          var pass1 =
            registerForm.querySelector(
              '[name="password"]'
            ).value;


          var pass2 =
            registerForm.querySelector(
              '[name="password_confirm"]'
            ).value;


          if (pass1 !== pass2) {

            showError(
              panelEl,
              'Passwords do not match.'
            );

            return;

          }


          setLoading(
            registerForm,
            true
          );


          postForm(
            'uc_account_register',
            registerForm
          )
            .then(
              function (res) {

                setLoading(
                  registerForm,
                  false
                );


                if (res.success) {

                  showSuccess(
                    panelEl,
                    'Account created. Welcome, ' +
                      res.data.name +
                      '.',
                    null
                  );

                } else {

                  showError(
                    panelEl,
                    res.data &&
                    res.data.message
                      ? res.data.message
                      : 'Something went wrong. Please try again.'
                  );

                }

              }
            )
            .catch(
              function () {

                setLoading(
                  registerForm,
                  false
                );


                showError(
                  panelEl,
                  'Something went wrong. Please try again.'
                );

              }
            );

        }
      );

    }


    /* SIGN OUT */

    var signoutBtn =
      document.getElementById(
        'ucAccountSignoutConfirm'
      );


    if (signoutBtn) {

      signoutBtn.addEventListener(
        'click',
        function () {

          var panelEl =
            signoutBtn.closest(
              '[data-uc-panel]'
            );


          var btnLabel =
            signoutBtn.querySelector(
              '.uc-btn-label'
            );


          var btnSpinner =
            signoutBtn.querySelector(
              '.uc-btn-spinner'
            );


          signoutBtn.disabled =
            true;


          if (btnLabel) {

            btnLabel.style.visibility =
              'hidden';

          }


          if (btnSpinner) {

            btnSpinner.hidden =
              false;

          }


          var data =
            new FormData();


          data.append(
            'action',
            'uc_account_logout'
          );


          data.append(
            'nonce',
            UC_ACCOUNT.logoutNonce
          );


          fetch(
            UC_ACCOUNT.ajaxUrl,
            {
              method: 'POST',
              credentials: 'same-origin',
              body: data
            }
          )
            .then(
              function (res) {

                return res.json();

              }
            )
            .then(
              function (res) {

                if (res.success) {

                  showSuccess(
                    panelEl,
                    "You've been signed out.",
                    res.data &&
                    res.data.redirect
                      ? res.data.redirect
                      : null
                  );

                } else {

                  signoutBtn.disabled =
                    false;


                  if (btnLabel) {

                    btnLabel.style.visibility =
                      'visible';

                  }


                  if (btnSpinner) {

                    btnSpinner.hidden =
                      true;

                  }

                }

              }
            )
            .catch(
              function () {

                signoutBtn.disabled =
                  false;


                if (btnLabel) {

                  btnLabel.style.visibility =
                    'visible';

                }


                if (btnSpinner) {

                  btnSpinner.hidden =
                    true;

                }

              }
            );

        }
      );

    }

  }
);
