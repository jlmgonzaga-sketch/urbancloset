(function () {
  'use strict';

  if (!window.UCCheckout) return;
  var usedKeys = {};

  function text(node) {
    return (node && node.textContent || '').replace(/\s+/g, ' ').trim();
  }
  function itemName(row) {
    var name = row.querySelector('.wc-block-components-product-name, .wc-block-components-order-summary-item__description a');
    return text(name);
  }
  function addButtons(cart) {
    var rows = document.querySelectorAll('.wc-block-components-order-summary-item');
    rows.forEach(function (row) {
      if (row.querySelector('.uc-checkout-remove')) return;
      var name = itemName(row);
      var item = cart.items.find(function (candidate) {
        return !usedKeys[candidate.key] && candidate.name === name;
      });
      if (!item) return;
      usedKeys[item.key] = true;
      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'uc-checkout-remove';
      button.dataset.cartItemKey = item.key;
      button.setAttribute('aria-label', 'Remove ' + item.name + ' from order');
      button.innerHTML = '<span aria-hidden="true">×</span> Remove';
      button.addEventListener('click', removeItem);
      var target = row.querySelector('.wc-block-components-order-summary-item__description') || row;
      target.appendChild(button);
    });
  }
  function load() {
    usedKeys = {};
    fetch(UCCheckout.cartUrl, { credentials: 'same-origin', headers: { 'Nonce': UCCheckout.nonce } })
      .then(function (response) { return response.ok ? response.json() : Promise.reject(); })
      .then(addButtons)
      .catch(function () {});
  }
  function removeItem(event) {
    var button = event.currentTarget;
    var key = button.dataset.cartItemKey;
    button.disabled = true;
    button.textContent = 'Removing…';
    fetch(UCCheckout.cartUrl + '/remove-item?key=' + encodeURIComponent(key), {
      method: 'POST', credentials: 'same-origin', headers: { 'Nonce': UCCheckout.nonce }
    }).then(function (response) {
      if (!response.ok) return Promise.reject();
      return response.json();
    }).then(function () {
      /* Reload from WooCommerce after its cart mutation so totals, shipping, and
         payment availability always match the server's cart state. */
      window.location.reload();
    }).catch(function () {
      button.disabled = false;
      button.textContent = 'Remove';
      window.alert(UCCheckout.error);
    });
  }
  document.addEventListener('DOMContentLoaded', load);
  new MutationObserver(function () { window.requestAnimationFrame(load); }).observe(document.documentElement, { childList: true, subtree: true });
}());
