<?php

use UltimatePostKit\Notices;
use UltimatePostKit\Utils;
use UltimatePostKit\Admin\ModuleService;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;

/**
 * Ultimate Post Kit Admin Settings Class
 */

 // Include rollback version functionality
require_once BDTUPK_ADMIN_PATH . 'class-rollback-version.php';

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
	 * @var UltimatePostKit_Rollback_Version
	 */
	public $rollback_version;

    function __construct() {
        $this->settings_api = new UltimatePostKit_Settings_API;

        if (!defined('BDTUPK_HIDE')) {
            add_action('admin_init', [$this, 'admin_init']);
            add_action('admin_menu', [$this, 'admin_menu'], 201);
        }


        // Initialize rollback version functionality
		$this->rollback_version = new UltimatePostKit\Admin\UltimatePostKit_Rollback_Version();

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
            
            $old_error_level = error_reporting();
 			error_reporting(E_ALL & ~E_WARNING); // Suppress warnings
 			$elements = $module->get_formatted_usage('raw');
 			error_reporting($old_error_level); // Restore
            
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
            
            $old_error_level = error_reporting();
 			error_reporting(E_ALL & ~E_WARNING); // Suppress warnings
 			$elements = $module->get_formatted_usage('raw');
 			error_reporting($old_error_level); // Restore
            
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
        if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_get_pro') {
            wp_redirect('https://postkit.pro/pricing/?utm_source=UPK&utm_medium=PluginPage&utm_campaign=30%OffOnUPK&coupon=FREETOPRO');
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
        if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_license_renew') {
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
			esc_html__('3rd Party Widgets', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_third_party_widget',
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
			esc_html__('Extra Options', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_extra_options',
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
		
		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Get Up to 60%', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_affiliate',
			[$this, 'plugin_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Rollback Version', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_rollback_version',
			[$this, 'plugin_page']
		);

        if (true == _is_upk_pro_activated()) {
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

    public function old_ultimate_post_kit_welcome() {
        $track_nw_msg = '';
        if (!Tracker::is_allow_track()) {
            $track_nw = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'ultimate-post-kit');
            $track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
        }
    ?>

        <div class="upk-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

            <div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="upk-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?>>

                        <?php
                        $used_widgets    = count(self::get_used_widgets());
                        $un_used_widgets = count(self::get_unused_widgets());

                        $core = $used_widgets + $un_used_widgets;
                        
                        ?>


                        <div class="upk-count-canvas-wrap">
                            <h1 class="upk-feature-title">
                                <?php echo esc_html_x('All Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                            </h1>
                            <div class="bdt-flex bdt-flex-between bdt-flex-middle">
                                <div class="upk-count-wrap">
                                    <div class="upk-widget-count">
                                        <?php echo esc_html_x('Used:', 'Frontend', 'ultimate-post-kit'); ?>
                                        <b><?php echo esc_html($used_widgets); ?></b>
                                    </div>
                                    <div class="upk-widget-count">
                                        <?php echo esc_html_x('Unused:', 'Frontend', 'ultimate-post-kit'); ?>
                                        <b><?php echo esc_html($un_used_widgets); ?></b>
                                    </div>
                                    <div class="upk-widget-count">
                                        <?php echo esc_html_x('Total:', 'Frontend', 'ultimate-post-kit'); ?>
                                        <b><?php echo esc_html($used_widgets) + esc_html($un_used_widgets); ?></b>
                                    </div>
                                </div>

                                <div class="upk-canvas-wrap">
                                    <canvas id="bdt-db-total-status" style="height: 100px; width: 100px;" data-label="Total Widgets Status - (<?php echo esc_attr($used_widgets) + esc_attr($un_used_widgets); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>" data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="upk-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?>>

                        <div class="upk-count-canvas-wrap">
                            <h1 class="upk-feature-title">
                                <?php echo esc_html_x('Active', 'Frontend', 'ultimate-post-kit'); ?>
                            </h1>
                            <div class="bdt-flex bdt-flex-between bdt-flex-middle">
                                <div class="upk-count-wrap">
                                    <div class="upk-widget-count">
                                        <?php echo esc_html_x('Core:', 'Frontend', 'ultimate-post-kit'); ?>
                                        <b id="bdt-total-widgets-status-core"></b>
                                    </div>
                                    <div class="upk-widget-count">
                                        <?php echo esc_html_x('Extensions:', 'Frontend', 'ultimate-post-kit'); ?>
                                        <b id="bdt-total-widgets-status-extensions"></b>
                                    </div>
                                    <div class="upk-widget-count">
                                        <?php echo esc_html_x('Total:', 'Frontend', 'ultimate-post-kit'); ?>
                                        <b id="bdt-total-widgets-status-heading"></b>
                                    </div>
                                </div>

                                <div class="upk-canvas-wrap">
                                    <canvas id="bdt-total-widgets-status" style="height: 100px; width: 100px;" data-label="Total Active Widgets Status" data-labels="<?php echo esc_attr('Core, Extensions'); ?>" data-bg="#0680d6, #E6F9FF" data-bg-hover="#0673e1, #b6f9e8">
                                    </canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bdt-width-1-1@m bdt-width-1-2@l">
                    <div class="upk-elementor-addons bdt-card bdt-card-body">
                        <a target="_blank" rel="" href="https://www.elementpack.pro/elements-demo/"></a>
                    </div>
                </div>
            </div>

            <?php if (!Tracker::is_allow_track()) : ?>
                <div class="bdt-border-rounded bdt-box-shadow-small bdt-alert-warning" bdt-alert>
                    <a href class="bdt-alert-close" bdt-close></a>
                    <div class="bdt-text-default">
                        <?php
                        esc_html_e('To view widgets analytics, Elementor Usage Data Sharing feature by Elementor needs to be activated. Please activate the feature to get widget analytics instantly ', 'ultimate-post-kit');
                        echo '<a href="' . esc_url(admin_url('admin.php?page=elementor')) . '">from here.</a>';
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-2-5@m upk-support-section">
                    <div class="upk-support-content bdt-card bdt-card-body">
                        <h1 class="upk-feature-title">
                            <?php echo esc_html_x('Support And Feedback', 'Frontend', 'ultimate-post-kit'); ?>
                        </h1>

                        <?php
                        $text = '<p>' . esc_html_x('Feeling like to consult with an expert? Take live Chat support immediately from', 'Frontend', 'ultimate-post-kit') . ' <a href="https://postkit.pro/" target="_blank" rel="">Ultimate Post Kit</a>. ' . esc_html_x('We are always ready to help you 24/7.', 'Frontend', 'ultimate-post-kit') . '</p>';
                        $second_text = '<p><strong>' . esc_html_x('Or if you’re facing technical issues with our plugin, then please create a support ticket', 'Frontend', 'ultimate-post-kit') . '</strong></p>';
                        ?>

                        <?php echo $text; ?>
                        <?php echo $second_text; ?>

                        <a class="bdt-button bdt-btn-blue bdt-margin-small-top bdt-margin-small-right" target="_blank" rel="" href="https://bdthemes.com/all-knowledge-base-of-ultimate-post-kit/">
                            <?php echo esc_html_x('Knowledge Base', 'Frontend', 'ultimate-post-kit'); ?>
                        </a>
                        <a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" href="https://bdthemes.com/support/">
                            <?php echo esc_html_x('Get Support', 'Frontend', 'ultimate-post-kit'); ?>
                        </a>
                    </div>
                </div>

                <div class="bdt-width-3-5@m">
                    <div class="bdt-card bdt-card-body upk-system-requirement">
                        <h1 class="upk-feature-title bdt-margin-small-bottom">
                            <?php echo esc_html_x('System Requirement', 'Frontend', 'ultimate-post-kit'); ?>
                        </h1>
                        <?php $this->ultimate_post_kit_system_requirement(); ?>
                    </div>
                </div>
            </div>

            <div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-1-2@m upk-support-section">
                    <div class="bdt-card bdt-card-body upk-feedback-bg">
                        <h1 class="upk-feature-title">
                            <?php echo esc_html_x('Missing Any Feature?', 'Frontend', 'ultimate-post-kit'); ?>
                        </h1>
                        <p style="max-width: 520px;">
                            <?php echo esc_html_x('Are you in need of a feature that’s not available in our plugin?
                            Feel free to do a feature request from here.', 'Frontend', 'ultimate-post-kit'); ?>
                        </p>
                        <a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" rel="" href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests">
                            <?php echo esc_html_x('Request Feature', 'Frontend', 'ultimate-post-kit'); ?>
                        </a>
                    </div>
                </div>

                <div class="bdt-width-1-2@m">
                    <div class="bdt-card bdt-card-body upk-tryaddon-bg">
                        <h1 class="upk-feature-title">
                            <?php echo esc_html_x('Try Our Plugins', 'Frontend', 'ultimate-post-kit'); ?>
                        </h1>
                        <p style="max-width: 520px;">
                            <?php printf(
                                /* translators: 1: opening strong tag 2: closing strong tag 3: opening strong tag 4: closing strong tag */
                                esc_html__('%1$sUltimate Post Kit, Prime Slider, Ultimate Store Kit, Pixel Gallery & Live Copy Paste %2$s addons for %3$sElementor%4$s is the best slider, blogs and eCommerce plugin for WordPress. Also, try our new plugin ZoloBlocks for Gutenberg.', 'ultimate-post-kit'),
                                '<strong>',
                                '</strong>',
                                '<strong>',
                                '</strong>'
                            ); ?>
                        </p>
                        <div class="bdt-others-plugins-link">
                            <a class="bdt-button bdt-btn-ep bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/ultimate-post-kit-lite/" bdt-tooltip="Ultimate Post Kit Lite provides more than 50+ essential elements for everyday applications to simplify the whole web building process. It's Free! Download it.">
                                <?php echo esc_html_x('Element pack', 'Frontend', 'ultimate-post-kit'); ?>
                            </a>
                            <a class="bdt-button bdt-btn-ps bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/bdthemes-prime-slider-lite/" bdt-tooltip="The revolutionary slider builder addon for Elementor with next-gen superb interface. It's Free! Download it.">
                                <?php echo esc_html_x('Prime Slider', 'Frontend', 'ultimate-post-kit'); ?>
                            </a>
                            <a class="bdt-button bdt-btn-zb bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/zoloblocks/" bdt-tooltip="<?php echo esc_html__('ZoloBlocks is a collection of creative Gutenberg blocks for WordPress. It\'s Free! Download it.', 'ultimate-post-kit'); ?>">ZoloBlocks</a><br>
                            <a class="bdt-button bdt-btn-usk bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/ultimate-store-kit/" bdt-tooltip="The only eCommmerce addon for answering all your online store design problems in one package. It's Free! Download it.">
                                <?php echo esc_html_x('Ultimate Store Kit', 'Frontend', 'ultimate-post-kit'); ?>
                            </a>
                            <a class="bdt-button bdt-btn-live-copy bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/live-copy-paste/" bdt-tooltip="Superfast cross-domain copy-paste mechanism for WordPress websites with true UI copy experience. It's Free! Download it.">
                                <?php echo esc_html_x('Live Copy Paste', 'Frontend', 'ultimate-post-kit'); ?>
                            </a>
                            <a class="bdt-button bdt-btn-pg bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/pixel-gallery/" bdt-tooltip="Pixel Gallery provides more than 30+ essential elements for everyday applications to simplify the whole web building process. It's Free! Download it.">
                                <?php echo esc_html_x('Pixel Gallery', 'Frontend', 'ultimate-post-kit'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    <?php
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
						<?php esc_html_e('Empower your web creation with powerful widgets, advanced extensions, and 2700+ ready templates and more.', 'ultimate-post-kit'); ?>
					</p>
					<a href="<?php echo admin_url('?ep_setup_wizard=show'); ?>"
						class="bdt-button bdt-welcome-button bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Setup Ultimate Post Kit', 'ultimate-post-kit'); ?></a>

					<div class="upk-dashboard-compare-section">
						<h4 class="upk-feature-sub-title">
							<?php printf(esc_html__('Unlock %sPremium Features%s', 'ultimate-post-kit'), '<strong class="upk-highlight-text">', '</strong>'); ?>
						</h4>
						<h1 class="upk-feature-title upk-dashboard-compare-title">
							<?php esc_html_e('Create Your Sleek Website with Ultimate Post Kit Pro!', 'ultimate-post-kit'); ?>
						</h1>
						<p><?php esc_html_e('Don\'t need more plugins. This pro addon helps you build complex or professional websites—visually stunning, functional and customizable.', 'ultimate-post-kit'); ?>
						</p>
						<ul>
							<li><?php esc_html_e('Dynamic Content and Integrations', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Enhanced Template Library', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Theme Builder', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Mega Menu Builder', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Powerful Widgets and Advanced Extensions', 'ultimate-post-kit'); ?>
							</li>
						</ul>
						<div class="upk-dashboard-compare-section-buttons">
							<a href="https://www.elementpack.pro/pricing/#a2a0062"
								class="bdt-button bdt-welcome-button bdt-margin-small-right"
								target="_blank"><?php esc_html_e('Compare Free Vs Pro', 'ultimate-post-kit'); ?></a>
							<a href="https://store.bdthemes.com/element-pack?utm_source=ElementPackLite&utm_medium=PluginPage&utm_campaign=ElementPackLite&coupon=FREETOPRO"
								class="bdt-button bdt-dashboard-sec-btn"
								target="_blank"><?php esc_html_e('Get Premium at 30% OFF', 'ultimate-post-kit'); ?></a>
						</div>
					</div>
				</div>

				<div class="upk-dashboard-item upk-dashboard-template-quick-access bdt-card bdt-card-body">
					<div class="upk-dashboard-template-section">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/template.jpg'; ?>"
							alt="Ultimate Post Kit Dashboard Template">
						<h1 class="upk-feature-title ">
							<?php esc_html_e('Faster Web Creation with Sleek and Ready-to-Use Templates!', 'ultimate-post-kit'); ?>
						</h1>
						<p><?php esc_html_e('Build your wordpress websites of any niche—not from scratch and in a single click.', 'ultimate-post-kit'); ?>
						</p>
						<a href="https://www.elementpack.pro/ready-templates/"
							class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
							target="_blank"><?php esc_html_e('View Templates', 'ultimate-post-kit'); ?></a>
					</div>

					<div class="upk-dashboard-quick-access bdt-margin-medium-top">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/support.svg'; ?>"
							alt="Ultimate Post Kit Dashboard Template">
						<h1 class="upk-feature-title">
							<?php esc_html_e('Getting Started with Quick Access', 'ultimate-post-kit'); ?>
						</h1>
						<ul>
							<li><a href="https://www.elementpack.pro/contact/"
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
					<a href="https://feedback.elementpack.pro/b/3v2gg80n/feature-requests/idea/new"
						class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Request Your Features', 'ultimate-post-kit'); ?></a>
				</div>

				<a href="https://www.youtube.com/watch?v=-e-kr4Vkh4E&list=PLP0S85GEw7DOJf_cbgUIL20qqwqb5x8KA" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-video-tutorial bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-video-alt3"></i>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Watch Video Tutorials', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('An invaluable resource for mastering WordPress, Elementor, and Web Creation', 'ultimate-post-kit'); ?>
					</p>
				</a>
				<a href="https://bdthemes.com/all-knowledge-base-of-element-pack/" target="_blank"
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
				<a href="https://wordpress.org/plugins/ultimate-post-kit-lite/#reviews" target="_blank"
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
                                <a href="https://postkit.pro/#a851ca7" target="_blank">
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
                                    <a href="https://postkit.pro/#a851ca7" target="_blank">
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
     * Display System Requirement
     *
     * @access public
     * @return void
     */

    function old_ultimate_post_kit_system_requirement() {
        $php_version        = phpversion();
        $max_execution_time = ini_get('max_execution_time');
        $memory_limit       = ini_get('memory_limit');
        $post_limit         = ini_get('post_max_size');
        $uploads            = wp_upload_dir();
        $upload_path        = $uploads['basedir'];
        $yes_icon           = wp_kses_post('<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>');
        $no_icon            = wp_kses_post('<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>');

        $environment = Utils::get_environment_info();


    ?>
        <ul class="check-system-status bdt-grid bdt-child-width-1-2@m bdt-grid-small ">
            <li>
                <div>

                    <span class="label1">
                        <?php echo esc_html_x('PHP Version: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>

                    <?php
                    if (version_compare($php_version, '7.0.0', '<')) {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 7.0 Recommended" bdt-tooltip>Currently: ' . $php_version . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $php_version . '</span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">
                        <?php echo esc_html_x('Max execution time: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>

                    <?php
                    if ($max_execution_time < '90') {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 90 Recommended" bdt-tooltip>Currently: ' . $max_execution_time . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $max_execution_time . '</span>';
                    }
                    ?>
                </div>
            </li>
            <li>
                <div>
                    <span class="label1">
                        <?php echo esc_html_x('Memory Limit: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>

                    <?php
                    if (intval($memory_limit) < '812') {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 812M Recommended" bdt-tooltip>Currently: ' . $memory_limit . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $memory_limit . '</span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">
                        <?php echo esc_html_x('Max Post Limit: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>

                    <?php
                    if (intval($post_limit) < '32') {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 32M Recommended" bdt-tooltip>Currently: ' . $post_limit . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $post_limit . '</span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">
                        <?php echo esc_html_x('Uploads folder writable: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>

                    <?php
                    if (!is_writable($upload_path)) {
                        echo $no_icon;
                    } else {
                        echo $yes_icon;
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">
                        <?php echo esc_html_x('MultiSite: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>

                    <?php
                    if ($environment['wp_multisite']) {
                        echo $yes_icon;
                        echo '<span class="label2">MultiSite</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">No MultiSite </span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">
                        <?php echo esc_html_x('GZip Enabled: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>

                    <?php
                    if ($environment['gzip_enabled']) {
                        echo $yes_icon;
                    } else {
                        echo $no_icon;
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">
                        <?php echo esc_html_x('Debug Mode: ', 'Frontend', 'ultimate-post-kit'); ?>
                    </span>
                    <?php
                    if ($environment['wp_debug_mode']) {
                        echo $no_icon;
                        echo '<span class="label2">Currently Turned On</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently Turned Off</span>';
                    }
                    ?>
                </div>
            </li>

        </ul>

        <div class="bdt-admin-alert">
            <?php
            printf(
                /* translators: 1: Note, 2: Ultimate Post Kit */
                esc_html__('%1$s If you have multiple addons like %2$s so you need some more requirement some cases so make sure you added more memory for others addon too.', 'ultimate-post-kit'),
                '<strong>'.esc_html__('Note:', 'ultimate-post-kit').'</strong>',
                '<strong>'.esc_html__('Ultimate Post Kit', 'ultimate-post-kit').'</strong>'
            ); ?>
        </div>
    <?php
    }

    /**
     * Display Plugin Page
     *
     * @access public
     * @return void
     */

    function old_plugin_page() {

        echo '<div class="wrap ultimate-post-kit-dashboard">';
        echo '<h1>' . BDTUPK_TITLE . ' '.esc_html__('Settings', 'ultimate-post-kit').'</h1>';

        $this->settings_api->show_navigation();

    ?>


        <div class="bdt-switcher bdt-tab-container bdt-container-xlarge">
            <div id="ultimate_post_kit_welcome_page" class="upk-option-page group">
                <?php $this->ultimate_post_kit_welcome(); ?>

                <?php if (!defined('BDTUPK_WL')) {
                    $this->footer_info();
                } ?>
            </div>

            <?php
            $this->settings_api->show_forms();
            ?>

            <?php if (_is_upk_pro_activated() !== true) : ?>
                <div id="ultimate_post_kit_get_pro" class="upk-option-page group">
                    <?php $this->ultimate_post_kit_get_pro(); ?>
                </div>
            <?php endif; ?>

            <div id="ultimate_post_kit_license_settings_page" class="upk-option-page group">

                <?php
                if (_is_upk_pro_activated() == true) {
                    apply_filters('upk_license_page', '');
                }

                ?>

                <?php if (!defined('BDTUPK_WL')) {
                    $this->footer_info();
                } ?>
            </div>
        </div>

        </div>

        <?php

        $this->script();

        ?>

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
							<img src="<?php echo BDTUPK_URL . 'assets/images/logo-with-text.svg'; ?>" alt="Ultimate Post Kit Logo">
						</div>
					</div>

					<div class="upk-dashboard-new-page-wrapper bdt-flex bdt-flex-wrap bdt-flex-middle">
						

						<!-- Always render save button, JavaScript will control visibility -->
						<div class="upk-dashboard-save-btn" style="display: none;">
							<button class="bdt-button bdt-button-primary ultimate-post-kit-settings-save-btn" type="submit">
								<?php esc_html_e('Save Settings', 'ultimate-post-kit'); ?>
							</button>
						</div>

						<!-- Custom Code Save Button Section -->
						<div class="upk-code-save-section" style="display: none;">
							<button type="button" id="upk-save-custom-code" class="bdt-button bdt-button-primary ultimate-post-kit-custom-code-save-btn">
								<?php esc_html_e('Save Custom Code', 'ultimate-post-kit'); ?>
							</button>
							<button type="button" id="upk-reset-custom-code" class="bdt-button bdt-button-primary ultimate-post-kit-custom-code-reset-btn">
								<?php esc_html_e('Reset Code', 'ultimate-post-kit'); ?>
							</button>
						</div>

						<!--  White Label Save Button Section -->
						<?php //if (self::is_white_label_license()): ?>
							<div class="upk-white-label-save-section" style="display: none;">
								<button type="button" 
										id="upk-save-white-label" 
										class="bdt-button bdt-button-primary ultimate-post-kit-white-label-save-btn">
										<?php esc_html_e('Save White Label Settings', 'ultimate-post-kit'); ?>
								</button>
							</div>
						<?php //endif; ?>

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

						<div id="ultimate_post_kit_extra_options_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_extra_options(); ?>
						</div>

						<div id="ultimate_post_kit_analytics_system_req_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_analytics_system_req_content(); ?>
						</div>

						<div id="ultimate_post_kit_other_plugins_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_others_plugin(); ?>
						</div>

						<div id="ultimate_post_kit_affiliate_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_affiliate_content(); ?>
						</div>

						<div id="ultimate_post_kit_rollback_version_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_rollback_version_content(); ?>
						</div>

                        <?php if (_is_upk_pro_activated() !== true) : ?>
                            <div id="ultimate_post_kit_get_pro" class="upk-option-page group">
                                <?php $this->ultimate_post_kit_get_pro(); ?>
                            </div>
                        <?php endif; ?>

                        <div id="ultimate_post_kit_license_settings_page" class="upk-option-page group">

                            <?php
                            if (_is_upk_pro_activated() == true) {
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

                jQuery('form.settings-save').on('submit', function(event) {
                    event.preventDefault();

                    bdtUIkit.notification({
                        message: '<div bdt-spinner></div> <?php esc_html_e('Please wait, Saving settings...', 'ultimate-post-kit') ?>',
                        timeout: false
                    });

                    jQuery(this).ajaxSubmit({
                        success: function() {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'ultimate-post-kit') ?>',
                                status: 'primary'
                            });
                        },
                        error: function(data) {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'ultimate-post-kit') ?>',
                                status: 'warning'
                            });
                        }
                    });

                    return false;
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
										data-label="Total Widgets Status - (<?php echo esc_html($used_widgets + $un_used_widgets); ?>)"
										data-labels="<?php echo esc_attr('Used, Unused'); ?>"
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
										data-label="Core Widgets Status - (<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>)"
										data-labels="<?php echo esc_attr('Used, Unused'); ?>"
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
									<div class="upk-widget-count"><?php esc_html_e('Core:', 'ultimate-post-kit'); ?> <b
											id="bdt-total-widgets-status-core">0</b></div>
									<div class="upk-widget-count"><?php esc_html_e('3rd Party:', 'ultimate-post-kit'); ?>
										<b id="bdt-total-widgets-status-3rd">0</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Extensions:', 'ultimate-post-kit'); ?>
										<b id="bdt-total-widgets-status-extensions">0</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Total:', 'ultimate-post-kit'); ?> <b
											id="bdt-total-widgets-status-heading">0</b></div>
								</div>

								<div class="upk-canvas-wrap">
									<canvas id="bdt-total-widgets-status" style="height: 100px; width: 100px;"
										data-label="Total Active Widgets Status"
										data-labels="<?php echo esc_attr('Core, 3rd Party, Extensions'); ?>"
										data-value="0,0,0"
										data-bg="#0680d6, #B0EBFF, #E6F9FF" data-bg-hover="#0673e1, #B0EBFF, #b6f9e8">
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
						echo '<span class="label2" title="Min: 90 Recommended" bdt-tooltip>Currently: ' . esc_html($max_execution_time) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . esc_html($max_execution_time) . '</span>';
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
						echo '<span class="label2" title="Min: 512M Recommended" bdt-tooltip>Currently: ' . esc_html($memory_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . esc_html($memory_limit) . '</span>';
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
						echo '<span class="label2" title="Min: 32M Recommended" bdt-tooltip>Currently: ' . wp_kses_post($post_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . wp_kses_post($post_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Uploads folder writable:', 'ultimate-post-kit'); ?></span>

					<?php
					if (!is_writable($upload_path)) {
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
			/* translators: %s: Plugin name 'Ultimate Post Kit' */
			printf(
				esc_html__('If you have multiple addons like %s so you may need to allocate additional memory for other addons as well.', 'ultimate-post-kit'),
				'<b>Ultimate Post Kit</b>'
			);
			?>
		</div>

		<?php
	}

    /**
	 * Others Plugin
	 */

	public function ultimate_post_kit_others_plugin() {
		// Define plugins with their paths and install URLs
		$plugins = [
			'prime_slider' => [
				'path' => 'bdthemes-prime-slider-lite/bdthemes-prime-slider.php',
				'install_url' => 'https://wordpress.org/plugins/bdthemes-prime-slider-lite/',
				'website_url' => 'https://primeslider.pro/'
			],
			'ultimate_post_kit' => [
				'path' => 'ultimate-post-kit/ultimate-post-kit.php', 
				'install_url' => 'https://wordpress.org/plugins/ultimate-post-kit/',
				'website_url' => 'https://postkit.pro/'
			],
			'ultimate_store_kit' => [
				'path' => 'ultimate-store-kit/ultimate-store-kit.php',
				'install_url' => 'https://wordpress.org/plugins/ultimate-store-kit/',
				'website_url' => 'https://storekit.pro/'
			],
			'pixel_gallery' => [
				'path' => 'pixel-gallery/pixel-gallery.php',
				'install_url' => 'https://wordpress.org/plugins/pixel-gallery/',
				'website_url' => 'https://pixelgallery.pro/'
			],
			'live_copy_paste' => [
				'path' => 'live-copy-paste/live-copy-paste.php',
				'install_url' => 'https://wordpress.org/plugins/live-copy-paste/',
				'website_url' => 'https://www.youtube.com/watch?v=KWxbZfPIcqU'
			],
			'zoloblocks' => [
				'path' => 'zoloblocks/zoloblocks.php',
				'install_url' => 'https://wordpress.org/plugins/zoloblocks/',
				'website_url' => 'https://zoloblocks.com/'
			],
			'spin_wheel' => [
				'path' => 'spin-wheel/spin-wheel.php',
				'install_url' => 'https://wordpress.org/plugins/spin-wheel/',
				'website_url' => 'https://spinwheel.bdthemes.com/'
			],
			'ai_image' => [
				'path' => 'ai-image/ai-image.php',
				'install_url' => 'https://wordpress.org/plugins/ai-image/',
				'website_url' => 'https://www.youtube.com/watch?v=cGmPFU_ju4s'
			],
			'dark_reader' => [
				'path' => 'dark-reader/dark-reader.php',
				'install_url' => 'https://wordpress.org/plugins/dark-reader/',
				'website_url' => 'https://wordpress.org/plugins/dark-reader/'
			],
			'ar_viewer' => [
				'path' => 'ar-viewer/ar-viewer.php',
				'install_url' => 'https://wordpress.org/plugins/ar-viewer/',
				'website_url' => 'https://wordpress.org/plugins/ar-viewer/'
			]
		];
		?>
		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="upk-dashboard-others-plugin">
				<!-- Prime Slider -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/prime-slider.svg'; ?>" alt="Prime Slider">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Prime Slider', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('100k+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							
							<p><?php esc_html_e('The revolutionary slider builder addon for Elementor with next-gen superb interface. It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-half"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php esc_html_e('4.5 out of 5 stars.', 'ultimate-post-kit'); ?>
								</span>
							</div>
						</div>
						
					</div>
				
					<div class="bdt-others-plugins-link">
				    	<?php echo $this->get_plugin_action_button($plugins['prime_slider']['path'], $plugins['prime_slider']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['prime_slider']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>

					
				</div>
				<!-- Ultimate Post Kit -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/ultimate-post-kit.svg'; ?>" alt="zoloblocks">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Ultimate Post Kit', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('30k+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							
							<p><?php esc_html_e('Best blogging addon for building quality blogging website with fine-tuned features and widgets. It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php esc_html_e('4.8 out of 5 stars.', 'ultimate-post-kit'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				     	<?php echo $this->get_plugin_action_button($plugins['ultimate_post_kit']['path'], $plugins['ultimate_post_kit']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ultimate_post_kit']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>
				<!-- Ultimate Store Kit -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/ultimate-store-kit.svg'; ?>" alt="zoloblocks">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Ultimate Store Kit', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('1000+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('The only eCommmerce addon for answering all your online store design problems in one package. It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-half"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php esc_html_e('4.4 out of 5 stars.', 'ultimate-post-kit'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
					    <?php echo $this->get_plugin_action_button($plugins['ultimate_store_kit']['path'], $plugins['ultimate_store_kit']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ultimate_store_kit']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>
				<!-- Pixel Gallery -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/pixel-gallery.svg'; ?>" alt="Pixel Gallery">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Pixel Gallery', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('3000+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('Pixel Gallery provides more than 30+ essential elements for everyday applications to simplify the whole web building process. It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php esc_html_e('5 out of 5 stars.', 'ultimate-post-kit'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
						<?php echo $this->get_plugin_action_button($plugins['pixel_gallery']['path'], $plugins['pixel_gallery']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['pixel_gallery']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>
				<!-- Live Copy Paste -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/live-copy-paste.svg'; ?>" alt="live copy paste">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Live Copy Paste', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('3000+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('Superfast cross-domain copy-paste mechanism for WordPress websites with true UI copy experience. It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-half"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php esc_html_e('4.3 out of 5 stars.', 'ultimate-post-kit'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['live_copy_paste']['path'], $plugins['live_copy_paste']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['live_copy_paste']['website_url']); ?>">
							<?php esc_html_e('Video Tutorial', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>
				<!-- ZoloBlocks -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/zoloblocks.svg'; ?>" alt="zoloblocks">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('ZoloBlocks', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('300+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('ZoloBlocks is a collection of blocks for the new WordPress block editor (Gutenberg). It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php esc_html_e('5 out of 5 stars.', 'ultimate-post-kit'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
						<?php echo $this->get_plugin_action_button($plugins['zoloblocks']['path'], $plugins['zoloblocks']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['zoloblocks']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>
				<!-- Spin Wheel -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/spin-wheel.svg'; ?>" alt="spin wheel">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Spin Wheel', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('100+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('Add a fun, interactive spin wheel to offer instant coupons, boost engagement, and grow your email list. It\'s free!.', 'ultimate-post-kit'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['spin_wheel']['path'], $plugins['spin_wheel']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['spin_wheel']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>

				<!-- Instant Image Generator -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/instant-image-generator.svg'; ?>" alt="instant image generator">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Instant Image Generator', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('100+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('Instant Image Generator (One Click Image Uploads from Pixabay, Pexels and OpenAI). It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['ai_image']['path'], $plugins['ai_image']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ai_image']['website_url']); ?>">
							<?php esc_html_e('Video Tutorial', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>

				<!-- Dark Reader -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/dark-reader.svg'; ?>" alt="dark reader">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('Dark Reader', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('New', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('Add beautiful dark mode to your WordPress site with customizable settings. Reduce eye strain and improve accessibility. It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['dark_reader']['path'], $plugins['dark_reader']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['dark_reader']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>

				<!-- AR Viewer -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/ar-viewer.svg'; ?>" alt="ar viewer">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title "><?php esc_html_e('AR Viewer', 'ultimate-post-kit'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('60+ active users', 'ultimate-post-kit'); ?></span>
							</div>
							<p><?php esc_html_e('Augmented Reality Viewer – 3D Model Viewer. It\'s Free! Download it.', 'ultimate-post-kit'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['ar_viewer']['path'], $plugins['ar_viewer']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ar_viewer']['website_url']); ?>">
							<?php esc_html_e('View Website', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>
			</div>
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
				$nonce = wp_create_nonce('ep_install_plugin_nonce');
				return '<a class="bdt-button bdt-welcome-button upk-install-plugin" 
				          data-plugin-slug="' . esc_attr($plugin_slug) . '" 
				          data-nonce="' . esc_attr($nonce) . '" 
				          href="#">' . 
				       __('Install', 'ultimate-post-kit') . '</a>';
		}
	}

    /**
	 * Display Affiliate Content
	 *
	 * @access public
	 * @return void
	 */

	public function ultimate_post_kit_affiliate_content() {
		?>
		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="upk-dashboard-affiliate">
				<div class="bdt-card bdt-card-body">
					<h1 class="upk-feature-title">
						<?php printf(esc_html__('Earn %s as an Affiliate', 'ultimate-post-kit'), '<strong class="upk-highlight-text">Up to 60% Commission</strong>'); ?>
					</h1>
					<p>
						<?php esc_html_e('Join our affiliate program and earn up to 60% commission on every sale you refer. It\'s a great way to earn passive income while promoting high-quality WordPress plugins.', 'ultimate-post-kit'); ?>
					</p>
					<div class="upk-affiliate-features">
						<h3 class="upk-affiliate-sub-title"><?php esc_html_e('Benefits of joining our affiliate program:', 'ultimate-post-kit'); ?></h3>
						<ul>
							<li><?php esc_html_e('Up to 60% commission on all sales', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Real-time tracking of referrals and sales', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Dedicated affiliate support', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Marketing materials provided', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Monthly payments via PayPal', 'ultimate-post-kit'); ?></li>
						</ul>
					</div>
					<a href="https://bdthemes.com/affiliate/?utm_sourcce=ep_wp_dashboard&utm_medium=affiliate_payout&utm_campaign=affiliate_onboarding" target="_blank"
						class="bdt-button bdt-welcome-button bdt-margin-small-top"><?php esc_html_e('Join Our Affiliate Program', 'ultimate-post-kit'); ?></a>
				</div>
			</div>
		</div>
		<?php
	}


    /**
	 * Extra Options Start Here
	 */

	/**
	 * Render Custom CSS & JS Section
	 * 
	 * @access public
	 * @return void
	 */
	public function render_custom_css_js_section() {
		?>
		<div class="upk-custom-code-section">
			<!-- Header Section -->
			<div class="upk-code-section-header">
				<h2 class="upk-section-title"><?php esc_html_e('Header Code Injection', 'ultimate-post-kit'); ?></h2>
				<p class="upk-section-description"><?php esc_html_e('Code added here will be injected into the &lt;head&gt; section of your website.', 'ultimate-post-kit'); ?></p>
			</div>
			<div class="upk-code-row bdt-grid bdt-grid-small" bdt-grid>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('CSS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw CSS code without &lt;style&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-css" name="ep_custom_css" class="upk-code-editor" data-mode="css" placeholder=".example {&#10;    background: red;&#10;    border-radius: 5px;&#10;    padding: 15px;&#10;}&#10;&#10;"><?php echo esc_textarea(get_option('ep_custom_css', '')); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('JS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw JavaScript code without &lt;script&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-js" name="ep_custom_js" class="upk-code-editor" data-mode="javascript" placeholder="alert('Hello, Ultimate Post Kit!');"><?php echo esc_textarea(get_option('ep_custom_js', '')); ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Footer Section -->
			<div class="upk-code-section-header bdt-margin-medium-top">
				<h2 class="upk-section-title"><?php esc_html_e('Footer Code Injection', 'ultimate-post-kit'); ?></h2>
				<p class="upk-section-description"><?php esc_html_e('Code added here will be injected before the closing &lt;/body&gt; tag of your website.', 'ultimate-post-kit'); ?></p>
			</div>
			<div class="upk-code-row bdt-grid bdt-grid-small bdt-margin-small-top" bdt-grid>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('CSS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw CSS code without &lt;style&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-css-2" name="ep_custom_css_2" class="upk-code-editor" data-mode="css" placeholder=".example {&#10;    background: green;&#10;}&#10;&#10;"><?php echo esc_textarea(get_option('ep_custom_css_2', '')); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('JS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw JavaScript code without &lt;script&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-js-2" name="ep_custom_js_2" class="upk-code-editor" data-mode="javascript" placeholder="console.log('Hello, Ultimate Post Kit!');"><?php echo esc_textarea(get_option('ep_custom_js_2', '')); ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Page Exclusion Section -->
			<div class="upk-code-section-header bdt-margin-medium-top">
				<h2 class="upk-section-title"><?php esc_html_e('Page & Post Exclusion Settings', 'ultimate-post-kit'); ?></h2>
				<p class="upk-section-description"><?php esc_html_e('Select pages and posts where you don\'t want any custom code to be injected. This applies to all sections above.', 'ultimate-post-kit'); ?></p>
			</div>
			<div class="upk-page-exclusion-wrapper">
				<label for="upk-excluded-pages" class="upk-exclusion-label">
					<?php esc_html_e('Exclude Pages & Posts:', 'ultimate-post-kit'); ?>
				</label>
				<select id="upk-excluded-pages" name="ep_excluded_pages[]" multiple class="upk-page-select">
					<option value=""><?php esc_html_e('-- Select pages/posts to exclude --', 'ultimate-post-kit'); ?></option>
					<?php
					$excluded_pages = get_option('ep_excluded_pages', array());
					if (!is_array($excluded_pages)) {
						$excluded_pages = array();
					}
					
					// Get all published pages
					$pages = get_pages(array(
						'sort_order' => 'ASC',
						'sort_column' => 'post_title',
						'post_status' => 'publish'
					));
					
					// Get recent posts (last 50)
					$posts = get_posts(array(
						'numberposts' => 50,
						'post_status' => 'publish',
						'post_type' => 'post',
						'orderby' => 'date',
						'order' => 'DESC'
					));
					
					// Display pages first
					if (!empty($pages)) {
						echo '<optgroup label="' . esc_attr__('Pages', 'ultimate-post-kit') . '">';
						foreach ($pages as $page) {
							$selected = in_array($page->ID, $excluded_pages) ? 'selected' : '';
							echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
						}
						echo '</optgroup>';
					}
					
					// Then display posts
					if (!empty($posts)) {
						echo '<optgroup label="' . esc_attr__('Recent Posts', 'ultimate-post-kit') . '">';
						foreach ($posts as $post) {
							$selected = in_array($post->ID, $excluded_pages) ? 'selected' : '';
							$post_date = date('M j, Y', strtotime($post->post_date));
							echo '<option value="' . esc_attr($post->ID) . '" ' . $selected . '>' . esc_html($post->post_title) . ' (' . $post_date . ')</option>';
						}
						echo '</optgroup>';
					}
					?>
				</select>
				<p class="upk-exclusion-help">
					<?php esc_html_e('Hold Ctrl (or Cmd on Mac) to select multiple items. Selected pages and posts will not load any custom CSS or JavaScript code. The list shows all pages and the 50 most recent posts.', 'ultimate-post-kit'); ?>
				</p>
			</div>

			<!-- Success/Error Messages -->
			<div id="upk-custom-code-message" class="upk-code-message bdt-margin-small-top" style="display: none;">
				<div class="bdt-alert bdt-alert-success" bdt-alert>
					<a href class="bdt-alert-close" bdt-close></a>
					<p><?php esc_html_e('Custom code saved successfully!', 'ultimate-post-kit'); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

    /**
	 * Extra Options Start Here
	 */

	public function ultimate_post_kit_extra_options() {
		?>
		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="upk-dashboard-extra-options">
				<div class="bdt-card bdt-card-body">
					<h1 class="upk-feature-title"><?php esc_html_e('Extra Options', 'ultimate-post-kit'); ?></h1>

					<div class="upk-extra-options-tabs">
						<ul class="bdt-tab" bdt-tab="connect: #upk-extra-options-tab-content; animation: bdt-animation-fade">
							<li class="bdt-active"><a
									href="#"><?php esc_html_e('Custom CSS & JS', 'ultimate-post-kit'); ?></a></li>
							<li><a href="#"><?php esc_html_e('White Label', 'ultimate-post-kit'); ?></a></li>
						</ul>

						<div id="upk-extra-options-tab-content" class="bdt-switcher">
							<!-- Custom CSS & JS Tab -->
							<div>
								<?php $this->render_custom_css_js_section(); ?>
							</div>
							
							<!-- White Label Tab -->
							<div>
								<?php //$this->render_white_label_section(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}


    /**
	 * Rollback Version Content
	 *
	 * @access public
	 * @return void
	 */
	public function ultimate_post_kit_rollback_version_content() {
		// Use the already initialized rollback version instance
		$this->rollback_version->ultimate_post_kit_rollback_version_content();
	}









}

new UltimatePostKit_Admin_Settings();
