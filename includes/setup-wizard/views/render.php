<?php
/**
 * Render all steps
 */

namespace UltimatePostKit\SetupWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bdt-step-header">
	<div class="bdt-shape-elements">
		<div class="bdt-shape bdt-shape-circle"></div>
		<div class="bdt-shape bdt-shape-square"></div>
		<div class="bdt-shape bdt-shape-triangle"></div>
		<div class="bdt-shape bdt-shape-dots"></div>
		<div class="bdt-shape bdt-shape-ring"></div>
		<div class="bdt-shape bdt-shape-plus"></div>
	</div>
	
	<div class="bdt-wizard-progress-header">
		<ul class="bdt-wizard-progress">
			<li class="bdt-wizard-progress-item active" data-step="welcome"><?php esc_html_e( 'Welcome', 'ultimate-post-kit' ); ?></li>
			<li class="bdt-wizard-progress-item" data-step="features"><?php esc_html_e( 'Choose Features', 'ultimate-post-kit' ); ?></li>
			<li class="bdt-wizard-progress-item" data-step="integration"><?php esc_html_e( 'Integration', 'ultimate-post-kit' ); ?></li>
			<li class="bdt-wizard-progress-item" data-step="finish"><?php esc_html_e( 'Good to Go', 'ultimate-post-kit' ); ?></li>
		</ul>
	</div>
	<div class="bdt-step-content">
		<div class="bdt-wizard-container">
			<?php
			require_once plugin_dir_path( BDTUPK__FILE__ ) . 'includes/setup-wizard/views/welcome.php';
			require_once plugin_dir_path( BDTUPK__FILE__ ) . 'includes/setup-wizard/views/features.php';
			require_once plugin_dir_path( BDTUPK__FILE__ ) . 'includes/setup-wizard/views/integration.php';
			require_once plugin_dir_path( BDTUPK__FILE__ ) . 'includes/setup-wizard/views/good-to-go.php';
			?>
		</div>
	</div>
</div>