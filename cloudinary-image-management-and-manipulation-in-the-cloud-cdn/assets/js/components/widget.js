/* global window wp */
if ( wp.media && window.CLDN ) {
	wp.media.events.on( 'editor:image-edit', function( props ) {
		props.metadata.cldoverwrite = null;
		const classes = props.image.className.split( ' ' );
		if ( classes.indexOf( 'cld-overwrite' ) >= 0 ) {
			props.metadata.cldoverwrite = 'true';
		}
	} );
	wp.media.events.on( 'editor:image-update', function( props ) {
		const classes = props.image.className.split( ' ' );
		if ( props.metadata.cldoverwrite && classes.indexOf( 'cld-overwrite' ) === -1 ) {
			classes.push( 'cld-overwrite' );
		} else if ( ! props.metadata.cldoverwrite && classes.indexOf( 'cld-overwrite' ) >= 0 ) {
			delete classes[ classes.indexOf( 'cld-overwrite' ) ];
		}

		props.image.className = classes.join( ' ' );
	} );

	// Intercept props and inject cld-overwrite class.
	let currentOverwrite = null;
	const imageProps = wp.media.string.props;
	wp.media.string.props = function( props, asset ) {
		if ( props.cldoverwrite ) {
			props.classes = [ 'cld-overwrite' ];
			currentOverwrite = true;
		}
		const newProps = imageProps( props, asset );
		return newProps;
	};
	// Intercept ajax post, and send the cld-overwrite flag, and transformations.
	wp.media.post = function( action, data ) {
		if ( 'send-attachment-to-editor' === action ) {
			const state = wp.media.editor.get().state();
			const attach = state.get( 'selection' ).get( data.attachment );
			if ( attach.attributes.transformations ) {
				data.attachment.transformations = attach.attributes.transformations;
			}
			if ( data.html.indexOf( 'cld-overwrite' ) > -1 || true === currentOverwrite ) {
				data.attachment.cldoverwrite = true;
				currentOverwrite = null;
			}
		}
		// Return the original.
		return wp.ajax.post( action, data );
	};

	const MediaFrame = wp.media.view.MediaFrame.Select;
	const MediaFramePost = wp.media.view.MediaFrame.Post;
	const MediaFrameImageDetails = wp.media.view.MediaFrame.ImageDetails;
	const MediaFrameVideoDetails = wp.media.view.MediaFrame.VideoDetails;
	const Cloudinary = wp.media.View.extend( {
		tagName: 'div',
		className: 'cloudinary-widget',
		template: wp.template( 'cloudinary-dam' ),
		active: false,
		toolbar: null,
		frame: null,
		ready() {
			const controller = this.controller;
			const selection = this.model.get( 'selection' );
			const library = this.model.get( 'library' );
			const attachment = wp.media.model.Attachment;
			// Set widget to same as model.
			CLDN.mloptions.multiple = controller.options.multiple;
			if ( this.cid !== this.active ) {
				CLDN.mloptions.inline_container = '#cloudinary-dam-' + controller.cid;
				if ( 1 === selection.length ) {
					const att = attachment.get( selection.models[ 0 ].id );
					if ( typeof att.attributes.public_id !== 'undefined' ) {
						CLDN.mloptions.asset = { resource_id: att.attributes.public_id };
					}
				} else {
					CLDN.mloptions.asset = null;
				}
				window.ml = cloudinary.openMediaLibrary( CLDN.mloptions, {
					insertHandler( data ) {
						for ( let i = 0; i < data.assets.length; i++ ) {
							const temp = data.assets[ i ];
							wp.media.post( 'cloudinary-down-sync', {
								nonce: CLDN.nonce,
								asset: temp,
							} ).done( function( asset ) {
								const update_asset = function( new_asset, attach ) {
									new_asset.uploading = false;
									attach.set( new_asset );
									wp.Uploader.queue.remove( attach );
									if ( wp.Uploader.queue.length === 0 ) {
										wp.Uploader.queue.reset();
									}
								};
								if ( typeof asset.resync !== 'undefined' ) {
									asset.resync.forEach( function( new_update_asset ) {
										const update_attach = attachment.get( new_update_asset.id );
										update_attach.set( new_update_asset );
									} );
								}
								if ( typeof asset.fetch !== 'undefined' ) {
									const attach = attachment.get( asset.attachment_id );
									attach.set( asset );
									library.add( attach );
									wp.Uploader.queue.add( attach );
									wp.ajax.send( {
										url: asset.fetch,
										beforeSend( request ) {
											request.setRequestHeader( 'X-WP-Nonce', CLDN.nonce );
										},
										data: {
											src: asset.url,
											filename: asset.filename,
											attachment_id: asset.attachment_id,
											transformations: asset.transformations,
										},
									} ).done( function( new_asset ) {
										const att = attachment.get( new_asset.id );
										update_asset( new_asset, att );
									} ).fail( function( err ) {
										update_asset( asset, attach );
										library.remove( attach );
										selection.remove( attach );

										if ( typeof err === 'string' ) {
											alert( err );
										} else if ( err.status === 500 ) {
											alert( 'HTTP error.' );
										}
									} );
								} else {
									const attach = attachment.get( asset.id );
									attach.set( asset );
									selection.add( attach );
								}
								if ( wp.Uploader.queue.length === 0 ) {
									wp.Uploader.queue.reset();
								}
								controller.content.mode( 'browse' );
							} );
						}
					},
				},
				document.querySelectorAll( '.dam-cloudinary' )[ 0 ]
				);
			}
			this.active = this.cid;
			return this;
		},
	} );
	const extend_type = function( type ) {
		const obj = {

			/**
			 * Bind region mode event callbacks.
			 *
			 * @see media.controller.Region.render
			 */
			bindHandlers() {
				type.prototype.bindHandlers.apply( this, arguments );
				this.on( 'content:render:cloudinary', this.cloudinaryContent, this );
			},

			/**
			 * Render callback for the router region in the `browse` mode.
			 *
			 * @param {wp.media.view.Router} routerView
			 */
			browseRouter( routerView ) {
				type.prototype.browseRouter.apply( this, arguments );
				routerView.set( {
					cloudinary: {
						text: 'Cloudinary',
						priority: 60,
					},
				} );
			},

			/**
			 * Render callback for the content region in the `upload` mode.
			 */
			cloudinaryContent() {
				const state = this.state();
				const view = new Cloudinary( {
					controller: this,
					model: state,
				} ).render();
				this.content.set( view );
			},
		};

		return obj;
	};
	// Extending the current media library frames to add a new tab to each area.
	wp.media.view.MediaFrame.Select = MediaFrame.extend( extend_type( MediaFrame ) );
	wp.media.view.MediaFrame.Post = MediaFramePost.extend( extend_type( MediaFramePost ) );
	wp.media.view.MediaFrame.ImageDetails = MediaFrameImageDetails.extend( extend_type( MediaFrameImageDetails ) );
	wp.media.view.MediaFrame.VideoDetails = MediaFrameVideoDetails.extend( extend_type( MediaFrameVideoDetails ) );
}
