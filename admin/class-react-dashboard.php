<?php
/**
 * Ultimate Post Kit — React admin dashboard.
 *
 * Replaces the body of the Ultimate Post Kit dashboard page with a React app
 * built from src/admin/app (output in build/admin), laid out like the Element
 * Pack and Prime Slider dashboards: app bar, tab strip, cards.
 *
 * Nothing new is exposed to the network: settings are saved through the
 * existing nonce- and capability-checked `ultimate_post_kit_settings_save`
 * AJAX action, and every value the app shows is passed in once, on page load.
 *
 * Panels that add-ons render in PHP — the tabs registered through
 * `ultimate_post_kit_dashboard_extra_tabs`, the `upk_license_page` hook and
 * the Rollback Version page (Ultimate Post Kit Pro: White Label, Custom CSS &
 * JS, License, Rollback) — are still rendered here, hidden, and moved into the
 * app's matching route, so their own markup, scripts and forms keep working
 * unchanged. An add-on can instead hand the app the data for a native page
 * through `ultimate_post_kit/admin/react_pages`. Other Plugins is rendered by
 * the app itself, from the existing `upk_get_plugins` and `upk_install_plugin`
 * AJAX actions.
 *
 * When the build is missing, or the `ultimate_post_kit/admin/react_dashboard`
 * filter returns false, the classic dashboard renders instead.
 *
 * @package UltimatePostKit
 */

