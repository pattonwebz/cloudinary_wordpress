/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { LayoutClassic, LayoutGridOneColumn, LayoutGridThreeColumn, LayoutGridTwoColumn } from './icons';

export const LAYOUT_OPTIONS = [
	{
		name: 'expanded-1',
		icon: <LayoutGridOneColumn />,
		label: __( 'Expanded - 1 Column', 'cloudinary' ),
	},
	{
		name: 'expanded-2',
		icon: <LayoutGridTwoColumn />,
		label: __( 'Expanded - 2 Column', 'cloudinary' ),
	},
	{
		name: 'expanded-3',
		icon: <LayoutGridThreeColumn />,
		label: __( 'Expanded - 3 Column', 'cloudinary' ),
	},
	{
		name: 'classic',
		icon: <LayoutClassic />,
		label: __( 'Classic', 'cloudinary' ),
	},
];

export const ALLOWED_MEDIA_TYPES = [ 'image' ];

export const COLORS = [
	{ name: 'red', color: '#f00' },
	{ name: 'white', color: '#fff' },
	{ name: 'blue', color: '#00f' },
];

export const ASPECT_RATIOS = [
	{ label: __( '1:1', 'cloudinary' ), value: '1:1' },
	{ label: __( '3:4', 'cloudinary' ), value: '3:4' },
	{ label: __( '4:3', 'cloudinary' ), value: '4:3' },
	{ label: __( '4:6', 'cloudinary' ), value: '4:6' },
	{ label: __( '6:4', 'cloudinary' ), value: '6:4' },
	{ label: __( '5:7', 'cloudinary' ), value: '5:7' },
	{ label: __( '7:5', 'cloudinary' ), value: '7:5' },
	{ label: __( '8:5', 'cloudinary' ), value: '8:5' },
	{ label: __( '5:8', 'cloudinary' ), value: '5:8' },
	{ label: __( '9:16', 'cloudinary' ), value: '9:16' },
	{ label: __( '16:9', 'cloudinary' ), value: '16:9' },
];

export const FADE_TRANSITIONS = [
	{ label: __( 'None', 'cloudinary' ), value: 'none' },
	{ label: __( 'Fade', 'cloudinary' ), value: 'fade' },
	{ label: __( 'Slide', 'cloudinary' ), value: 'slide' },
];

export const NAVIGATION = [
	{ label: __( 'Always', 'cloudinary' ), value: 'always' },
	{ label: __( 'None', 'cloudinary' ), value: 'none' },
	{ label: __( 'MouseOver', 'cloudinary' ), value: 'mouseover' },
];

export const ZOOM_TYPE = [
	{ label: __( 'Inline', 'cloudinary' ), value: 'inline' },
	{ label: __( 'Flyout', 'cloudinary' ), value: 'flyout' },
	{ label: __( 'Popup', 'cloudinary' ), value: 'popup' },
];

export const ZOOM_TRIGGER = [
	{ label: __( 'Click', 'cloudinary' ), value: 'click' },
	{ label: __( 'Hover', 'cloudinary' ), value: 'hover' },
];
