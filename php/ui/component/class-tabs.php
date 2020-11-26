<?php
/**
 * Abstract UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

/**
 * Class Component
 *
 * @package Cloudinary\UI
 */
class Tabs extends Panel {

	/**
	 * Create the start of the tabs wrapper.
	 *
	 * @return string
	 */
	protected function start_tabs() {
		return '<ul ' . $this->build_attributes( $this->get_attributes( 'wrapper' ) ) . ' >';
	}

	/**
	 * Renders the tabs.
	 */
	public function render() {
		$html   = array();
		$html[] = $this->start_tabs();
		$active = $this->get_active_setting();
		$url    = admin_url( 'admin.php?page=' . $this->setting->get_parent()->get_slug() );
		foreach ( $this->setting->get_settings() as $setting ) {
			$url  = add_query_arg( array( 'tab' => $setting->get_slug() ), $url );
			$atts = array(
				'class' => array(
					'settings-ui-component-tab',
				),
			);
			if ( $active === $setting ) {
				$atts['class'][] = 'active';
			}
			$link_atts = array(
				'href' => $url,
			);
			$html[]    = '<li ' . $this->build_attributes( $atts ) . ' >';
			$html[]    = '<a ' . $this->build_attributes( $link_atts ) . ' >';
			$html[]    = $setting->get_param( 'title', 'asdasd' );
			$html[]    = '</a>';
			$html[]    = '</li>';
		}
		$html[] = $this->end_tabs();

		$html[] = $this->settings();

		return self::compile_html( $html );
	}

	/**
	 * Create the end of the tabs wrapper.
	 *
	 * @return string
	 */
	protected function end_tabs() {
		return '</ul>';
	}

	/**
	 * Render the settings of the active tab.
	 *
	 * @return string
	 */
	protected function settings() {
		return $this->get_active_setting()->render_component();
	}
}

