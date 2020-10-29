const Sync = {
	progress: document.getElementById( 'progress-wrapper' ),
	submitButton: document.getElementById( 'submit' ),
	stopButton: document.getElementById( 'stop-sync' ),
	completed: document.getElementById( 'completed-notice' ),
	show: 'inline-block',
	hide: 'none',
	isRunning: false,
	getStatus: function getStatus() {
		const url = cloudinaryApi.restUrl + 'cloudinary/v1/attachments';

		wp.ajax
			.send( {
				url,
				type: 'GET',
				beforeSend( request ) {
					request.setRequestHeader( 'X-WP-Nonce', cloudinaryApi.nonce );
				},
			} )
			.done( function( data ) {
				Sync.isRunning = data.is_running;
				if ( Sync.isRunning ) {
					setTimeout( Sync.getStatus, 10000 );
				}
				Sync._updateUI( data );
			} );
	},
	stopSync: function stopSync() {
		const url = cloudinaryApi.restUrl + 'cloudinary/v1/sync';

		Sync.isRunning = false;

		wp.ajax
			.send( {
				url,
				data: {
					stop: true,
				},
				beforeSend( request ) {
					request.setRequestHeader( 'X-WP-Nonce', cloudinaryApi.nonce );
				},
			} )
			.done( function( data ) {
				Sync._updateUI( data );
			} );
	},
	pushAttachments: function pushAttachments() {
		const url = cloudinaryApi.restUrl + 'cloudinary/v1/sync';

		Sync.isRunning = true;
		Sync.progress.style.display = Sync.show;

		wp.ajax
			.send( {
				url,
				beforeSend( request ) {
					request.setRequestHeader( 'X-WP-Nonce', cloudinaryApi.nonce );
				},
			} )
			.done( function() {
				setTimeout( Sync.getStatus, 10000 );
			} );
	},
	_updateUI: function _updateUI( data ) {
		if ( data.percent < 100 && typeof data.started !== 'undefined' ) {
			this.submitButton.style.display = this.hide;
			this.stopButton.style.display = this.show;
		} else if ( data.percent >= 100 && typeof data.started !== 'undefined' ) {
			this.submitButton.style.display = this.hide;
			this.stopButton.style.display = this.show;
		} else if ( data.pending > 0 ) {
			this.submitButton.style.display = this.show;
			this.stopButton.style.display = this.hide;
		} else if ( data.processing > 0 ) {
			this.stopButton.style.display = this.show;
		} else {
			this.stopButton.style.display = this.hide;
		}

		if ( data.percent === 100 ) {
			this.completed.style.display = this.show;
		}

		if ( this.isRunning ) {
			this.progress.style.display = this.show;
		} else {
			this.progress.style.display = this.hide;
		}
	},
	_start: function _start( e ) {
		e.preventDefault();
		Sync.stopButton.style.display = Sync.show;
		Sync.submitButton.style.display = Sync.hide;
		Sync.pushAttachments();
	},
	_reset: function _reset() {
		Sync.submitButton.style.display = Sync.hide;
		Sync.getStatus();
	},
	_init( fn ) {
		if ( typeof cloudinaryApi !== 'undefined' ) {
			if (
				document.attachEvent ?
					document.readyState === 'complete' :
					document.readyState !== 'loading'
			) {
				fn();
			} else {
				document.addEventListener( 'DOMContentLoaded', fn );
			}
		}
	},
};

export default Sync;

// Add it a trigger watch to stop deactivation.
const triggers = document.getElementsByClassName( 'cld-deactivate' );
[ ...triggers ].forEach( ( trigger ) => {
	trigger.addEventListener( 'click', function( ev ) {
		if (
			! confirm(
				wp.i18n.__(
					'Caution: Your storage setting is currently set to "Cloudinary only", disabling the plugin will result in broken links to media assets. Are you sure you want to continue?',
					'cloudinary'
				)
			)
		) {
			ev.preventDefault();
		}
	} );
} );

// Init.
Sync._init( function() {
	Sync._reset();
	Sync.submitButton.addEventListener( 'click', Sync._start );
	Sync.stopButton.addEventListener( 'click', Sync.stopSync );
} );
