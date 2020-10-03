/**
 * Internal dependencies
 */
import Gallery from './components/gallery';
import Video from './components/video';
import Featured from './components/featured-image';

// jQuery, because reasons.
window.$ = window.jQuery;

// Global Constants
export const cloudinaryBlocks = {
	Video,
	Featured,
	Gallery,
};
