<?php

namespace UltimatePostKit;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly


require_once BDTUPK_ADMIN_PATH . 'class-settings-api.php';

if (current_user_can('manage_options')) {
	require_once BDTUPK_ADMIN_PATH . 'admin-feeds.php';
}

// element pack admin settings here
require_once BDTUPK_ADMIN_PATH . 'admin-settings.php';

/**
 * Admin class
 */

class Admin
{

	public function __construct()
	{

		// Embed the Script on our Plugin's Option Page Only
		if (isset($_GET['page']) && ($_GET['page'] == 'ultimate_post_kit_options')) {
			add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
		}
		add_action('admin_init', [$this, 'enqueue_admin_script']);

		// Admin settings controller
		require_once BDTUPK_ADMIN_PATH . 'module-settings.php';

		// register_activation_hook(BDTUPK__FILE__, 'install_and_activate');


	}


	function install_and_activate()
	{

		// I don't know of any other redirect function, so this'll have to do.
		wp_redirect(admin_url('admin.php?page=ultimate_post_kit_options'));
		// You could use a header(sprintf('Location: %s', admin_url(...)); here instead too.
	}

	/**
	 * Enqueue styles
	 * @access public
	 */

	public function enqueue_styles()
	{

		$direction_suffix = is_rtl() ? '.rtl' : '';
		// $suffix           = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style('upk-admin', BDTUPK_ADMIN_ASSETS_URL . 'css/upk-admin' . $direction_suffix . '.css', [], BDTUPK_VER);
		wp_enqueue_script('upk-admin', BDTUPK_ASSETS_URL . 'js/upk-admin.min.js', ['jquery'], BDTUPK_VER, true);
		wp_enqueue_style('upk-editor', BDTUPK_ASSETS_URL . 'css/upk-editor' . $direction_suffix . '.css', [], BDTUPK_VER);

		wp_enqueue_style('bdt-uikit', BDTUPK_ADMIN_ASSETS_URL . 'css/bdt-uikit' . $direction_suffix . '.css', [], '3.17.0');
		wp_enqueue_style('upk-font', BDTUPK_ASSETS_URL . 'css/upk-font' . $direction_suffix . '.css', [], BDTUPK_VER);

		wp_enqueue_script('bdt-uikit', BDTUPK_ADMIN_ASSETS_URL . 'js/bdt-uikit.min.js', ['jquery'], '3.17.0');
	}

	/**
	 * Row meta
	 * @access public
	 * @return array
	 */

	public function plugin_row_meta($plugin_meta, $plugin_file)
	{
		if (BDTUPK_PBNAME === $plugin_file) {
			$row_meta = [
				'docs'  => '<a href="https://postkit.pro/contact/" aria-label="' . esc_attr(__('Go for Get Support', 'ultimate-post-kit')) . '" target="_blank">' . __('Get Support', 'ultimate-post-kit') . '</a>',
				'video' => '<a href="https://www.youtube.com/playlist?list=PLP0S85GEw7DOJf_cbgUIL20qqwqb5x8KA" aria-label="' . esc_attr(__('View Ultimate Post Kit Video Tutorials', 'ultimate-post-kit')) . '" target="_blank">' . __('Video Tutorials', 'ultimate-post-kit') . '</a>',
			];

			$plugin_meta = array_merge($plugin_meta, $row_meta);
		}

		return $plugin_meta;
	}

	/**
	 * Action meta
	 * @access public
	 * @return array
	 */


	public function plugin_action_meta($links)
	{

		$links = array_merge([sprintf('<a href="%s">%s</a>', ultimate_post_kit_dashboard_link('#ultimate_post_kit_welcome'), esc_html__('Settings', 'ultimate-post-kit'))], $links);

		$links = array_merge($links, [
			sprintf(
				'<a href="%s">%s</a>',
				ultimate_post_kit_dashboard_link('#license'),
				esc_html__('License', 'ultimate-post-kit')
			)
		]);

		return $links;
	}

	/**
	 * Change Ultimate Post Kit Name
	 * @access public
	 * @return string
	 */

	public function ultimate_post_kit_name_change($translated_text, $text, $domain)
	{
		switch ($translated_text) {
			case 'Ultimate Post Kit Pro':
				$translated_text = BDTUPK_TITLE;
				break;
		}

		return $translated_text;
	}

	/**
	 * Hiding plugins //still in testing purpose
	 * @access public
	 */

	public function hide_ultimate_post_kit()
	{
		global $wp_list_table;
		$hide_plg_array = array('ultimate-post-kit/ultimate-post-kit.php');
		$all_plugins    = $wp_list_table->items;

		foreach ($all_plugins as $key => $val) {
			if (in_array($key, $hide_plg_array)) {
				unset($wp_list_table->items[$key]);
			}
		}
	}

	/**
	 * Register admin script
	 * @access public
	 */

	public function enqueue_admin_script()
	{
		
		// $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('upk-notice', BDTUPK_ADMIN_ASSETS_URL . 'js/upk-notice.min.js', ['jquery'], BDTUPK_VER,  true);

		$script_config = [
			'ajaxurl'	=> admin_url('admin-ajax.php'),
			'nonce'		=> wp_create_nonce('ultimate-post-kit'),
		];
		wp_localize_script('upk-notice', 'UltimatePostKitNoticeConfig', $script_config);

		if (isset($_GET['page']) && ($_GET['page'] == 'ultimate_post_kit_options')) {
			wp_enqueue_script('chart', BDTUPK_ADMIN_ASSETS_URL . 'js/chart.min.js', ['jquery'], '3.9.1', true);
			wp_enqueue_script('upk-admin', BDTUPK_ADMIN_ASSETS_URL  . 'js/upk-admin.min.js', ['jquery', 'chart'], BDTUPK_VER, true);
		}else{
			wp_enqueue_script('upk-admin', BDTUPK_ADMIN_ASSETS_URL  . 'js/upk-admin.min.js', ['jquery'], BDTUPK_VER, true);
		}

	}
}
