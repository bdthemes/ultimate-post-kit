<?php

/**
 * Ultimate Post Kit widget filters
 * @since 3.0.0
 */

use UltimatePostKit\Admin\ModuleService;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Settings Filters
if (!function_exists('upk_is_dashboard_enabled')) {
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- established function name relied on across the plugin family / feedback SDK; renaming would break integration.
    function upk_is_dashboard_enabled() {
        return apply_filters('ultimatepostkit/settings/dashboard', true);
    }
}

if (!function_exists('ultimate_post_kit_is_widget_enabled')) {
    function ultimate_post_kit_is_widget_enabled($widget_id, $options = []) {

        if(!$options){
            $options = get_option('ultimate_post_kit_active_modules', []);
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $widget_id = str_replace('-','_', $widget_id);
            return apply_filters("ultimatepostkit/widget/{$widget_id}", true);
        }
    }
}

if (!function_exists('ultimate_post_kit_is_extend_enabled')) {
    function ultimate_post_kit_is_extend_enabled($widget_id, $options = []) {

        if(!$options){
            $options = get_option('ultimate_post_kit_elementor_extend', []);
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $widget_id = str_replace('-','_', $widget_id);
            return apply_filters("ultimatepostkit/extend/{$widget_id}", true);
        }
    }
}

// if (!function_exists('ultimate_post_kit_is_third_party_enabled')) {
//     function ultimate_post_kit_is_third_party_enabled($widget_id, $options = []) {

//         if(!$options){
//             $options = get_option('ultimate_post_kit_third_party_widget', []);
//         }

//         if( ModuleService::is_module_active($widget_id, $options)){
//             $widget_id = str_replace('-','_', $widget_id);
//             return apply_filters("ultimatepostkit/widget/{$widget_id}", true);
//         }
//     }
// }

if (!function_exists('ultimate_post_kit_is_asset_optimization_enabled')) {
    /**
     * Whether Asset Optimization (Ultimate Post Kit > Features) is switched on.
     *
     * With it on, the widget CSS and JS files a page loads are joined into one
     * combined stylesheet and one combined script for that page; see
     * UltimatePostKit\Includes\Asset_Combiner.
     *
     * @return bool
     */
    function ultimate_post_kit_is_asset_optimization_enabled() {
        $enabled = ('on' === ultimate_post_kit_option('asset-manager', 'ultimate_post_kit_other_settings', 'off'));

        return (bool) apply_filters('ultimatepostkit/optimization/asset_manager', $enabled);
    }
}

if (!function_exists('ultimate_post_kit_module_asset_depends')) {
    /**
     * The shared handles every widget stylesheet or script depends on.
     *
     * The site stylesheet and script (`upk-site`, which also carries
     * UltimatePostKitConfig) are not loaded on every page: a page without an
     * Ultimate Post Kit widget has no use for them. Every `upk-<module>` handle
     * depends on them instead, so a page loads them exactly when it loads a widget,
     * in the head when Elementor declares the page's widgets there. Ultimate Post
     * Kit Pro registers its widgets with the same handles.
     *
     * @param string $type      'css' or 'js'.
     * @param string $module_id Module slug.
     * @return string[]
     */
    function ultimate_post_kit_module_asset_depends($type, $module_id = '') {
        $depends = 'js' === $type ? ['jquery', 'upk-site'] : ['upk-site'];

        /**
         * Filters the handles an Ultimate Post Kit widget stylesheet or script depends on.
         *
         * @param string[] $depends   Handles the module file depends on.
         * @param string   $type      'css' or 'js'.
         * @param string   $module_id Module slug.
         */
        return (array) apply_filters('ultimate_post_kit/module/asset_depends', $depends, $type, $module_id);
    }
}


