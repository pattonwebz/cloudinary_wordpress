<?php
/**
 * Settings Page section template.
 *
 * @package Cloudinary
 */

$tab     = $this->get_tab(); // phpcs:ignore
$section = $tab->get_option_slug();
$classes = array(
	'settings-tab-section',
);
if ( $tab->has_param('classes') ) {
	$classes = array_merge( $classes, $tab->get_param('classes') );
}
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" id="tab-<?php echo esc_attr( $tab->get_slug()  ); ?>">
	<div class="settings-tab-section-fields">
		<?php do_settings_sections( $section ); ?>
	</div>
</div>
