/* global window wp cloudinaryApi */

const Deactivate = {
	tbLink: document.getElementById( 'cld-deactivation-link' ),
	pluginListLinks: document.querySelectorAll( '.cld-deactivate-link, .cld-deactivate' ),
	options: document.querySelectorAll( '.cloudinary-deactivation input[type="radio"]' ),
	submitButton: document.querySelector( '.cloudinary-deactivation .button-primary' ),
	skipButton: document.querySelector( '.cloudinary-deactivation .button-link' ),
	reason: '',
	more: null,
	deactivationUrl: '',

	addEvents: function() {
		const context = this;
		[ ...context.pluginListLinks ].forEach( ( link ) => {
			link.addEventListener( 'click', function( ev ) {
				ev.preventDefault();
				context.deactivationUrl = ev.target.getAttribute( 'href' );
				context.tbLink.click();
				document.getElementById( 'TB_window' ).setAttribute(
					'style',
					'bottom: 0;' +
					'height: 450px;' +
					'left: 0;' +
					'margin: auto;' +
					'right: 0;' +
					'top: 0;' +
					'visibility: visible;' +
					'width: 550px;'
				);
			});
		});

		context.skipButton.addEventListener( 'click', function( ev ) {
			window.location.href = context.deactivationUrl;
		});

		[ ...context.options ].forEach( (option) => {
			option.addEventListener( 'change', function( ev ) {
				context.submitButton.removeAttribute( 'disabled' );
				context.reason = ev.target.value;
				context.more = ev.target.parentNode.querySelector( 'textarea' );
			});
		});

		context.submitButton.addEventListener( 'click', function( ev ) {
			wp.ajax.send( {
				url: cloudinaryApi.endpoint,
				data: {
					reason: context.reason,
					more: context.more.value,
				},
				beforeSend: function( request ) {
					request.setRequestHeader( 'X-WP-Nonce', cloudinaryApi.nonce );
				},
			} ).done( function( data ) {
				window.location.href = context.deactivationUrl;
			} );
		})
	},

	/**
	 *
	 */
	init: function() {
		this.addEvents();
	}
}

Deactivate.init();

export default Deactivate;
