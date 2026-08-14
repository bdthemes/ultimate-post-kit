<?php

use UltimatePostKit\Notices;
use UltimatePostKit\Utils;
use UltimatePostKit\Admin\ModuleService;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


/**
 * Ultimate Post Kit Admin Settings Class
 */

class UltimatePostKit_Admin_Settings {

    public static $modules_list  = null;
    public static $modules_names = null;

    public static $modules_list_only_widgets  = null;
    public static $modules_names_only_widgets = null;

    public static $modules_list_only_3rdparty  = null;
    public static $modules_names_only_3rdparty = null;

    const PAGE_ID = 'ultimate_post_kit_options';

    private $settings_api;

    public  $responseObj;
    public  $licenseMessage;
    public  $showMessage  = false;
    private $is_activated = false;

    /**
	 * Rollback version instance
	 * 
	 * @var Rollback_Version
	 */
	public $rollback_version;

    function __construct() {
        $this->settings_api = new UltimatePostKit_Settings_API;

        if (!defined('BDTUPK_HIDE')) {
            add_action('admin_init', [$this, 'admin_init']);
            add_action('admin_menu', [$this, 'admin_menu'], 201);
        }
		
		// Plugin installation (admin only)
		add_action('wp_ajax_upk_install_plugin', [$this, 'install_plugin_ajax']);
		
		

		if (_is_upk_pro_activated()) {
			// Initialize rollback version functionality
			add_action('admin_init', [$this, 'rollback_init']);
		}

    }

	public function rollback_init() {
		if ( class_exists('\UltimatePostKitPro\Rollback_Version') ) {
			$this->rollback_version = new \UltimatePostKitPro\Rollback_Version();
		}
	}

    /**
     * Get used widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */
    public static function get_used_widgets() {

        $used_widgets = array();

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module     = Module::instance();
            
 			$elements = $module->get_formatted_usage('raw');
            
            $upk_widgets = self::get_upk_widgets_names();

            if (is_array($elements) || is_object($elements)) {

                foreach ($elements as $post_type => $data) {
                    foreach ($data['elements'] as $element => $count) {
                        if (in_array($element, $upk_widgets, true)) {
                            if (isset($used_widgets[$element])) {
                                $used_widgets[$element] += $count;
                            } else {
                                $used_widgets[$element] = $count;
                            }
                        }
                    }
                }
            }
        }

