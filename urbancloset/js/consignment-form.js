(function () {
  'use strict';
  var modal = document.getElementById('consignment-form');
  if (!modal) return;
  var panel = modal.querySelector('.consignment-modal__panel');
  var canvas = document.getElementById('uc-consign-signature');
  var field = document.getElementById('uc-consign-signature-data');
  var form = modal.querySelector('form');
  var nonceField = form.querySelector('input[name="uc_consignment_nonce"]');
  var nonceReady = false;
  var noncePending = false;
  var drawing = false;
  var dirty = false;
  var lastFocused;
  var context = canvas && canvas.getContext('2d');

  function open() {
    lastFocused = document.activeElement;
    modal.hidden = false;
    document.body.classList.add('consignment-modal-open');
    panel.focus();
    refreshNonce();
  }
  function refreshNonce() {
    if (!window.UCConsignment || !UCConsignment.nonceUrl || noncePending) return;
    noncePending = true;
    nonceReady = false;
    fetch(UCConsignment.nonceUrl, { credentials: 'same-origin', cache: 'no-store' })
      .then(function (response) { if (!response.ok) throw new Error('Request failed'); return response.json(); })
      .then(function (result) {
        if (!result.success || !result.data || !result.data.nonce) throw new Error('Nonce unavailable');
        nonceField.value = result.data.nonce;
        nonceReady = true;
      })
      .catch(function () { nonceReady = false; })
      .then(function () { noncePending = false; });
  }
  function close() {
    modal.hidden = true;
    document.body.classList.remove('consignment-modal-open');
    if (lastFocused) lastFocused.focus();
  }
  function point(event) {
    var rect = canvas.getBoundingClientRect();
    var source = event.touches ? event.touches[0] : event;
    return { x: (source.clientX - rect.left) * (canvas.width / rect.width), y: (source.clientY - rect.top) * (canvas.height / rect.height) };
  }
  function start(event) {
    drawing = true;
    var p = point(event);
    context.beginPath(); context.moveTo(p.x, p.y);
    event.preventDefault();
  }
  function draw(event) {
    if (!drawing) return;
    var p = point(event);
    context.lineTo(p.x, p.y); context.stroke(); dirty = true;
    event.preventDefault();
  }
  function stop() { drawing = false; }
  function clear() { context.clearRect(0, 0, canvas.width, canvas.height); dirty = false; field.value = ''; }

  document.querySelectorAll('[data-consignment-open]').forEach(function (button) { button.addEventListener('click', open); });
  modal.querySelectorAll('[data-consignment-close]').forEach(function (button) { button.addEventListener('click', close); });
  document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !modal.hidden) close(); });
  if (location.hash === '#consignment-form') open();

  if (canvas && context) {
    context.strokeStyle = '#2c211b'; context.lineWidth = 3; context.lineCap = 'round'; context.lineJoin = 'round';
    canvas.addEventListener('pointerdown', start); canvas.addEventListener('pointermove', draw);
    canvas.addEventListener('pointerup', stop); canvas.addEventListener('pointerleave', stop); canvas.addEventListener('pointercancel', stop);
    modal.querySelector('[data-signature-clear]').addEventListener('click', clear);
    form.addEventListener('submit', function (event) {
      if (!nonceReady) { event.preventDefault(); alert('Please wait a moment and try submitting again.'); refreshNonce(); return; }
      if (!dirty) { event.preventDefault(); canvas.focus(); alert('Please draw your signature before sending your request.'); return; }
      field.value = canvas.toDataURL('image/png');
    });
  }
}());
