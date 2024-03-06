<?php

use UltimatePostKit\Notices;
use UltimatePostKit\Utils;
use UltimatePostKit\Admin\ModuleService;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;

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

    function __construct() {
        $this->settings_api = new UltimatePostKit_Settings_API;

        if (!defined('BDTUPK_HIDE')) {
            add_action('admin_init', [$this, 'admin_init']);
            add_action('admin_menu', [$this, 'admin_menu'], 201);
        }

        if (!Tracker::is_allow_track()) {
            add_action('admin_notices', [$this, 'allow_tracker_activate_notice'], 10, 3);
        }

        /**
         * Mini-Cart issue fixed
         * Check if MiniCart activate in EP and Elementor
         * If both is activated then Show Notice
         */

        $upk_3rdPartyOption = get_option('ultimate_post_kit_third_party_widget');

        $el_use_mini_cart = get_option('elementor_use_mini_cart_template');

        if ($el_use_mini_cart !== false && $upk_3rdPartyOption !== false) {
            if ($upk_3rdPartyOption) {
                if ('yes' == $el_use_mini_cart && isset($upk_3rdPartyOption['wc-mini-cart']) && 'off' !== trim($upk_3rdPartyOption['wc-mini-cart'])) {
                    add_action('admin_notices', [$this, 'el_use_mini_cart'], 10, 3);
                }
            }
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
            $elements   = $module->get_formatted_usage('raw');
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
            $elements   = $module->get_formatted_usage('raw');
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
     * Get used only separate 3rdParty widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_used_only_3rdparty() {

        $used_widgets = array();

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module     = Module::instance();
            $elements   = $module->get_formatted_usage('raw');
            $upk_widgets = self::get_upk_only_3rdparty_names();

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
     * Get unused separate 3rdparty widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_only_3rdparty() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $upk_widgets = self::get_upk_only_3rdparty_names();

        $used_widgets = self::get_used_only_3rdparty();

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
            [$this, 'display_page']
        );

        add_submenu_page(
            self::PAGE_ID,
            BDTUPK_TITLE,
            esc_html__('Extensions', 'ultimate-post-kit'),
            'manage_options',
            self::PAGE_ID . '#ultimate_post_kit_elementor_extend',
            [$this, 'display_page']
        );

        add_submenu_page(
            self::PAGE_ID,
            BDTUPK_TITLE,
            esc_html__('API Settings', 'ultimate-post-kit'),
            'manage_options',
            self::PAGE_ID . '#ultimate_post_kit_api_settings',
            [$this, 'display_page']
        );

        if (!defined('BDTUPK_LO')) {

            add_submenu_page(
                self::PAGE_ID,
                BDTUPK_TITLE,
                esc_html__('Other Settings', 'ultimate-post-kit'),
                'manage_options',
                self::PAGE_ID . '#ultimate_post_kit_other_settings',
                [$this, 'display_page']
            );

            // add_submenu_page(
            //     self::PAGE_ID,
            //     BDTUPK_TITLE,
            //     esc_html__('License', 'ultimate-post-kit'),
            //     'manage_options',
            //     self::PAGE_ID . '#ultimate_post_kit_license_settings',
            //     [$this, 'display_page']
            // );
        }

        if (true !== _is_upk_pro_activated()) {
            add_submenu_page(
                self::PAGE_ID,
                BDTUPK_TITLE,
                esc_html__('Get Pro', 'ultimate-post-kit'),
                'manage_options',
                self::PAGE_ID . '#ultimate_post_kit_get_pro',
                [$this, 'display_page']
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
     * Get SVG Icons of Ultimate Post Kit
     *
     * @access public
     * @return array
     */

    public function get_settings_sections() {
        $sections = [
            [
                'id'    => 'ultimate_post_kit_active_modules',
                'title' => esc_html__('Core Widgets', 'ultimate-post-kit')
            ],
            [
                'id'    => 'ultimate_post_kit_elementor_extend',
                'title' => esc_html__('Extensions', 'ultimate-post-kit')
            ],
            [
                'id'    => 'ultimate_post_kit_api_settings',
                'title' => esc_html__('API Settings', 'ultimate-post-kit'),
            ],
            [
                'id'    => 'ultimate_post_kit_other_settings',
                'title' => esc_html__('Other Settings', 'ultimate-post-kit'),
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
        $track_nw_msg = '';
        if (!Tracker::is_allow_track()) {
            $track_nw = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'ultimate-post-kit');
            $track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
        }
?>

        <div class="upk-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="upk-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?>>

                        <?php
                        $used_widgets    = count(self::get_used_widgets());
                        $un_used_widgets = count(self::get_unused_widgets());
                        ?>


                        <div class="upk-count-canvas-wrap bdt-flex bdt-flex-between">
                            <div class="upk-count-wrap">
                                <h1 class="upk-feature-title">
                                    <?php echo esc_html_x('All Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                                </h1>
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
                                <canvas id="bdt-db-total-status" style="height: 120px; width: 120px;" data-label="Total Widgets Status - (<?php echo esc_attr($used_widgets) + esc_attr($un_used_widgets); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>" data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="upk-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?>>

                        <div class="upk-count-canvas-wrap bdt-flex bdt-flex-between">
                            <div class="upk-count-wrap">
                                <h1 class="upk-feature-title">
                                    <?php echo esc_html_x('Active', 'Frontend', 'ultimate-post-kit'); ?>
                                </h1>
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
                                <canvas id="bdt-total-widgets-status" style="height: 120px; width: 120px;" data-label="Total Active Widgets Status" data-labels="<?php echo esc_attr('Core, Extensions'); ?>" data-bg="#0680d6, #E6F9FF" data-bg-hover="#0673e1, #b6f9e8">
                                </canvas>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bdt-width-1-2@m bdt-width-1-2@l">
                    <div class="upk-elementor-addons bdt-card bdt-card-body">
                        <a target="_blank" rel="" href="https://www.elementpack.pro/elements-demo/"></a>
                    </div>
                </div>
            </div>


            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-1-3@m upk-support-section">
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

                <div class="bdt-width-2-3@m">
                    <div class="bdt-card bdt-card-body upk-system-requirement">
                        <h1 class="upk-feature-title bdt-margin-small-bottom">
                            <?php echo esc_html_x('System Requirement', 'Frontend', 'ultimate-post-kit'); ?>
                        </h1>
                        <?php $this->ultimate_post_kit_system_requirement(); ?>
                    </div>
                </div>
            </div>

            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
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
                            <?php echo esc_html_x('Try Our Addons', 'Frontend', 'ultimate-post-kit'); ?>
                        </h1>
                        <p style="max-width: 520px;">
                            <?php printf(
                                esc_html__('%1$sElement Pack, Prime Slider, Ultimate Store Kit, Pixel Gallery & Live Copy Paste %2$s addons for %3$sElementor%4$s is the best slider, blogs and eCommerce plugin for WordPress.', 'ultimate-post-kit'),
                                '<b>',
                                '</b>',
                                '<b>',
                                '</b>'
                            ); ?>
                        </p>
                        <div class="bdt-others-plugins-link">
                            <a class="bdt-button bdt-btn-ep bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/bdthemes-element-pack-lite/" bdt-tooltip="Element Pack Lite provides more than 50+ essential elements for everyday applications to simplify the whole web building process. It's Free! Download it.">
                                <?php echo esc_html_x('Element pack', 'Frontend', 'ultimate-post-kit'); ?>
                            </a>
                            <a class="bdt-button bdt-btn-ps bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/bdthemes-prime-slider-lite/" bdt-tooltip="The revolutionary slider builder addon for Elementor with next-gen superb interface. It's Free! Download it.">
                                <?php echo esc_html_x('Prime Slider', 'Frontend', 'ultimate-post-kit'); ?>
                            </a>
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
                    <h1 class="bdt-text-bold">
                        <?php echo esc_html_x('WHY GO WITH PRO?', 'Frontend', 'ultimate-post-kit'); ?>
                    </h1>
                    <h2>
                        <?php echo esc_html_x('Just Compare With Ultimate Post Kit Free Vs Pro', 'Frontend', 'ultimate-post-kit'); ?>
                    </h2>


                    <div>

                        <ul class="bdt-list bdt-list-divider bdt-text-left bdt-text-normal" style="font-size: 16px;">


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

                        </ul>


                        <div class="upk-dashboard-divider"></div>


                        <div class="upk-more-features">
                            <ul class="bdt-list bdt-list-divider bdt-text-left" style="font-size: 16px;">
                                <li>
                                    <div class="bdt-grid">
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
                                    <div class="bdt-grid">
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
                                    <div class="bdt-grid">
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
                                    <div class="bdt-grid">
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
                                <div class="upk-purchase-button">
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

    function ultimate_post_kit_system_requirement() {
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
                esc_html__('%1$s If you have multiple addons like %2$s so you need some more requirement some cases so make sure you added more memory for others addon too.', 'ultimate-post-kit'),
                '<strong>Note:</strong>', 
                '<b>Ultimate Post Kit</b>'
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

    function plugin_page() {

        echo '<div class="wrap ultimate-post-kit-dashboard">';
        echo '<h1>' . BDTUPK_TITLE . ' Settings</h1>';

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

                jQuery(window).on('load', function() {
                    hashHandler();
                });

                window.addEventListener("hashchange", hashHandler, true);

                jQuery('.toplevel_page_ultimate_post_kit_options > ul > li > a ').on('click', function(event) {
                    jQuery(this).parent().siblings().removeClass('current');
                    jQuery(this).parent().addClass('current');
                });

                jQuery('#ultimate_post_kit_active_modules_page a.upk-active-all-widget').click(function() {

                    jQuery('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_active_modules_page a.upk-deactive-all-widget').click(function() {

                    jQuery('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-active-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_third_party_widget_page a.upk-active-all-widget').click(function() {

                    jQuery('#ultimate_post_kit_third_party_widget_page .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_third_party_widget_page a.upk-deactive-all-widget').click(function() {

                    jQuery('#ultimate_post_kit_third_party_widget_page .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-active-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_elementor_extend_page a.upk-active-all-widget').click(function() {

                    jQuery('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_elementor_extend_page a.upk-deactive-all-widget').click(function() {

                    jQuery('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-active-all-widget').removeClass('bdt-active');
                });

                jQuery('form.settings-save').submit(function(event) {
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
     * v6 Notice
     * This notice is very important to show minimum 3 to 5 next update released version.
     *
     * @access public
     */

    public function v6_activate_notice() {

        Notices::add_notice(
            [
                'id'               => 'version-6',
                'type'             => 'warning',
                'dismissible'      => true,
                'dismissible-time' => 43200,
                'message'          => __('There are very important changes in our major version <strong>v6.0.0</strong>. If you are continuing with the Ultimate Post Kit plugin from an earlier version of v6.0.0 then you must read this article carefully <a href="https://bdthemes.com/knowledge-base/read-before-upgrading-to-ultimate-post-kit-pro-version-6-0" target="_blank">from here</a>. <br> And if you are using this plugin from v6.0.0 there is nothing to worry about you. Thank you.', 'ultimate-post-kit'),
            ]
        );
    }
    /**
     * 
     * Check mini-Cart of Elementor Activated or Not
     * It's better to not use multiple mini-Cart on the same time.
     * Transient Expire on 15 days
     *
     * @access public
     */

    public function el_use_mini_cart() {

        Notices::add_notice(
            [
                'id'               => 'upk-el-use-mini-cart',
                'type'             => 'warning',
                'dismissible'      => true,
                'dismissible-time' => MONTH_IN_SECONDS / 2,
                'message'          => __('We can see you activated the <strong>Mini-Cart</strong> of Elementor Pro and also Ultimate Post Kit Pro. We will recommend you to choose one of them, otherwise you will get conflict. Thank you.', 'ultimate-post-kit'),
            ]
        );
    }
    /**
     * 
     * Allow Tracker deactivated warning
     * If Allow Tracker disable in elementor then this notice will be show
     *
     * @access public
     */

    public function allow_tracker_activate_notice() {

        Notices::add_notice(
            [
                'id'               => 'upk-allow-tracker',
                'type'             => 'warning',
                'dismissible'      => true,
                'dismissible-time' => MONTH_IN_SECONDS * 2,
                'message'          => __('Please activate <strong>Usage Data Sharing</strong> features from Elementor, otherwise Widgets Analytics will not work. Please activate the settings from <strong>Elementor > Settings > General Tab >  Usage Data Sharing.</strong> Thank you.', 'ultimate-post-kit'),
            ]
        );
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
}

new UltimatePostKit_Admin_Settings();
