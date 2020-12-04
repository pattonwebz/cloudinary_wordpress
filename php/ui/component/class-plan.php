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

	/**
	 * Holds the components build blueprint.
	 *
	 * @var string
	 */
	protected $blueprint = 'title/|plan_box|plan_heading/|plan_summary|/plan_summary|/plan_box';

	/**
	 * Setup action before rendering.
	 */
	protected function pre_render() {
		$connection = get_plugin_instance()->get_component( 'connect' );
		$this->setting->set_param( 'plan_heading', $connection->get_usage_stat( 'plan' ) );
	}

	/**
	 * Filter the title parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function title( $struct ) {

		$struct['element'] = 'h2';

		return parent::title( $struct );
	}

	/**
	 * Filter the plan box parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function plan_box( $struct ) {
		$struct['element']             = 'div';
		$struct['attributes']['class'] = array(
			'cld-panel-inner',
		);

		return $struct;
	}

	/**
	 * Filter the plan heading parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
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
	 * Filter the plan summary parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
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

	/**
	 * Filter the plan wrapper parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function plan_wrap( $struct ) {
		$struct['element']             = 'div';
		$struct['attributes']['class'] = array(
			'cld-panel-inner',
		);

		return $struct;
	}

	/**
	 * Creates the bullet points of the plan.
	 *
	 * @return array
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
