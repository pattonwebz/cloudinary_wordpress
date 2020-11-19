<?php
/**
 * Cloudinary Setting.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Settings;

/**
 * Class Setting.
 *
 * Setting for Cloudinary.
 */
class Setting {

	/**
	 * The setting params.
	 *
	 * @var array
	 */
	protected $params = array();

	/**
	 * Parent of the setting.
	 *
	 * @var Setting|null
	 */
	public $parent;

	/**
	 * @var Setting[]
	 */
	public $children = array();
	/**
	 * The plugin component that created the setting.
	 *
	 * @var \Cloudinary\Media|\Cloudinary\Sync|\Cloudinary\Settings_Page|\Cloudinary\REST_API|\Cloudinary\Connect
	 */
	public $owner;

	/**
	 * Setting slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Setting Value.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Setting constructor.
	 *
	 * @param string       $slug The setting slug
	 * @param self::owner  $owner The plugin component that created the setting.
	 * @param Setting|null $parent
	 *
	 * @return $this
	 */
	public function __construct( $slug, $owner, $parent = null ) {
		$this->owner  = $owner;
		$this->slug   = $slug;
		$this->parent = $parent;
		// Connect setting to parent.
		if ( $parent instanceof Setting ) {
			$parent->add_child( $this );
		}

		return $this;
	}

	/**
	 * Get all the param keys.
	 *
	 * @return array
	 */
	public function get_param_keys() {
		return array_keys( $this->params );
	}

	/**
	 * Set a struct param.
	 *
	 * @var string $param Param key to set.
	 * @var mixed  $value The value to set.
	 */
	public function set_param( $param, $value ) {
		$this->params[ $param ] = $value;
	}

	/**
	 *
	 * Check if a param exists.
	 *
	 * @param string $param The param to check.
	 *
	 * @return bool
	 *
	 */
	public function has_param( $param ) {
		return isset( $this->params[ $param ] );
	}

	/**
	 * Get params param.
	 *
	 * @param string $param The param to get.
	 * @param mixed
	 *
	 * @return mixed
	 *
	 */
	public function get_param( $param, $default = null ) {
		return isset( $this->params[ $param ] ) ? $this->params[ $param ] : $default;
	}

	/**
	 * Get the whole params.
	 *
	 * @return array
	 */
	public function get_params() {
		return $this->params;
	}

	/**
	 * Get the params recursive.
	 *
	 * @return array
	 */
	public function get_params_recursive() {
		$params = $this->get_params();
		if ( $this->has_children() ) {
			$child_slugs = $this->get_children_slugs();
			foreach ( $child_slugs as $child_slug ) {
				$child                             = $this->get_child( $child_slug );
				$params['settings'][ $child_slug ] = $child->get_params_recursive();
			}
		}

		return $params;
	}

	/**
	 * Check if setting has a parent.
	 *
	 * @return bool
	 */
	public function has_parent() {
		return ! empty( $this->parent );
	}

	/**
	 * Check if setting has children.
	 *
	 * @return bool
	 */
	public function has_children() {
		return ! empty( $this->children );
	}

	/**
	 * Get all children settings.
	 *
	 * @return Setting[]
	 */
	public function get_children() {
		return $this->children;
	}

	/**
	 * Get all slugs of children.
	 *
	 * @return array
	 */
	public function get_children_slugs() {
		return array_keys( $this->children );
	}

	/**
	 * Get a child setting.
	 *
	 * @param string $slug The child slug to get.
	 *
	 * @return Setting|null
	 */
	public function get_child( $slug ) {
		return isset( $this->children[ $slug ] ) ? $this->children[ $slug ] : null;
	}

	/**
	 * Register a setting.
	 *
	 * @param array $params The setting params.
	 */
	public function register_setting( $params ) {
		$owner   = get_class( $this->owner );
		$default = array(
			'title'       => $owner,
			'description' => null,
			'slug'        => strtolower( $owner ),
			'assets'      => array(),
			'fields'      => array(),
		);
		$params  = wp_parse_args( $params, $default );
		foreach ( $params as $param => $value ) {
			$this->set_param( $param, $value );
		}

	}

	/**
	 * Get the setting slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get the option slug.
	 *
	 * @return string
	 */
	public function get_option_slug() {
		$option_slugs = array(
			$this->get_param( 'options_slug' ),
		);
		if ( $this->has_parent() ) {
			$option_slugs[] = $this->parent->get_option_slug();
		}

		$option_slugs = array_filter( $option_slugs );
		$option_slugs = array_reverse( $option_slugs );

		return implode( '_', $option_slugs );
	}

	/**
	 * Get the value slug.
	 *
	 * @return string
	 */
	public function get_value_slug() {
		$value_slug = null;
		if ( $this->has_param( 'options_slug' ) ) {
			$value_slug = $this->get_slug();
		} elseif ( $this->has_parent() ) {
			$value_slug = $this->parent->get_value_slug();
		}

		return $value_slug;
	}

	/**
	 * Get the value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		$value = array();
		if ( $this->has_param( 'options_slug' ) ) {
			$option_slug = $this->get_option_slug();
			$value       = get_option( $option_slug, array() );
		}
		if ( $this->has_children() ) {
			$child_values = $this->get_child_values();
			if ( ! empty( $child_values ) ) {
				$value = array_merge( $value, $child_values );
			}
		}

		return $value;
	}

	/**
	 * Get the value of a child.
	 *
	 * @param string $child_slug The child slug to get a value for.
	 *
	 * @return mixed
	 */
	public function get_child_value( $child_slug ) {
		$value = null;
		if ( isset( $this->children[ $child_slug ] ) ) {
			$value = $this->children[ $child_slug ]->get_value();
		}

		return $value;
	}

	/**
	 * Get the value of a child.
	 *
	 * @return mixed
	 */
	public function get_child_values() {
		$values = array();
		if ( $this->has_children() ) {
			foreach ( $this->get_children_slugs() as $child_slug ) {
				$value_slug            = $this->get_child( $child_slug )->get_value_slug();
				$values[ $value_slug ] = $this->get_child_value( $child_slug );
			}
		}

		return $values;
	}

	/**
	 * Add a child setting.
	 *
	 * @param Setting $child The child setting.
	 */
	public function add_child( $child ) {
		$this->children[ $child->get_slug() ] = $child;
	}
}
