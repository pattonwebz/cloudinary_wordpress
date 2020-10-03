/**
 * External dependencies
 */
import React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

export default registerBlockType( 'cloudinary/gallery', {
	title: __( 'Cloudinary Gallery', 'cloudinary' ),
	description: __( 'Add a gallery powered by the Cloudinary Gallery Widget to your post.', 'cloudinary' ),
	category: 'common',
	icon: 'format-gallery',
	edit() {
		return <div>Hello World, step 1 (from the editor).</div>;
	},
	save() {
		return <div>Hello World, step 1 (from the frontend).</div>;
	},
} );
