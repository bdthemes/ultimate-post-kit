<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Bdt_Admin_Api_Biggopti', false ) ) {

	/**
	 * Global admin API biggopti module controller.
	 */
	final class Bdt_Admin_Api_Biggopti {

		/** @var self|null */
		private static $instance = null;

		/** @var string */
		private $plugin_id = '';

		/**
		 * Bootstrap the module from index.php.
		 *
		 * @param string $module_dir Absolute path to admin-api-biggopti/.
		 * @return void
		 */
		public static function boot( $module_dir ) {
			if ( ! is_admin() ) {
				return;
			}

			self::define_constants( $module_dir );

			$plugin_id = \Bdt_Admin_Api_Biggopti_Helper::plugin_id();

			if ( ! \Bdt_Admin_Api_Biggopti_Registry::claim( $plugin_id ) ) {
				return;
			}

			self::get_instance();
		}

		/**
		 * @param string $module_dir
		 * @return void
		 */
		private static function define_constants( $module_dir ) {
			if ( ! defined( 'BDT_ADMIN_API_BIGGOPTI_PATH' ) ) {
				define( 'BDT_ADMIN_API_BIGGOPTI_PATH', trailingslashit( $module_dir ) );
			}

			if ( ! defined( 'BDT_ADMIN_API_BIGGOPTI_URL' ) ) {
				define( 'BDT_ADMIN_API_BIGGOPTI_URL', \Bdt_Admin_Api_Biggopti_Helper::module_url() );
			}
		}

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			$this->plugin_id = \Bdt_Admin_Api_Biggopti_Helper::plugin_id();

			foreach ( \Bdt_Admin_Api_Biggopti_Helper::dismiss_ajax_actions() as $ajax_action ) {
				add_action( 'wp_ajax_' . $ajax_action, [ $this, 'dismiss_offer' ] );
			}

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );
			add_action( 'admin_enqueue_scripts', [ $this, 'dequeue_legacy_assets' ], 9999 );
		}

		public function dequeue_legacy_assets() {
			if ( ! \Bdt_Admin_Api_Biggopti_Registry::is_controller( $this->plugin_id ) ) {
				return;
			}

			\Bdt_Admin_Api_Biggopti_Helper::dequeue_legacy_assets();
		}

		public function enqueue_assets() {
			if ( ! \Bdt_Admin_Api_Biggopti_Registry::is_controller( $this->plugin_id ) ) {
				return;
			}

			$handle = \Bdt_Admin_Api_Biggopti_Helper::script_handle();

			wp_enqueue_style(
				$handle,
				BDT_ADMIN_API_BIGGOPTI_URL . 'style.css',
				[],
				\Bdt_Admin_Api_Biggopti_Helper::version()
			);

			wp_enqueue_script(
				$handle,
				BDT_ADMIN_API_BIGGOPTI_URL . 'script.js',
				[ 'jquery' ],
				\Bdt_Admin_Api_Biggopti_Helper::version(),
				true
			);

			$config_var = \Bdt_Admin_Api_Biggopti_Helper::config_var();

			wp_add_inline_script(
				$handle,
				'window.BdtAdminApiBiggoptiBootstrap=' . wp_json_encode(
					[
						'configVar' => $config_var,
					]
				) . ';',
				'before'
			);

			$config = \Bdt_Admin_Api_Biggopti_Helper::get_script_config( $this->plugin_id );

			wp_localize_script( $handle, $config_var, $config );

			$legacy_key = \Bdt_Admin_Api_Biggopti_Helper::legacy_config_key();
			if ( '' !== $legacy_key ) {
				wp_localize_script( $handle, $legacy_key, $config );
			}
		}

		public function dismiss_offer() {
			$nonce      = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
			$display_id = isset( $_POST['display_id'] ) ? sanitize_text_field( wp_unslash( $_POST['display_id'] ) ) : '';
			$id         = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

			if ( ! \Bdt_Admin_Api_Biggopti_Helper::verify_dismiss_nonce( $nonce ) ) {
				wp_send_json_error();
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error();
			}

			$raw_display_id = '' !== $display_id ? $display_id : $id;
			$display_id     = \Bdt_Admin_Api_Biggopti_Helper::normalize_display_id( $raw_display_id );

			if ( '' === $display_id ) {
				wp_send_json_error();
			}

			if ( ! \Bdt_Admin_Api_Biggopti_Helper::save_dismissed_display_id( $display_id ) ) {
				wp_send_json_error();
			}

			wp_send_json_success(
				[
					'display_id' => $display_id,
					'dismissed'  => true,
				]
			);
		}
	}
}
