<?php

namespace UltimatePostKit;

use UltimatePostKit\Admin\ModuleService;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

final class Manager
{
    private $_modules = [];

    private function is_module_active($module_id)
    {

        $module_data = $this->get_module_data($module_id);
        $options = get_option('ultimate_post_kit_active_modules', []);

        if (!isset($options[$module_id])) {
            return $module_data['default_activation'];
        } else {
            if ($options[$module_id] == "on") {
                return true;
            } else {
                return false;
            }
        }
    }

    private function has_module_style($module_id)
    {

        $module_data = $this->get_module_data($module_id);

        if (isset($module_data['has_style'])) {
            return $module_data['has_style'];
        } else {
            return false;
        }
    }
    private function has_module_script($module_id)
    {

        $module_data = $this->get_module_data($module_id);

        if (isset($module_data['has_script'])) {
            return $module_data['has_script'];
        } else {
            return false;
        }
    }

    private function get_module_data($module_id)
    {
        return isset($this->_modules[$module_id]) ? $this->_modules[$module_id] : false;
    }

    public function register_module_and_assets()
    {

        ModuleService::get_widget_settings(function ($settings) {
            $core_widgets        = $settings['settings_fields']['ultimate_post_kit_active_modules'];
            $extensions          = $settings['settings_fields']['ultimate_post_kit_elementor_extend'];
            // $third_party_widgets = $settings['settings_fields']['ultimate_post_kit_third_party_widget'];

            /**
             * Our Widget
             */
            foreach ($core_widgets as $widget) {
                if (ultimate_post_kit_is_widget_enabled($widget['name'])) {
                    $this->load_module_instance($widget);
                }
            }

            /**
             * Extension
             */
            foreach ($extensions as $extension) {
                if (ultimate_post_kit_is_extend_enabled($extension['name'])) {
                    $this->load_module_instance($extension);
                }
            }

            // Static module if need
            $this->load_module_instance(['name' => 'elementor']);

        });
    }

    public function load_module_instance($module)
    {

        if(isset($_GET['page']) && 'ultimate_post_kit_options' ==  $_GET['page']){
            return;
        }

        $direction = is_rtl() ? '.rtl' : '';
        $suffix    = '.min';

        $module_id  = $module['name'];
        $class_name = str_replace('-', ' ', $module_id);
        $class_name = str_replace(' ', '', ucwords($class_name));
        $class_name = __NAMESPACE__ . '\\Modules\\' . $class_name . '\\Module';


        if (!ultimate_post_kit_is_preview()) {
            // register widgets css
            if (ModuleService::has_module_style( $module_id, BDTUPK_MODULES_PATH )) {
                wp_register_style('upk-' . $module_id, BDTUPK_URL . 'assets/css/upk-' . $module_id . $direction . '.css', [], BDTUPK_VER);
            }
            // register widget JS
            if (ModuleService::has_module_script( $module_id, BDTUPK_MODULES_PATH )) {
                wp_register_script('upk-' . $module_id, BDTUPK_URL . 'assets/js/widgets/upk-' . $module_id . $suffix . '.js', ['jquery', 'elementor-frontend'], BDTUPK_VER, true);
            }
        }


        if (class_exists($class_name)) {
            $class_name::instance();
        }
    }

    public function __construct()
    {

        $this->register_module_and_assets();
    }
}
