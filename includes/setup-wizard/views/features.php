<?php
/**
 * Integration Step
 */

namespace UltimatePostKit\SetupWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$widget_map     = \UltimatePostKit\Includes\Setup_Wizard::get_widget_map();
$active_modules = get_option( 'ultimate_post_kit_active_modules', array() );


?>

<div class="bdt-wizard-step bdt-setup-wizard-features" data-step="features">
	<!-- <h2>Choose your features</h2>
	<p>You may enable the widgets and extensions you need for your current project while keeping others turned off.</p> -->
	<form method="post" action="admin-ajax.php?action=ultimate_post_kit_settings_save" id="upk_setup_wizard_modules">
		<input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=ultimate_post_kit_options">
		<input type="hidden" name="id" value="ultimate_post_kit_active_modules">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'ultimate-post-kit-settings-save-nonce' ) ); ?>">
		<input type="hidden" name="action" value="ultimate_post_kit_settings_save">

		<div class="bdt-features-list">
			<div class="widget-filter bdt-flex bdt-flex-wrap bdt-flex-between bdt-flex-middle">
				<div class="category-dropdown">
					<label for="category-select"><?php esc_html_e('Filter by:', 'ultimate-post-kit'); ?></label>
					<select id="category-select">
						<option value="all"><?php esc_html_e('All', 'ultimate-post-kit'); ?></option>
						<option value="new"><?php esc_html_e('New', 'ultimate-post-kit'); ?></option>
						<option value="grid"><?php esc_html_e('Grid', 'ultimate-post-kit'); ?></option>
						<option value="list"><?php esc_html_e('List', 'ultimate-post-kit'); ?></option>
						<option value="carousel"><?php esc_html_e('Carousel', 'ultimate-post-kit'); ?></option>
						<option value="slider"><?php esc_html_e('Slider', 'ultimate-post-kit'); ?></option>
						<option value="tabs"><?php esc_html_e('Tabs', 'ultimate-post-kit'); ?></option>
						<option value="timeline"><?php esc_html_e('Timeline', 'ultimate-post-kit'); ?></option>
						<option value="template-builder"><?php esc_html_e('Template Builder', 'ultimate-post-kit'); ?></option>
						<option value="loop"><?php esc_html_e('Loop Builder', 'ultimate-post-kit'); ?></option>
						<option value="others"><?php esc_html_e('Others', 'ultimate-post-kit'); ?></option>
					</select>
				</div>
				<div class="input-btn-wrap bdt-flex bdt-flex-wrap bdt-flex-between">
					<input type="text" placeholder="<?php esc_attr_e('Search widgets...', 'ultimate-post-kit'); ?>" class="widget-search" value="">
					<div class="bulk-action-buttons bdt-flex">
						<button class="bulk-action activate"><?php esc_html_e('Activate All', 'ultimate-post-kit'); ?></button>
						<button class="bulk-action deactivate"><?php esc_html_e('Deactivate All', 'ultimate-post-kit'); ?></button>
					</div>
				</div>
			</div>
			
			<div class="widget-list-container">
				<ul class="widget-list">
					<?php foreach ( $widget_map as $widget ) : ?>
						<?php
						$is_checked = isset( $active_modules[ $widget['name'] ] ) && 'on' === $active_modules[ $widget['name'] ] ? 'checked' : '';

						$pro_class = '';
						if (!empty($widget['widget_type']) && 'pro' == $widget['widget_type'] && true !== _is_upk_pro_activated()) {
							$pro_class = ' upk-setup-wizard-pro-widget';
						}
						?>
						<li class="<?php echo esc_attr( $widget['widget_type'] . $pro_class ); ?>"
							data-type="<?php echo isset( $widget['content_type'] ) ? esc_attr( $widget['content_type'] ) : ''; ?>"
							data-label="<?php echo esc_attr( strtolower( $widget['label'] ) ); ?>">
							<div class="widget-item-clickable bdt-flex bdt-flex-middle bdt-flex-between">
								<span class="bdt-flex bdt-text-left"><?php echo esc_html( $widget['label'] ); ?></span>
								<label class="switch">
									<input type="hidden" name="ultimate_post_kit_active_modules[<?php echo esc_attr( $widget['name'] ); ?>]" value="off">
									<input type="checkbox" name="ultimate_post_kit_active_modules[<?php echo esc_attr( $widget['name'] ); ?>]" <?php echo esc_html( $is_checked ); ?> value="on" class="checkbox" id="bdt_upk_ultimate_post_kit_active_modules[<?php echo esc_attr( $widget['name'] ); ?>]">
									<span class="slider"></span>
								</label>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		
		<div class="wizard-navigation bdt-margin-top">
			<button class="bdt-button bdt-button-primary" type="submit" id="save-and-continue">
				<?php esc_html_e('Save and Continue', 'ultimate-post-kit'); ?>
			</button>
			<div class="bdt-close-button bdt-margin-left bdt-wizard-next" data-step="integration"><?php esc_html_e('Skip', 'ultimate-post-kit'); ?></div>
		</div>
	</form>

	<div class="bdt-wizard-navigation">
		<button class="bdt-button bdt-button-secondary bdt-wizard-prev" data-step="welcome">
			<span><i class="dashicons dashicons-arrow-left-alt"></i></span>
			<?php esc_html_e( 'Previous Step', 'ultimate-post-kit' ); ?>
		</button>
	</div>
</div>

