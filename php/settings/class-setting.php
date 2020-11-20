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
	protected $parent;

	/**
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
			'title'       => $owner,
			'description' => null,
			'slug'        => strtolower( $owner ),
			'assets'      => array(),
			'fields'      => array(),
		);
		$params  = wp_parse_args( $params, $default );
		foreach ( $params as $param => $value ) {
			if ( 'settings' === $param ) {
				$this->register_setting_settings( $value );
			} else {
				$this->set_param( $param, $value );
			}
		}

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
		} else {
			$option_slugs[] = $this->get_slug(); // The root setting always prefixes settings slug.
		}

		$option_slugs = array_filter( $option_slugs );
		$option_slugs = array_reverse( $option_slugs );

		return implode( '_', $option_slugs );
	}

	/**
	 * Get the settings option parent. The parent along the chane that saves the settings.
	 *
	 * @return Setting
	 */
	public function get_option_parent() {
		$option_parent = $this;
		if ( ! $this->has_param( 'options_slug' ) && $this->has_parent() ) {
			$option_parent = $this->get_parent();
		}

		return $option_parent;
	}

	/**
	 * Set the settings value.
	 *
	 * @param mixed $value The value to set.
	 */
	public function set_value( $value ) {
		if ( is_array( $value ) && $this->has_parent() ) {
			// Attempt to match array keys to settings settings.
			foreach ( $value as $key => $val ) {
				if ( $this->has_setting( $key ) ) {
					$this->get_setting( $key )->set_value( $val );
				}
			}
		}
		$this->value = $value;
		if ( $this->has_parent() ) {
			$parent = $this->get_root_setting();
			if ( $parent->has_value() ) {
				// Only rebuild if parent has been set.
				$root = $parent->get_option_values( true );
				$parent->set_value( $root );
			}
		}
	}

	/**
	 * Save the value of a setting.
	 *
	 * @return bool
	 */
	public function save_value() {
		if ( $this->has_param( 'options_slug' ) ) {
			$slug = $this->get_option_slug();

			return update_option( $slug, $this->value );
		}
		if ( $this->has_parent() ) {
			$parent_value = $this->parent->get_value();


		}
	}

	/**
	 * Load settings value.
	 */
	public function load_value() {
		if ( $this->has_param( 'options_slug' ) ) {
			$option_slug  = $this->get_option_slug();
			$option_value = get_option( $option_slug );
			if ( ! empty( $option_value ) ) {
				$this->set_value( $option_value );
				// Push
			}
		}
		if ( is_null( $this->value ) && $this->has_param( 'default' ) ) {
			$this->value = $this->get_param( 'default' );
		}
		if ( $this->has_settings() ) {
			foreach ( $this->get_settings() as $setting ) {
				$setting->load_value();
			}
		}
		if ( ! $this->has_parent() ) {
			$root        = $this->get_option_values();
			$this->value = $root;
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
			if ( $this->has_param( 'options_slug' ) || ! $this->has_parent() ) {
				$value = $this->get_option_values();
			} else {
				$value = $this->get_setting_values( $rebuild );
			}
			$this->value = $value;
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
	 * Get the value of a setting.
	 *
	 * @param bool $rebuild Flag to rebuild sub settings.
	 *
	 * @return mixed
	 */
	public function get_option_values( $rebuild = false ) {
		$values = $this->value;
		if ( $this->has_settings() ) {
			if ( is_null( $values ) ) {
				$values = array();
			}
			foreach ( $this->get_settings() as $setting ) {
				$setting_value = $setting->get_value( $rebuild );
				$value_parent  = $setting->get_option_parent( $rebuild );
				$value_slug    = $value_parent->get_slug();
				if ( is_string( $setting_value ) || is_null( $setting_value ) ) {
					$value_slug = $setting->get_slug();
				} elseif ( isset( $values[ $value_slug ] ) ) {
					$setting_value = wp_parse_args( $setting_value, $values[ $value_slug ] );
				}
				$values[ $value_slug ] = $setting_value;
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
			if ( ! $this->has_param( 'options_slug' ) ) {
				$this->set_param( 'options_slug', $this->get_slug() );
			}
			$args = array(
				'slug' => $setting,
			);
			$new_setting->register_setting( $args );
			$new_setting->set_value( null ); // Set value to null

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
