<?php
/**
 * Abstract UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI;

use Cloudinary\Settings\Setting;

/**
 * Abstract Component.
 *
 * @package Cloudinary\UI
 */
abstract class Component {

	/**
	 * Holds the components type.
	 *
	 * @var string
	 */

	protected $type;
	/**
	 * Holds the parent setting for this component.
	 *
	 * @var Setting
	 */
	protected $setting;

	/**
	 * Holds the components build parts.
	 *
	 * @var array
	 */
	protected $build_parts;

	/**
	 * Holds the components build blueprint.
	 *
	 * @var string
	 */
	protected $blueprint = 'wrap|header|icon/|title/|collapse/|/header|body|/body|settings/|/wrap';

	/**
	 * Holds a list of the Components used parts.
	 *
	 * @var array
	 */
	protected $used_parts;
	/**
	 * Holds the components built HTML parts.
	 *
	 * @var array
	 */
	protected $html = array();

	/**
	 * Render component for a setting.
	 * Component constructor.
	 *
	 * @param Setting $setting The parent Setting.
	 */
	public function __construct( $setting ) {
		$this->setting = $setting;
		$class         = strtolower( get_class( $this ) );
		$class_name    = substr( strrchr( $class, '\\' ), 1 );
		$this->type    = str_replace( '_', '-', $class_name );

		// Setup the components parts for render.
		$this->setup_component_parts();
	}

	/**
	 * Magic caller to filter component parts dynamically.
	 *
	 * @param string $name The part name.
	 * @param array  $args array of args to pass to the filter.
	 *
	 * @return mixed
	 */
	public function __call( $name, $args ) {
		$struct = $args[0];
		if ( $this->setting->has_param( $name ) ) {
			$struct['content'] = $this->setting->get_param( $name );
		}

		// Apply type to each structs.
		$struct['attributes']['class'][] = 'cld-' . $this->type;

		return $struct;
	}

	/**
	 * Setup the components build parts.
	 */
	private function setup_component_parts() {

		$build_parts = array(
			'wrap'        => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'header'      => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'icon'        => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'title'       => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'collapse'    => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'tooltip'     => array(
				'element'    => 'span',
				'attributes' => array(
					'class' => array(),
				),
			),
			'body'        => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'settings'    => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'prefix'      => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'suffix'      => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
			'description' => array(
				'element'    => 'div',
				'attributes' => array(
					'class' => array(),
				),
			),
		);

		/**
		 * Filter the components build parts.
		 *
		 * @param array $build_parts The build parts.
		 * @param self  $type        The component object.
		 *
		 * @return array
		 */
		$structs = apply_filters( 'setup_component_parts', $build_parts, $this );
		foreach ( $structs as $name => $struct ) {
			$struct['attributes']['class'][] = 'cld-ui-' . $name;
			$this->register_component_part( $name, $struct );
		}
	}

	/**
	 * Registers a new component part type.
	 *
	 * @param string $name   Name for the part type.
	 * @param array  $struct The array structure for the part type.
	 */
	public function register_component_part( $name, $struct ) {
		$base                       = array(
			'element'    => 'div',
			'attributes' => array(),
			'children'   => array(),
			'content'    => null,
		);
		$this->build_parts[ $name ] = wp_parse_args( $struct, $base );
	}

	/**
	 * Sanitize the value.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_value( $value ) {
		return wp_kses_post( $value );
	}

	/**
	 * Renders the component.
	 */
	public function render() {

		// Setup the component.
		$this->pre_render();

		// Build the blueprint parts list.
		$blueprint   = $this->setting->get_param( 'blueprint', $this->blueprint );
		$build_parts = explode( '|', $blueprint );

		// Build the multi-dimensional array.
		$struct = $this->build_struct( $build_parts );
		$this->compile_structures( $struct );

		// Output html.
		return self::compile_html( $this->html );
	}

	/**
	 * Build the structures from the build parts.
	 *
	 * @param array $parts Array of build parts to build.
	 *
	 * @return array
	 */
	protected function build_struct( &$parts ) {

		$struct = array();

		while ( ! empty( $parts ) ) {
			$part  = array_shift( $parts );
			$state = $this->get_state( $part );
			if ( 'close' === $state ) {
				return $struct;
			}
			$name                 = trim( $part, '/' );
			$part_struct          = $this->get_part( $name );
			$part_struct['state'] = $state;
			$part_struct['name']  = $name;
			$struct[ $name ]      = $this->{$name}( $part_struct );
			if ( 'open' === $state ) {
				$struct[ $name ]['children'] += $this->build_struct( $parts );
			}
		}

		return $struct;

	}

