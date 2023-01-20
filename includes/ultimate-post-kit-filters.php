<?php

/**
 * Prime Slider widget filters
 * @since 3.0.0
 */

use UltimatePostKit\Admin\ModuleService;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Settings Filters
if (!function_exists('upk_is_dashboard_enabled')) {
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

// if (!function_exists('ultimate_post_kit_is_asset_optimization_enabled')) {
//     function ultimate_post_kit_is_asset_optimization_enabled() {
//         $asset_manager = ultimate_post_kit_option('asset-manager', 'ultimate_post_kit_other_settings', 'off');
//         if( $asset_manager == 'on'){
//             return apply_filters("ultimatepostkit/optimization/asset_manager", true);
//         }
//     }
// }


