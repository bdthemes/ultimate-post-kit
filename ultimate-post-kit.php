<?php

/**
 * Plugin Name: Ultimate Post Kit
 * Plugin URI: https://postkit.pro/
 * Description: <a href="https://postkit.pro/">Ultimate Post Kit</a> is a packed of post related elementor widgets. This plugin gives you post related widget features for elementor page builder plugin.
 * Version: 4.0.18
 * Author: BdThemes
 * Author URI: https://bdthemes.com/
 * Text Domain: ultimate-post-kit
 * Domain Path: /languages
 * License: GPL3
 * Elementor requires at least: 3.28
 * Elementor tested up to: 3.33.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Some pre define value for easy use
define( 'BDTUPK_VER', '4.0.18' );
define( 'BDTUPK__FILE__', __FILE__ );

/**
 * Loads translations
 *
 * @return void
 */

if ( ! function_exists( 'ultimate_post_kit_load_textdomain' ) ) {
	function ultimate_post_kit_load_textdomain() {
		load_plugin_textdomain( 'ultimate-post-kit', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'ultimate_post_kit_load_textdomain' );
}


if ( ! function_exists( '_is_upk_pro_installed' ) ) {

	function _is_upk_pro_installed() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$file_path         = 'ultimate-post-kit-pro/ultimate-post-kit-pro.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}

if ( ! function_exists( '_is_upk_pro_activated' ) ) {

	function _is_upk_pro_activated() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$file_path = 'ultimate-post-kit-pro/ultimate-post-kit-pro.php';

		if ( is_plugin_active( $file_path ) ) {
			return true;
		}

		return false;
	}
}

// Load white label configuration if it exists (before defining BDTUPK_TITLE)
if ( ! defined( 'BDTUPK_WL' ) ) {
    if ( get_option( 'upk_white_label_enabled' ) ) {
        define( 'BDTUPK_WL', true );
		$white_label_config = dirname( __FILE__ ) . '/admin/white-label/white-label-config.php';
		if ( file_exists( $white_label_config ) ) {
			require_once( $white_label_config );
		}
	}
}



// Helper function here
require_once ( dirname( __FILE__ ) . '/includes/helper.php' );

if ( ! _is_upk_pro_activated() ) {
	require_once BDTUPK_INC_PATH . 'class-pro-widget-map.php';
}

if ( function_exists( 'upk_license_validation' ) && true !== upk_license_validation() ) {
	require_once BDTUPK_INC_PATH . 'class-pro-widget-map.php';
}

require_once ( dirname( __FILE__ ) . '/includes/utils.php' );

// Widgets filters here
require_once ( BDTUPK_INC_PATH . 'ultimate-post-kit-filters.php' );


/**
 * Plugin load here correctly
 * Also loaded the language file from here
 */
function ultimate_post_kit_load_plugin() {

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'ultimate_post_kit_fail_load' );

		return;
	}

	require_once( dirname( __FILE__ ) . '/includes/setup-wizard/init.php' );

	// Element pack widget and assets loader
	require_once ( BDTUPK_PATH . 'loader.php' );
	
	// Initialize custom CSS/JS injection on frontend
	add_action( 'wp_head', 'upk_inject_header_custom_code', 999 );
	add_action( 'wp_footer', 'upk_inject_footer_custom_code', 999 );
}

add_action( 'plugins_loaded', 'ultimate_post_kit_load_plugin' );


/**
 * Check Elementor installed and activated correctly
 */
function ultimate_post_kit_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$admin_message  = '<p>' . esc_html__( 'Ops! Ultimate Post Kit not working because you need to activate the Elementor plugin first.', 'ultimate-post-kit' ) . '</p>';
		$admin_message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Elementor Now', 'ultimate-post-kit' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		$install_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		$admin_message = '<p>' . esc_html__( 'Ops! Ultimate Post Kit not working because you need to install the Elementor plugin', 'ultimate-post-kit' ) . '</p>';
		$admin_message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Elementor Now', 'ultimate-post-kit' ) ) . '</p>';
	}

	echo '<div class="error">' . $admin_message . '</div>';
}

/**
 * Check the elementor installed or not
 */
if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path         = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}



/**
 * Review Automation Integration
 */

if ( ! function_exists( 'rc_upk_core_plugin' ) ) {
	function rc_upk_core_plugin() {

		require_once BDTUPK_INC_PATH . 'feedback-hub/start.php';

		rc_dynamic_init( array(
			'sdk_version'  => '1.0.0',
			'plugin_name'  => 'Ultimate Post Kit',
			'plugin_icon'  => BDTUPK_ASSETS_URL . 'images/logo.svg',
			'slug'         => 'ultimate_post_kit_options',
			'menu'         => array(
				'slug' => 'ultimate_post_kit_options',
			),
			'review_url'   => 'https://bdt.to/ultimate-post-kit-elementor-addons-review',
			'plugin_title' => 'Yay! Great that you\'re using Ultimate Post Kit',
			'plugin_msg'   => '<p>Loved using Ultimate Post Kit on your website? Share your experience in a review and help us spread the love to everyone right now. Good words will help the community.</p>',
		) );

	}
	add_action( 'admin_init', 'rc_upk_core_plugin' );
}