	/**
	 * Go through the structures and compile.
	 *
	 * @param array $structure The components structures.
	 */
	protected function compile_structures( $structure ) {
		foreach ( $structure as $name => $struct ) {
			$this->handle_structure( $name, $struct );
		}
	}

	/**
	 * Get a blueprint parts state.
	 *
	 * @param string $part The part name.
	 *
	 * @return string
	 */
	public function get_state( $part ) {
		$state = 'open';
		$pos   = strpos( $part, '/' );
		if ( is_int( $pos ) ) {
			switch ( $pos ) {
				case 0:
					$state = 'close';
					break;
				default:
					$state = 'void';
			}
		}

		return $state;
	}

	/**
	 * Handles a structure part before rendering.
	 *
	 * @param string $name   The name of the part.
	 * @param array  $struct The parts structure.
	 */
	public function handle_structure( $name, $struct ) {
		if ( $this->has_content( $name, $struct ) ) {
			$this->compile_part( $struct );
		}
	}

	/**
	 * Recursively check if the current structure has content.
	 *
	 * @param string | null $name   The name of the part.
	 * @param array         $struct The part structure.
	 *
	 * @return bool
	 */
	public function has_content( $name, $struct = array() ) {
		$return = ! empty( $struct['content'] ) || $this->setting->has_param( $name ) || ! empty( $struct['render'] );
		if ( false === $return && ! empty( $struct['children'] ) ) {
			foreach ( $struct['children'] as $child => $child_struct ) {
				if ( true === $this->has_content( $child, $child_struct ) ) {
					$return = true;
					break;
				}
			}
		}

		return $return;
	}

	/**
	 * Build a component part.
	 *
	 * @param array $struct The component part structure array.
	 */
	public function compile_part( $struct ) {
		$this->open_tag( $struct );
		if ( ! $this->is_void_element( $struct['element'] ) ) {
			$this->add_content( $this->setting->get_param( $struct['name'], $struct['content'] ) );
			if ( ! empty( $struct['children'] ) ) {
				foreach ( $struct['children'] as $name => $child ) {
					$this->handle_structure( $name, $child );
				}
			}
			$this->close_tag( $struct );
		}
	}

	/**
	 * Opens a new tag.
	 *
	 * @param array $struct The tag structure.
	 */
	protected function open_tag( $struct ) {
		if ( ! empty( $struct['element'] ) ) {
			$this->html[] = $this->build_tag( $struct['element'], 'open', $struct['attributes'] );
		}
	}

	/**
	 * Closes an open tag.
	 *
	 * @param array $struct The tag structure.
	 */
	protected function close_tag( $struct ) {
		if ( ! empty( $struct['element'] ) ) {
			$this->html[] = $this->build_tag( $struct['element'], 'close', $struct['attributes'] );
		}
	}

	/**
	 * Adds the content to the html.
	 *
	 * @param string $content The content to add.
	 */
	protected function add_content( $content ) {

		if ( ! is_string( $content ) && is_callable( $content ) ) {
			$this->html[] = call_user_func( $content );
		} else {
			$this->html[] = $content;
		}
	}

	/**
	 * Check if an element type is a void element.s
	 *
	 * @param string $element The element to check.
	 *
	 * @return bool
	 */
	public function is_void_element( $element ) {
		$void_elements = array(
			'area',
			'base',
			'br',
			'col',
			'embed',
			'hr',
			'img',
			'input',
			'link',
			'meta',
			'param',
			'source',
			'track',
			'wbr',
		);

		return in_array( strtolower( $element ), $void_elements, true );
	}

	/**
	 * Build an HTML tag.
	 *
	 * @param string $element    The element to build.
	 * @param string $state      The element state.
	 * @param array  $attributes The attributes for the tags.
	 *
	 * @return string
	 */
	protected function build_tag( $element, $state, $attributes = array() ) {

		$prefix_element = 'close' === $state ? '/' : '';
		$tag            = array();
		$tag[]          = $prefix_element . $element;
		$tag[]          = self::build_attributes( $attributes );
		$tag[]          = $this->is_void_element( $element ) ? '/' : null;

		return self::compile_tag( $tag );
	}

