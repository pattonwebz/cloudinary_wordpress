<?php
/**
 * Utilities for Cloudinary.
 *
 * @package Cloudinary
 */

namespace Cloudinary;

class Utils {
	/**
	 * Flattens a multi-dimensional array
	 *
	 * @param array $array
	 * 
	 * @return array
	 */
	public static function flatten( array $array ) {
		$return = array();

		array_walk_recursive( $array, function( $item ) use ( &$return ) { 
			$return[] = $item; 
		} );

		return $return;
	}

	/**
	 * Check if string begins with needle.
	 *
	 * @param string $haystack
	 * @param string $needle
	 * 
	 * @return bool
	 */
	public static function starts_with( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}

	/**
	 * Check if string ends with needle.
	 *
	 * @param string $haystack
	 * @param string $needle
	 * 
	 * @return bool
	 */
	public static function ends_with( $haystack, $needle ) {
		$length = strlen( $needle );

		if ( ! $length ) {
			return true;
		}

		return substr( $haystack, -$length ) === $needle;
	}
}