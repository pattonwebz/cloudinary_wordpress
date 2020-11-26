<?php
/**
 * Cloudinary Setting.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Settings;

use Cloudinary\UI\Component;

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
	protected $parent;

	/**
	 * Holds this settings child settings.
	 *
	 * @var Setting[]
	 */
	protected $settings = array();

	/**
	 * The plugin component that created the setting.
	 *
	 * @var \Cloudinary\Media|\Cloudinary\Sync|\Cloudinary\Settings_Page|\Cloudinary\REST_API|\Cloudinary\Connect
	 */
	protected $owner;

	/**
	 * Setting slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Setting Value.
	 *
	 * @var mixed
	 */
	protected $value = null;

	/**
	 * This settings component object.
	 *
	 * @var Component
	 */
	protected $component;

	/**
	 * Setting constructor.
	 *
	 * @param string       $slug   The setting slug.
	 * @param self::owner  $owner  The plugin component that created the setting.
	 * @param Setting|null $parent The parent setting for this setting.
	 *
	 * @return $this
	 */
	public function __construct( $slug, $owner, $parent = null ) {
		$this->owner  = $owner;
		$this->slug   = $slug;
		$this->parent = $parent;
		// Connect setting to parent.
		if ( $parent instanceof Setting ) {
			$parent->add_setting( $this );
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
	 * Set a parameter and value to the setting.
	 *
	 * @param string $param Param key to set.
	 * @param mixed  $value The value to set.
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
	 */
	public function has_param( $param ) {
		return isset( $this->params[ $param ] );
	}

	/**
	 * Get params param.
	 *
	 * @param string $param   The param to get.
	 * @param mixed  $default The default value for this param is a value is not found.
	 *
	 * @return mixed
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
		if ( $this->has_settings() ) {
			$setting_slugs = $this->get_setting_slugs();
			foreach ( $setting_slugs as $setting_slug ) {
				$setting                             = $this->get_setting( $setting_slug );
				$params['settings'][ $setting_slug ] = $setting->get_params_recursive();
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
	 * Check if setting has settings.
	 *
	 * @return bool
	 */
	public function has_settings() {
		return ! empty( $this->settings );
	}

	/**
	 * Check if setting has settings.
	 *
	 * @param string $setting_slug The setting slug to check.
	 *
	 * @return bool
	 */
	public function has_setting( $setting_slug ) {
		$setting_slugs = $this->get_setting_slugs();

		return in_array( $setting_slug, $setting_slugs, true );
	}

	/**
	 * Get the parent setting.
	 *
	 * @return Setting|null
	 */
	public function get_parent() {
		$parent = null;
		if ( $this->has_parent() ) {
			$parent = $this->parent;
		}

		return $parent;
	}

	/**
	 * Get all settings settings.
	 *
	 * @return Setting[]
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get all slugs of settings.
	 *
	 * @return array
	 */
	public function get_setting_slugs() {
		return array_keys( $this->settings );
	}

	/**
	 * Get a setting setting.
	 *
	 * @param string $slug The setting slug to get.
	 *
	 * @return Setting|null
	 */
	public function get_setting( $slug ) {
		$setting = null;
		if ( $this->has_setting( $slug ) ) {
			return $this->settings[ $slug ];
		}

		$setting = $this->add_setting( $slug );

		return $setting;
	}

	/**
	 * Get a setting recursively.
	 *
	 * @param string $slug The setting slug to get.
	 *
	 * @return Setting|null
	 */
	public function find_setting( $slug ) {
		$setting = null;
		if ( $this->has_settings() ) {
			if ( $this->has_setting( $slug ) ) {
				return $this->get_setting( $slug );
			}
			$setting = $this->find_setting_recursively( $slug );
		}

		return ! is_null( $setting ) ? $setting : $this->get_setting( $slug ); // return a dynamic setting.
	}

	/**
	 * Recursivly find a setting.
	 *
	 * @param string $slug The setting slug to get.
	 *
	 * @return Setting|null
	 */
	protected function find_setting_recursively( $slug ) {
		$found = null;
		// loop through settings to find it.
		foreach ( $this->get_settings() as $sub_setting ) {
			if ( $sub_setting->has_settings() ) {
				if ( $sub_setting->has_setting( $slug ) ) {
					$found = $sub_setting->get_setting( $slug );
					break;
				}
				$found = $sub_setting->find_setting_recursively( $slug );
				if ( ! is_null( $found ) ) {
					break;
				}
			}
		}

		return $found;
	}

	/**
	 * Register a setting.
	 *
	 * @param array $params The setting params.
	 */
	public function register_setting( $params ) {
		$owner   = get_class( $this->owner );
		$default = array(
			'title'       => null,
			'description' => null,
			'slug'        => strtolower( $owner ),
			'assets'      => array(),
			'fields'      => array(),
			'type'        => 'component',
		);
		$params  = wp_parse_args( $params, $default );
		foreach ( $params as $param => $value ) {
			if ( 'settings' === $param ) {
				$this->register_setting_settings( $value );
			} else {
				$this->set_param( $param, $value );
			}
		}
		$this->setup_component();

	}

	/**
	 * Setup the settings render component.
	 */
	protected function setup_component() {
		$this->component = Component::init( $this );
	}

	/**
	 * Register sub settings.
	 *
	 * @param array $settings The array of sub settings.
	 */
	protected function register_setting_settings( $settings ) {
		foreach ( $settings as $setting => $structure ) {
			$setting = new Setting( $setting, $this->owner, $this );
			$setting->register_setting( $structure );
		}
	}

	/**
	 * Get the settings Component.
	 *
	 * @return \Cloudinary\UI\Component
	 */
	public function get_component() {
		return $this->component;
	}

	/**
	 * Render the settings Component.
	 *
	 * @return string
	 */
	public function render_component() {
		return $this->get_component()->render();
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
		$option_slug = null;
		if ( $this->has_param( 'options_slug' ) ) {
			return $this->get_param( 'options_slug' );
		} elseif ( $this->has_parent() ) {
			$option_slug = $this->get_parent()->get_option_slug();
		}

		if ( is_null( $option_slug ) && $this->has_parent() && ! $this->get_parent()->has_parent() ) {
			// Set an auto option slug if the parent is the root item.
			$option_slug = $this->get_parent()->get_slug() . '_' . $this->get_slug();
		}

		return $option_slug;
	}

	/**
	 * Set the settings value.
	 *
	 * @param mixed $value The value to set.
	 */
	public function set_value( $value ) {
		if ( is_array( $value ) ) {
			// Attempt to match array keys to settings settings.
			foreach ( $value as $key => $val ) {
				$this->find_setting( $key )->set_value( $val );
			}
		}
		$this->value = $value;
	}

	/**
	 * Save the value of a setting to the first lower options slug.
	 *
	 * @return bool
	 */
	public function save_value() {
		if ( $this->has_param( 'options_slug' ) ) {
			$slug = $this->get_option_slug();

			return update_option( $slug, $this->value );
		} elseif ( $this->has_parent() ) {
			return $this->get_parent()->save_value();
		}

		return false;
	}

	/**
	 * Load settings value.
	 */
	public function load_value() {
		// Keep track of loaded options as to prevent looping.
		static $configured_options = array();

		$option_slug = $this->get_option_slug();
		if ( ! empty( $configured_options[ $option_slug ] ) ) {
			return;
		}
		if ( ! is_null( $option_slug ) ) {
			$option_value = get_option( $option_slug );
			if ( ! empty( $option_value ) ) {
				// Push value to child settings.
				$this->set_value( $option_value );
			}
			$configured_options[ $option_slug ] = true;
		}
		if ( is_null( $this->value ) && $this->has_param( 'default' ) ) {
			$this->value = $this->get_param( 'default' );
		}
		if ( $this->has_settings() ) {
			foreach ( $this->get_settings() as $setting ) {
				if ( ! $setting->has_value() ) {
					$setting->load_value();
				}
			}
		}
	}

	/**
	 * Check if setting has a value.
	 *
	 * @return bool
	 */
	public function has_value() {
		return ! is_null( $this->value );
	}

	/**
	 * Get the value for a setting.
	 *
	 * @param bool $rebuild Flag to rebuild sub settings.
	 *
	 * @return mixed
	 */
	public function get_value( $rebuild = false ) {

		if ( true === $rebuild || is_null( $this->value ) && $this->has_settings() ) {
			$this->value = $this->get_setting_values( $rebuild );
		}

		return $this->value;
	}

	/**
	 * Get the value of a setting.
	 *
	 * @param bool $rebuild Flag to rebuild sub settings.
	 *
	 * @return mixed
	 */
	public function get_setting_values( $rebuild = false ) {
		$values = $this->value;
		if ( $this->has_settings() ) {

			foreach ( $this->get_settings() as $setting ) {
				$setting_value                  = $setting->get_value( $rebuild );
				$values[ $setting->get_slug() ] = $setting_value;
			}
		}

		return $values;
	}

	/**
	 * Add a setting setting.
	 *
	 * @param Setting|string $setting The setting object or slug of a setting to add.
	 *
	 * @return Setting
	 */
	public function add_setting( $setting ) {
		if ( is_string( $setting ) ) {
			// Create a dynamic setting.
			$new_setting = new Setting( $setting, $this, $this );
			if ( ! $this->has_param( 'options_slug' ) && $this->has_parent() ) {
				$this->set_param( 'options_slug', $this->get_slug() );
			}
			$args = array(
				'slug' => $setting,
			);
			$new_setting->register_setting( $args );
			$new_setting->set_value( null ); // Set value to null.

			return $new_setting;
		}
		$this->settings[ $setting->get_slug() ] = $setting;

		return $setting;
	}

	/**
	 * Get the root setting.
	 *
	 * @return Setting
	 */
	public function get_root_setting() {
		$parent = $this;
		if ( $this->has_parent() ) {
			$parent = $this->get_parent()->get_root_setting();
		}

		return $parent;
	}
}
