<?php
/**
 * Integration Step
 */

namespace UltimatePostKit\SetupWizard;

if (!defined('ABSPATH')) {
    exit;
}

$upk_plugins = array(
    array(
        'name'        => 'Prime Slider',
        'slug'        => 'bdthemes-prime-slider-lite/bdthemes-prime-slider.php',
        'description' => 'Create eye-catching sliders for your website quickly and easily.',
        'recommended' => true,
    ),
    array(
        'name'        => 'ZoloBlocks',
        'slug'        => 'zoloblocks/zoloblocks.php',
        'description' => 'Build amazing WordPress pages with helpful and flexible Gutenberg blocks.',
        'recommended' => false,
    ),
    array(
        'name'        => 'Ultimate Post Kit',
        'slug'        => 'ultimate-post-kit/ultimate-post-kit.php',
        'description' => 'Design beautiful post layouts with simple, ready-made blocks.',
        'recommended' => true,
    ),
    array(
        'name'        => 'Ultimate Store Kit',
        'slug'        => 'ultimate-store-kit/ultimate-store-kit.php',
        'description' => 'Improve your online store with tools to display products better.',
        'recommended' => true,
    ),
    array(
        'name'        => 'Pixel Gallery',
        'slug'        => 'pixel-gallery/pixel-gallery.php',
        'description' => 'Show off your photos in a stylish, responsive gallery.',
        'recommended' => false,
    ),
    array(
        'name'        => 'Live Copy Paste',
        'slug'        => 'live-copy-paste/live-copy-paste.php',
        'description' => 'Copy and paste website elements between WordPress sites instantly.',
        'recommended' => false,
    ),
    array(
        'name'        => 'Instant Image Generator',
        'slug'        => 'ai-image/ai-image.php',
        'description' => 'Generate unique images using AI directly in WordPress.',
        'recommended' => false,
    ),
    array(
        'name'        => 'AR Viewer',
        'slug'        => 'ar-viewer/ar-viewer.php',
        'description' => 'Let users view your products in 3D with augmented reality.',
        'recommended' => false,
    ),
    array(
        'name'        => 'Spin Wheel',
        'slug'        => 'spin-wheel/spin-wheel.php',
        'description' => 'Engage your visitors with a fun and interactive spin wheel.',
        'recommended' => false,
    ),
);
?>

<div class="bdt-wizard-step bdt-setup-wizard-integration" data-step="integration">
    <h2><?php esc_html_e('Add More Firepower', 'ultimate-post-kit'); ?></h2>
    <p><?php esc_html_e('You can onboard additional powerful plugins to extend your web design capabilities.', 'ultimate-post-kit'); ?></p>

    <div class="progress-bar-container">
        <div id="plugin-install-progress" class="progress-bar"></div>
    </div>

    <form method="POST" id="upk-install-plugins">
        <div class="bdt-plugin-list">
            <?php
            foreach ($upk_plugins as $plugin) :
                $is_active = is_plugin_active($plugin['slug']);
                $is_recommended = $plugin['recommended'] && !$is_active;
            ?>
                <label class="plugin-item" data-slug="<?php echo esc_attr($plugin['slug']); ?>">
                    <span class="bdt-flex bdt-flex-middle bdt-flex-between bdt-margin-small-bottom">
						<span class="bdt-plugin-name">
							<?php echo wp_kses_post($plugin['name']); ?>
						</span>
                        <div class="bdt-plugin-badge-switch-wrap">

                        <?php if ($is_recommended) : ?>
                            <span class="recommended-badge"><?php esc_html_e('Recommended', 'ultimate-post-kit'); ?></span>
                        <?php endif; ?>
                        
                        <?php if ($is_active) : ?>
                            <span class="active-badge"><?php esc_html_e('ACTIVE', 'ultimate-post-kit'); ?></span>
                        <?php endif; ?>
                         <?php
                         if (!$is_active) : ?>
                             <label class="switch">
                                 <input type="checkbox" class="plugin-slider-checkbox" <?php echo wp_kses_post($plugin['recommended']) ? 'checked' : ''; ?>
                                        name="plugins[]<?php echo isset($plugin['slug']) ? wp_kses_post($plugin['slug']) : ''; ?>">
                                 <span class="slider round"></span>
                             </label>
                         <?php
                         endif;
                         ?>
                        </div>
					</span>
                    <span class="plugin-text">
						<?php echo wp_kses_post($plugin['description']); ?>
					</span>
                </label>
            <?php
            endforeach; ?>
        </div>
        
        <div class="wizard-navigation bdt-margin-top">
            <button class="bdt-button bdt-button-primary d-none" type="submit" id="upk-install-plugins-btn">
                <?php esc_html_e('Install and Continue', 'ultimate-post-kit'); ?>
            </button>
            <div class="bdt-close-button bdt-margin-left bdt-wizard-next" data-step="finish"><?php esc_html_e('Skip', 'ultimate-post-kit'); ?></div>
        </div>
    </form>

    <div class="bdt-wizard-navigation">
        <button class="bdt-button bdt-button-secondary bdt-wizard-prev" data-step="features">
            <span><i class="dashicons dashicons-arrow-left-alt"></i></span>
            <?php esc_html_e('Previous Step', 'ultimate-post-kit'); ?>
        </button>
    </div>
</div>
