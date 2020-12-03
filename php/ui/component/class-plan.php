<?php
/**
 * Plan UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

use function Cloudinary\get_plugin_instance;
use Cloudinary\UI\Component;

/**
 * Plan Component to render plan status.
 *
 * @package Cloudinary\UI
 */
class Plan extends Component {

	public function render() {
		$connection = get_plugin_instance()->get_component( 'connect' );
		$this->setting->set_param( 'plan', $connection->get_usage_stat( 'plan' ) );


		// Component heading/title.
		if ( $this->setting->has_param( 'title' ) ) {
			$html[] = $this->heading();
		}

		// Main Component Wrapper.
		$html[] = $this->start_wrapper();

		$html[] = $this->plan_heading();

		// Component Content.
		$html[] = $this->content();

		// End component wrapper.
		$html[] = $this->end_wrapper();

		// Do settings.
		if ( $this->setting->has_settings() ) {
			$html[] = $this->settings();
		}

		return self::compile_html( $html );
	}

	protected function plan_heading() {
		$html   = array();
		$atts   = array(
			'style' => 'color: var(--accent-color);',
		);
		$html[] = '<h2 ' . $this->build_attributes( $atts ) . ' >';
		$html[] = $this->setting->get_param( 'plan' );
		$html[] = '</h2>';

		return self::compile_html( $html );
	}

	/**
	 * Get a specific set of attributes.
	 *
	 * @param string $attribute_point The key of the attribute point to get.
	 *
	 * @return array
	 */
	public function get_attributes( $attribute_point ) {

		$attributes = parent::get_attributes( $attribute_point );
		switch ( $attribute_point ) {
			case 'wrapper':
				$attributes['class'] = array(
					'cld-box',
					'cld-box--inner',
				);
				break;
			case 'content':
				$attributes['class'] = 'mt-0.75';
				break;
		}

		return $attributes;
	}

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {

		// Component heading/title.

		$items  = array(
			__( '1,000 Transformations', 'cloudinary' ),
			__( '1 GB Storage', 'cloudinary' ),
			__( '1 GB Bandwidth', 'cloudinary' ),
		);
		$html   = array();
		$html[] = '<h4>' . __( '25 Monthly Credits', 'cloudinary' ) . '</h4>';
		$html[] = '<div ' . $this->build_attributes( $this->get_attributes( 'content' ) ) . '>';
		$html[] = '<p ' . $this->build_attributes( $this->get_attributes( 'content' ) ) . '>';
		$html[] = __( '1 Credit =', 'cloudinary' );
		$html[] = '</p>';
		$html[] = '<ul>';
		foreach ( $items as $item ) {
			$html[] = '<li>';
			$html[] = '<span>' . $item . '</span>';
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';


		return self::compile_html( $html );
	}
}
