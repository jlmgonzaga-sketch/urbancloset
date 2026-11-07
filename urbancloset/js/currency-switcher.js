(function () {
  'use strict';

  var selector = document.querySelector('[data-uc-currency]');
  if (!selector) return;
  var selected = localStorage.getItem('uc-display-currency') || 'KWD';
  /* Fallback estimates guarantee the selector still changes the display.
     Current live rates replace these when the rate service is available. */
  var rates = { KWD: 1, USD: 3.25, EUR: 2.99 };

  function format(amount, currency) {
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: currency, currencyDisplay: 'narrowSymbol', minimumFractionDigits: currency === 'KWD' ? 3 : 2, maximumFractionDigits: currency === 'KWD' ? 3 : 2 }).format(amount);
  }
  function priceNodes() {
    return document.querySelectorAll('.woocommerce-Price-amount, .amount, .price, .wc-block-formatted-money-amount, .wc-block-components-formatted-money-amount, .wc-block-components-totals-item__value, .wc-block-components-order-summary-item__total-price, .wc-block-components-order-summary-item__individual-prices');
  }
  function baseValue(node) {
    if (node.dataset.ucKwd) return Number(node.dataset.ucKwd);
    var value = (node.textContent || '').replace(/,/g, '').match(/\d+(?:\.\d+)?/);
    if (!value) return null;
    node.dataset.ucKwd = value[0];
    return Number(value[0]);
  }
  function updatePrices() {
    priceNodes().forEach(function (node) {
      var amount = baseValue(node);
      if (amount === null || !isFinite(amount)) return;
      var rendered = format(amount * (rates[selected] || 1), selected);
      if (node.dataset.ucRendered !== rendered) {
        node.textContent = rendered;
        node.dataset.ucRendered = rendered;
      }
      node.setAttribute('title', selected === 'KWD' ? 'Kuwaiti dinar' : 'Display estimate; checkout is charged in Kuwaiti dinar.');
    });
  }
  function setCurrency(currency) {
    selected = currency;
    localStorage.setItem('uc-display-currency', currency);
    selector.value = currency;
    updatePrices();
  }
  selector.addEventListener('change', function () { setCurrency(selector.value); });

  /* Fetch current display rates. Payments and WooCommerce order totals remain in KWD. */
  fetch('https://api.frankfurter.app/latest?from=KWD&to=USD,EUR')
    .then(function (response) { if (!response.ok) throw new Error(); return response.json(); })
    .then(function (data) { if (data.rates && data.rates.USD && data.rates.EUR) { rates.USD = data.rates.USD; rates.EUR = data.rates.EUR; } setCurrency(selected); })
    .catch(function () { setCurrency(selected); });

  new MutationObserver(function () { window.requestAnimationFrame(updatePrices); }).observe(document.body, { childList: true, subtree: true });
  setCurrency(selected);
}());
