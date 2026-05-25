<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Bdt_Admin_Api_Biggopti_Helper', false ) ) {

	/**
	 * Helper methods for admin-api-biggopti module.
	 */
	final class Bdt_Admin_Api_Biggopti_Helper {

		/** @var array<string, mixed>|null */
		private static $port = null;

		/**
		 * @return array<string, mixed>
		 */
		private static function port_config() {
			if ( null === self::$port ) {
				$config = include __DIR__ . '/config.php';
				self::$port = apply_filters(
					'bdt_admin_api_biggopti_port_config',
					is_array( $config ) ? $config : []
				);
			}

			return self::$port;
		}

		/**
		 * @param string|null $key
		 * @param mixed       $default
		 * @return mixed
		 */
		public static function port( $key = null, $default = null ) {
			$port = self::port_config();

			if ( null === $key ) {
				return $port;
			}

			return array_key_exists( $key, $port ) ? $port[ $key ] : $default;
		}

		/**
		 * @param string $key constants map key.
		 * @return mixed|null
		 */
		public static function constant( $key ) {
			$map  = self::port( 'constants', [] );
			$name = is_array( $map ) && isset( $map[ $key ] ) ? (string) $map[ $key ] : '';

			if ( '' === $name || ! defined( $name ) ) {
				return null;
			}

			return constant( $name );
		}

		public static function slug() {
			return (string) self::port( 'slug', '' );
		}

		public static function underscore() {
			return (string) self::port( 'underscore', '' );
		}

		public static function options_page() {
			return (string) self::port( 'options_page', '' );
		}

		public static function notice_class() {
			return (string) self::port( 'notice_class', '' );
		}

		public static function legacy_config_key() {
			return (string) self::port( 'legacy_config_key', '' );
		}

		public static function dismiss_action() {
			$underscore = self::underscore();

			return '' !== $underscore
				? $underscore . '_admin_api_biggopti_dismiss'
				: 'bdt_admin_api_biggopti_dismiss';
		}

		public static function nonce_action() {
			return (string) apply_filters( 'bdt_admin_api_biggopti_nonce_action', self::slug() );
		}

		public static function product_slug() {
			return self::slug();
		}

		/**
		 * Host plugin basename (folder/file.php).
		 *
		 * @return string
		 */
		public static function plugin_basename() {
			$from_const = self::constant( 'plugin_basename' );

			if ( is_string( $from_const ) && '' !== $from_const ) {
				return (string) $from_const;
			}

			$from_config = self::port( 'plugin_basename', '' );

			return is_string( $from_config ) ? $from_config : '';
		}

		/**
		 * Map host plugin basename to API list slug (white_list / black_list).
		 *
		 * @return array<string, string>
		 */
		public static function plugin_list_slug_map() {
			$defaults = [
				'bdthemes-element-pack-lite/bdthemes-element-pack-lite.php' => 'element-pack-free',
				'bdthemes-element-pack/bdthemes-element-pack.php'           => 'element-pack-pro',
				'bdthemes-prime-slider-lite/bdthemes-prime-slider.php'       => 'prime-slider-free',
				'bdthemes-prime-slider/bdthemes-prime-slider.php'           => 'prime-slider-pro',
				'ultimate-post-kit/ultimate-post-kit.php'                   => 'ultimate-post-kit-free',
				'ultimate-post-kit-pro/ultimate-post-kit-pro.php'           => 'ultimate-post-kit-pro',
				'ultimate-store-kit/ultimate-store-kit.php'                 => 'ultimate-store-kit-free',
				'ultimate-store-kit-pro/ultimate-store-kit-pro.php'         => 'ultimate-store-kit-pro',
				'pixel-gallery/pixel-gallery.php'                           => 'pixel-gallery-free',
				'pixel-gallery-pro/pixel-gallery-pro.php'                   => 'pixel-gallery-pro',
				'spin-wheel/spin-wheel.php'                                 => 'spin-wheel-free',
				'spin-wheel-pro/spin-wheel-pro.php'                         => 'spin-wheel-pro',
				'zoloblocks/zoloblocks.php'                                 => 'zoloblocks-free',
				'zoloblocks-pro/zoloblocks-pro.php'                         => 'zoloblocks-pro',
				'website-accessibility/website-accessibility.php'           => 'one-accessibility-free',
				'website-accessibility-pro/website-accessibility-pro.php'   => 'one-accessibility-pro',
				'sigmaforms-pro/sigmaforms-pro.php'                         => 'sigma-forms-pro',
				'sigma-media-manager/sigma-media-manager.php'               => 'sigma-media-manager-pro',
				'dark-reader/dark-reader.php'                               => 'dark-reader-free',
				'dark-reader-pro/dark-reader-pro.php'                       => 'dark-reader-pro',
				'launchguard-pro/launchguard-pro.php'                       => 'launch-guard-pro',
				'smart-admin-assistant/smart-admin-assistant.php'             => 'smart-admin-assistant-free',
				'smart-admin-assistant-pro/smart-admin-assistant-pro.php'     => 'smart-admin-assistant-pro',
				'sigma-store-locator/sigma-store-locator.php'               => 'sigma-store-locator-pro',
				'ai-image/ai-image.php'                                     => 'ai-image-free',
				'live-copy-paste/live-copy-paste.php'                       => 'live-copy-paste-free',
			];

			$from_config = self::port( 'plugin_list_slug_map', [] );
			$merged      = array_merge( $defaults, is_array( $from_config ) ? $from_config : [] );

			return apply_filters( 'bdt_admin_api_biggopti_plugin_list_slug_map', $merged );
		}

		/**
		 * @return bool
		 */
		public static function validate_zoloblocks_pro_license() {
			if ( ! class_exists( 'ZoloPro\\Admin\\License\\ZoloBlocksBase' ) ) {
				return false;
			}

			$info = \ZoloPro\Admin\License\ZoloBlocksBase::get_register_info();

			return is_object( $info ) && ! empty( $info->is_valid );
		}

		/**
		 * @return bool
		 */
		public static function validate_one_accessibility_pro_license() {
			if ( class_exists( 'bdthemes\\websiteaccessibilitypro\\Admin\\License' ) ) {
				$license = \bdthemes\websiteaccessibilitypro\Admin\License::get_instance();

				if ( is_object( $license ) && method_exists( $license, 'is_license_valid' ) ) {
					return true === $license->is_license_valid();
				}
			}

			if ( ! class_exists( 'bdthemes\\websiteaccessibilitypro\\Admin\\License\\LicenseHelper' ) ) {
				return false;
			}

			return true === \bdthemes\websiteaccessibilitypro\Admin\License\LicenseHelper::is_license_active();
		}

		/**
		 * Map Pro plugin basenames to license validation callbacks.
		 *
		 * Free plugins are omitted — they are not license-gated.
		 * Value may be a function name string or a callable array. Empty string = not mapped yet.
		 *
		 * @return array<string, string|array<int, string>|callable>
		 */
		public static function plugin_license_callback_map() {
			$defaults = [
				'bdthemes-element-pack/bdthemes-element-pack.php'           => 'bdt_license_validation',
				'bdthemes-prime-slider/bdthemes-prime-slider.php'           => 'ps_license_validation',
				'ultimate-post-kit-pro/ultimate-post-kit-pro.php'          => 'upk_license_validation',
				'ultimate-store-kit-pro/ultimate-store-kit-pro.php'         => 'usk_license_validation',
				'pixel-gallery-pro/pixel-gallery-pro.php'                   => 'pg_license_validation',
				'spin-wheel-pro/spin-wheel-pro.php'                         => [ 'SWP_Pro_Helper', 'sw_license_validation' ],
				'zoloblocks-pro/zoloblocks-pro.php'                         => [ __CLASS__, 'validate_zoloblocks_pro_license' ],
				'website-accessibility-pro/website-accessibility-pro.php'   => [ __CLASS__, 'validate_one_accessibility_pro_license' ],
				'sigmaforms-pro/sigmaforms-pro.php'                         => [ 'SigmaForms\\Plugin', 'is_license_valid' ],
				'sigma-media-manager/sigma-media-manager.php'                 => [ 'SMM_License_Helper', 'is_license_active' ],
				'dark-reader-pro/dark-reader-pro.php'                         => [ 'Bdthemes\\DarkReaderPro\\DarkReaderPro', 'is_license_valid' ],
				'launchguard-pro/launchguard-pro.php'                         => [ 'LaunchGuard', 'is_license_valid' ],
				'smart-admin-assistant-pro/smart-admin-assistant-pro.php'     => [ 'Bdthemes\\SmartAdminAssistantPro\\SmartAdminAssistantPro', 'is_license_valid' ],
				'sigma-store-locator/sigma-store-locator.php'                 => [ 'SSL_License_Helper', 'is_license_active' ],
			];

			$from_config = self::port( 'plugin_license_callback_map', [] );
			$merged      = array_merge( $defaults, is_array( $from_config ) ? $from_config : [] );

			return apply_filters( 'bdt_admin_api_biggopti_plugin_license_callback_map', $merged );
		}

		/**
		 * @param string|array<int, string>|callable|null $callback
		 * @return bool
		 */
		public static function is_license_callback_valid( $callback ) {
			if ( empty( $callback ) || ! is_callable( $callback ) ) {
				return false;
			}

			return true === call_user_func( $callback );
		}

		/**
		 * License callback for the current host plugin basename.
		 *
		 * @return string|array<int, string>|callable|null
		 */
		public static function host_license_callback() {
			$basename = self::plugin_basename();
			$map      = self::plugin_license_callback_map();

			if ( '' === $basename || ! isset( $map[ $basename ] ) ) {
				return null;
			}

			$callback = $map[ $basename ];

			return ! empty( $callback ) ? $callback : null;
		}

		/**
		 * Whether the current host plugin has a valid Pro license.
		 *
		 * @return bool
		 */
		public static function is_host_license_valid() {
			$callback = self::host_license_callback();

			if ( null === $callback ) {
				return (bool) apply_filters( 'bdt_admin_api_biggopti_is_host_license_valid', false, self::plugin_basename() );
			}

			return self::is_license_callback_valid( $callback );
		}

		/**
		 * Whether any mapped BdThemes product has a valid Pro license on this site.
		 *
		 * @return bool
		 */
		public static function has_any_valid_pro_license() {
			$seen     = [];
			$active   = false;

			foreach ( self::plugin_license_callback_map() as $callback ) {
				if ( empty( $callback ) || ! is_callable( $callback ) ) {
					continue;
				}

				$key = is_array( $callback ) ? implode( '::', $callback ) : (string) $callback;

				if ( isset( $seen[ $key ] ) ) {
					continue;
				}

				$seen[ $key ] = true;

				if ( self::is_license_callback_valid( $callback ) ) {
					$active = true;
					break;
				}
			}

			return (bool) apply_filters( 'bdt_admin_api_biggopti_has_any_valid_pro_license', $active );
		}

		/**
		 * API slug for white_list / black_list matching on the current host plugin.
		 *
		 * @return string
		 */
		public static function list_slug() {
			$override = self::port( 'list_slug', '' );

			if ( is_string( $override ) && '' !== trim( $override ) ) {
				return sanitize_title( $override );
			}

			$basename = self::plugin_basename();
			$map      = self::plugin_list_slug_map();

			if ( '' !== $basename && isset( $map[ $basename ] ) ) {
				return sanitize_title( (string) $map[ $basename ] );
			}

			return self::slug();
		}

		/**
		 * Whether a plugin basename is a mapped Pro product (license-gated).
		 *
		 * @param string $basename
		 * @return bool
		 */
		public static function is_pro_plugin_basename( $basename ) {
			$license_map = self::plugin_license_callback_map();

			return is_string( $basename )
				&& '' !== $basename
				&& isset( $license_map[ $basename ] )
				&& ! empty( $license_map[ $basename ] );
		}

		/**
		 * Whether a mapped plugin counts as active for black_list matching.
		 *
		 * Free plugins: WordPress active only.
		 * Pro plugins: WordPress active + valid license callback.
		 *
		 * @param string $basename Plugin basename from plugin_list_slug_map().
		 * @return bool
		 */
		public static function is_plugin_effective_for_black_list( $basename ) {
			if ( ! is_string( $basename ) || '' === $basename ) {
				return false;
			}

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( ! is_plugin_active( $basename ) ) {
				return false;
			}

			if ( ! self::is_pro_plugin_basename( $basename ) ) {
				return true;
			}

			$license_map = self::plugin_license_callback_map();
			$callback    = $license_map[ $basename ];

			return self::is_license_callback_valid( $callback );
		}

		/**
		 * API list slugs for mapped plugins that count as active on this site.
		 *
		 * Used so offer black_list hides promos when a blacklisted plugin is present:
		 * - Free: plugin is active in WordPress.
		 * - Pro: plugin is active in WordPress and license validation passes.
		 *
		 * @return string[]
		 */
		public static function active_list_slugs() {
			$map   = self::plugin_list_slug_map();
			$slugs = [];

			foreach ( $map as $basename => $list_slug ) {
				if ( ! self::is_plugin_effective_for_black_list( (string) $basename ) ) {
					continue;
				}

				$slug = sanitize_title( (string) $list_slug );

				if ( '' !== $slug ) {
					$slugs[] = $slug;
				}
			}

			$slugs = array_values( array_unique( $slugs ) );

			return apply_filters( 'bdt_admin_api_biggopti_active_list_slugs', $slugs );
		}

		public static function submenu_selector() {
			$page = self::options_page();

			return '' !== $page ? '#toplevel_page_' . $page . ' .wp-submenu' : '';
		}

		public static function plugin_id() {
			$from_const = self::constant( 'plugin_basename' );

			if ( $from_const ) {
				return sanitize_key( (string) $from_const );
			}

			return sanitize_key(
				(string) apply_filters(
					'bdt_admin_api_biggopti_plugin_id',
					(string) self::port( 'plugin_id', '' )
				)
			);
		}

		public static function is_pro() {
			if ( self::is_host_license_valid() ) {
				return true;
			}

			$callback = self::port( 'is_pro_callback', '' );

			if ( is_string( $callback ) && '' !== $callback && is_callable( $callback ) ) {
				return (bool) call_user_func( $callback );
			}

			return (bool) apply_filters( 'bdt_admin_api_biggopti_is_pro', false, self::plugin_id() );
		}

		/**
		 * @return array<string, string>
		 */
		public static function fallback_promo() {
			$promo = self::port( 'fallback_promo', [] );

			return is_array( $promo ) ? $promo : [];
		}

		public static function version() {
			$version = self::constant( 'version' );

			return $version ? (string) $version : '1.0.0';
		}

		public static function assets_url() {
			$url = self::constant( 'assets_url' );

			return $url ? (string) $url : '';
		}

		public static function module_url() {
			$admin_url = self::constant( 'admin_url' );

			if ( $admin_url ) {
				return trailingslashit( (string) $admin_url ) . 'admin-api-biggopti/';
			}

			return trailingslashit( plugin_dir_url( __FILE__ ) );
		}

		public static function is_plugin_dashboard() {
			$page = self::options_page();

			return '' !== $page
				&& isset( $_GET['page'] )
				&& $page === sanitize_text_field( wp_unslash( $_GET['page'] ) );
		}

		public static function api_url() {
			return (string) apply_filters(
				'bdt_admin_api_biggopti_api_url',
				(string) self::port( 'api_url', '' )
			);
		}

		public static function registry_key() {
			return (string) apply_filters(
				'bdt_admin_api_biggopti_registry_key',
				(string) self::port( 'registry_key', 'BdtAdminApiBiggopti' )
			);
		}

		public static function script_handle() {
			return (string) apply_filters(
				'bdt_admin_api_biggopti_script_handle',
				(string) self::port( 'script_handle', 'bdt-admin-api-biggopti' )
			);
		}

		public static function config_var() {
			return (string) apply_filters(
				'bdt_admin_api_biggopti_config_var',
				(string) self::port( 'config_var', 'BdtAdminApiBiggoptiConfig' )
			);
		}

		public static function current_sector() {
			return self::is_plugin_dashboard() ? 'plugin_dashboard' : '';
		}

		/**
		 * @return bool
		 */
		public static function use_test_api_response() {
			return (bool) self::port( 'use_test_api_response', false );
		}

		/**
		 * @return array<int|string, mixed>|null
		 */
		public static function get_test_api_response() {
			if ( ! self::use_test_api_response() ) {
				return null;
			}

			$file = (string) self::port( 'test_api_response_file', 'test-api-response.json' );
			$base = defined( 'BDT_ADMIN_API_BIGGOPTI_PATH' )
				? BDT_ADMIN_API_BIGGOPTI_PATH
				: trailingslashit( __DIR__ );
			$path = $base . ltrim( $file, '/' );

			if ( ! is_readable( $path ) ) {
				return null;
			}

			$raw = file_get_contents( $path );

			if ( false === $raw || '' === $raw ) {
				return null;
			}

			$data = json_decode( $raw, true );

			return is_array( $data ) ? $data : null;
		}

		/**
		 * Global WP option for site-wide author promo suppression.
		 *
		 * Stored globally; enforcement requires at least one registered Pro license to be active.
		 *
		 * @return string
		 */
		public static function global_author_suppression_option() {
			return (string) apply_filters( 'bdt_admin_api_biggopti_global_author_suppression_option', 'bdt_hide_promotional_offers' );
		}

		/**
		 * Registered Pro products that may save global author promo suppression.
		 *
		 * @return array<string, array<string, string>>
		 */
		public static function promo_suppression_sources() {
			$defaults = [
				'ultimate-post-kit' => [
					'product_slug'     => 'ultimate-post-kit',
					'license_callback' => 'upk_license_validation',
				],
				'prime-slider'      => [
					'product_slug'     => 'prime-slider',
					'license_callback' => 'ps_license_validation',
				],
			];

			$from_config = self::port( 'promo_suppression_sources', [] );
			$merged      = array_merge( $defaults, is_array( $from_config ) ? $from_config : [] );

			return apply_filters( 'bdt_admin_api_biggopti_promo_suppression_sources', $merged );
		}

		/**
		 * @param string $product_slug
		 * @return array<string, string>|null
		 */
		public static function get_promo_suppression_source( $product_slug ) {
			$product_slug = self::normalize_product_slug( (string) $product_slug );
			$sources      = self::promo_suppression_sources();

			return isset( $sources[ $product_slug ] ) && is_array( $sources[ $product_slug ] )
				? $sources[ $product_slug ]
				: null;
		}

		/**
		 * @param array<string, string> $source
		 * @return bool
		 */
		public static function is_promo_suppression_source_pro_active( $source ) {
			if ( ! is_array( $source ) ) {
				return false;
			}

			$license_callback = isset( $source['license_callback'] ) ? $source['license_callback'] : '';

			if ( ! empty( $license_callback ) ) {
				return self::is_license_callback_valid( $license_callback );
			}

			$pro_callback = isset( $source['pro_active_callback'] ) ? (string) $source['pro_active_callback'] : '';

			if ( '' !== $pro_callback && function_exists( $pro_callback ) ) {
				return (bool) call_user_func( $pro_callback );
			}

			return false;
		}

		/**
		 * Whether any registered Pro product has an active license (author hide gate).
		 *
		 * @return bool
		 */
		public static function has_any_promo_suppression_pro_active() {
			return self::has_any_valid_pro_license();
		}

		/**
		 * @return array<string, string>
		 */
		public static function promo_duration_labels() {
			$labels = [
				''         => __( "Don't hide", 'bdthemes-prime-slider' ),
				'1_week'   => __( '1 week', 'bdthemes-prime-slider' ),
				'1_month'  => __( '1 month', 'bdthemes-prime-slider' ),
				'6_month'  => __( '6 months', 'bdthemes-prime-slider' ),
				'1_year'   => __( '1 year', 'bdthemes-prime-slider' ),
				'lifetime' => __( 'Lifetime', 'bdthemes-prime-slider' ),
			];

			return apply_filters( 'bdt_admin_api_biggopti_promo_duration_labels', $labels );
		}

		/**
		 * @param string $duration
		 * @return int Seconds, 0 for lifetime, -1 if invalid.
		 */
		public static function promo_duration_to_seconds( $duration ) {
			$durations = [
				'1_week'   => WEEK_IN_SECONDS,
				'1_month'  => MONTH_IN_SECONDS,
				'6_month'  => 6 * MONTH_IN_SECONDS,
				'1_year'   => YEAR_IN_SECONDS,
				'lifetime' => 0,
			];

			$duration = sanitize_key( (string) $duration );

			return isset( $durations[ $duration ] ) ? (int) $durations[ $duration ] : -1;
		}

		/**
		 * @param array<string, mixed> $data
		 * @return bool
		 */
		public static function is_promo_suppression_entry_active( $data ) {
			if ( ! is_array( $data ) || empty( $data['duration'] ) ) {
				return false;
			}

			$expires_at = isset( $data['expires_at'] ) ? (int) $data['expires_at'] : 0;

			if ( 0 === $expires_at ) {
				return 'lifetime' === $data['duration'];
			}

			return time() < $expires_at;
		}

		/**
		 * @param array<string, mixed> $data
		 * @return array<string, mixed>
		 */
		public static function normalize_promo_suppression_entry( $data ) {
			if ( ! self::is_promo_suppression_entry_active( $data ) ) {
				return [];
			}

			return $data;
		}

		/**
		 * Read stored suppression (UI / persistence), regardless of Pro status.
		 *
		 * @param string $option_name
		 * @return array<string, mixed>
		 */
		public static function get_promo_suppression_stored( $option_name ) {
			self::maybe_migrate_legacy_promo_suppression_options();

			$option_name = sanitize_key( (string) $option_name );

			if ( '' === $option_name ) {
				return [];
			}

			$data = get_option( $option_name, [] );

			if ( ! is_array( $data ) ) {
				return [];
			}

			$data = self::normalize_promo_suppression_entry( $data );

			if ( empty( $data ) ) {
				delete_option( $option_name );
			}

			return $data;
		}

		/**
		 * @param string $product_slug Unused; kept for callers that pass a product context.
		 * @return array<string, mixed>
		 */
		public static function get_promo_author_suppression( $product_slug = '' ) {
			unset( $product_slug );

			return self::get_promo_suppression_stored( self::global_author_suppression_option() );
		}

		/**
		 * @param string $product_slug Unused; kept for callers that pass a product context.
		 * @return bool
		 */
		public static function is_promo_author_suppression_effective( $product_slug = '' ) {
			unset( $product_slug );

			return ! empty( self::get_promo_author_suppression() );
		}

		/**
		 * Save global hide-all-author-offers preference (no Pro license required).
		 *
		 * @param bool $hidden
		 * @return bool
		 */
		public static function set_promo_author_suppression_hidden( $hidden ) {
			return self::save_promo_suppression_option(
				self::global_author_suppression_option(),
				$hidden ? 'lifetime' : ''
			);
		}

		/**
		 * @param string $option_name
		 * @param string $duration
		 * @return bool
		 */
		public static function save_promo_suppression_option( $option_name, $duration ) {
			$option_name = sanitize_key( (string) $option_name );
			$duration    = sanitize_key( (string) $duration );

			if ( '' === $option_name ) {
				return false;
			}

			if ( '' === $duration ) {
				delete_option( $option_name );
				return true;
			}

			$seconds = self::promo_duration_to_seconds( $duration );

			if ( $seconds < 0 ) {
				return false;
			}

			return update_option(
				$option_name,
				[
					'duration'   => $duration,
					'enabled_at' => time(),
					'expires_at' => 0 === $seconds ? 0 : time() + $seconds,
				],
				false
			);
		}

		/**
		 * @param string $product_slug Product saving the global author preference (requires its Pro license).
		 * @param string $duration
		 * @return bool
		 */
		public static function save_promo_author_suppression( $product_slug, $duration ) {
			$source = self::get_promo_suppression_source( $product_slug );

			if ( ! $source || ! self::is_promo_suppression_source_pro_active( $source ) ) {
				return false;
			}

			return self::save_promo_suppression_option( self::global_author_suppression_option(), $duration );
		}

		/**
		 * Whether any mapped BdThemes Pro license allows saving author promo suppression.
		 *
		 * @return bool
		 */
		public static function can_save_promo_author_suppression() {
			return self::has_any_valid_pro_license();
		}

		/**
		 * Save global author promo suppression when any mapped Pro license is active.
		 *
		 * @param string $duration
		 * @return bool
		 */
		public static function save_global_promo_author_suppression( $duration ) {
			if ( ! self::can_save_promo_author_suppression() ) {
				return false;
			}

			return self::save_promo_suppression_option( self::global_author_suppression_option(), $duration );
		}

		/**
		 * @param array<string, mixed> $candidate
		 * @param array<string, mixed> $current
		 * @return bool
		 */
		protected static function is_promo_suppression_preferred( $candidate, $current ) {
			if ( empty( $current ) ) {
				return ! empty( $candidate );
			}

			if ( empty( $candidate ) ) {
				return false;
			}

			$candidate_expires = isset( $candidate['expires_at'] ) ? (int) $candidate['expires_at'] : 0;
			$current_expires   = isset( $current['expires_at'] ) ? (int) $current['expires_at'] : 0;

			if ( 0 === $candidate_expires && 0 !== $current_expires ) {
				return true;
			}

			if ( 0 !== $candidate_expires && 0 === $current_expires ) {
				return false;
			}

			return $candidate_expires > $current_expires;
		}

		/**
		 * Merge legacy per-plugin author keys into the global author option.
		 *
		 * @return void
		 */
		public static function maybe_migrate_legacy_promo_suppression_options() {
			static $done = false;

			if ( $done ) {
				return;
			}

			$done = true;

			$global_key   = self::global_author_suppression_option();
			$global_raw   = get_option( $global_key, [] );
			$global_entry = is_array( $global_raw ) && self::is_promo_suppression_entry_active( $global_raw )
				? self::normalize_promo_suppression_entry( $global_raw )
				: [];
			$best_author  = $global_entry;

			$legacy_author_keys = [
				'promo_hide_author_offers',
				'upk_promo_hide_author_offers',
				'ps_promo_hide_author_offers',
			];

			$legacy_author_keys = array_values( array_unique( array_filter( $legacy_author_keys ) ) );

			foreach ( $legacy_author_keys as $legacy_key ) {
				if ( $legacy_key === $global_key ) {
					continue;
				}

				$legacy_raw = get_option( $legacy_key, null );

				if ( null === $legacy_raw ) {
					continue;
				}

				if ( is_array( $legacy_raw ) && self::is_promo_suppression_entry_active( $legacy_raw ) ) {
					$legacy_entry = self::normalize_promo_suppression_entry( $legacy_raw );

					if ( self::is_promo_suppression_preferred( $legacy_entry, $best_author ) ) {
						$best_author = $legacy_entry;
					}
				}

				delete_option( $legacy_key );
			}

			if ( ! empty( $best_author ) && self::is_promo_suppression_preferred( $best_author, $global_entry ) ) {
				update_option( $global_key, $best_author, false );
			}

			$legacy_plugin_keys = [
				'upk_promo_hide_plugin_offers',
				'ps_promo_hide_plugin_offers',
			];

			foreach ( $legacy_plugin_keys as $legacy_plugin_key ) {
				delete_option( $legacy_plugin_key );
			}

			delete_option( 'promo_hide_plugin_offers' );
		}

		/**
		 * Site-wide author promo suppression flag for JS (no Pro license required).
		 *
		 * @return array{hideAuthorOffers: bool}
		 */
		public static function get_promo_suppression_config() {
			$config = [
				'hideAuthorOffers' => self::is_promo_author_suppression_effective(),
			];

			$filtered = apply_filters( 'bdt_admin_api_biggopti_promo_suppression', $config );

			if ( ! is_array( $filtered ) ) {
				return $config;
			}

			$config['hideAuthorOffers'] = ! empty( $filtered['hideAuthorOffers'] );

			return $config;
		}

		/**
		 * @param string $plugin_id
		 * @return array<string, mixed>
		 */
		public static function get_script_config( $plugin_id = '' ) {
			if ( '' === $plugin_id ) {
				$plugin_id = self::plugin_id();
			}

			$config = [
				'pluginId'                => $plugin_id,
				'isController'            => true,
				'configVar'               => self::config_var(),
				'registryKey'             => self::registry_key(),
				'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
				'nonce'                   => wp_create_nonce( self::nonce_action() ),
				'isPro'                   => self::is_pro(),
				'assetsUrl'               => self::assets_url(),
				'dismissedDisplayIds'     => self::get_dismissed_display_ids(),
				'currentSector'           => self::current_sector(),
				'apiUrl'                  => self::api_url(),
				'productSlug'             => self::product_slug(),
				'listSlug'                => self::list_slug(),
				'activeListSlugs'         => self::active_list_slugs(),
				'displayIdPrefix'         => self::display_id_prefix(),
				'legacyDisplayIdPrefixes' => self::legacy_display_id_prefixes(),
				'pluginOptionsPage'       => self::options_page(),
				'submenuSelector'         => self::submenu_selector(),
				'dismissAction'           => self::dismiss_action(),
				'noticeClass'             => self::notice_class(),
				'legacyConfigKey'         => self::legacy_config_key(),
				'fallbackPromo'           => self::fallback_promo(),
				'promoSuppression'        => self::get_promo_suppression_config(),
				'testApiData'             => self::get_test_api_response(),
				'useTestApiResponse'      => self::use_test_api_response(),
			];

			return apply_filters( 'bdt_admin_api_biggopti_script_config', $config, $plugin_id );
		}

		/**
		 * @param string $key
		 * @return string[]
		 */
		private static function port_list( $key ) {
			return array_values(
				array_unique(
					array_filter(
						(array) self::port( $key, [] )
					)
				)
			);
		}

		public static function display_id_prefix() {
			return (string) apply_filters(
				'bdt_admin_api_biggopti_display_id_prefix',
				'bdt-admin-biggopti-api-biggopti-'
			);
		}

		public static function dismissals_option() {
			return (string) apply_filters(
				'bdt_admin_api_biggopti_dismissals_option',
				'bdt_biggopti_dismissals'
			);
		}

		/**
		 * Legacy DOM / storage prefixes from older BiggOpt plugins.
		 *
		 * @return string[]
		 */
		public static function legacy_display_id_prefixes() {
			return array_values(
				array_unique(
					array_filter(
						(array) apply_filters(
							'bdt_admin_api_biggopti_legacy_display_id_prefixes',
							self::port_list( 'legacy_display_id_prefixes' )
						)
					)
				)
			);
		}

		/**
		 * @return string[]
		 */
		public static function all_display_id_prefixes() {
			$prefixes = array_merge(
				[ self::display_id_prefix() ],
				self::legacy_display_id_prefixes()
			);

			return array_values( array_unique( array_filter( $prefixes ) ) );
		}

		/**
		 * Normalize any stored or posted offer key to bare display_id.
		 *
		 * @param string $key Raw display id or DOM id.
		 * @return string
		 */
		public static function normalize_display_id( $key ) {
			$key = is_string( $key ) ? trim( $key ) : '';

			if ( '' === $key ) {
				return '';
			}

			foreach ( self::all_display_id_prefixes() as $prefix ) {
				if ( '' !== $prefix && strpos( $key, $prefix ) === 0 ) {
					return substr( $key, strlen( $prefix ) );
				}
			}

			return $key;
		}

		/**
		 * @return string[]
		 */
		public static function dismiss_nonce_actions() {
			$actions = array_merge(
				[ self::nonce_action() ],
				self::port_list( 'legacy_dismiss_nonce_actions' )
			);

			return array_values(
				array_unique(
					array_filter(
						(array) apply_filters(
							'bdt_admin_api_biggopti_dismiss_nonce_actions',
							$actions
						)
					)
				)
			);
		}

		/**
		 * @return string[]
		 */
		public static function dismiss_ajax_actions() {
			$actions = array_merge(
				[
					self::dismiss_action(),
					'bdt_admin_api_biggopti_dismiss',
				],
				self::port_list( 'legacy_dismiss_ajax_actions' )
			);

			return array_values(
				array_unique(
					array_filter(
						(array) apply_filters(
							'bdt_admin_api_biggopti_dismiss_ajax_actions',
							$actions
						)
					)
				)
			);
		}

		/**
		 * @return string[]
		 */
		public static function legacy_script_handles() {
			return array_values(
				array_unique(
					array_filter(
						(array) apply_filters(
							'bdt_admin_api_biggopti_legacy_script_handles',
							self::port_list( 'legacy_script_handles' )
						)
					)
				)
			);
		}

		/**
		 * @return string[]
		 */
		public static function legacy_style_handles() {
			return array_values(
				array_unique(
					array_filter(
						(array) apply_filters(
							'bdt_admin_api_biggopti_legacy_style_handles',
							self::port_list( 'legacy_style_handles' )
						)
					)
				)
			);
		}

		/**
		 * @param string $nonce
		 * @return bool
		 */
		public static function verify_dismiss_nonce( $nonce ) {
			if ( ! is_string( $nonce ) || '' === $nonce ) {
				return false;
			}

			foreach ( self::dismiss_nonce_actions() as $action ) {
				if ( wp_verify_nonce( $nonce, $action ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * @return array<string, mixed>
		 */
		public static function get_dismissals_store() {
			$store = get_option( self::dismissals_option(), [] );

			return is_array( $store ) ? $store : [];
		}

		/**
		 * All dismissed offer ids stored in the options table.
		 *
		 * @return string[]
		 */
		public static function get_dismissed_display_ids() {
			$dismissed_display_ids = [];

			foreach ( array_keys( self::get_dismissals_store() ) as $key ) {
				if ( ! is_string( $key ) || '' === $key ) {
					continue;
				}

				$normalized = self::normalize_display_id( $key );

				if ( '' !== $normalized ) {
					$dismissed_display_ids[] = $normalized;
				}
			}

			return array_values( array_unique( array_filter( $dismissed_display_ids ) ) );
		}

		/**
		 * Persist a dismissed offer id in the options table.
		 *
		 * @param string $display_id
		 * @return bool
		 */
		public static function save_dismissed_display_id( $display_id ) {
			$display_id = self::normalize_display_id( $display_id );

			if ( '' === $display_id ) {
				return false;
			}

			$dismissals_option                 = self::get_dismissals_store();
			$dismissals_option[ $display_id ] = [
				'dismissed_at' => time(),
			];

			update_option( self::dismissals_option(), $dismissals_option, false );

			return true;
		}

		/**
		 * @param string $display_id
		 * @return bool
		 */
		public static function is_display_id_dismissed( $display_id ) {
			$display_id = self::normalize_display_id( $display_id );

			if ( '' === $display_id ) {
				return false;
			}

			$store = self::get_dismissals_store();

			if ( isset( $store[ $display_id ] ) ) {
				return true;
			}

			foreach ( self::all_display_id_prefixes() as $prefix ) {
				if ( '' !== $prefix && isset( $store[ $prefix . $display_id ] ) ) {
					return true;
				}
			}

			return false;
		}

		public static function dequeue_legacy_assets() {
			foreach ( self::legacy_script_handles() as $handle ) {
				wp_dequeue_script( $handle );
				wp_deregister_script( $handle );
			}

			foreach ( self::legacy_style_handles() as $handle ) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
		}

		/**
		 * Normalize a product slug (strip -lite / -pro suffixes).
		 *
		 * @param string $slug Raw slug or folder name.
		 * @return string
		 */
		public static function normalize_product_slug( $slug ) {
			$slug = sanitize_title( (string) $slug );

			if ( strlen( $slug ) > 5 && '-lite' === substr( $slug, -5 ) ) {
				$slug = substr( $slug, 0, -5 );
			}

			if ( strlen( $slug ) > 4 && '-pro' === substr( $slug, -4 ) ) {
				$slug = substr( $slug, 0, -4 );
			}

			return $slug;
		}
	}
}
