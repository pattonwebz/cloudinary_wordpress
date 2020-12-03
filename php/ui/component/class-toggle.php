<?php
/**
 * Toggle Field UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;


/**
 * Class Toggle Component
 *
 * @package Cloudinary\UI
 */
class Toggle extends Text {

	public function __construct( $setting ) {
		parent::__construct( $setting );

		$this->attributes['wrapper']['class'] = 'cld-toggle';
		$this->setting->set_param( 'content', true );
	}

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {
		$atts          = $this->get_attributes( 'content' );
		$atts['type']  = 'checkbox';
		$atts['name']  = $this->setting->get_option_name() . '[' . $this->setting->get_slug() . ']';
		$atts['id']      = $this->setting->get_slug();
		$atts['value'] = 1;
		if( $this->setting->get_value() ){
			$atts['checked'] = 'checked';
		}
		if ( $this->setting->has_param( 'required' ) ) {
			$atts['required'] = 'required';
		}

		$wrap_atts  = array(
			'class' => array(
				'cld-toggle__input',
				'mt-0.5',
			),
		);
		$label_atts = array(
			'class' => 'cld-toggle__label',
		);
		$html       = array();
		$html[]     = '<label ' . $this->build_attributes( $wrap_atts ) . ' >';
		$html[]     = '<input ' . $this->build_attributes( $atts ) . ' />';
		$html[]     = '<span ' . $this->build_attributes( array( 'class' => 'cld-toggle__input__slider' ) ) . ' />';
		$html[]     = '</label>';

		$html[]     = '<label ' . $this->build_attributes( $label_atts ) . ' >';
		$html[]     = $this->setting->get_param( 'label' );
		$html[]     = '</label>';

		return self::compile_html( $html );
	}
}