use UltimatePostKit\Admin\ModuleService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UltimatePostKit_React_Dashboard {

	const HANDLE  = 'upk-react-admin';
	const PAGE_ID = 'ultimate_post_kit_options';

	/**
	 * On/off settings sections the app manages, in tab order.
	 */
	const SECTIONS = [
		'ultimate_post_kit_active_modules',
		'ultimate_post_kit_elementor_extend',
		'ultimate_post_kit_other_settings',
	];

	/**
	 * The API keys section (text fields grouped per service).
	 */
	const API_SECTION = 'ultimate_post_kit_api_settings';

	/**
	 * Whether the React dashboard renders on this request.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$built = is_readable( BDTUPK_PATH . 'build/admin/index.js' )
			&& is_readable( BDTUPK_PATH . 'build/admin/index.asset.php' );

		/**
		 * Filters whether the React dashboard replaces the classic one.
		 *
		 * @param bool $enabled Whether the built app is available.
		 */
		return (bool) apply_filters( 'ultimate_post_kit/admin/react_dashboard', $built );
	}

	/**
	 * The classic settings page, which renders the PHP panels.
	 *
	 * @var UltimatePostKit_Admin_Settings
	 */
	private $settings;

	/**
	 * @param UltimatePostKit_Admin_Settings $settings The classic settings page.
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );
	}

	/**
	 * Whether the current admin screen is the Ultimate Post Kit dashboard.
	 *
	 * @param string $hook_suffix Admin page hook suffix.
	 * @return bool
	 */
	private function is_dashboard_screen( $hook_suffix = '' ) {
		if ( 'toplevel_page_' . self::PAGE_ID === $hook_suffix ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only picks which assets load.
		return is_admin() && isset( $_GET['page'] ) && self::PAGE_ID === sanitize_key( wp_unslash( $_GET['page'] ) );
	}

	/**
	 * `admin_body_class`: full-bleed layout hooks for the dashboard page.
	 *
	 * @param string $classes Body classes.
	 * @return string
	 */
	public function body_class( $classes ) {
		if ( $this->is_dashboard_screen() && self::is_enabled() ) {
			$classes .= ' upk-react-admin';
		}

		return $classes;
	}

	/**
	 * `admin_enqueue_scripts`: the app bundle and its bootstrap data.
	 *
	 * @param string $hook_suffix Admin page hook suffix.
	 * @return void
	 */
	public function enqueue( $hook_suffix ) {
		if ( ! $this->is_dashboard_screen( $hook_suffix ) || ! self::is_enabled() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$asset = include BDTUPK_PATH . 'build/admin/index.asset.php';
		$deps  = isset( $asset['dependencies'] ) ? (array) $asset['dependencies'] : [];
		$ver   = isset( $asset['version'] ) ? $asset['version'] : BDTUPK_VER;

		wp_enqueue_script( self::HANDLE, BDTUPK_URL . 'build/admin/index.js', $deps, $ver, true );

		if ( is_readable( BDTUPK_PATH . 'build/admin/index.css' ) ) {
			wp_enqueue_style( self::HANDLE, BDTUPK_URL . 'build/admin/index.css', [], $ver );
			wp_style_add_data( self::HANDLE, 'rtl', 'replace' );
			wp_style_add_data( self::HANDLE, 'suffix', '' );
		}

		wp_set_script_translations( self::HANDLE, 'ultimate-post-kit', BDTUPK_PATH . 'languages' );

		wp_localize_script( self::HANDLE, 'UltimatePostKitAdmin', $this->get_config() );
	}

	/**
	 * Render the dashboard page: the app's mount node, and the PHP-rendered
	 * panels the app moves into its own routes.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<div class="wrap upk-react-wrap">
			<h1 class="screen-reader-text"><?php echo esc_html( BDTUPK_TITLE ); ?></h1>
			<div id="upk-admin-app"></div>

			<div id="upk-admin-panels" hidden>
				<?php
				$react_pages = $this->get_react_pages();
				foreach ( $this->get_panels() as $panel ) :
					if ( isset( $react_pages[ $panel['id'] ] ) ) {
						continue; // Rendered by the app.
					}
					?>
					<div class="upk-admin-panel ultimate-post-kit-dashboard" data-upk-panel="<?php echo esc_attr( $panel['id'] ); ?>">
						<?php call_user_func( $panel['render'] ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Cached result of get_react_pages().
	 *
	 * @var array|null
	 */
	private $react_pages = null;

	/**
	 * Add-on pages the app renders itself, keyed by panel id.
	 *
	 * An add-on that registers a dashboard tab can hand the app the data for a
	 * native page instead of PHP markup: `[ panel id => data ]`. The app then
	 * renders that page (Ultimate Post Kit Pro: License, White Label, Custom
	 * CSS & JS, Rollback) and the PHP panel is not rendered. Panels without
	 * data keep their PHP markup, so older add-on versions work unchanged.
	 *
	 * @return array<string, array>
	 */
	private function get_react_pages() {
		if ( null === $this->react_pages ) {
			/**
			 * Filters the add-on pages the dashboard app renders natively.
			 *
			 * @param array<string, array> $pages Panel id => page data.
			 */
			$pages             = (array) apply_filters( 'ultimate_post_kit/admin/react_pages', [] );
			$this->react_pages = array_filter( $pages, 'is_array' );
		}

		return $this->react_pages;
	}

	/**
	 * Whether Ultimate Post Kit Pro is active.
	 *
	 * @return bool
	 */
	private static function is_pro() {
		return function_exists( '_is_upk_pro_activated' ) && true === _is_upk_pro_activated();
	}

	/**
	 * PHP-rendered panels: id, title, icon (a dashicons class) and renderer.
	 *
	 * @return array[]
	 */
	private function get_panels() {
		$panels = [];

		/** This filter is documented in admin/class-settings-api.php */
		foreach ( (array) apply_filters( 'ultimate_post_kit_dashboard_extra_tabs', [] ) as $tab ) {
			if ( empty( $tab['id'] ) || empty( $tab['title'] ) || ! isset( $tab['callback'] ) || ! is_callable( $tab['callback'] ) ) {
				continue;
			}

			$callback = $tab['callback'];
			$panels[] = [
				'id'     => sanitize_key( $tab['id'] ),
				'title'  => html_entity_decode( wp_strip_all_tags( (string) $tab['title'] ), ENT_QUOTES, 'UTF-8' ),
				'icon'   => isset( $tab['icon'] ) ? (string) $tab['icon'] : 'dashicons dashicons-screenoptions',
				'render' => function () use ( $callback ) {
					call_user_func( $callback );
				},
			];
		}

		if ( ! self::is_pro() ) {
			return $panels;
		}

		// Rollback Version: Pro's page, rendered through the classic settings page.
		$rollback = isset( $this->settings->rollback_version ) ? $this->settings->rollback_version : null;
		if ( $rollback && method_exists( $rollback, 'upk_rollback_version_content' ) ) {
			$panels[] = [
				'id'     => 'ultimate_post_kit_rollback_version',
				'title'  => __( 'Rollback Version', 'ultimate-post-kit' ),
				'icon'   => 'dashicons dashicons-update',
				'render' => function () use ( $rollback ) {
					$rollback->upk_rollback_version_content();
				},
			];
		}

		// License, unless white label hides it (as the classic tab list does).
		if ( ! defined( 'BDTUPK_LO' ) || false === UltimatePostKit_Admin_Settings::license_wl_status() ) {
			$panels[] = [
				'id'     => 'ultimate_post_kit_license_settings',
				'title'  => __( 'License', 'ultimate-post-kit' ),
				'icon'   => 'dashicons dashicons-admin-network',
				'render' => function () {
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- established hook name relied on by Ultimate Post Kit Pro.
					apply_filters( 'upk_license_page', '' );
				},
			];
		}

		return $panels;
	}

	/**
	 * Bootstrap data for the app (window.UltimatePostKitAdmin).
	 *
	 * @return array
	 */
	private function get_config() {
		$user = wp_get_current_user();

		$react_pages = $this->get_react_pages();
		$panels      = array_map(
			function ( $panel ) use ( $react_pages ) {
				$item = [
					'id'    => $panel['id'],
					'title' => $panel['title'],
					'icon'  => $panel['icon'],
				];

				if ( isset( $react_pages[ $panel['id'] ] ) ) {
					$item['data'] = $react_pages[ $panel['id'] ];
				}

				return $item;
			},
			$this->get_panels()
		);

		return [
			'version'            => BDTUPK_VER,
			'title'              => BDTUPK_TITLE,
			'whiteLabel'         => 'Ultimate Post Kit' !== BDTUPK_TITLE,
			'isPro'              => self::is_pro(),
			'userName'           => $user ? $user->display_name : '',
			'adminUrl'           => admin_url(),
			'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
			'saveNonce'          => wp_create_nonce( 'ultimate-post-kit-settings-save-nonce' ),
			'logoUrl'            => BDTUPK_ASSETS_URL . 'images/logo-with-text.svg',
			'logoIconUrl'        => BDTUPK_ASSETS_URL . 'images/logo.svg',
			'newPageUrl'         => admin_url( 'post-new.php?post_type=page' ),
			'setupWizardUrl'     => admin_url( '?upk_setup_wizard=show' ),
			'templateBuilderUrl' => self::is_pro() && current_user_can( 'edit_pages' ) ? admin_url( 'edit.php?post_type=upk-template-builder' ) : '',
			'docsUrl'            => 'https://bdthemes.com/knowledge-base/ultimate-post-kit/',
			'supportUrl'         => 'https://bdthemes.com/support/',
			'videosUrl'          => 'https://www.youtube.com/watch?v=zNeoRz94cPw&list=PLP0S85GEw7DNBnZCb4RtJzlf38GCJ7z1b',
			'demoUrl'            => 'https://postkit.pro/',
			'featureUrl'         => 'https://feedback.bdthemes.com/b/6vr2250l/feature-requests/idea/new',
			'reviewUrl'          => 'https://wordpress.org/support/plugin/ultimate-post-kit/reviews/',
			'whatsNewUrl'        => 'https://wordpress.org/plugins/ultimate-post-kit/#developers',
			'pricingUrl'         => 'https://postkit.pro/pricing/',
			'getProUrl'          => 'https://store.bdthemes.com/ultimate-post-kit?utm_source=UltimatePostKit&utm_medium=PluginPage&utm_campaign=UltimatePostKit&coupon=FREETOPRO',
			'sections'           => $this->get_sections(),
			'system'             => $this->get_system_checks(),
			'releases'           => $this->get_releases(),
			'panels'             => $panels,
			'assets'             => $this->get_asset_stats(),
			'plugins'            => [
				'nonce'        => wp_create_nonce( 'upk_get_plugins_nonce' ),
				'installNonce' => wp_create_nonce( 'upk_install_plugin_nonce' ),
				'canInstall'   => current_user_can( 'install_plugins' ),
				'canActivate'  => current_user_can( 'activate_plugins' ),
			],
			'environment'        => [
				'wp_version'  => get_bloginfo( 'version' ),
				'php_version' => phpversion(),
				'server'      => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '',
				'language'    => get_locale(),
			],
		];
	}

	/**
	 * Plain text from a label or description that may hold HTML or entities.
	 *
	 * @param mixed $text Raw text.
	 * @return string
	 */
	private static function plain( $text ) {
		return trim( preg_replace( '/\s+/', ' ', html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES, 'UTF-8' ) ) );
	}

	/**
	 * Every settings section with its fields and current values.
	 *
	 * @return array
	 */
	private function get_sections() {
		$fields_by_section = ModuleService::get_widget_settings(
			function ( $settings ) {
				return $settings['settings_fields'];
			}
		);

		$is_pro    = self::is_pro();
		$used      = class_exists( 'UltimatePostKit_Admin_Settings' ) ? (array) UltimatePostKit_Admin_Settings::get_used_widgets() : [];
		$has_usage = class_exists( '\Elementor\Modules\Usage\Module' );
		$sections  = [];

		foreach ( self::SECTIONS as $section_id ) {
			$fields = isset( $fields_by_section[ $section_id ] ) ? (array) $fields_by_section[ $section_id ] : [];
			$stored = get_option( $section_id, [] );
			$stored = is_array( $stored ) ? $stored : [];
			$items  = [];
			$group  = null;

			foreach ( $fields as $field ) {
				$type = isset( $field['type'] ) ? $field['type'] : '';

				if ( 'start_group' === $type ) {
					$group = [
						'title' => isset( $field['label'] ) ? self::plain( $field['label'] ) : '',
						'desc'  => isset( $field['desc'] ) ? wp_kses_post( (string) $field['desc'] ) : '',
					];
					continue;
				}

				if ( 'end_group' === $type ) {
					$group = null;
					continue;
				}

				if ( 'checkbox' !== $type || empty( $field['name'] ) ) {
					continue;
				}

				$name    = (string) $field['name'];
				$default = isset( $field['default'] ) ? (string) $field['default'] : 'off';
				$value   = isset( $stored[ $name ] ) ? (string) $stored[ $name ] : $default;
				$slug    = 'upk-' . str_replace( '_', '-', $name );
				$is_paid = isset( $field['widget_type'] ) && 'pro' === $field['widget_type'];

				$item = [
					'name'         => $name,
					'label'        => isset( $field['label'] ) ? self::plain( $field['label'] ) : $name,
					'enabled'      => 'on' === $value,
					'default'      => $default,
					'widget_type'  => $is_paid ? 'pro' : 'free',
					'content_type' => isset( $field['content_type'] ) ? (string) $field['content_type'] : '',
					'demo_url'     => ! empty( $field['demo_url'] ) ? esc_url_raw( $field['demo_url'] ) : '',
					'video_url'    => ! empty( $field['video_url'] ) ? esc_url_raw( $field['video_url'] ) : '',
					'usage_count'  => isset( $used[ $slug ] ) ? (int) $used[ $slug ] : 0,
					'has_usage'    => $has_usage,
					'is_locked'    => $is_paid && ! $is_pro,
				];

				if ( ! empty( $field['tooltip'] ) ) {
					$item['tooltip']     = self::plain( $field['tooltip'] );
					$item['description'] = $item['tooltip'];
				}

				if ( $group ) {
					$item['group']       = $group;
					$item['description'] = self::plain( $group['desc'] );
				}

				$items[] = $item;
			}

			$sections[ $section_id ] = [
				'id'     => $section_id,
				'fields' => $items,
				// Every stored value, so a save never drops fields the app does not list.
				'stored' => array_map( 'strval', array_filter( $stored, 'is_scalar' ) ),
			];
		}

		$sections[ self::API_SECTION ] = $this->get_api_section(
			isset( $fields_by_section[ self::API_SECTION ] ) ? (array) $fields_by_section[ self::API_SECTION ] : []
		);

		return $sections;
	}

	/**
	 * The API keys section: one group per service and its text fields.
	 *
	 * Only users with manage_options get this page (see enqueue()); they can
	 * already read these keys in the classic form's inputs.
	 *
	 * @param array $fields Settings schema for the section.
	 * @return array
	 */
	private function get_api_section( $fields ) {
		$stored = get_option( self::API_SECTION, [] );
		$stored = is_array( $stored ) ? $stored : [];
		$groups = [];
		$inputs = [];
		$group  = null;

		foreach ( $fields as $field ) {
			$type = isset( $field['type'] ) ? $field['type'] : '';
			$name = isset( $field['name'] ) ? (string) $field['name'] : '';

			if ( 'start_group' === $type ) {
				$group    = preg_replace( '/_group_start$/', '', $name );
				$groups[] = [
					'key'       => $group,
					'label'     => isset( $field['label'] ) ? self::plain( $field['label'] ) : $group,
					'desc'      => isset( $field['desc'] ) ? wp_kses_post( (string) $field['desc'] ) : '',
					'video_url' => ! empty( $field['video_url'] ) ? esc_url_raw( $field['video_url'] ) : '',
					'fields'    => [],
				];
				continue;
			}

			if ( 'end_group' === $type ) {
				$group = null;
				continue;
			}

			if ( ! in_array( $type, [ 'text', 'password', 'url' ], true ) || '' === $name || null === $group ) {
				continue;
			}

			$groups[ count( $groups ) - 1 ]['fields'][] = $name;

			$inputs[] = [
				'name'        => $name,
				'label'       => isset( $field['label'] ) ? self::plain( $field['label'] ) : $name,
				'placeholder' => isset( $field['placeholder'] ) ? (string) $field['placeholder'] : '',
				'desc'        => isset( $field['desc'] ) ? wp_kses_post( (string) $field['desc'] ) : '',
				'group'       => $group,
			];
		}

		return [
			'id'     => self::API_SECTION,
			'fields' => [],
			'groups' => $groups,
			'inputs' => $inputs,
			'stored' => array_map( 'strval', array_filter( $stored, 'is_scalar' ) ),
		];
	}

	/**
	 * Asset Optimization state and the combined files it has written.
	 *
	 * @return array
	 */
	private function get_asset_stats() {
		$stats = class_exists( '\UltimatePostKit\Includes\Asset_Combiner' )
			? \UltimatePostKit\Includes\Asset_Combiner::stats()
			: [ 'css' => 0, 'js' => 0, 'bytes' => 0, 'version' => '' ];

		return [
			'enabled' => function_exists( 'ultimate_post_kit_is_asset_optimization_enabled' ) && ultimate_post_kit_is_asset_optimization_enabled(),
			'css'     => (int) $stats['css'],
			'js'      => (int) $stats['js'],
			'size'    => size_format( (int) $stats['bytes'] ),
			'version' => isset( $stats['version'] ) ? (string) $stats['version'] : '',
		];
	}

	/**
	 * Server environment checks for the System Status page.
	 *
	 * @return array[]
	 */
	private function get_system_checks() {
		$php_version   = phpversion();
		$max_execution = (int) ini_get( 'max_execution_time' );
		$memory_limit  = (string) ini_get( 'memory_limit' );
		$post_limit    = (string) ini_get( 'post_max_size' );
		$uploads       = wp_upload_dir( null, false );
		$writable      = empty( $uploads['error'] ) && wp_is_writable( $uploads['basedir'] );
		$debug         = defined( 'WP_DEBUG' ) && WP_DEBUG;

		return [
			[
				'id'    => 'php',
				'label' => __( 'PHP version', 'ultimate-post-kit' ),
				'value' => $php_version,
				'pass'  => version_compare( $php_version, '7.4.0', '>=' ),
				'hint'  => __( 'Min: 7.4', 'ultimate-post-kit' ),
			],
			[
				'id'    => 'max_execution_time',
				'label' => __( 'Max execution time', 'ultimate-post-kit' ),
				'value' => (string) $max_execution,
				'pass'  => 0 === $max_execution || $max_execution >= 90,
				'hint'  => __( 'Min: 90 recommended', 'ultimate-post-kit' ),
			],
			[
				'id'    => 'memory_limit',
				'label' => __( 'Memory limit', 'ultimate-post-kit' ),
				'value' => $memory_limit,
				'pass'  => '-1' === $memory_limit || wp_convert_hr_to_bytes( $memory_limit ) >= 512 * MB_IN_BYTES,
				'hint'  => __( 'Min: 512M recommended', 'ultimate-post-kit' ),
			],
			[
				'id'    => 'post_max_size',
				'label' => __( 'Max post size', 'ultimate-post-kit' ),
				'value' => $post_limit,
				'pass'  => wp_convert_hr_to_bytes( $post_limit ) >= 32 * MB_IN_BYTES,
				'hint'  => __( 'Min: 32M recommended', 'ultimate-post-kit' ),
			],
			[
				'id'    => 'uploads_writable',
				'label' => __( 'Uploads folder writable', 'ultimate-post-kit' ),
				'value' => $writable ? __( 'Yes', 'ultimate-post-kit' ) : __( 'No', 'ultimate-post-kit' ),
				'pass'  => $writable,
			],
			[
				'id'    => 'gzip',
				'label' => __( 'GZip enabled', 'ultimate-post-kit' ),
				'value' => function_exists( 'gzencode' ) ? __( 'Yes', 'ultimate-post-kit' ) : __( 'No', 'ultimate-post-kit' ),
				'pass'  => function_exists( 'gzencode' ),
			],
			[
				'id'    => 'multisite',
				'label' => __( 'Multisite', 'ultimate-post-kit' ),
				'value' => is_multisite() ? __( 'Multisite', 'ultimate-post-kit' ) : __( 'Single site', 'ultimate-post-kit' ),
				'pass'  => true,
			],
			[
				'id'    => 'debug',
				'label' => __( 'Debug mode', 'ultimate-post-kit' ),
				'value' => $debug ? __( 'On', 'ultimate-post-kit' ) : __( 'Off', 'ultimate-post-kit' ),
				'pass'  => ! $debug,
			],
		];
	}

	/**
	 * The latest releases from readme.txt, for the Recent activity card.
	 *
	 * @param int $limit Number of releases.
	 * @return array[]
	 */
	private function get_releases( $limit = 5 ) {
		$readme = BDTUPK_PATH . 'readme.txt';

		if ( ! is_readable( $readme ) ) {
			return [];
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local plugin file.
		$text = (string) file_get_contents( $readme );
		$at   = strpos( $text, '== Changelog ==' );

		if ( false === $at ) {
			return [];
		}

		$text     = substr( $text, $at );
		$releases = [];

		if ( ! preg_match_all( '/^=\s*([0-9][0-9.]*)\s*(?:\[([^\]]*)\])?\s*=\s*$/m', $text, $heads, PREG_OFFSET_CAPTURE | PREG_SET_ORDER ) ) {
			return [];
		}

		$map = [
			'added'      => 'added',
			'new'        => 'added',
			'fixed'      => 'fixed',
			'fix'        => 'fixed',
			'security'   => 'fixed',
			'improved'   => 'updated',
			'updated'    => 'updated',
			'changed'    => 'updated',
			'compliance' => 'updated',
			'removed'    => 'removed',
		];

		foreach ( $heads as $index => $head ) {
			if ( count( $releases ) >= $limit ) {
				break;
			}

			$start = $head[0][1] + strlen( $head[0][0] );
			$end   = isset( $heads[ $index + 1 ] ) ? $heads[ $index + 1 ][0][1] : strlen( $text );
			$body  = substr( $text, $start, $end - $start );

			preg_match_all( '/^\*\s*(?:([A-Za-z]+):)?\s*(.+)$/m', $body, $lines, PREG_SET_ORDER );

			$sections   = [];
			$highlights = [];

			foreach ( $lines as $line ) {
				$kind = strtolower( trim( (string) $line[1] ) );
				$key  = isset( $map[ $kind ] ) ? $map[ $kind ] : 'updated';

				$sections[ $key ] = isset( $sections[ $key ] ) ? $sections[ $key ] + 1 : 1;
				$highlights[]     = wp_strip_all_tags( trim( (string) $line[2] ) );
			}

			$releases[] = [
				'version'    => $head[1][0],
				'date'       => isset( $head[2][0] ) ? trim( (string) $head[2][0] ) : '',
				'is_current' => BDTUPK_VER === $head[1][0],
				'sections'   => $sections,
				'highlights' => array_slice( $highlights, 0, 2 ),
			];
		}

		return $releases;
	}
}
