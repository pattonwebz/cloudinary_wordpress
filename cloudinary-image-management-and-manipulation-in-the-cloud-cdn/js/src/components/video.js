/* global window wp */

import { __ } from '@wordpress/i18n';
import { ToggleControl, PanelBody } from '@wordpress/components';
import { cloneElement } from '@wordpress/element';

const { withSelect } = wp.data;

const Video = {
	_init: function() {
		if ( typeof CLD_VIDEO_PLAYER === 'undefined' ) {
			return;
		}

		// Gutenberg Video Settings
		wp.hooks.addFilter(
			'blocks.registerBlockType',
			'Cloudinary/Media/Video',
			function( settings, name ) {
				if ( name === 'core/video' ) {
					if ( 'off' !== CLD_VIDEO_PLAYER.video_autoplay_mode ) {
						settings.attributes.autoplay.default = true;
					}

					if ( 'on' === CLD_VIDEO_PLAYER.video_loop ) {
						settings.attributes.loop.default = true;
					}

					if ( 'off' === CLD_VIDEO_PLAYER.video_controls ) {
						settings.attributes.controls.default = false;
					}
				}
				return settings;
			}
		);
	},
};

export default Video;

// Init.
Video._init();

let cldAddToggle = function( settings, name ) {

	if ( 'core/image' === name || 'core/video' === name ) {
		if ( !settings.attributes ) {
			settings.attributes = {};
		}

		settings.attributes.overwrite_transformations = {
			type: 'boolean',
		};

		settings.attributes.transformations = {
			type: 'boolean',
		};

	}

	return settings;
};

wp.hooks.addFilter( 'blocks.registerBlockType', 'cloudinary/addAttributes', cldAddToggle );

/**
 * Get AMP Lightbox toggle control.
 *
 * @param {Object} props Props.
 *
 * @return {Component} Element.
 */
const TransformationsToggle = ( props ) => {
	const { attributes: { overwrite_transformations, transformations }, setAttributes } = props;
	
	if ( ! transformations ) {
		return null;
  }
  
	return (
		<PanelBody title={__( 'Transformations', 'cloudinary' )}>
			<ToggleControl
				label={__( 'Overwrite Transformations', 'cloudinary' )}
				checked={overwrite_transformations}
				onChange={( value ) => {
					setAttributes( { overwrite_transformations: value } );
				}}
			/>
		</PanelBody>
	);
};

let ImageInspectorControls = ( props ) => {
	const { setAttributes, media } = props;
	const { InspectorControls } = wp.editor;
	
	if ( media && media.transformations ) {
		setAttributes( { transformations: true } );
	}

	return (
		<InspectorControls>
			<TransformationsToggle {...props} />
		</InspectorControls>
	);
};

ImageInspectorControls = withSelect( ( select, ownProps ) => ( {
  ...ownProps,
  media: ownProps.attributes.id ? select( 'core' ).getMedia( ownProps.attributes.id ) : null
} ))( ImageInspectorControls );

const cldFilterBlocksEdit = ( BlockEdit ) => {
	return ( props ) => {
		const { name } = props;
		let inspectorControls = null;

		if ( 'core/image' === name || 'core/video' === name ) {
			inspectorControls = <ImageInspectorControls {...props} />;
		}
		
		return (
			<>
				{inspectorControls}
				<BlockEdit {...props} />
			</>
		);
	}
};

wp.hooks.addFilter( 'editor.BlockEdit', 'cloudinary/filterEdit', cldFilterBlocksEdit, 20 );

const cldfilterBlocksSave = ( element, blockType, attributes ) => {
	if ( 'core/image' === blockType.name && attributes.overwrite_transformations ) {
		let children = cloneElement( element.props.children );
		let classname = children.props.children[ 0 ].props.className ? children.props.children[ 0 ].props.className : '';
		let child = cloneElement( children.props.children[ 0 ], { className: classname + ' cld-overwrite' } );
		let neChildren = cloneElement( children, { children: [ child, false ] } );
		return cloneElement( element, { children: neChildren } );
	}

	if ( 'core/video' === blockType.name && attributes.overwrite_transformations ) {
		let children = cloneElement( element.props.children[ 0 ], { className: ' cld-overwrite' } );
		return cloneElement( element, { children } );
	}

	return element;
};

wp.hooks.addFilter( 'blocks.getSaveElement', 'cloudinary/filterSave', cldfilterBlocksSave );