	/**
	 * Get a build part to construct.
	 *
	 * @param string $part The part name.
	 *
	 * @return array
	 */
	public function get_part( $part ) {
		$struct = array(
			'element'    => $part,
			'attributes' => array(),
			'children'   => array(),
			'state'      => null,
			'content'    => null,
		);
		if ( isset( $this->build_parts[ $part ] ) ) {
			$struct = wp_parse_args( $this->build_parts[ $part ], $struct );
		}

		return $struct;
	}

	/**
	 * Filter the title parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function title( $struct ) {
		$struct['content'] = $this->setting->get_param( 'title', $this->setting->get_param( 'page_title' ) );

		return $struct;
	}

	/**
	 * Filter the tooltip parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function tooltip( $struct ) {

		$struct['attributes']['class'] = array(
			'dashicons',
			'dashicons-editor-help',
		);
		$struct['attributes']['title'] = $this->setting->get_param( 'tooltip' );


		return $struct;
	}

	/**
	 * Filter the icon parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function icon( $struct ) {

		$icon   = $this->setting->get_param( 'icon' );
		$method = 'dashicon';
		if ( false === strpos( $icon, 'dashicons' ) ) {
			$method = 'image_icon';
		}

		return $this->$method( $struct );
	}

	/**
	 * Filter the dashicon parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function dashicon( $struct ) {
		$struct['element']               = 'img';
		$struct['attributes']['class'][] = 'dashicons';
		$struct['attributes']['class'][] = $this->setting->get_param( 'icon' );

		return $struct;
	}

	/**
	 * Filter the image icons parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function image_icon( $struct ) {
		$struct['element']           = 'img';
		$struct['attributes']['src'] = $this->setting->get_param( 'icon' );

		return $struct;
	}

	/**
	 * Filter the settings parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function settings( $struct ) {
		$struct['element'] = '';
		if ( $this->setting->has_settings() ) {
			$html = array();
			foreach ( $this->setting->get_settings() as $setting ) {
				$html[] = $setting->get_component()->render();
			}
			$struct['content'] = self::compile_html( $html );
		}

		return $struct;
	}

	/**
	 * Builds and sanitizes attributes for an HTML tag.
	 *
	 * @param array $attributes Array of key value attributes to build.
	 *
	 * @return string
	 */
	public static function build_attributes( $attributes ) {

		$return = array();
		foreach ( $attributes as $attribute => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}
			$return[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}

		return implode( ' ', $return );

	}

	/**
	 * Compiles HTML parts array into a string.
	 *
	 * @param array $html HTML parts array.
	 *
	 * @return string
	 */
	public static function compile_html( $html ) {
		$html = array_filter( $html );

		return implode( '', $html );
	}

	/**
	 * Compiles a tag from a parts array into a string.
	 *
	 * @param array $tag Tag parts array.
	 *
	 * @return string
	 */
	public static function compile_tag( $tag ) {
		$tag = array_filter( $tag );

		return '<' . implode( ' ', $tag ) . '>';
	}

	/**
	 * Init the component.
	 *
	 * @param Setting $setting The setting object.
	 *
	 * @return self
	 */
	public static function init( $setting ) {

		$caller = get_called_class();
		$type   = $setting->get_param( 'type' );
		// Set Caller.
		$component = "{$caller}\\{$type}";
		// Final check if type is callable component.
		if ( ! is_string( $type ) || ! self::is_component_type( $type ) ) {
			// Set to a default HTML component if not found.
			$type = 'html';
			// Check what type this component needs to be.
			if ( is_callable( $type ) ) {
				$setting->set_param( 'callback', $type );
				$setting->set_param( 'type', 'custom' );
				$type = 'custom';
			}
			$component = "{$caller}\\{$type}";
		}

		return new $component( $setting );
	}

	/**
	 * Check if the type is a component.
	 *
	 * @param string $type The type to check.
	 *
	 * @return bool
	 */
	public static function is_component_type( $type ) {
		$caller = get_called_class();

		// Check that this type of component exists.
		return is_callable( array( $caller . '\\' . $type, 'init' ) );
	}

	/**
	 * Setup action before rendering.
	 */
	protected function pre_render() {
	}
}
