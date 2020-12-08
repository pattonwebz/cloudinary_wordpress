<?php
/**
 * This file holds global utility functions and polyfills.
 *
 * @package Cloudinary
 */

if ( ! function_exists( 'wp_get_original_image_path' ) ) {
	/**
	 * POLYFILL. Retrieves the path to an uploaded image file.
	 *
	 * Similar to `get_attached_file()` however some images may have been processed after uploading
	 * to make them suitable for web use. In this case the attached "full" size file is usually replaced
	 * with a scaled down version of the original image. This function always returns the path
	 * to the originally uploaded image file.
	 *
	 * @param int  $attachment_id Attachment ID.
	 * @param bool $unfiltered    Optional. Passed through to `get_attached_file()`. Default false.
	 * @return string|false Path to the original image file or false if the attachment is not an image.
	 */
	function wp_get_original_image_path( $attachment_id, $unfiltered = false ) {
		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			return false;
		}

		$image_meta = wp_get_attachment_metadata( $attachment_id );
		$image_file = get_attached_file( $attachment_id, $unfiltered );

		if ( empty( $image_meta['original_image'] ) ) {
			$original_image = $image_file;
		} else {
			$original_image = path_join( dirname( $image_file ), $image_meta['original_image'] );
		}

		/**
		 * Filters the path to the original image.
		 *
		 * @param string $original_image Path to original image file.
		 * @param int $attachment_id Attachment ID.
		 */
		return apply_filters( 'wp_get_original_image_path', $original_image, $attachment_id );
	}
}
