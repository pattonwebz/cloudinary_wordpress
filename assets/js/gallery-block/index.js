/* global defaultGalleryConfig */

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

const {
	aspectRatio,
	carouselLocation,
	carouselOffset,
	carouselThumbnailHeight,
	carouselThumbnailSelectedStyle,
	carouselThumbnailWidth,
	carouselButtonShape,
	zoomTrigger,
	zoomType,
	zoomViewerPosition,
	activeColor,
	onPrimaryColor,
	primaryColor,
	transition,
	carouselStyle,
} = defaultGalleryConfig;

registerBlockType('cloudinary/gallery', {
	title: __('Cloudinary Gallery', 'cloudinary'),
	description: __(
		'Add a gallery powered by the Cloudinary Gallery Widget to your post.',
		'cloudinary'
	),
	category: 'widgets',
	icon: 'format-gallery',
	attributes: {
		displayProps_mode: { type: 'string', default: 'classic' },
		displayProps_columns: { type: 'number', default: 1 },
		themeProps_primary: { type: 'string', default: primaryColor || '#FFF' },
		themeProps_onPrimary: {
			type: 'string',
			default: onPrimaryColor || '#FFF',
		},
		themeProps_active: { type: 'string', default: activeColor || '#FFF' },
		transition: { type: 'string', default: transition || 'fade' },
		aspectRatio: { type: 'string', default: aspectRatio || '1:1' },
		navigation: { type: 'string', default: 'always' },
		zoom: {
			type: 'boolean',
			default: zoomTrigger && zoomTrigger !== 'none',
		},
		zoomProps_type: { type: 'string', default: zoomType || 'inline' },
		zoomProps_viewerPosition: {
			type: 'string',
			default: zoomViewerPosition || 'top',
		},
		zoomProps_trigger: {
			type: 'string',
			default: zoomTrigger !== 'none' ? zoomTrigger : 'click',
		},
		carouselLocation: {
			type: 'string',
			default: carouselLocation || 'left',
		},
		carouselOffset: {
			type: 'number',
			default: Number(carouselOffset) || 0,
		},
		carouselStyle: {
			type: 'string',
			default: carouselStyle || 'thumbnails',
		},
		thumbnailProps_width: {
			type: 'number',
			default: Number(carouselThumbnailWidth) || 1,
		},
		thumbnailProps_height: {
			type: 'number',
			default: Number(carouselThumbnailHeight) || 1,
		},
		thumbnailProps_navigationShape: {
			type: 'string',
			default: carouselButtonShape || 'round',
		},
		thumbnailProps_selectedStyle: {
			type: 'string',
			default: carouselThumbnailSelectedStyle || 'all',
		},
		thumbnailProps_selectedBorderPosition: {
			type: 'string',
			default: 'all',
		},
		thumbnailProps_selectedBorderWidth: { type: 'number', default: 4 },
		thumbnailProps_mediaSymbolShape: { type: 'string', default: 'round' },
		selectedImages: { type: 'array', default: [] },
		container: { type: 'string' },
	},
	edit,
	save,
});
