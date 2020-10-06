/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';

registerBlockType( 'cloudinary/gallery', {
	title: __( 'Cloudinary Gallery', 'cloudinary' ),
	description: __( 'Add a gallery powered by the Cloudinary Gallery Widget to your post.', 'cloudinary' ),
	category: 'widgets',
	icon: 'format-gallery',
	attributes: {
		layout: { type: 'string', default: 'expanded-1' },
		primaryColor: { type: 'string', default: '#FFF' },
		onPrimaryColor: { type: 'string', default: '#FFF' },
		activeColor: { type: 'string', default: '#FFF' },
		aspectRatio: { type: 'string', default: '1:1' },
		navigation: { type: 'string', default: 'always' },
		showZoom: { type: 'boolean', default: true },
		zoomType: { type: 'string', default: 'inline' },
		zoomTrigger: { type: 'string', default: 'click' },
	},
	edit,
	save,
} );
