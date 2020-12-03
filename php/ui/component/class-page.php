<?php
/**
 * Page UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

/**
 * Page Class Component
 *
 * @package Cloudinary\UI
 */
class Page extends Panel {

	public function __construct( $setting ) {
		parent::__construct( $setting );
		$this->attributes['wrapper']['class'] = 'cld-settings__container';
	}

	/**
	 * Renders the component.
	 */
	public function render() {

		$html = array(
			'<div class="cloudinary">',
		);

		if ( $this->setting->has_param( 'page_header' ) ) {
			$html[] = $this->setting->get_param( 'page_header' )->get_component()->render();
		}

		$this->setting->set_param('content', $this->setting->has_settings() );
		$html[] = parent::render();

		$html[] = '</div>';

		return self::compile_html( $html );
	}

	/**
	 * Create the start of the tabs wrapper.
	 *
	 * @return string
	 */
	protected function start_tabs() {
		$atts = array(
			'class' => 'cld-tabs',
			'role'  => 'tablist',
		);

		return '<ul ' . $this->build_attributes( $atts ) . ' >';
	}

	/**
	 * Render the tabs index.
	 */
	protected function tab_bar() {
		$html[] = $this->start_tabs();
		$active = $this->get_active_setting();
		$url    = add_query_arg( array( 'page' => $this->setting->get_slug() ), admin_url( 'admin.php' ) );
		foreach ( $this->setting->get_settings() as $setting ) {
			$url      = add_query_arg( array( 'tab' => $setting->get_slug() ), $url );
			$tab_atts = array(
				'class'         => array(
					'cld-tabs__tab',
				),
				'role'          => 'tab',
				'aria-selected' => 'false',
				'aria-controls' => $setting->get_slug() . '-tab',
				'id'            => $setting->get_slug(),
			);
			if ( $active === $setting ) {
				$tab_atts['class'][]       = 'cld-tabs__tab--active';
				$tab_atts['aria-selected'] = 'true';
			}
			$link_att = array(
				'href' => $url,
			);
			$html[]   = '<li ' . $this->build_attributes( $tab_atts ) . ' >';
			$html[]   = '<a ' . $this->build_attributes( $link_att ) . ' >';
			$html[]   = $setting->get_param( 'menu_title' );
			$html[]   = '</a>';
			$html[]   = '</li>';
		}
		$html[] = $this->end_tabs();

		return self::compile_html( $html );
	}

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {


		$option_name = $this->get_option_name();
		settings_errors( $option_name );
		// Set the attributes for the field.
		$option = array(
			'type'  => 'hidden',
			'name'  => 'option_page',
			'value' => $option_name,
		);
		// Set the attributes for the field action.
		$action = array(
			'type'  => 'hidden',
			'name'  => 'action',
			'value' => 'update',
		);
		$html   = array(
			'<input ' . $this->build_attributes( $option ) . ' />',
			'<input ' . $this->build_attributes( $action ) . ' />',
			wp_nonce_field( $option_name . '-options', '_wpnonce', true, false ),
		);


		return self::compile_html( $html );
	}

	/**
	 * Get the option name for this component.
	 *
	 * @return string
	 */
	protected function get_option_name() {
		// Get the options setting input field.
		$option_name = $this->setting->get_option_name();
		if ( $this->setting->has_param( 'has_tabs' ) ) {
			$option_name = $this->get_active_setting()->get_option_name();
		}

		return $option_name;
	}

	/**
	 * Start the component Wrapper.
	 *
	 * @return string
	 */
	protected function start_wrapper() {
		$html = array();

		if ( $this->setting->has_parent() && $this->setting->has_param( 'has_tabs' ) && 1 < $this->setting->get_setting_slugs() ) {
			$html[] = $this->tab_bar();
		}

		$form_atts = array(
			'method'     => 'post',
			'action'     => 'options.php',
			'novalidate' => 'novalidate',
			'class'      => 'render-trigger',
		);

		$html[] = '<form ' . $this->build_attributes( $form_atts ) . ' >';
		$html[] = '<div ' . $this->build_attributes( $this->get_attributes( 'wrapper' ) ) . '>';
		// Don't print out a header if we have a defined page header.
		if ( ! $this->setting->has_param( 'page_header' ) ) {
			$html[] = '<h1>' . $this->setting->get_param( 'page_title' ) . '</h1>';
		}

		return self::compile_html( $html );
	}

	/**
	 * Create the end of the wrapper HTML.
	 *
	 * @return string
	 */
	protected function end_wrapper() {
		$html = array(
			'</div>',
			'</form>',
		);

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
		if ( $this->setting->has_param( 'has_tabs' ) ) {
			return $this->get_active_setting()->render_component();
		}

		return parent::settings();
	}
}

