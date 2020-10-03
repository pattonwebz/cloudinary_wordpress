/* global wp */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

import { ToggleControl } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';

// Set our component.
let FeaturedTransformationsToggle = ( props ) => {
	return (
		<>
			{ props.modalClass &&
			<ToggleControl
				label={ __( 'Overwrite Transformations', 'cloudinary' ) }
				checked={ props.overwrite_featured_transformations }
				onChange={ ( value ) => props.setOverwrite( value ) }
			/>
			}
		</>
	);
};

// Setup our properties.
FeaturedTransformationsToggle = withSelect( ( select ) => ( {
	overwrite_featured_transformations: select( 'core/editor' )?.getEditedPostAttribute( 'meta' )._cloudinary_featured_overwrite ?? false,
} ) )( FeaturedTransformationsToggle );

// Setup our update method.
FeaturedTransformationsToggle = withDispatch(
	( dispatch ) => {
		return {
			setOverwrite: ( value ) => {
				dispatch( 'core/editor' ).editPost( { meta: { _cloudinary_featured_overwrite: value } } );
			},
		};
	}
)( FeaturedTransformationsToggle );

// Hook in and add our component.
const cldFilterFeatured = ( BlockEdit ) => {
	return ( props ) => {
		// We only need this on a MediaUpload component that has a value.
		return (
			<>
				<BlockEdit { ...props } />
				{ !! props.value &&
					<FeaturedTransformationsToggle { ...props } />
				}
			</>
		);
	};
};

// Setup an init wrapper.
const Featured = {
	_init() {
		// Add it to Media Upload to allow for deeper connection with getting
		// the media object, to determine if an asset has transformations.
		// Also adds deeper support for other image types within Guttenberg.
		// @todo: find other locations (i.e Video poster).
		wp.hooks.addFilter( 'editor.MediaUpload', 'cloudinary/filter-featured-image', cldFilterFeatured );
	},
};

// Push Init.
Featured._init();

// Export to keep it in scope.
export default Featured;
