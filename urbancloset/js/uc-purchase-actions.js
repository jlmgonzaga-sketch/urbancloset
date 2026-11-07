/**
 * UrbanCloset — Make an Offer modal
 *
 * Handles the Make an Offer AJAX submission.
 * Buy Now is handled server-side and does not require JS.
 */

(function () {
	'use strict';

	var overlay = document.getElementById( 'uc-offer-modal-overlay' );

	if ( ! overlay ) {
		return;
	}

	var closeBtn   = document.getElementById( 'uc-offer-modal-close' );
	var form       = document.getElementById( 'uc-offer-form' );
	var errorBox   = document.getElementById( 'uc-offer-error' );
	var submitBtn  = document.getElementById( 'uc-offer-submit' );
	var successBox = document.getElementById( 'uc-offer-success' );


	function openModal() {
		overlay.classList.add( 'open' );
		document.body.style.overflow = 'hidden';

		var firstField = document.getElementById( 'uc-offer-amount' );

		if ( firstField ) {
			window.setTimeout( function () {
				firstField.focus();
			}, 50 );
		}
	}


	function closeModal() {
		overlay.classList.remove( 'open' );
		document.body.style.overflow = '';
	}


	document.addEventListener( 'click', function ( e ) {

		var trigger = e.target.closest
			? e.target.closest( '.uc-make-offer-link' )
			: null;

		if ( trigger ) {
			e.preventDefault();
			openModal();
		}
	} );


	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', closeModal );
	}


	overlay.addEventListener( 'click', function ( e ) {

		if ( e.target === overlay ) {
			closeModal();
		}

	} );


	document.addEventListener( 'keydown', function ( e ) {

		if (
			'Escape' === e.key &&
			overlay.classList.contains( 'open' )
		) {
			closeModal();
		}

	} );


	if ( ! form ) {
		return;
	}


	form.addEventListener( 'submit', function ( e ) {

		e.preventDefault();


		if ( errorBox ) {
			errorBox.hidden = true;
			errorBox.textContent = '';
		}


		if ( submitBtn ) {
			submitBtn.disabled = true;
			submitBtn.classList.add( 'is-loading' );
		}


		var data = new FormData( form );

		data.append( 'action', 'uc_submit_offer' );

		data.append(
			'nonce',
			window.ucOfferData
				? window.ucOfferData.nonce
				: ''
		);


		var ajaxUrl = window.ucOfferData
			? window.ucOfferData.ajaxUrl
			: '/wp-admin/admin-ajax.php';


		fetch( ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data
		} )

		.then( function ( response ) {

			/*
			 * Do NOT immediately call response.json().
			 *
			 * If WordPress/PHP returns an error page or PHP warning,
			 * response.json() itself fails and we only see
			 * "Network error".
			 *
			 * Read the raw response first so we can identify the
			 * actual server problem.
			 */
			return response.text().then( function ( text ) {

				return {
					ok: response.ok,
					status: response.status,
					text: text
				};

			} );

		} )

		.then( function ( response ) {

			if ( submitBtn ) {
				submitBtn.disabled = false;
				submitBtn.classList.remove( 'is-loading' );
			}


			var json = null;


			try {

				json = JSON.parse( response.text );

			} catch ( jsonError ) {

				console.error(
					'Urban Closet Make an Offer AJAX response was not JSON:',
					response.text
				);


				if ( errorBox ) {

					errorBox.textContent =
						'The website could not process the offer. Please try again or contact us.';

					errorBox.hidden = false;

				}

				return;

			}


			if ( json && json.success ) {

				form.hidden = true;

				if ( successBox ) {
					successBox.hidden = false;
				}

				return;
			}


			if ( errorBox ) {

				errorBox.textContent =
					(
						json &&
						json.data &&
						json.data.message
					)
						? json.data.message
						: 'Something went wrong. Please try again.';

				errorBox.hidden = false;

			}

		} )

		.catch( function ( error ) {

			console.error(
				'Urban Closet Make an Offer AJAX request failed:',
				error
			);


			if ( submitBtn ) {
				submitBtn.disabled = false;
				submitBtn.classList.remove( 'is-loading' );
			}


			if ( errorBox ) {

				errorBox.textContent =
					'Unable to contact the website server. Please try again.';

				errorBox.hidden = false;

			}

		} );

	} );

} )();