<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Bdt_Admin_Api_Biggopti_Registry', false ) ) {

	/**
	 * Cross-plugin singleton gate for the admin API biggopti module.
	 *
	 * Multiple plugins may ship this module; only the first successful claim
	 * becomes the active controller for the current request lifecycle.
	 */
	final class Bdt_Admin_Api_Biggopti_Registry {

		/** @var string|null */
		private static $controller_id = null;

		/**
		 * Attempt to claim the controller role for a plugin instance.
		 *
		 * @param string $plugin_id Stable plugin identifier (e.g. plugin basename).
		 * @return bool True when this instance becomes the controller.
		 */
		public static function claim( $plugin_id ) {
			if ( null !== self::$controller_id ) {
				return false;
			}

			self::$controller_id = sanitize_key( (string) $plugin_id );

			if ( ! defined( 'BDT_ADMIN_API_BIGGOPTI_ACTIVE' ) ) {
				define( 'BDT_ADMIN_API_BIGGOPTI_ACTIVE', self::$controller_id );
			}

			return true;
		}

		/**
		 * @return string|null
		 */
		public static function controller_id() {
			return self::$controller_id;
		}

		/**
		 * @return bool
		 */
		public static function is_controller( $plugin_id ) {
			return self::$controller_id === sanitize_key( (string) $plugin_id );
		}
	}
}
