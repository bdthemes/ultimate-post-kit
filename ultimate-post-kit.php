<?php

/**
 * Plugin Name: Ultimate Post Kit
 * Plugin URI: https://bdthemes.com/ultimate-post-kit/
 * Description: <a href="https://bdthemes.com/ultimate-post-kit/">Ultimate Post Kit</a> is a packed of post related elementor widgets. This plugin gives you post related widget features for elementor page builder plugin.
 * Version: 3.4.4
 * Author: BdThemes
 * Author URI: https://bdthemes.com/
 * Text Domain: ultimate-post-kit
 * Domain Path: /languages
 * License: GPL3
 * Elementor requires at least: 3.0.0
 * Elementor tested up to: 3.12.1
 */

if (!defined('ABSPATH')) {
	exit;
}

// Some pre define value for easy use
define('BDTUPK_VER', '3.4.4');
define('BDTUPK__FILE__', __FILE__);


if (!function_exists('_is_upk_pro_installed')) {

	function _is_upk_pro_installed(){

		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$file_path = 'ultimate-post-kit-pro/ultimate-post-kit-pro.php';
		$installed_plugins = get_plugins();

		return isset($installed_plugins[$file_path]);
	}
}

if (!function_exists('_is_upk_pro_activated')) {

	function _is_upk_pro_activated(){
		
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$file_path = 'ultimate-post-kit-pro/ultimate-post-kit-pro.php';

		if (is_plugin_active($file_path)) {
			return true;
		}

		return false;
	}
}


if (function_exists('upk_fs')) {
	upk_fs()->set_basename(true, __FILE__);
} else {
	if (!function_exists('upk_fs')) {
		// Create a helper function for easy SDK access.
		function upk_fs(){
			global $upk_fs;

			if (!isset($upk_fs)) {
				// Activate multisite network integration.
				if (!defined('WP_FS__PRODUCT_8719_MULTISITE')) {
					define('WP_FS__PRODUCT_8719_MULTISITE', true);
				}

				// Include Freemius SDK.
				require_once dirname(__FILE__) . '/freemius/start.php';

				$upk_fs = fs_dynamic_init(
					array(
						'id'                  => '8719',
						'slug'                => 'ultimate-post-kit',
						'premium_slug'        => 'ultimate-post-kit-pro',
						'type'                => 'plugin',
						'public_key'          => 'pk_5ee4f096c0fc2fde7ea8dc9bb2899',
						'is_premium'          => false,
						// If your plugin is a serviceware, set this option to false.
						'has_premium_version' => false,
						'has_addons'          => true,
						'has_paid_plans'      => false,
						// Set the SDK to work in a sandbox mode (for development & testing).
						// IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
						'secret_key'          => 'sk_I>m2ZGqwJkLv:>~hFQn_6+tH-d4QF',
					)
				);
			}

			return $upk_fs;
		}

		// Init Freemius.
		upk_fs();
		// Signal that SDK was initiated.
		do_action('upk_fs_loaded');
	}

	// Helper function here
	require_once(dirname(__FILE__) . '/includes/helper.php');

	if (!_is_upk_pro_activated()) {
		require_once BDTUPK_INC_PATH . 'class-pro-widget-map.php';
	}

	if (function_exists('upk_license_validation') && true !== upk_license_validation()) {
		require_once BDTUPK_INC_PATH . 'class-pro-widget-map.php';
	}

	require_once(dirname(__FILE__) . '/includes/utils.php');

	// Widgets filters here
	require_once(BDTUPK_INC_PATH . 'ultimate-post-kit-filters.php');


	/**
	 * Plugin load here correctly
	 * Also loaded the language file from here
	 */
	function ultimate_post_kit_load_plugin(){
		load_plugin_textdomain('ultimate-post-kit', false, basename(dirname(__FILE__)) . '/languages');

		if (!did_action('elementor/loaded')) {
			add_action('admin_notices', 'ultimate_post_kit_fail_load');

			return;
		}

		// Element pack widget and assets loader
		require_once(BDTUPK_PATH . 'loader.php');
	}

	add_action('plugins_loaded', 'ultimate_post_kit_load_plugin');


	/**
	 * Check Elementor installed and activated correctly
	 */
	function ultimate_post_kit_fail_load(){
		$screen = get_current_screen();
		if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
			return;
		}

		$plugin = 'elementor/elementor.php';

		if (_is_elementor_installed()) {
			if (!current_user_can('activate_plugins')) {
				return;
			}
			$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
			$admin_message  = '<p>' . esc_html__('Ops! Ultimate Post Kit not working because you need to activate the Elementor plugin first.', 'ultimate-post-kit') . '</p>';
			$admin_message  .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__('Activate Elementor Now', 'ultimate-post-kit')) . '</p>';
		} else {
			if (!current_user_can('install_plugins')) {
				return;
			}
			$install_url   = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
			$admin_message = '<p>' . esc_html__('Ops! Ultimate Post Kit not working because you need to install the Elementor plugin', 'ultimate-post-kit') . '</p>';
			$admin_message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__('Install Elementor Now', 'ultimate-post-kit')) . '</p>';
		}

		echo '<div class="error">' . $admin_message . '</div>';
	}

	/**
	 * Check the elementor installed or not
	 */
	if (!function_exists('_is_elementor_installed')) {

		function _is_elementor_installed(){
			$file_path         = 'elementor/elementor.php';
			$installed_plugins = get_plugins();

			return isset($installed_plugins[$file_path]);
		}
	}
}
