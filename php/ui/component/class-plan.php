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

	protected $blueprint = 'title/|plan_box|plan_heading/|plan_summary|/plan_summary|/plan_box';

	protected function pre_render() {
		$connection = get_plugin_instance()->get_component( 'connect' );
		$this->setting->set_param( 'plan_heading', $connection->get_usage_stat( 'plan' ) );
	}


	protected function title( $struct ) {

		$struct['element'] = 'h2';

		return parent::title( $struct );
	}


	protected function plan_box( $struct ) {
		$struct['element']             = 'div';
		$struct['attributes']['class'] = array(
			'cld-panel-inner',
		);

		return $struct;
	}

	public function sscontent() {


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

		return self::compile_html( $html );
	}

	protected function plan_heading( $struct ) {
		$html   = array();
		$atts   = array(
			'style' => 'color: var(--accent-color);',
		);
		$html[] = '<h2 ' . $this->build_attributes( $atts ) . ' >';
		$html[] = $this->setting->get_param( 'plan' );
		$html[] = '</h2>';

		$struct['element'] = 'h2';

		return parent::plan_heading( $struct );
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

	protected function plan_summary( $struct ) {


		$summary            = $this->get_part( 'h4' );
		$summary['content'] = $this->setting->get_param( 'plan_heading', __( '25 Monthly Credits', 'cloudinary' ) );

		$detail            = $this->get_part( 'span' );
		$detail['content'] = __( '1 Credit =', 'cloudinary' );


		$struct['children']['h4']   = $summary;
		$struct['children']['span'] = $detail;
		$struct['children']['ul']   = $this->content();
		$struct['element']          = 'div';

		return $struct;
	}

	protected function plan_wrap( $struct ) {
		$struct['element']             = 'div';
		$struct['attributes']['class'] = array(
			'cld-panel-inner',
		);

		return $struct;
	}

	/**
	 * Creates the Content/Input HTML.
	 */
	protected function content() {

		$points = $this->get_part( 'ul' );
		$items  = array(
			__( '1,000 Transformations', 'cloudinary' ),
			__( '1 GB Storage', 'cloudinary' ),
			__( '1 GB Bandwidth', 'cloudinary' ),
		);
		$li     = $this->get_part( 'li' );
		foreach ( $items as $item ) {
			$child                = $li;
			$child['content']     = $item;
			$points['children'][] = $child;
		}

		return $points;
	}
}
