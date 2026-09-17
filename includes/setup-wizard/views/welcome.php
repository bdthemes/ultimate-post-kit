<?php
/**
 * Welcome Step
 */

namespace UltimatePostKit\SetupWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bdt-wizard-step bdt-text-center active" data-step="welcome">
    <div class="bdt-welcome-header">
        <div class="bdt-logo-container">
            <img src="<?php echo esc_url( BDTUPK_ASSETS_URL . 'images/logo.svg' ); ?>" alt="Ultimate Post Kit Logo" class="bdt-logo">
        </div>
        <h2><?php esc_html_e( 'Welcome to Ultimate Post Kit', 'ultimate-post-kit' ); ?></h2>
    </div>

    <div class="bdt-welcome-scroll">
    <div class="bdt-welcome-features">
        <div class="bdt-features-grid">
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-admin-customizer"></span>
                </div>
                <h3><?php esc_html_e( '80+ Widgets', 'ultimate-post-kit' ); ?></h3>
                <p><?php esc_html_e( 'Powerful elements for unlimited design possibilities', 'ultimate-post-kit' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-layout"></span>
                </div>
                <h3><?php esc_html_e( 'Ready Templates', 'ultimate-post-kit' ); ?></h3>
                <p><?php esc_html_e( 'Professional templates to jumpstart your projects', 'ultimate-post-kit' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-performance"></span>
                </div>
                <h3><?php esc_html_e( 'Fast & Optimized', 'ultimate-post-kit' ); ?></h3>
                <p><?php esc_html_e( 'Built with performance in mind for lightning-fast websites', 'ultimate-post-kit' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-admin-page"></span>
                </div>
                <h3><?php esc_html_e( 'Live Copy & Paste', 'ultimate-post-kit' ); ?></h3>
                <p><?php esc_html_e( 'Copy and paste supported designs straight into your pages', 'ultimate-post-kit' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-controls-play"></span>
                </div>
                <h3><?php esc_html_e( 'Widget Animations', 'ultimate-post-kit' ); ?></h3>
                <p><?php esc_html_e( 'Add animation options to supported widgets', 'ultimate-post-kit' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-sos"></span>
                </div>
                <h3><?php esc_html_e( 'Dedicated Support', 'ultimate-post-kit' ); ?></h3>
                <p><?php esc_html_e( 'Regular updates and expert help whenever you need a hand', 'ultimate-post-kit' ); ?></p>
            </div>
        </div>
    </div>

    <?php require plugin_dir_path( BDTUPK__FILE__ ) . 'includes/setup-wizard/views/subscribe.php'; ?>
    </div>

    <div class="bdt-wizard-navigation">
        <button class="bdt-button bdt-button-primary bdt-wizard-next" data-step="features">
            <?php esc_html_e( 'Get Started', 'ultimate-post-kit' ); ?>
            <span><i class="dashicons dashicons-arrow-right-alt"></i></span>
        </button>
    </div>
</div>