        return $used_widgets;
    }

    /**
     * Get used separate widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_used_only_widgets() {

        $used_widgets = array();

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module     = Module::instance();
            
 			$elements = $module->get_formatted_usage('raw');
            
            $upk_widgets = self::get_upk_only_widgets();

            if (is_array($elements) || is_object($elements)) {

                foreach ($elements as $post_type => $data) {
                    foreach ($data['elements'] as $element => $count) {
                        if (in_array($element, $upk_widgets, true)) {
                            if (isset($used_widgets[$element])) {
                                $used_widgets[$element] += $count;
                            } else {
                                $used_widgets[$element] = $count;
                            }
                        }
                    }
                }
            }
        }

        return $used_widgets;
    }

    /**
     * Get unused widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_widgets() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $upk_widgets = self::get_upk_widgets_names();

        $used_widgets = self::get_used_widgets();

        $unused_widgets = array_diff($upk_widgets, array_keys($used_widgets));

        return $unused_widgets;
    }

    /**
     * Get unused separate widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_only_widgets() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $upk_widgets = self::get_upk_only_widgets();

        $used_widgets = self::get_used_only_widgets();

        $unused_widgets = array_diff($upk_widgets, array_keys($used_widgets));

        return $unused_widgets;
    }

    /**
     * Get widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_upk_widgets_names() {
        $names = self::$modules_names;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'upk-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list
            );
        }

        return $names;
    }

    /**
     * Get separate widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_upk_only_widgets() {
        $names = self::$modules_names_only_widgets;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'upk-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list_only_widgets
            );
        }

        return $names;
    }

    /**
     * Get separate 3rdParty widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_upk_only_3rdparty_names() {
        $names = self::$modules_names_only_3rdparty;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'upk-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list_only_3rdparty
            );
        }

        return $names;
    }

    /**
     * Get URL with page id
     *
     * @access public
     *
     */

    public static function get_url() {
        return admin_url('admin.php?page=' . self::PAGE_ID);
    }

    /**
     * Init settings API
     *
     * @access public
     *
     */

    public function admin_init() {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->ultimate_post_kit_admin_settings());

        //initialize settings
        $this->settings_api->admin_init();
        $this->upk_redirect_to_get_pro();
        if (true === _is_upk_pro_activated()) {
            $this->bdt_redirect_to_renew_link();
        }
    }

    /**
     * Add Plugin Menus
     *
     * @access public
     *
     */

     // Redirect to Ultimate Post Kit Pro pricing page
    public function upk_redirect_to_get_pro() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only check of the current admin page slug for display routing, no form data processed.
        if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_get_pro') {
            // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- Intentional redirect to a fixed, hardcoded external URL; wp_safe_redirect would block off-site hosts.
            wp_redirect('https://postkit.pro/pricing/');
            exit;
        }
    }

    /**
     * Redirect to license renewal page
     *
     * @access public
     *
     */
    public function bdt_redirect_to_renew_link() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only check of the current admin page slug for display routing, no form data processed.
        if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_license_renew') {
            // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- Intentional redirect to a fixed, hardcoded external URL; wp_safe_redirect would block off-site hosts.
            wp_redirect('https://account.bdthemes.com/');
            exit;
        }
    }

    /**
	 * Add Plugin Menus
	 *
	 * @access public
	 *
	 */

	public function admin_menu() {
		add_menu_page(
			BDTUPK_TITLE . ' ' . esc_html__('Dashboard', 'ultimate-post-kit'),
			BDTUPK_TITLE,
			'manage_options',
			self::PAGE_ID,
			[$this, 'plugin_page'],
			$this->ultimate_post_kit_icon(),
			58
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Core Widgets', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_active_modules',
			[$this, 'plugin_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Extensions', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_elementor_extend',
			[$this, 'plugin_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Special Features', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_other_settings',
			[$this, 'plugin_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('API Settings', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_api_settings',
			[$this, 'plugin_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('System Status', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_analytics_system_req',
			[$this, 'plugin_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Other Plugins', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_other_plugins',
			[$this, 'plugin_page']
		);
		
		// add_submenu_page(
		// 	self::PAGE_ID,
		// 	BDTUPK_TITLE,
		// 	esc_html__('Get Up to 60%', 'ultimate-post-kit'),
		// 	'manage_options',
		// 	self::PAGE_ID . '#ultimate_post_kit_affiliate',
		// 	[$this, 'plugin_page']
		// );
		
		if (true == _is_upk_pro_activated()) {
			add_submenu_page(
				self::PAGE_ID,
				BDTUPK_TITLE,
				esc_html__('Rollback Version', 'ultimate-post-kit'),
				'manage_options',
				self::PAGE_ID . '#ultimate_post_kit_rollback_version',
				[$this, 'plugin_page']
			);

            add_submenu_page(
                self::PAGE_ID,
                BDTUPK_TITLE,
                esc_html__('Template Builder', 'ultimate-post-kit'),
                'edit_pages',
                'edit.php?post_type=upk-template-builder',
            );
        }

	}

    /**
     * Get SVG Icons of Ultimate Post Kit
     *
     * @access public
     * @return string
     */

    public function ultimate_post_kit_icon() {
        return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNC4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCA5MDkuMyA4ODMuOCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgOTA5LjMgODgzLjg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNBN0FBQUQ7fQ0KPC9zdHlsZT4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik04MTEuMiwyNzIuOUg2ODEuNnYxMjkuN2MwLDEzLjYtMTEsMjQuNy0yNC43LDI0LjdoLTEwNWMtMTMuNiwwLTI0LjctMTEtMjQuNy0yNC43YzAsMCwwLDAsMCwwdi0xMDUNCgljMC0xMy42LDExLTI0LjcsMjQuNi0yNC43YzAsMCwwLDAsMCwwaDEyOS43VjE0My4zYzAtMTMuNi0xMS0yNC43LTI0LjctMjQuN0gzOTcuNmMtMTMuNiwwLTI0LjcsMTEtMjQuNywyNC43YzAsMCwwLDAsMCwwdjQ3MS41DQoJYzAsMTMuNi0xMSwyNC42LTI0LjYsMjQuN2MwLDAsMCwwLDAsMGgtMTA1Yy0xMy42LDAtMjQuNy0xMS0yNC43LTI0Ljd2LTM3NWMwLTEzLjYtMTEtMjQuNy0yNC43LTI0LjdIODljLTEzLjYsMC0yNC43LDExLTI0LjcsMjQuNw0KCWMwLDAsMCwwLDAsMHY1MjkuNGMwLDEzLjYsMTEsMjQuNywyNC43LDI0LjdoNDEzLjZjMTMuNiwwLDI0LjctMTEuMSwyNC43LTI0LjdWNjA2LjJjMC0xMy42LDExLTI0LjcsMjQuNy0yNC43aDI1OS4zDQoJYzEzLjYsMCwyNC43LTExLDI0LjctMjQuN1YyOTcuNkM4MzUuOSwyODQsODI0LjksMjczLDgxMS4yLDI3Mi45QzgxMS4yLDI3Mi45LDgxMS4yLDI3Mi45LDgxMS4yLDI3Mi45eiIvPg0KPHJlY3QgeD0iNzMyIiB5PSI4Mi42IiBjbGFzcz0ic3QwIiB3aWR0aD0iMzQuOCIgaGVpZ2h0PSIzNC44Ii8+DQo8cmVjdCB4PSI3OTEiIHk9IjE0OS43IiBjbGFzcz0ic3QwIiB3aWR0aD0iMjMuOSIgaGVpZ2h0PSIyMy45Ii8+DQo8cmVjdCB4PSI4MDMiIHk9IjgyLjYiIGNsYXNzPSJzdDAiIHdpZHRoPSIxNy44IiBoZWlnaHQ9IjE3LjgiLz4NCjxyZWN0IHg9Ijg2Ni43IiB5PSIxNTUuOCIgY2xhc3M9InN0MCIgd2lkdGg9IjE3LjgiIGhlaWdodD0iMTcuOCIvPg0KPHJlY3QgeD0iODI4LjkiIHk9IjQ0LjMiIGNsYXNzPSJzdDAiIHdpZHRoPSI4LjkiIGhlaWdodD0iOC45Ii8+DQo8cmVjdCB4PSI4NzcuNCIgeT0iMzgiIGNsYXNzPSJzdDAiIHdpZHRoPSI3LjIiIGhlaWdodD0iNy4yIi8+DQo8cmVjdCB4PSI4NTIuNiIgeT0iODciIGNsYXNzPSJzdDAiIHdpZHRoPSI4LjkiIGhlaWdodD0iOC45Ii8+DQo8cmVjdCB4PSI3MzUuNCIgeT0iMTgyLjgiIGNsYXNzPSJzdDAiIHdpZHRoPSIxOS43IiBoZWlnaHQ9IjE5LjciLz4NCjxyZWN0IHg9IjgyNi4zIiB5PSIyMDQuNiIgY2xhc3M9InN0MCIgd2lkdGg9IjE0LjEiIGhlaWdodD0iMTQuMSIvPg0KPC9zdmc+DQo=';
    }

    /**
	 * Get SVG Icons of Element Pack
	 *
	 * @access public
	 * @return array
	 */

	public function get_settings_sections() {
		$sections = [
			[
				'id' => 'ultimate_post_kit_active_modules',
				'title' => esc_html__('Core Widgets', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'ultimate_post_kit_elementor_extend',
				'title' => esc_html__('Extensions', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'ultimate_post_kit_other_settings',
				'title' => esc_html__('Special Features', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'ultimate_post_kit_api_settings',
				'title' => esc_html__('API Settings', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-admin-settings',
			],
		];

		return $sections;
	}

    /**
     * Merge Admin Settings
     *
     * @access protected
     * @return array
     */

    protected function ultimate_post_kit_admin_settings() {

        return ModuleService::get_widget_settings(function ($settings) {
            $settings_fields    = $settings['settings_fields'];

            self::$modules_list = $settings_fields['ultimate_post_kit_active_modules'];
            self::$modules_list_only_widgets  = $settings_fields['ultimate_post_kit_active_modules'];

            return $settings_fields;
        });
    }

    /**
	 * Get Welcome Panel
	 *
	 * @access public
	 * @return void
	 */

	public function ultimate_post_kit_welcome() {

		?>

		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="upk-dashboard-welcome-container">

				<div class="upk-dashboard-item upk-dashboard-welcome bdt-card bdt-card-body">
					<h1 class="upk-feature-title upk-dashboard-welcome-title">
						<?php esc_html_e('Welcome to Ultimate Post Kit!', 'ultimate-post-kit'); ?>
					</h1>
					<p class="upk-dashboard-welcome-desc">
						<?php esc_html_e('Empower your web creation with powerful widgets, advanced extensions, ready templates and more.', 'ultimate-post-kit'); ?>
					</p>
					<a href="<?php echo esc_url( admin_url( '?upk_setup_wizard=show' ) ); ?>"
						class="bdt-button bdt-welcome-button bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Setup Ultimate Post Kit', 'ultimate-post-kit'); ?></a>

					<div class="upk-dashboard-compare-section">
						<h4 class="upk-feature-sub-title">
							<?php
							/* translators: 1: opening strong tag, 2: closing strong tag */
							printf(esc_html__('Unlock %1$sPremium Features%2$s', 'ultimate-post-kit'), '<strong class="upk-highlight-text">', '</strong>'); ?>
						</h4>
						<h1 class="upk-feature-title upk-dashboard-compare-title">
							<?php esc_html_e('Create Your Sleek Website with Ultimate Post Kit Pro!', 'ultimate-post-kit'); ?>
						</h1>
						<p><?php esc_html_e('Don\'t need more plugins. This pro addon helps you build complex or professional websites—visually stunning, functional and customizable.', 'ultimate-post-kit'); ?>
						</p>
						<ul>
							<li><?php esc_html_e('Dynamic Content and Integrations', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Live Copy Paste', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Template Builder', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Custom Meta Fields - Category Image, Audio Link, Video Link', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Powerful Widgets and Advanced Extensions', 'ultimate-post-kit'); ?>
							</li>
						</ul>
						<div class="upk-dashboard-compare-section-buttons">
							<a href="https://postkit.pro/pricing/"
								class="bdt-button bdt-welcome-button bdt-margin-small-right"
								target="_blank"><?php esc_html_e('Compare Free Vs Pro', 'ultimate-post-kit'); ?></a>
							<a href="https://store.bdthemes.com/ultimate-post-kit?utm_source=UltimatePostKit&utm_medium=PluginPage&utm_campaign=UltimatePostKit&coupon=FREETOPRO"
								class="bdt-button bdt-dashboard-sec-btn"
								target="_blank"><?php esc_html_e('Get Premium at 30% OFF', 'ultimate-post-kit'); ?></a>
						</div>
					</div>
				</div>

				<div class="upk-dashboard-item upk-dashboard-template-quick-access bdt-card bdt-card-body">
					<div class="upk-dashboard-template-section">
						<img src="<?php echo esc_url( BDTUPK_ADMIN_URL . 'assets/images/template.jpg' ); ?>"
							alt="<?php echo esc_attr__( 'Ultimate Post Kit Dashboard Template', 'ultimate-post-kit' ); ?>">
						<h1 class="upk-feature-title ">
							<?php esc_html_e('Faster Web Creation with Sleek and Ready-to-Use Templates!', 'ultimate-post-kit'); ?>
						</h1>
						<p><?php esc_html_e('Build your wordpress websites of any niche—not from scratch and in a single click.', 'ultimate-post-kit'); ?>
						</p>
						<a href="https://postkit.pro/"
							class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
							target="_blank"><?php esc_html_e('View Templates', 'ultimate-post-kit'); ?></a>
					</div>

					<div class="upk-dashboard-quick-access bdt-margin-medium-top">
						<img src="<?php echo esc_url( BDTUPK_ADMIN_URL . 'assets/images/support.jpg' ); ?>"
							alt="<?php echo esc_attr__( 'Ultimate Post Kit Dashboard Template', 'ultimate-post-kit' ); ?>">
						<h1 class="upk-feature-title">
							<?php esc_html_e('Getting Started with Quick Access', 'ultimate-post-kit'); ?>
						</h1>
						<ul>
							<li><a href="https://postkit.pro/contact/"
									target="_blank"><?php esc_html_e('Contact Us', 'ultimate-post-kit'); ?></a></li>
							<li><a href="https://bdthemes.com/support/"
									target="_blank"><?php esc_html_e('Help Centre', 'ultimate-post-kit'); ?></a></li>
							<li><a href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/idea/new"
									target="_blank"><?php esc_html_e('Request a Feature', 'ultimate-post-kit'); ?></a>
							</li>
						</ul>
						<div class="upk-dashboard-support-section">
							<h1 class="upk-feature-title">
								<i class="dashicons dashicons-phone"></i>
								<?php esc_html_e('24/7 Support', 'ultimate-post-kit'); ?>
							</h1>
							<p><?php esc_html_e('Helping you get real-time solutions related to web creation with WordPress, Elementor, and Ultimate Post Kit.', 'ultimate-post-kit'); ?>
							</p>
							<a href="https://bdthemes.com/support/" class="bdt-margin-small-top"
								target="_blank"><?php esc_html_e('Get Your Support', 'ultimate-post-kit'); ?></a>
						</div>
					</div>
				</div>

				<div class="upk-dashboard-item upk-dashboard-request-feature bdt-card bdt-card-body">
					<h1 class="upk-feature-title upk-dashboard-template-quick-title">
						<?php esc_html_e('What\'s Stacking You?', 'ultimate-post-kit'); ?>
					</h1>
					<p><?php esc_html_e('We are always here to help you. If you have any feature request, please let us know.', 'ultimate-post-kit'); ?>
					</p>
					<a href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/idea/new"
						class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Request Your Features', 'ultimate-post-kit'); ?></a>
				</div>

				<a href="https://www.youtube.com/watch?v=zNeoRz94cPw&list=PLP0S85GEw7DNBnZCb4RtJzlf38GCJ7z1b" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-video-tutorial bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-video-alt3"></i>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Watch Video Tutorials', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('An invaluable resource for mastering WordPress, Elementor, and Web Creation', 'ultimate-post-kit'); ?>
					</p>
				</a>
				<a href="https://bdthemes.com/knowledge-base/ultimate-post-kit/" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-documentation bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-tools"></i>
					</span>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Read Easy Documentation', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('A way to eliminate the challenges you might face', 'ultimate-post-kit'); ?></p>
				</a>
				<a href="https://www.facebook.com/bdthemes" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-community bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-users"></i>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Join Our Community', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('A platform for the opportunity to network, collaboration and innovation', 'ultimate-post-kit'); ?>
					</p>
				</a>
				<a href="https://wordpress.org/plugins/ultimate-post-kit/#reviews" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-review bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-star-filled"></i>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Show Your Love', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('A way of the assessment of code', 'ultimate-post-kit'); ?></p>
				</a>
			</div>

		</div>

		<?php
	}

    /**
     * Get Pro
     *
     * @access public
     * @return void
     */

    function ultimate_post_kit_get_pro() {
    ?>
        <div class="upk-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card" style="max-width: 800px; margin-left: auto; margin-right: auto;">
                <div class="bdt-width-1-1@m upk-comparision bdt-text-center">

                    <div class="bdt-flex bdt-flex-between bdt-flex-middle">
                        <div class="bdt-text-left">
                            <h1 class="bdt-text-bold">
                                <?php echo esc_html_x('WHY GO WITH PRO?', 'Frontend', 'ultimate-post-kit'); ?>
                            </h1>
                            <h2>
                                <?php echo esc_html_x('Just Compare With Ultimate Post Kit Free Vs Pro', 'Frontend', 'ultimate-post-kit'); ?>
                            </h2>

                        </div>
                        <?php if (true !== _is_upk_pro_activated()) : ?>
                            <div class="upk-purchase-button">
                                <a href="https://postkit.pro/pricing/" target="_blank">
                                    <?php echo esc_html_x('Purchase Now', 'Frontend', 'ultimate-post-kit'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div>

                        <ul class="bdt-list bdt-list-divider bdt-text-left bdt-text-normal" style="font-size: 15px;">


                            <li class="bdt-text-bold">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Features', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m">
                                        <?php echo esc_html_x('Free', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m">
                                        <?php echo esc_html_x('Pro', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m"><span bdt-tooltip="pos: top-left; title: Lite have 35+ Widgets but Pro have 100+ core widgets">
                                            <?php echo esc_html_x('Core Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                                        </span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Theme Compatibility', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Dynamic Content & Custom Fields Capabilities', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Proper Documentation', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Updates & Support', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Ready Made Pages', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Ready Made Blocks', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Elementor Extended Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Live Copy or Paste', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Duplicator', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Video Link Meta', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Category Image', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Rooten Theme Pro Features', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Priority Support', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>

                        </ul>


                        <!-- <div class="upk-dashboard-divider"></div> -->


                        <div class="upk-more-features bdt-card bdt-card-body bdt-margin-medium-top bdt-padding-large">
                            <ul class="bdt-list bdt-list-divider bdt-text-left" style="font-size: 15px;">
                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Incredibly Advanced', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Refund or Cancel Anytime', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Dynamic Content', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Super-Flexible Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('24/7 Premium Support', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Third Party Plugins', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Special Discount!', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Custom Field Integration', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('With Live Chat Support', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Trusted Payment Methods', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Interactive Effects', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Video Tutorial', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <!-- <div class="upk-dashboard-divider"></div> -->

                            <?php if (true !== _is_upk_pro_activated()) : ?>
                                <div class="upk-purchase-button bdt-margin-medium-top">
                                    <a href="https://postkit.pro/pricing/" target="_blank">
                                        <?php echo esc_html_x('Purchase Now', 'Frontend', 'ultimate-post-kit'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    <?php
    }

    /**
	 * Display Plugin Page
	 *
	 * @access public
	 * @return void
	 */

	public function plugin_page() {

		?>

		<div class="wrap ultimate-post-kit-dashboard">
			<h1></h1> <!-- don't remove this div, it's used for the notice container -->
		
			<div class="upk-dashboard-wrapper bdt-margin-top">
				<div class="upk-dashboard-header bdt-flex bdt-flex-wrap bdt-flex-between bdt-flex-middle"
					bdt-sticky="offset: 32; animation: bdt-animation-slide-top-small; duration: 300">

					<div class="bdt-flex bdt-flex-wrap bdt-flex-middle">
						<!-- Header Shape Elements -->
						<div class="upk-header-elements">
							<span class="upk-header-element upk-header-circle"></span>
							<span class="upk-header-element upk-header-dots"></span>
							<span class="upk-header-element upk-header-line"></span>
							<span class="upk-header-element upk-header-square"></span>
							<span class="upk-header-element upk-header-wave"></span>
						</div>

						<div class="upk-logo">
							<?php
							echo '<img src="' . esc_url( BDTUPK_URL . 'assets/images/logo-with-text.svg' ) . '" alt="' . esc_attr__( 'Ultimate Post Kit Logo', 'ultimate-post-kit' ) . '">';
							?>
						</div>
					</div>

					<div class="upk-dashboard-new-page-wrapper bdt-flex bdt-flex-wrap bdt-flex-middle">
						

						<!-- Always render save button, JavaScript will control visibility -->
						<div class="upk-dashboard-save-btn" style="display: none;">
							<button class="bdt-button bdt-button-primary ultimate-post-kit-settings-save-btn" type="submit">
								<?php esc_html_e('Save Settings', 'ultimate-post-kit'); ?>
							</button>
						</div>


						<div class="upk-dashboard-new-page">
							<a class="bdt-flex bdt-flex-middle" href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>" class=""><i class="dashicons dashicons-admin-page"></i>
								<?php echo esc_html__('Create New Page', 'ultimate-post-kit') ?>
							</a>
						</div>
					</div>
				</div>

				<div class="upk-dashboard-container bdt-flex">
					<div class="upk-dashboard-nav-container-wrapper">
						<div class="upk-dashboard-nav-container-inner" bdt-sticky="end: !.upk-dashboard-container; offset: 115; animation: bdt-animation-slide-top-small; duration: 300">

							<!-- Navigation Shape Elements -->
							<div class="upk-nav-elements">
								<span class="upk-nav-element upk-nav-circle"></span>
								<span class="upk-nav-element upk-nav-dots"></span>
								<span class="upk-nav-element upk-nav-line"></span>
								<span class="upk-nav-element upk-nav-square"></span>
								<span class="upk-nav-element upk-nav-triangle"></span>
								<span class="upk-nav-element upk-nav-plus"></span>
								<span class="upk-nav-element upk-nav-wave"></span>
							</div>

						<?php $this->settings_api->show_navigation(); ?>
						</div>
					</div>


					<div class="bdt-switcher bdt-tab-container bdt-container-xlarge bdt-flex-1">
						<div id="ultimate_post_kit_welcome_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_welcome(); ?>
						</div>

						<?php $this->settings_api->show_forms(); ?>

						<div id="ultimate_post_kit_analytics_system_req_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_analytics_system_req_content(); ?>
						</div>

						<div id="ultimate_post_kit_other_plugins_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_others_plugin(); ?>
						</div>

						<!-- <div id="ultimate_post_kit_affiliate_page" class="upk-option-page group">
							<?php //$this->ultimate_post_kit_affiliate_content(); ?>
						</div> -->

						<?php if (true == _is_upk_pro_activated()) : ?>
							<div id="ultimate_post_kit_rollback_version_page" class="upk-option-page group">
								<?php $this->upk_rollback_version_content(); ?>
							</div>
						<?php endif; ?>

                        <?php if (_is_upk_pro_activated() !== true) : ?>
                            <div id="ultimate_post_kit_get_pro" class="upk-option-page group">
                                <?php $this->ultimate_post_kit_get_pro(); ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        // Render tab bodies for any add-on-registered dashboard tabs.
                        // The core plugin provides only this extension point; the tab
                        // ids/order match the nav items built in show_navigation().
                        foreach ($this->settings_api->get_extra_dashboard_tabs() as $upk_extra_tab) {
                            if (empty($upk_extra_tab['id'])) {
                                continue;
                            }
                            echo '<div id="' . esc_attr($upk_extra_tab['id']) . '_page" class="upk-option-page group">';
                            if (isset($upk_extra_tab['callback']) && is_callable($upk_extra_tab['callback'])) {
                                call_user_func($upk_extra_tab['callback']);
                            }
                            echo '</div>';
                        }
                        ?>

                        <div id="ultimate_post_kit_license_settings_page" class="upk-option-page group">

                            <?php
                            if (_is_upk_pro_activated() == true) {
                                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- established hook name relied on across the plugin family; renaming would break integration.
                                apply_filters('upk_license_page', '');
                            }

                            ?>
                        </div>

					</div>
				</div>

				<?php if (!defined('BDTUPK_WL') || false == self::license_wl_status()) {
					$this->footer_info();
				} ?>
			</div>

		</div>

		<?php

		$this->script();

	}




    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    function script() {
    	?>
        <script>
            jQuery(document).ready(function() {
                jQuery('.upk-no-result').removeClass('bdt-animation-shake');
            });

            function filterSearch(e) {
                var parentID = '#' + jQuery(e).data('id');
                var search = jQuery(parentID).find('.bdt-search-input').val().toLowerCase();

                jQuery(".upk-options .upk-option-item").filter(function() {
                    jQuery(this).toggle(jQuery(this).attr('data-widget-name').toLowerCase().indexOf(search) > -1)
                });

                if (!search) {
                    jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "");
                    jQuery(parentID).find('.upk-widget-all').trigger('click');
                } else {
                    jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "filter: [data-widget-name*='" + search + "']");
                    jQuery(parentID).find('.bdt-search-input').removeClass('bdt-active'); // Thanks to Bar-Rabbas
                    jQuery(parentID).find('.bdt-search-input').trigger('click');
                }
            }

            jQuery('.upk-options-parent').each(function(e, item) {
                var eachItem = '#' + jQuery(item).attr('id');
                jQuery(eachItem).on("beforeFilter", function() {
                    jQuery(eachItem).find('.upk-no-result').removeClass('bdt-animation-shake');
                });

                jQuery(eachItem).on("afterFilter", function() {

                    var isElementVisible = false;
                    var i = 0;

                    if (jQuery(eachItem).closest(".upk-options-parent").eq(i).is(":visible")) {} else {
                        isElementVisible = true;
                    }

                    while (!isElementVisible && i < jQuery(eachItem).find(".upk-option-item").length) {
                        if (jQuery(eachItem).find(".upk-option-item").eq(i).is(":visible")) {
                            isElementVisible = true;
                        }
                        i++;
                    }

                    if (isElementVisible === false) {
                        jQuery(eachItem).find('.upk-no-result').addClass('bdt-animation-shake');
                    }
                });


            });


            jQuery('.upk-widget-filter-nav li a').on('click', function(e) {
                jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('');
                jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('').attr('bdt-filter-control', '');
            });


            jQuery(document).ready(function($) {
                'use strict';

                function hashHandler() {
                    var $tab = jQuery('.ultimate-post-kit-dashboard .bdt-tab');
                    if (window.location.hash) {
                        var hash = window.location.hash.substring(1);
                        bdtUIkit.tab($tab).show(jQuery('#bdt-' + hash).data('tab-index'));
                    }
                }

                function onWindowLoad() {
                    hashHandler();
                }

                if (document.readyState === 'complete') {
					onWindowLoad();
				} else {
					jQuery(window).on('load', onWindowLoad);
				}

                window.addEventListener("hashchange", hashHandler, true);

                jQuery('.toplevel_page_ultimate_post_kit_options > ul > li > a ').on('click', function(event) {
                    jQuery(this).parent().siblings().removeClass('current');
                    jQuery(this).parent().addClass('current');
                });

                jQuery('#ultimate_post_kit_active_modules_page a.upk-active-all-widget').on('click', function(e) {
                    e.preventDefault();

                    jQuery('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_active_modules_page a.upk-deactive-all-widget').on('click', function(e) {
                    e.preventDefault();
                    jQuery('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-active-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_elementor_extend_page a.upk-active-all-widget').on('click', function(e) {
                    e.preventDefault();

                    jQuery('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_elementor_extend_page a.upk-deactive-all-widget').on('click', function(e) {
                    e.preventDefault();
                    jQuery('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-active-all-widget').removeClass('bdt-active');
                });

                // Activate/Deactivate all widgets functionality
				$('#ultimate_post_kit_active_modules_page a.upk-active-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function () {
						$(this).attr('checked', 'checked').prop("checked", true);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_active_modules_page a.upk-deactive-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

				$('#ultimate_post_kit_active_modules_page a.upk-deactive-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_active_modules_page .checkbox:visible').each(function () {
						$(this).removeAttr('checked').prop("checked", false);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_active_modules_page a.upk-active-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

				$('#ultimate_post_kit_elementor_extend_page a.upk-active-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_elementor_extend_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function () {
						$(this).attr('checked', 'checked').prop("checked", true);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_elementor_extend_page a.upk-deactive-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

				$('#ultimate_post_kit_elementor_extend_page a.upk-deactive-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function () {
						$(this).removeAttr('checked').prop("checked", false);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_elementor_extend_page a.upk-active-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

                jQuery('#ultimate_post_kit_active_modules_page .upk-pro-inactive .checkbox').each(function() {
                    jQuery(this).removeAttr('checked');
                    jQuery(this).attr("disabled", true);
                });

            });

            jQuery(document).ready(function ($) {
                const getProLink = $('a[href="admin.php?page=ultimate_post_kit_options_get_pro"]');
                if (getProLink.length) {
                    getProLink.attr('target', '_blank');
                }
            });

            // License Renew Redirect
            jQuery(document).ready(function ($) {
                const renewalLink = $('a[href="admin.php?page=ultimate_post_kit_options_license_renew"]');
                if (renewalLink.length) {
                    renewalLink.attr('target', '_blank');
                }
            });

			// Dynamic Save Button Control
			jQuery(document).ready(function ($) {
				// Define pages that need save button - only specific settings pages
				const pagesWithSave = [
					'ultimate_post_kit_active_modules',        // Core widgets
					'ultimate_post_kit_elementor_extend',      // Extensions
					'ultimate_post_kit_other_settings',        // Special features
					'ultimate_post_kit_api_settings'           // API settings
				];

				function toggleSaveButton() {
					const currentHash = window.location.hash.substring(1);
					const saveButton = $('.upk-dashboard-save-btn');
					
					// Check if current page should have save button
					if (pagesWithSave.includes(currentHash)) {
						saveButton.fadeIn(200);
					} else {
						saveButton.fadeOut(200);
					}
				}

				// Force save button to be visible for settings pages
				function forceSaveButtonVisible() {
					const currentHash = window.location.hash.substring(1);
					const saveButton = $('.upk-dashboard-save-btn');
					
					if (pagesWithSave.includes(currentHash)) {
						saveButton.show();
					}
				}

				// Initial check
				toggleSaveButton();

				// Listen for hash changes
				$(window).on('hashchange', function() {
					toggleSaveButton();
				});

				// Listen for tab clicks
				$('.bdt-dashboard-navigation a').on('click', function() {
					setTimeout(toggleSaveButton, 100);
				});

				// Also listen for navigation menu clicks (from show_navigation())
				$(document).on('click', '.bdt-tab a, .bdt-subnav a, .upk-dashboard-nav a, [href*="#ultimate_post_kit"]', function() {
					setTimeout(toggleSaveButton, 100);
				});

				// Listen for bulk active/deactive button clicks to maintain save button visibility
				$(document).on('click', '.upk-active-all-widget, .upk-deactive-all-widget', function() {
					setTimeout(forceSaveButtonVisible, 50);
				});

				// Listen for individual checkbox changes to maintain save button visibility
				$(document).on('change', '#ultimate_post_kit_elementor_extend_page .checkbox, #ultimate_post_kit_active_modules_page .checkbox', function() {
					setTimeout(forceSaveButtonVisible, 50);
				});

				// Update URL when navigation items are clicked
				$(document).on('click', '.bdt-tab a, .bdt-subnav a, .upk-dashboard-nav a', function(e) {
					const href = $(this).attr('href');
					if (href && href.includes('#')) {
						const hash = href.substring(href.indexOf('#'));
						if (hash && hash.length > 1) {
							// Update browser URL with the hash
							const currentUrl = window.location.href.split('#')[0];
							const newUrl = currentUrl + hash;
							window.history.pushState(null, null, newUrl);
							
							// Trigger hash change event for other listeners
							$(window).trigger('hashchange');
						}
					}
				});

				// Handle save button click
				$(document).on('click', '.ultimate-post-kit-settings-save-btn', function(e) {
					e.preventDefault();
					
					// Find the active form in the current tab
					const currentHash = window.location.hash.substring(1);
					let targetForm = null;
					
					// Look for forms in the active tab content
					if (currentHash) {
						// Try to find form in the specific tab page
						targetForm = $('#' + currentHash + '_page form.settings-save');
						
						// If not found, try without _page suffix
						if (!targetForm || targetForm.length === 0) {
							targetForm = $('#' + currentHash + ' form.settings-save');
						}
						
						// Try to find any form in the active tab content
						if (!targetForm || targetForm.length === 0) {
							targetForm = $('#' + currentHash + '_page form');
						}
					}
					
					// Fallback to any visible form with settings-save class
					if (!targetForm || targetForm.length === 0) {
						targetForm = $('form.settings-save:visible').first();
					}
					
					// Last fallback - any visible form
					if (!targetForm || targetForm.length === 0) {
						targetForm = $('.bdt-switcher .group:visible form').first();
					}
					
					if (targetForm && targetForm.length > 0) {
						// Show loading notification
						// bdtUIkit.notification({
						// 	message: '<div bdt-spinner></div> <?php //esc_html_e('Please wait, Saving settings...', 'ultimate-post-kit') ?>',
						// 	timeout: false
						// });

						// Submit form using AJAX (same logic as existing form submission)
						targetForm.ajaxSubmit({
							success: function () {
								// Show success message using UIkit notification (same as main settings)
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'ultimate-post-kit') ?>',
									status: 'primary',
									pos: 'top-center'
								});
							},
							error: function (data) {
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'ultimate-post-kit') ?>',
									status: 'warning'
								});
							}
						});
					} else {
						// Show error if no form found
						bdtUIkit.notification({
							message: '<span bdt-icon="icon: warning"></span> <?php esc_html_e('No settings form found to save.', 'ultimate-post-kit') ?>',
							status: 'warning'
						});
					}
				});

			});

			// Chart.js initialization for system status canvas charts
			function initUltimatePostKitCharts() {
				// Wait for Chart.js to be available
				if (typeof Chart === 'undefined') {
					setTimeout(initUltimatePostKitCharts, 500);
					return;
				}

				// Chart instances storage
				window.upkChartInstances = window.upkChartInstances || {};
				window.upkChartsInitialized = false;

				// Function to create a chart
				function createChart(canvasId) {
					var canvas = document.getElementById(canvasId);
					if (!canvas) {
						return;
					}

					var $canvas = jQuery('#' + canvasId);
					var valueStr = $canvas.data('value');
					var labelsStr = $canvas.data('labels');
					var bgStr = $canvas.data('bg');

					if (!valueStr || !labelsStr || !bgStr) {
						return;
					}

					// Parse data
					var values = valueStr.toString().split(',').map(v => parseInt(v.trim()) || 0);
					var labels = labelsStr.toString().split(',').map(l => l.trim());
					var colors = bgStr.toString().split(',').map(c => c.trim());

					// Destroy existing chart using Chart.js built-in method
					var existingChart = Chart.getChart(canvas);
					if (existingChart) {
						existingChart.destroy();
					}

					// Also destroy from our instance storage
					if (window.upkChartInstances && window.upkChartInstances[canvasId]) {
						window.upkChartInstances[canvasId].destroy();
						delete window.upkChartInstances[canvasId];
					}

					// Create new chart
					try {
						var newChart = new Chart(canvas, {
							type: 'doughnut',
							data: {
								labels: labels,
								datasets: [{
									data: values,
									backgroundColor: colors,
									borderWidth: 0
								}]
							},
							options: {
								responsive: true,
								maintainAspectRatio: false,
								plugins: {
									legend: { display: false },
									tooltip: { enabled: true }
								},
								cutout: '60%'
							}
						});
						
						// Store in our instance storage
						if (!window.upkChartInstances) window.upkChartInstances = {};
						window.upkChartInstances[canvasId] = newChart;
					} catch (error) {
						// Do nothing
					}
				}

				// Update total widgets status
				function updateTotalStatus() {
					var coreCount = jQuery('#ultimate_post_kit_active_modules_page input:checked').length;
					var extensionsCount = jQuery('#ultimate_post_kit_elementor_extend_page input:checked').length;

					jQuery('#bdt-total-widgets-status-core').text(coreCount);
					jQuery('#bdt-total-widgets-status-extensions').text(extensionsCount);
					jQuery('#bdt-total-widgets-status-heading').text(coreCount + extensionsCount);
					
					jQuery('#bdt-total-widgets-status').attr('data-value', [coreCount, extensionsCount].join(','));
				}

				// Initialize all charts once
				function initAllCharts() {
					// Check if charts already exist and are properly rendered
					if (window.upkChartInstances && Object.keys(window.upkChartInstances).length >= 4) {
						return;
					}
					
					// Update total status first
					updateTotalStatus();
					
					// Create all charts
					var chartCanvases = [
						'bdt-db-total-status',
						'bdt-db-only-widget-status', 
						'bdt-total-widgets-status'
					];

					var successfulCharts = 0;
					chartCanvases.forEach(function(canvasId) {
						var canvas = document.getElementById(canvasId);
						if (canvas && canvas.offsetParent !== null) { // Check if canvas is visible
							createChart(canvasId);
							if (window.upkChartInstances && window.upkChartInstances[canvasId]) {
								successfulCharts++;
							}
						}
					});
				}

				// Check if we're currently on system status tab and initialize
				function checkAndInitIfOnSystemStatus() {
					if (window.location.hash === '#ultimate_post_kit_analytics_system_req') {
						setTimeout(initAllCharts, 300);
					}
				}

				// Initialize charts when DOM is ready
				jQuery(document).ready(function() {
					// Only initialize if we're on the system status tab
					setTimeout(checkAndInitIfOnSystemStatus, 500);
				});

				// Add click handler for System Status tab to create/refresh charts
				jQuery(document).on('click', 'a[href="#ultimate_post_kit_analytics_system_req"], a[href*="ultimate_post_kit_analytics_system_req"]', function() {
					setTimeout(function() {
						// Always recreate charts when tab is clicked to ensure they're visible
						initAllCharts();
					}, 200);
				});
			}

			// Start the chart initialization
			setTimeout(initUltimatePostKitCharts, 1000);

			// Handle plugin installation via AJAX
			jQuery(document).on('click', '.upk-install-plugin', function(e) {
				e.preventDefault();
				
				var $button = jQuery(this);
				var pluginSlug = $button.data('plugin-slug');
				var nonce = $button.data('nonce');
				var originalText = $button.text();
				
				// Disable button and show loading state
				$button.prop('disabled', true)
					   .text('<?php echo esc_js(__('Installing...', 'ultimate-post-kit')); ?>')
					   .addClass('bdt-installing');
				
				// Perform AJAX request
				jQuery.ajax({
					url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
					type: 'POST',
					data: {
						action: 'upk_install_plugin',
						plugin_slug: pluginSlug,
						nonce: nonce
					},
					success: function(response) {
						if (response.success) {
							// Show success message
							$button.text('<?php echo esc_js(__('Installed!', 'ultimate-post-kit')); ?>')
								   .removeClass('bdt-installing')
								   .addClass('bdt-installed');
							
							// Show success notification
							if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-yes"></span> ' + response.data.message,
									status: 'success'
								});
							}
							
							// Reload the page after 2 seconds to update button states
							setTimeout(function() {
								window.location.reload();
							}, 2000);
							
						} else {
							// Show error message
							$button.prop('disabled', false)
								   .text(originalText)
								   .removeClass('bdt-installing');
							
							// Show error notification
							if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-warning"></span> ' + response.data.message,
									status: 'danger'
								});
							}
						}
					},
					error: function() {
						// Handle network/server errors
						$button.prop('disabled', false)
							   .text(originalText)
							   .removeClass('bdt-installing');
						
						// Show error notification
						if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
							bdtUIkit.notification({
								message: '<span class="dashicons dashicons-warning"></span> <?php echo esc_js(__('Installation failed. Please try again.', 'ultimate-post-kit')); ?>',
								status: 'danger'
							});
						}
					}
				});
			});

			
        </script>
    	<?php
    }

    /**
     * Display Footer
     *
     * @access public
     * @return void
     */

    function footer_info() {
    ?>

        <div class="ultimate-post-kit-footer-info bdt-margin-medium-top">

            <div class="bdt-grid ">

                <div class="bdt-width-auto@s upk-setting-save-btn">



                </div>

                <div class="bdt-width-expand@s bdt-text-right">
                    <p class="">
                        Ultimate Post Kit Pro plugin made with love by <a target="_blank" href="https://bdthemes.com">BdThemes</a> Team.
                        <br>All rights reserved by <a target="_blank" href="https://bdthemes.com">BdThemes.com</a>.
                    </p>
                </div>
            </div>

        </div>

<?php
    }
    
    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages         = get_pages();
        $pages_options = [];
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }


    public static function license_wl_status() {
		$status = get_option('ultimate_post_kit_license_title_status');
		
		if ($status) {
			return true;
		}
		
		return false;
	}



    /**
	 * Display Analytics and System Requirements
	 *
	 * @access public
	 * @return void
	 */

	public function ultimate_post_kit_analytics_system_req_content() {
		?>
		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="upk-dashboard-analytics-system">

				<?php $this->ultimate_post_kit_widgets_status(); ?>

				<div class="bdt-grid bdt-grid-medium bdt-margin-medium-top" bdt-grid
					bdt-height-match="target: > div > .bdt-card">
					<div class="bdt-width-1-1">
						<div class="bdt-card bdt-card-body upk-system-requirement">
							<h1 class="upk-feature-title bdt-margin-small-bottom">
								<?php esc_html_e('System Requirement', 'ultimate-post-kit'); ?>
							</h1>
							<?php $this->ultimate_post_kit_system_requirement(); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Others Plugin - Using standalone plugin manager
	 */
	public function ultimate_post_kit_others_plugin() {
		// Include and render the standalone others plugin manager
		require_once BDTUPK_INC_PATH . 'setup-wizard/ultimate-post-kit-others-plugin.php';
		
		// Call the helper function to render the plugin manager
		ultimate_post_kit_others_plugin();
	}

    /**
	 * Widgets Status
	 */

	public function ultimate_post_kit_widgets_status() {
		$track_nw_msg = '';
		if (!Tracker::is_allow_track()) {
			$track_nw = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'ultimate-post-kit');
			$track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
		}
		?>
		<div class="upk-dashboard-widgets-status">
			<div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="upk-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_widgets = count(self::get_used_widgets());
						$un_used_widgets = count(self::get_unused_widgets());
						?>

						<div class="upk-count-canvas-wrap">
							<h1 class="upk-feature-title"><?php esc_html_e('All Widgets', 'ultimate-post-kit'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="upk-count-wrap">
									<div class="upk-widget-count"><?php esc_html_e('Used:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($used_widgets); ?>
										</b></div>
									<div class="upk-widget-count"><?php esc_html_e('Unused:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($un_used_widgets); ?>
										</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Total:', 'ultimate-post-kit'); ?>
										<b>
											<?php echo esc_html($used_widgets + $un_used_widgets); ?>
										</b>
									</div>
								</div>

								<div class="upk-canvas-wrap">
									<canvas id="bdt-db-total-status" style="height: 100px; width: 100px;"
										data-label="<?php
											/* translators: %s: Total number of widgets */
											echo esc_attr( sprintf( __( 'Total Widgets Status - (%s)', 'ultimate-post-kit' ), $used_widgets + $un_used_widgets ) ); ?>"
										data-labels="<?php echo esc_attr( sprintf( '%1$s, %2$s', __( 'Used', 'ultimate-post-kit' ), __( 'Unused', 'ultimate-post-kit' ) ) ); ?>"
										data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>"
										data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="upk-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_widgets = count(self::get_used_only_widgets());
						$unused_only_widgets = count(self::get_unused_only_widgets());
						?>


						<div class="upk-count-canvas-wrap">
							<h1 class="upk-feature-title"><?php esc_html_e('Core', 'ultimate-post-kit'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="upk-count-wrap">
									<div class="upk-widget-count"><?php esc_html_e('Used:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($used_only_widgets); ?>
										</b></div>
									<div class="upk-widget-count"><?php esc_html_e('Unused:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($unused_only_widgets); ?>
										</b></div>
									<div class="upk-widget-count"><?php esc_html_e('Total:', 'ultimate-post-kit'); ?>
										<b>
											<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>
										</b>
									</div>
								</div>

								<div class="upk-canvas-wrap">
									<canvas id="bdt-db-only-widget-status" style="height: 100px; width: 100px;"
										data-label="<?php
											/* translators: %s: Total number of core widgets */
											echo esc_attr( sprintf( __( 'Core Widgets Status - (%s)', 'ultimate-post-kit' ), $used_only_widgets + $unused_only_widgets ) ); ?>"
										data-labels="<?php echo esc_attr( sprintf( '%1$s, %2$s', __( 'Used', 'ultimate-post-kit' ), __( 'Unused', 'ultimate-post-kit' ) ) ); ?>"
										data-value="<?php echo esc_attr($used_only_widgets) . ',' . esc_attr($unused_only_widgets); ?>"
										data-bg="#EF476F, #ffcdd9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="upk-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<div class="upk-count-canvas-wrap">
							<h1 class="upk-feature-title"><?php esc_html_e('Active', 'ultimate-post-kit'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="upk-count-wrap">
									<div class="upk-widget-count"><?php esc_html_e('Core:', 'ultimate-post-kit'); ?> 
										<b id="bdt-total-widgets-status-core">0</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Extensions:', 'ultimate-post-kit'); ?>
										<b id="bdt-total-widgets-status-extensions">0</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Total:', 'ultimate-post-kit'); ?> <b
											id="bdt-total-widgets-status-heading">0</b></div>
								</div>

								<div class="upk-canvas-wrap">
									<canvas id="bdt-total-widgets-status" style="height: 100px; width: 100px;"
										data-label="<?php echo esc_attr__( 'Total Active Widgets Status', 'ultimate-post-kit' ); ?>"
										data-labels="<?php echo esc_attr( sprintf( '%1$s, %2$s', __( 'Core', 'ultimate-post-kit' ), __( 'Extensions', 'ultimate-post-kit' ) ) ); ?>"
										data-value="0,0,0"
										data-bg="#0680d6, #B0EBFF" data-bg-hover="#0673e1, #B0EBFF">
									</canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<?php if (!Tracker::is_allow_track()): ?>
			<div class="bdt-border-rounded bdt-box-shadow-small bdt-alert-warning" bdt-alert>
				<a href class="bdt-alert-close" bdt-close></a>
				<div class="bdt-text-default">
				<?php
					printf(
						/* translators: 1: opening bold tag, 2: closing bold tag */
						esc_html__('To view widgets analytics, Elementor %1$sUsage Data Sharing%2$s feature by Elementor needs to be activated. Please activate the feature to get widget analytics instantly ', 'ultimate-post-kit'),
						'<b>', '</b>'
					);

					echo ' <a href="' . esc_url(admin_url('admin.php?page=elementor-settings')) . '">' . esc_html__('from here.', 'ultimate-post-kit') . '</a>';
				?>
				</div>
			</div>
		<?php endif; ?>

		<?php
	}

    /**
	 * Display System Requirement
	 *
	 * @access public
	 * @return void
	 */

	public function ultimate_post_kit_system_requirement() {
		$php_version = phpversion();
		$max_execution_time = ini_get('max_execution_time');
		$memory_limit = ini_get('memory_limit');
		$post_limit = ini_get('post_max_size');
		$uploads = wp_upload_dir();
		$upload_path = $uploads['basedir'];
		$yes_icon = '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
		$no_icon = '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';

		$environment = Utils::get_environment_info();

		?>
		<ul class="check-system-status bdt-grid bdt-child-width-1-2@m  bdt-grid-small ">
			<li>
				<div>
					<span class="label1"><?php esc_html_e('PHP Version:', 'ultimate-post-kit'); ?></span>

					<?php
					if (version_compare($php_version, '7.4.0', '<')) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__('Min: 7.4 Recommended', 'ultimate-post-kit') . '" bdt-tooltip>' . esc_html__('Currently:', 'ultimate-post-kit') . ' ' . esc_html($php_version) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently:', 'ultimate-post-kit') . ' ' . esc_html($php_version) . '</span>';
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max execution time:', 'ultimate-post-kit'); ?> </span>
					<?php
					if ($max_execution_time < '90') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__( 'Min: 90 Recommended', 'ultimate-post-kit' ) . '" bdt-tooltip>' . esc_html__( 'Currently:', 'ultimate-post-kit' ) . ' ' . esc_html( $max_execution_time ) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__( 'Currently:', 'ultimate-post-kit' ) . ' ' . esc_html( $max_execution_time ) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1"><?php esc_html_e('Memory Limit:', 'ultimate-post-kit'); ?> </span>

					<?php
					if (intval($memory_limit) < '512') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__( 'Min: 512M Recommended', 'ultimate-post-kit' ) . '" bdt-tooltip>' . esc_html__( 'Currently:', 'ultimate-post-kit' ) . ' ' . esc_html( $memory_limit ) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__( 'Currently:', 'ultimate-post-kit' ) . ' ' . esc_html( $memory_limit ) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max Post Limit:', 'ultimate-post-kit'); ?> </span>

					<?php
					if (intval($post_limit) < '32') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__( 'Min: 32M Recommended', 'ultimate-post-kit' ) . '" bdt-tooltip>' . esc_html__( 'Currently:', 'ultimate-post-kit' ) . ' ' . esc_html( $post_limit ) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__( 'Currently:', 'ultimate-post-kit' ) . ' ' . esc_html( $post_limit ) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Uploads folder writable:', 'ultimate-post-kit'); ?></span>

					<?php
					if (!wp_is_writable($upload_path)) {
						echo wp_kses_post($no_icon);
					} else {
						echo wp_kses_post($yes_icon);
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('MultiSite:', 'ultimate-post-kit'); ?></span>

					<?php
					if ($environment['wp_multisite']) {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('MultiSite Enabled', 'ultimate-post-kit') . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Single Site', 'ultimate-post-kit') . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('GZip Enabled:', 'ultimate-post-kit'); ?></span>

					<?php
					if ($environment['gzip_enabled']) {
						echo wp_kses_post($yes_icon);
					} else {
						echo wp_kses_post($no_icon);
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Debug Mode:', 'ultimate-post-kit'); ?></span>
					<?php
					if ($environment['wp_debug_mode']) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned On', 'ultimate-post-kit') . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned Off', 'ultimate-post-kit') . '</span>';
					}
					?>
				</div>

			</li>

		</ul>

		<div class="bdt-admin-alert">
			<strong><?php esc_html_e('Note:', 'ultimate-post-kit'); ?></strong>
			<?php
			printf(
				/* translators: %s: Plugin name 'Ultimate Post Kit' */
				esc_html__('If you have multiple addons like %s so you may need to allocate additional memory for other addons as well.', 'ultimate-post-kit'),
				'<b>Ultimate Post Kit</b>'
			);
			?>
		</div>

		<?php
	}

    /**
	 * Check plugin status (installed, active, or not installed)
	 * 
	 * @param string $plugin_path Plugin file path
	 * @return string 'active', 'installed', or 'not_installed'
	 */
	private function get_plugin_status($plugin_path) {
		// Check if plugin is active
		if (is_plugin_active($plugin_path)) {
			return 'active';
		}
		
		// Check if plugin is installed but not active
		$installed_plugins = get_plugins();
		if (isset($installed_plugins[$plugin_path])) {
			return 'installed';
		}
		
		// Plugin is not installed
		return 'not_installed';
	}


	/**
	 * Handle AJAX plugin installation
	 * 
	 * @access public
	 * @return void
	 */
	public function install_plugin_ajax() {
		// Check nonce
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!wp_verify_nonce( $nonce, 'upk_install_plugin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed', 'ultimate-post-kit')]);
		}

		// Check user capability
		if (!current_user_can('install_plugins')) {
			wp_send_json_error(['message' => __('You do not have permission to install plugins', 'ultimate-post-kit')]);
		}

		$plugin_slug = isset($_POST['plugin_slug']) ? sanitize_text_field(wp_unslash($_POST['plugin_slug'])) : '';

		if (empty($plugin_slug)) {
			wp_send_json_error(['message' => __('Plugin slug is required', 'ultimate-post-kit')]);
		}

		// Include necessary WordPress files
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

		// Get plugin information
		$api = plugins_api('plugin_information', [
			'slug' => $plugin_slug,
			'fields' => [
				'sections' => false,
			],
		]);

		if (is_wp_error($api)) {
			wp_send_json_error(['message' => __('Plugin not found: ', 'ultimate-post-kit') . $api->get_error_message()]);
		}

		// Install the plugin
		$skin = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader($skin);
		$result = $upgrader->install($api->download_link);

		if (is_wp_error($result)) {
			wp_send_json_error(['message' => __('Installation failed: ', 'ultimate-post-kit') . $result->get_error_message()]);
		} elseif ($skin->get_errors()->has_errors()) {
			wp_send_json_error(['message' => __('Installation failed: ', 'ultimate-post-kit') . $skin->get_error_messages()]);
		} elseif (is_null($result)) {
			wp_send_json_error(['message' => __('Installation failed: Unable to connect to filesystem', 'ultimate-post-kit')]);
		}

		// Get installation status
		$install_status = install_plugin_install_status($api);
		
		wp_send_json_success([
			'message' => __('Plugin installed successfully!', 'ultimate-post-kit'),
			'plugin_file' => $install_status['file'],
			'plugin_name' => $api->name
		]);
	}

    /**
	 * Extract plugin slug from plugin path
	 * 
	 * @param string $plugin_path Plugin file path
	 * @return string Plugin slug
	 */
	private function extract_plugin_slug_from_path($plugin_path) {
		$parts = explode('/', $plugin_path);
		return isset($parts[0]) ? $parts[0] : '';
	}

    /**
	 * Get plugin action button HTML based on plugin status
	 * 
	 * @param string $plugin_path Plugin file path
	 * @param string $install_url Plugin installation URL
	 * @param string $plugin_slug Plugin slug for activation
	 * @return string Button HTML
	 */
	private function get_plugin_action_button($plugin_path, $install_url, $plugin_slug = '') {
		$status = $this->get_plugin_status($plugin_path);
		
		switch ($status) {
			case 'active':
				return '';
				
			case 'installed':
				$activate_url = wp_nonce_url(
					add_query_arg([
						'action' => 'activate',
						'plugin' => $plugin_path
					], admin_url('plugins.php')),
					'activate-plugin_' . $plugin_path
				);
				return '<a class="bdt-button bdt-welcome-button" href="' . esc_url($activate_url) . '">' . 
				       __('Activate', 'ultimate-post-kit') . '</a>';
				
			case 'not_installed':
			default:
				$plugin_slug = $this->extract_plugin_slug_from_path($plugin_path);
				$nonce = wp_create_nonce('upk_install_plugin_nonce');
				return '<a class="bdt-button bdt-welcome-button upk-install-plugin" 
				          data-plugin-slug="' . esc_attr($plugin_slug) . '" 
				          data-nonce="' . esc_attr($nonce) . '" 
				          href="#">' . 
				       __('Install', 'ultimate-post-kit') . '</a>';
		}
	}



    /**
	 * Rollback Version Content
	 *
	 * @access public
	 * @return void
	 */
	public function upk_rollback_version_content() {
		// Use the already initialized rollback version instance
		$this->rollback_version->upk_rollback_version_content();
	}


}

new UltimatePostKit_Admin_Settings();
