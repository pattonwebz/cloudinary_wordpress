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
	public $settings = array();
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
			$setting = $this->settings[ $slug ];
		}

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
		if ( $this->has_setting( $slug ) ) {
			$setting = $this->get_setting( $slug );
		} else {
			// loop through settings to find it.
			foreach ( $this->get_settings() as $sub_setting ) {
				$setting = $sub_setting->find_setting( $slug );
				if ( ! is_null( $setting ) ) {
					break;
				}
			}
		}
		// If none found, we can make a temp setting to store/cache.
		if ( is_null( $setting ) && ! $this->has_parent() ) {
			// Create on the root setting only.
			$setting = new Setting( $slug, $this, $this );
			$setting->set_value( null ); // Set value to null
		}

		return $setting;
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
	 * Get the value slug.
	 *
	 * @return string
	 */
	public function get_value_slug() {
		$value_slug = $this->get_slug();
		if ( ! $this->has_param( 'options_slug' ) && $this->has_parent() ) {
			$value_slug = $this->parent->get_value_slug();
		}

		return $value_slug;
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
				$setting = $this->get_setting( $key );
				if ( ! is_null( $setting ) ) {
					$setting->set_value( $val );
				}
			}
		}
		$this->value = $value;
	}

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
	protected function load_value() {
		if ( $this->has_param( 'options_slug' ) ) {
			$option_slug  = $this->get_option_slug();
			$option_value = get_option( $option_slug  );
			var_dump( $option_value );
			if ( ! empty( $option_value ) ) {
				$this->set_value( $option_value );
				// Push
			}
		}
	}

	/**
	 * Get the value.
	 *
	 * @return mixed
	 */
	public function get_value() {

		if ( empty( $this->value ) ) {
			$this->load_value();
		}
		if ( $this->has_settings() ) {
			$setting_values = $this->get_setting_values();
			$setting_slug   = $this->get_slug();
			if ( ! empty( $setting_values ) ) {
				$this->value = $setting_values;
			}
		}

		return $this->value;
	}

	/**
	 * Get the value of a setting.
	 *
	 * @return mixed
	 */
	public function get_setting_values() {
		$values = array();
		if ( $this->has_settings() ) {

			foreach ( $this->get_settings() as $setting ) {

				$value_slug    = $setting->get_slug();
				$setting_value = $setting->get_value();

				if ( is_null( $setting_value ) ) {
					continue;
				}
				if ( ! is_array( $setting_value ) ) {
					$setting_value = array( $setting->get_slug() => $setting_value );
				}
				if ( empty( $values[ $value_slug ] ) ) {
					$values[ $value_slug ] = array();
				}
				$values[$value_slug] = array_merge( $values[$value_slug],  $setting_value);
			}
		}

		if ( ! $this->has_parent() && 1 === count( $values ) ) {
			//	return array_shift( $values );
		}

		return $values;
	}

	/**
	 * Add a setting setting.
	 *
	 * @param Setting $setting The setting setting.
	 */
	public function add_setting( $setting ) {
		$this->settings[ $setting->get_slug() ] = $setting;
	}
}
