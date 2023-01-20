<?php

namespace UltimatePostKit;

use Elementor\Plugin;
use UltimatePostKit\Admin;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

/**
 * Main class for element pack
 */
class Ultimate_Post_Kit_Loader {

	/**
	 * @var Ultimate_Post_Kit_Loader
	 */
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_modules_manager;

	private $classes_aliases;

	public $elements_data = [
		'sections' => [],
		'columns'  => [],
		'widgets'  => [],
	];

	/**
	 * @return string
	 * @deprecated
	 *
	 */
	public function get_version() {
		return BDTUPK_VER;
	}

	/**
	 * return active theme
	 */
	public function get_theme() {
		return wp_get_theme();
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&#8217; huh?', 'ultimate-post-kit'), '1.6.0');
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&#8217; huh?', 'ultimate-post-kit'), '1.6.0');
	}

	/**
	 * @return Plugin
	 */

	public static function elementor() {
		return Plugin::$instance;
	}

	/**
	 * @return Ultimate_Post_Kit_Loader
	 */
	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		do_action('bdthemes_ultimate_post_kit/init');
		return self::$_instance;
	}


	/**
	 * we loaded module manager + admin php from here
	 * @return [type] [description]
	 */
	private function _includes() {
		$category_image = ultimate_post_kit_option('category_image', 'ultimate_post_kit_other_settings', 'off');
		$duplicator = ultimate_post_kit_option('duplicator', 'ultimate_post_kit_other_settings', 'off');
		$live_copy = ultimate_post_kit_option('live-copy', 'ultimate_post_kit_other_settings', 'off');


		// Admin settings controller
		require_once BDTUPK_ADMIN_PATH . 'module-settings.php';

		// Dynamic Select control
		require BDTUPK_INC_PATH . 'controls/select-input/dynamic-select-input-module.php';
		require BDTUPK_INC_PATH . 'controls/select-input/dynamic-select.php';

		//require BDTUPK_PATH . 'base/ultimate-post-kit-base.php';

		// all widgets control from here
		require_once BDTUPK_PATH . 'traits/global-widget-controls.php';
		require_once BDTUPK_PATH . 'traits/global-swiper-functions.php';
		require_once BDTUPK_INC_PATH . 'modules-manager.php';

		if ($category_image == 'on') {
			require BDTUPK_INC_PATH . 'ultimate-post-kit-category-image.php';
		}
		// if ($category_image == 'on') {
		require BDTUPK_INC_PATH . 'ultimate-post-kit-metabox.php';
		// }

		if (!class_exists('BdThemes_Duplicator')) {
			if ($duplicator == 'on') {
				require BDTUPK_PATH . 'includes/class-duplicator.php';
			}
		}

		if (!class_exists('BdThemes_Live_Copy')) {
			if (($live_copy == 'on') && (!is_plugin_active('live-copy-paste/live-copy-paste.php'))) {
				require_once BDTUPK_PATH . 'includes/live-copy/class-live-copy.php';
			}
		}

		if (is_admin()) {
			if (!defined('BDTUPK_CH')) {
				// Notice class
				require_once BDTUPK_ADMIN_PATH . 'admin-notice.php';
				require_once BDTUPK_ADMIN_PATH . 'admin.php';

				// Load admin class for admin related content process
				new Admin();
			}
		}
	}

	/**
	 * Autoloader function for all classes files
	 *
	 * @param  [type] class [description]
	 *
	 * @return [type]        [description]
	 */
	public function autoload($class) {
		if (0 !== strpos($class, __NAMESPACE__)) {
			return;
		}

		$class_to_load = $class;

		if (!class_exists($class_to_load)) {
			$filename = strtolower(
				preg_replace(
					['/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/'],
					['', '$1-$2', '-', DIRECTORY_SEPARATOR],
					$class_to_load
				)
			);
			$filename = BDTUPK_PATH . $filename . '.php';

			if (is_readable($filename)) {
				include($filename);
			}
		}
	}

	public function register_site_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_register_style('upk-all-styles', BDTUPK_ASSETS_URL . 'css/upk-all-styles' . $direction_suffix . '.css', [], BDTUPK_VER);
		wp_register_style('upk-font', BDTUPK_ASSETS_URL . 'css/upk-font' . $direction_suffix . '.css', [], BDTUPK_VER);
	}

	public function register_site_scripts() {

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script('goodshare', BDTUPK_ASSETS_URL . 'vendor/js/goodshare' . $suffix . '.js', ['jquery'], '4.1.2', true);
		wp_register_script('scrolline', BDTUPK_ASSETS_URL . 'vendor/js/jquery.scrolline' . $suffix . '.js', ['jquery'], '4.1.2', true);
		wp_register_script('news-ticker-js', BDTUPK_ASSETS_URL . 'vendor/js/newsticker' . $suffix . '.js', ['jquery'], '', true);
		wp_register_script('upk-animations', BDTUPK_ASSETS_URL . 'js/extensions/upk-animations' . $suffix . '.js', ['jquery'], '', true);

		wp_register_script('upk-all-scripts', BDTUPK_ASSETS_URL . 'js/upk-all-scripts' . $suffix . '.js', [
			'jquery',
			'elementor-frontend',
			'scrolline'
		], BDTUPK_VER, true);
	}


	/**
	 * Loading site related style from here.
	 * @return [type] [description]
	 */
	public function enqueue_site_styles() {

		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_enqueue_style('upk-site', BDTUPK_ASSETS_URL . 'css/upk-site' . $direction_suffix . '.css', [], BDTUPK_VER);
	}


	/**
	 * Loading site related script that needs all time such as uikit.
	 * @return [type] [description]
	 */
	public function enqueue_site_scripts() {

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';


		wp_enqueue_script('upk-site', BDTUPK_ASSETS_URL . 'js/upk-site' . $suffix . '.js', [
			'jquery',
			'elementor-frontend'
		], BDTUPK_VER, true); // tooltip file should be separate

		$script_config = [
			'ajaxurl'       => admin_url('admin-ajax.php'),
			'nonce'         => wp_create_nonce('upk-site'),
			'mailchimp'     => [
				'subscribing' => esc_html_x('Subscribing you please wait...', 'Mailchimp String', 'ultimate-post-kit'),
			],
			'elements_data' => $this->elements_data,
		];


		$script_config = apply_filters('ultimate_post_kit/frontend/localize_settings', $script_config);

		// TODO for editor script
		wp_localize_script('upk-site', 'UltimatePostKitConfig', $script_config);
	}

	public function enqueue_editor_scripts() {

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script('upk-editor', BDTUPK_ASSETS_URL . 'js/upk-editor' . $suffix . '.js', [
			'backbone-marionette',
			'elementor-common-modules',
			'elementor-editor-modules',
		], BDTUPK_VER, true);

		$_is_upk_pro_activated = false;
		if (function_exists('upk_license_validation') && true === upk_license_validation()) {
			$_is_upk_pro_activated = true;
		}

		$localize_data = [
			'pro_installed'  => _is_upk_pro_activated(),
			'pro_license_activated'  => $_is_upk_pro_activated,
			'promotional_widgets'   => [],
		];

		if (!$_is_upk_pro_activated) {
			$pro_widget_map = new \UltimatePostKit\Includes\Pro_Widget_Map();
			$localize_data['promotional_widgets'] = $pro_widget_map->get_pro_widget_map();
		}

		wp_localize_script('upk-editor', 'UltimatePostKitConfigEditor', $localize_data);
	}

	/**
	 * Load editor editor related style from here
	 * @return [type] [description]
	 */
	public function enqueue_preview_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_enqueue_style('upk-preview', BDTUPK_ASSETS_URL . 'css/upk-preview' . $direction_suffix . '.css', '', BDTUPK_VER);
	}


	public function enqueue_editor_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_enqueue_style('upk-editor', BDTUPK_ASSETS_URL . 'css/upk-editor' . $direction_suffix . '.css', '', BDTUPK_VER);
		wp_enqueue_style('upk-font', BDTUPK_URL . 'assets/css/upk-font' . $direction_suffix . '.css', [], BDTUPK_VER);
	}

	/**
	 * initialize the category
	 * @return [type] [description]
	 */
	public function ultimate_post_kit_init() {
		$this->_modules_manager = new Manager();
	}

	/**
	 * initialize the category
	 * @return [type] [description]
	 */
	public function ultimate_post_kit_category_register() {

		$elementor = Plugin::$instance;

		// Add element category in panel
		$elementor->elements_manager->add_category(BDTUPK_SLUG, ['title' => BDTUPK_TITLE, 'icon' => 'font']);
	}


	private function setup_hooks() {
		add_action('elementor/elements/categories_registered', [$this, 'ultimate_post_kit_category_register']);
		add_action('elementor/init', [$this, 'ultimate_post_kit_init']);

		add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);

		add_action('elementor/frontend/before_register_styles', [$this, 'register_site_styles']);
		add_action('elementor/frontend/before_register_scripts', [$this, 'register_site_scripts']);

		add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_preview_styles']);
		// add_action('elementor/editor/before_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
		add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);

		add_action('elementor/frontend/after_register_styles', [$this, 'enqueue_site_styles']);
		add_action('elementor/frontend/before_enqueue_scripts', [$this, 'enqueue_site_scripts']);
	}

	/**
	 * Ultimate_Post_Kit_Loader constructor.
	 */
	private function __construct() {
		// Register class automatically
		spl_autoload_register([$this, 'autoload']);
		// Include some backend files
		$this->_includes();

		// Finally hooked up all things here
		$this->setup_hooks();
	}
}

if (!defined('BDTUPK_TESTS')) {
	// In tests we run the instance manually.
	Ultimate_Post_Kit_Loader::instance();
}

// handy fundtion for push data
function ultimate_post_kit_config() {
	return Ultimate_Post_Kit_Loader::instance();
}
