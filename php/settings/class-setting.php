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
	 * Get params param.
	 *
	 * @param string $param The param to get.
	 * @param mixed
	 *
	 * @return mixed
	 *
	 */
	public function get_param( $param, $default = null ) {
		$return = isset( $this->params[ $param ] ) ? $this->params[ $param ] : $default;

		return $return;
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
		$default = array(
			'title'       => '',
			'description' => null,
			'slug'        => '',
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
	 * Add a child setting.
	 *
	 * @param Setting $child The child setting.
	 */
	public function add_child( $child ) {
		$this->children[ $child->get_slug() ] = $child;
	}
}
