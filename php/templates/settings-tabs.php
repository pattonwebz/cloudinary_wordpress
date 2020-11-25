<?php
/**
 * Settings Page tabs template.
 *
 * @package Cloudinary
 */

$current_page = $this->get_page();
if ( ! $current_page->has_settings() ) {
	return;
}
$nav_tabs    = $current_page->get_settings();
$config      = $this->plugin->config;
$connect     = $this->plugin->components['connect'];
$active_tabs = array_filter(
	$nav_tabs,
	function ( $tab ) use ( $config, $connect ) {
		// If this tab has "require_config" set, ensure we're fully connected to cloudinary.
		if ( $tab->has_param('requires_config' ) &&
			( empty( $config['connect'] ) || empty( $connect ) || empty( $connect->is_connected() ) )
		) {
			return false;
		}

		return true;
	}
);

?>
<?php if ( ! empty( $active_tabs ) && 1 < count( $active_tabs ) ) : ?>
	<h2 class="nav-tab-wrapper wp-clearfix">
		<?php foreach ( $active_tabs as $tab_slug => $tab_definition ) : ?>
			<a <?php $this->build_tab_attributes( $tab_slug ); ?>><?php echo esc_html( $tab_definition['title'] ); ?></a>
		<?php endforeach; ?>
	</h2>
<?php endif; ?>
