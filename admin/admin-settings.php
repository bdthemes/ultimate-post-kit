<?php

use UltimatePostKit\Notices;
use UltimatePostKit\Utils;
use UltimatePostKit\Admin\ModuleService;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;


/**
 * Ultimate Post Kit Admin Settings Class
 */

class UltimatePostKit_Admin_Settings {

    public static $modules_list  = null;
    public static $modules_names = null;

    public static $modules_list_only_widgets  = null;
    public static $modules_names_only_widgets = null;

    public static $modules_list_only_3rdparty  = null;
    public static $modules_names_only_3rdparty = null;

    const PAGE_ID = 'ultimate_post_kit_options';

    private $settings_api;

    public  $responseObj;
    public  $licenseMessage;
    public  $showMessage  = false;
    private $is_activated = false;

    /**
	 * Rollback version instance
	 * 
	 * @var Rollback_Version
	 */
	public $rollback_version;

    function __construct() {
        $this->settings_api = new UltimatePostKit_Settings_API;

        if (!defined('BDTUPK_HIDE')) {
            add_action('admin_init', [$this, 'admin_init']);
            add_action('admin_menu', [$this, 'admin_menu'], 201);
        }
		
		// Handle white label access link
		$this->handle_white_label_access();
		
		// Add custom CSS/JS functionality
		$this->init_custom_code_functionality();
		
		// White label settings (admin only)
		add_action( 'wp_ajax_upk_save_white_label', [ $this, 'save_white_label_ajax' ] );
		add_action( 'wp_ajax_upk_revoke_white_label_token', [ $this, 'revoke_white_label_token_ajax' ] );
		add_action( 'admin_head', [ $this, 'inject_white_label_icon_css' ] );
		
		// Plugin installation (admin only)
		add_action('wp_ajax_upk_install_plugin', [$this, 'install_plugin_ajax']);
		
		

		if (_is_upk_pro_activated()) {
			// Initialize rollback version functionality
			add_action('admin_init', [$this, 'rollback_init']);
		}

    }

	public function rollback_init() {
		if ( class_exists('\UltimatePostKitPro\Rollback_Version') ) {
			$this->rollback_version = new \UltimatePostKitPro\Rollback_Version();
		}
	}

	
	
	
		/**
	 * Initialize Custom Code Functionality
	 * 
	 * @access public
	 * @return void
	 */
	public function init_custom_code_functionality() {
		// AJAX handler for saving custom code (admin only)
		add_action( 'wp_ajax_upk_save_custom_code', [ $this, 'save_custom_code_ajax' ] );
		
		
		// Admin scripts (admin only)
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_custom_code_scripts' ] );
		
		// Frontend injection is now handled by global functions in the main plugin file
		self::init_frontend_injection();
	}

	/**
	 * Initialize frontend injection hooks (works on both admin and frontend)
	 * 
	 * @access public static
	 * @return void
	 */
	public static function init_frontend_injection() {
		// Frontend hooks are now registered in the main plugin file
		// This method is kept for backwards compatibility but does nothing
	}

	/**
	 * Enqueue scripts for custom code editor
	 * 
	 * @access public
	 * @return void
	 */
	public function enqueue_custom_code_scripts( $hook ) {
		if ( $hook !== 'toplevel_page_ultimate_post_kit_options' ) {
			return;
		}

		// Enqueue WordPress built-in CodeMirror 
		wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		wp_enqueue_code_editor( array( 'type' => 'application/javascript' ) );
		
		// Enqueue WordPress media library scripts
		wp_enqueue_media();
		
		// Enqueue the admin script if it exists
		$admin_script_path = BDTUPK_ASSETS_PATH . 'js/upk-admin.js';
		if ( file_exists( $admin_script_path ) ) {
			wp_enqueue_script( 
				'upk-admin-script', 
				BDTUPK_ASSETS_URL . 'js/upk-admin.js', 
				[ 'jquery', 'media-upload', 'media-views', 'code-editor' ], 
				BDTUPK_VER, 
				true 
			);
			
			// Localize script with AJAX data
			wp_localize_script( 'upk-admin-script', 'upk_admin_ajax', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'upk_custom_code_nonce' ),
				'white_label_nonce' => wp_create_nonce( 'upk_white_label_nonce' )
			] );
		} else {
			// Fallback: localize to jquery if the admin script doesn't exist
			wp_localize_script( 'jquery', 'upk_admin_ajax', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'upk_custom_code_nonce' ),
				'white_label_nonce' => wp_create_nonce( 'upk_white_label_nonce' )
			] );
		}
	}

	/**
	 * AJAX handler for saving white label settings
	 * 
	 * @access public
	 * @return void
	 */
	public function save_white_label_ajax() {
		
		// Check nonce and permissions
		if (!wp_verify_nonce($_POST['nonce'], 'upk_white_label_nonce')) {
			wp_send_json_error(['message' => __('Security check failed', 'ultimate-post-kit')]);
		}

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('You do not have permission to manage white label settings', 'ultimate-post-kit')]);
		}

		// Check license eligibility
		if (!self::is_white_label_license()) {
			wp_send_json_error(['message' => __('Your license does not support white label features', 'ultimate-post-kit')]);
		}

		// Get white label settings
		$white_label_enabled = isset($_POST['upk_white_label_enabled']) ? (bool) $_POST['upk_white_label_enabled'] : false;
		$hide_license = isset($_POST['upk_white_label_hide_license']) ? (bool) $_POST['upk_white_label_hide_license'] : false;
		$bdtupk_hide = isset($_POST['upk_white_label_bdtupk_hide']) ? (bool) $_POST['upk_white_label_bdtupk_hide'] : false;
		$white_label_title = isset($_POST['upk_white_label_title']) ? sanitize_text_field($_POST['upk_white_label_title']) : '';
		$white_label_icon = isset($_POST['upk_white_label_icon']) ? esc_url_raw($_POST['upk_white_label_icon']) : '';
		$white_label_icon_id = isset($_POST['upk_white_label_icon_id']) ? absint($_POST['upk_white_label_icon_id']) : 0;
		$white_label_logo = isset($_POST['upk_white_label_logo']) ? esc_url_raw($_POST['upk_white_label_logo']) : '';
		$upk_white_label_logo_id = isset($_POST['upk_white_label_logo_id']) ? absint($_POST['upk_white_label_logo_id']) : 0;
		
		// Save settings
		update_option('upk_white_label_enabled', $white_label_enabled);
		update_option('upk_white_label_hide_license', $hide_license);
		update_option('upk_white_label_bdtupk_hide', $bdtupk_hide);
		update_option('upk_white_label_title', $white_label_title);
		update_option('upk_white_label_icon', $white_label_icon);
		update_option('upk_white_label_icon_id', $white_label_icon_id);
		update_option('upk_white_label_logo', $white_label_logo);
		update_option('upk_white_label_logo_id', $upk_white_label_logo_id);

		// Set license title status
		if ($white_label_enabled) {
			update_option('ultimate_post_kit_license_title_status', true);
		} else {
			delete_option('ultimate_post_kit_license_title_status');
		}

		// Only send access email if both white label mode AND BDTUPK_HIDE are enabled
		if ($white_label_enabled && $bdtupk_hide) {
			$email_sent = $this->send_white_label_access_email();
		}

		wp_send_json_success([
			'message' => __('White label settings saved successfully', 'ultimate-post-kit'),
			'bdtupk_hide' => $bdtupk_hide,
			'email_sent' => isset($email_sent) ? $email_sent : false
		]);
	}

	/**
	 * Send white label access email with special link
	 * 
	 * @access private
	 * @return bool
	 */
	private function send_white_label_access_email() {
		
		$license_email = self::get_license_email();
		$admin_email = get_bloginfo( 'admin_email' );
		$license_key = self::get_license_key();
		$site_name = get_bloginfo( 'name' );
		$site_url = get_bloginfo( 'url' );
		
		// Generate secure access token with additional entropy
		$access_token = wp_hash( $license_key . time() . wp_salt() . wp_generate_password( 32, false ) );
		
		// Store access token in database with no expiration
		$token_data = [
			'token' => $access_token,
			'license_key' => $license_key,
			'created_at' => current_time( 'timestamp' ),
			'user_id' => get_current_user_id()
		];
		
		update_option( 'upk_white_label_access_token', $token_data );
		
		// Generate access URL using token instead of license key for security
		// Add white_label_tab=1 parameter to automatically switch to White Label tab
		$access_url = admin_url( 'admin.php?page=ultimate_post_kit_options&upk_wl=1&token=' . $access_token . '&white_label_tab=1#ultimate_post_kit_extra_options' );
		
		// Email subject
		$subject = sprintf( '[%s] Ultimate Post Kit White Label Access Instructions', $site_name );
		
		// Email message
		$message = $this->get_white_label_email_template( $site_name, $site_url, $access_url, $license_key );
		
		// Email headers
		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $site_name . ' <' . $admin_email . '>'
		];
		
		$email_sent = false;
		
		// Send to license email
		if ( ! empty( $license_email ) && is_email( $license_email ) ) {
			$email_sent = wp_mail( $license_email, $subject, $message, $headers );
			
			// If on localhost or email failed, save email content for manual access
			if ( ! $email_sent || $this->is_localhost() ) {
				$this->save_email_content_for_localhost( $access_url, $message, $license_email );
			}
		}
		
		return $email_sent;
	}

	/**
	 * Check if running on localhost
	 * 
	 * @access private
	 * @return bool
	 */
	private function is_localhost() {
		$server_name = $_SERVER['SERVER_NAME'] ?? '';
		$server_addr = $_SERVER['SERVER_ADDR'] ?? '';
		
		$localhost_indicators = [
			'localhost',
			'127.0.0.1',
			'::1',
			'.local',
			'.test',
			'.dev'
		];
		
		foreach ( $localhost_indicators as $indicator ) {
			if ( strpos( $server_name, $indicator ) !== false || 
				 strpos( $server_addr, $indicator ) !== false ) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Save email content for localhost testing
	 * 
	 * @access private
	 * @param string $access_url
	 * @param string $email_content
	 * @param string $recipient_email
	 * @return void
	 */
	private function save_email_content_for_localhost( $access_url, $email_content, $recipient_email ) {
		$email_data = [
			'access_url' => $access_url,
			'email_content' => $email_content,
			'recipient_email' => $recipient_email,
			'message' => 'Email functionality not available on localhost. Use the access URL below:'
		];
		
		// Save for admin notice display
		update_option( 'upk_localhost_email_data', $email_data );
	}

	/**
	 * Get white label email template
	 * 
	 * @access private
	 * @param string $site_name
	 * @param string $site_url  
	 * @param string $access_url
	 * @param string $license_key
	 * @return string
	 */
	private function get_white_label_email_template( $site_name, $site_url, $access_url, $license_key ) {
		$masked_license = substr( $license_key, 0, 8 ) . '****-****-****-' . substr( $license_key, -4 );
		
		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<title>Ultimate Post Kit White Label Access</title>
			<style>
				body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
				.container { max-width: 600px; margin: 0 auto; padding: 20px; }
				.header { background: #2196F3; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
				.content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
				.access-link { background: #2196F3; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
				.warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
				.footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<h1>🔒 Ultimate Post Kit White Label Access</h1>
				</div>
				<div class="content">
					<h2>Important: Save This Email!</h2>
					
					<p>Hello,</p>
					
					<p>You have successfully enabled <strong>BDTUPK_HIDE mode</strong> for Ultimate Post Kit Pro on <strong><?php echo esc_html( $site_name ); ?></strong>.</p>
					
					<div class="warning">
						<h3>⚠️ IMPORTANT</h3>
						<p>The plugin interface is hidden from your WordPress admin. Use below link to modify white label settings.</p>

						<p style="text-align: center;">
							<a href="<?php echo esc_url( $access_url ); ?>" class="access-link">Access White Label Settings</a>
						</p>
					</div>					
					
					<p><strong>Direct Link:</strong><br>
					<a href="<?php echo esc_url( $access_url ); ?>"><?php echo esc_html( $access_url ); ?></a></p>
					
					
					<h3>🔧 What You Can Do</h3>
					<p>Using the access link above, you can:</p>
					<ul>
						<li>Disable BDTUPK_HIDE mode</li>
						<li>Modify white label settings</li>
					</ul>
					
					<p>Need help? <a href="https://bdthemes.com/support/" target="_blank">Contact support</a> with your license key.</p>
					
				</div>
			</div>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Handle white label access link
	 * 
	 * @access private
	 * @return void
	 */
	private function handle_white_label_access() {
		// Check if this is a white label access request
		if ( ! isset( $_GET['upk_wl'] ) || ! isset( $_GET['token'] ) ) {
			return;
		}

		// Check user capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		$upk_wl = sanitize_text_field( $_GET['upk_wl'] );
		$access_token = sanitize_text_field( $_GET['token'] );

		// Check if upk_wl is set to 1
		if ( $upk_wl !== '1' ) {
			$this->show_access_error( 'Invalid access parameter. Please use the correct link from your email.' );
			return;
		}

		// Validate the access token
		if ( ! $this->validate_white_label_access_token( $access_token ) ) {
			$this->show_access_error( 'Invalid or expired access token. Please use the correct access link from your email.' );
			return;
		}

		// Valid access - temporarily allow access by setting a flag
		add_action('admin_init', [$this, 'admin_init']);
        add_action('admin_menu', [$this, 'admin_menu'], 201);

		// Add success notice
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-success is-dismissible">';
			echo '<p><strong>✅ White Label Access Granted!</strong> You can now modify white label settings.</p>';
			echo '</div>';
		} );
	}

	/**
	 * Show access error page
	 * 
	 * @access private
	 * @param string $message
	 * @return void
	 */
	private function show_access_error( $message ) {
		wp_die( 
			'<h1>🔒 Ultimate Post Kit White Label Access</h1>' .
			'<p><strong>Access Denied:</strong> ' . esc_html( $message ) . '</p>' .
			'<p>If you need assistance, please contact support with your license information.</p>' .
			'<p><a href="' . admin_url() . '" class="button button-primary">← Return to Dashboard</a></p>',
			'Access Denied',
			[ 'response' => 403 ]
		);
	}

	/**
	 * Inject white label icon CSS
	 * 
	 * @access public
	 * @return void
	 */
	public function inject_white_label_icon_css() {
		$white_label_enabled = get_option('upk_white_label_enabled', false);
		$white_label_icon = get_option('upk_white_label_icon', '');
		
		// Only inject CSS when white label is enabled AND a custom icon is set
		if ( $white_label_enabled && ! empty( $white_label_icon ) ) {
			echo '<style type="text/css">';
			echo '#toplevel_page_ultimate_post_kit_options .wp-menu-image {';
			echo 'background-image: url(' . esc_url( $white_label_icon ) . ') !important;';
			echo 'background-size: 20px 20px !important;';
			echo 'background-repeat: no-repeat !important;';
			echo 'background-position: center !important;';
			echo '}';
			echo '#toplevel_page_ultimate_post_kit_options .wp-menu-image:before {';
			echo 'display: none !important;';
			echo '}';
			echo '#toplevel_page_ultimate_post_kit_options .wp-menu-image img {';
			echo 'display: none !important;';
			echo '}';
			echo '</style>';
		}
		// When white label is disabled or no icon is set, don't inject any CSS
		// This allows WordPress's original icon to display naturally
	}

    /**
     * Get used widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */
    public static function get_used_widgets() {

        $used_widgets = array();

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module     = Module::instance();
            
            $old_error_level = error_reporting();
 			error_reporting(E_ALL & ~E_WARNING); // Suppress warnings
 			$elements = $module->get_formatted_usage('raw');
 			error_reporting($old_error_level); // Restore
            
            $upk_widgets = self::get_upk_widgets_names();

            if (is_array($elements) || is_object($elements)) {

                foreach ($elements as $post_type => $data) {
                    foreach ($data['elements'] as $element => $count) {
                        if (in_array($element, $upk_widgets, true)) {
                            if (isset($used_widgets[$element])) {
                                $used_widgets[$element] += $count;
                            } else {
                                $used_widgets[$element] = $count;
                            }
                        }
                    }
                }
            }
        }

        return $used_widgets;
    }

    /**
     * Get used separate widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_used_only_widgets() {

        $used_widgets = array();

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module     = Module::instance();
            
            $old_error_level = error_reporting();
 			error_reporting(E_ALL & ~E_WARNING); // Suppress warnings
 			$elements = $module->get_formatted_usage('raw');
 			error_reporting($old_error_level); // Restore
            
            $upk_widgets = self::get_upk_only_widgets();

            if (is_array($elements) || is_object($elements)) {

                foreach ($elements as $post_type => $data) {
                    foreach ($data['elements'] as $element => $count) {
                        if (in_array($element, $upk_widgets, true)) {
                            if (isset($used_widgets[$element])) {
                                $used_widgets[$element] += $count;
                            } else {
                                $used_widgets[$element] = $count;
                            }
                        }
                    }
                }
            }
        }

        return $used_widgets;
    }

    /**
     * Get unused widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_widgets() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $upk_widgets = self::get_upk_widgets_names();

        $used_widgets = self::get_used_widgets();

        $unused_widgets = array_diff($upk_widgets, array_keys($used_widgets));

        return $unused_widgets;
    }

    /**
     * Get unused separate widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_only_widgets() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $upk_widgets = self::get_upk_only_widgets();

        $used_widgets = self::get_used_only_widgets();

        $unused_widgets = array_diff($upk_widgets, array_keys($used_widgets));

        return $unused_widgets;
    }

    /**
     * Get widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_upk_widgets_names() {
        $names = self::$modules_names;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'upk-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list
            );
        }

        return $names;
    }

    /**
     * Get separate widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_upk_only_widgets() {
        $names = self::$modules_names_only_widgets;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'upk-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list_only_widgets
            );
        }

        return $names;
    }

    /**
     * Get separate 3rdParty widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_upk_only_3rdparty_names() {
        $names = self::$modules_names_only_3rdparty;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'upk-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list_only_3rdparty
            );
        }

        return $names;
    }

    /**
     * Get URL with page id
     *
     * @access public
     *
     */

    public static function get_url() {
        return admin_url('admin.php?page=' . self::PAGE_ID);
    }

    /**
     * Init settings API
     *
     * @access public
     *
     */

    public function admin_init() {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->ultimate_post_kit_admin_settings());

        //initialize settings
        $this->settings_api->admin_init();
        $this->upk_redirect_to_get_pro();
        if (true === _is_upk_pro_activated()) {
            $this->bdt_redirect_to_renew_link();
        }
    }

    /**
     * Add Plugin Menus
     *
     * @access public
     *
     */

     // Redirect to Ultimate Post Kit Pro pricing page
    public function upk_redirect_to_get_pro() {
        if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_get_pro') {
            wp_redirect('https://bdthemes.com/deals/?utm_source=WordPress_org&utm_medium=bfcm_cta&utm_campaign=ultimate_post_kit');
            exit;
        }
    }

    /**
     * Redirect to license renewal page
     *
     * @access public
     *
     */
    public function bdt_redirect_to_renew_link() {
        if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_license_renew') {
            wp_redirect('https://account.bdthemes.com/');
            exit;
        }
    }

    /**
	 * Add Plugin Menus
	 *
	 * @access public
	 *
	 */

	public function admin_menu() {
		add_menu_page(
			BDTUPK_TITLE . ' ' . esc_html__('Dashboard', 'ultimate-post-kit'),
			BDTUPK_TITLE,
			'manage_options',
			self::PAGE_ID,
			[$this, 'plugin_page'],
			$this->ultimate_post_kit_icon(),
			58
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Core Widgets', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_active_modules',
			[$this, 'plugin_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Extensions', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_elementor_extend',
			[$this, 'plugin_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Special Features', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_other_settings',
			[$this, 'plugin_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('API Settings', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_api_settings',
			[$this, 'plugin_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Extra Options', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_extra_options',
			[$this, 'plugin_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('System Status', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_analytics_system_req',
			[$this, 'plugin_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTUPK_TITLE,
			esc_html__('Other Plugins', 'ultimate-post-kit'),
			'manage_options',
			self::PAGE_ID . '#ultimate_post_kit_other_plugins',
			[$this, 'plugin_page']
		);
		
		// add_submenu_page(
		// 	self::PAGE_ID,
		// 	BDTUPK_TITLE,
		// 	esc_html__('Get Up to 60%', 'ultimate-post-kit'),
		// 	'manage_options',
		// 	self::PAGE_ID . '#ultimate_post_kit_affiliate',
		// 	[$this, 'plugin_page']
		// );
		
		if (true == _is_upk_pro_activated()) {
			add_submenu_page(
				self::PAGE_ID,
				BDTUPK_TITLE,
				esc_html__('Rollback Version', 'ultimate-post-kit'),
				'manage_options',
				self::PAGE_ID . '#ultimate_post_kit_rollback_version',
				[$this, 'plugin_page']
			);

            add_submenu_page(
                self::PAGE_ID,
                BDTUPK_TITLE,
                esc_html__('Template Builder', 'ultimate-post-kit'),
                'edit_pages',
                'edit.php?post_type=upk-template-builder',
            );
        }

		if (true !== _is_upk_pro_activated()) {
			add_submenu_page(
				self::PAGE_ID,
				BDTUPK_TITLE,
				esc_html__('Black Friday Limited Offer Up To 87%', 'ultimate-post-kit'),
				'manage_options',
				self::PAGE_ID . '_get_pro',
				[$this, 'display_page']
			);
		}

	}

    /**
     * Get SVG Icons of Ultimate Post Kit
     *
     * @access public
     * @return string
     */

    public function ultimate_post_kit_icon() {
        return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNC4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCA5MDkuMyA4ODMuOCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgOTA5LjMgODgzLjg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNBN0FBQUQ7fQ0KPC9zdHlsZT4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik04MTEuMiwyNzIuOUg2ODEuNnYxMjkuN2MwLDEzLjYtMTEsMjQuNy0yNC43LDI0LjdoLTEwNWMtMTMuNiwwLTI0LjctMTEtMjQuNy0yNC43YzAsMCwwLDAsMCwwdi0xMDUNCgljMC0xMy42LDExLTI0LjcsMjQuNi0yNC43YzAsMCwwLDAsMCwwaDEyOS43VjE0My4zYzAtMTMuNi0xMS0yNC43LTI0LjctMjQuN0gzOTcuNmMtMTMuNiwwLTI0LjcsMTEtMjQuNywyNC43YzAsMCwwLDAsMCwwdjQ3MS41DQoJYzAsMTMuNi0xMSwyNC42LTI0LjYsMjQuN2MwLDAsMCwwLDAsMGgtMTA1Yy0xMy42LDAtMjQuNy0xMS0yNC43LTI0Ljd2LTM3NWMwLTEzLjYtMTEtMjQuNy0yNC43LTI0LjdIODljLTEzLjYsMC0yNC43LDExLTI0LjcsMjQuNw0KCWMwLDAsMCwwLDAsMHY1MjkuNGMwLDEzLjYsMTEsMjQuNywyNC43LDI0LjdoNDEzLjZjMTMuNiwwLDI0LjctMTEuMSwyNC43LTI0LjdWNjA2LjJjMC0xMy42LDExLTI0LjcsMjQuNy0yNC43aDI1OS4zDQoJYzEzLjYsMCwyNC43LTExLDI0LjctMjQuN1YyOTcuNkM4MzUuOSwyODQsODI0LjksMjczLDgxMS4yLDI3Mi45QzgxMS4yLDI3Mi45LDgxMS4yLDI3Mi45LDgxMS4yLDI3Mi45eiIvPg0KPHJlY3QgeD0iNzMyIiB5PSI4Mi42IiBjbGFzcz0ic3QwIiB3aWR0aD0iMzQuOCIgaGVpZ2h0PSIzNC44Ii8+DQo8cmVjdCB4PSI3OTEiIHk9IjE0OS43IiBjbGFzcz0ic3QwIiB3aWR0aD0iMjMuOSIgaGVpZ2h0PSIyMy45Ii8+DQo8cmVjdCB4PSI4MDMiIHk9IjgyLjYiIGNsYXNzPSJzdDAiIHdpZHRoPSIxNy44IiBoZWlnaHQ9IjE3LjgiLz4NCjxyZWN0IHg9Ijg2Ni43IiB5PSIxNTUuOCIgY2xhc3M9InN0MCIgd2lkdGg9IjE3LjgiIGhlaWdodD0iMTcuOCIvPg0KPHJlY3QgeD0iODI4LjkiIHk9IjQ0LjMiIGNsYXNzPSJzdDAiIHdpZHRoPSI4LjkiIGhlaWdodD0iOC45Ii8+DQo8cmVjdCB4PSI4NzcuNCIgeT0iMzgiIGNsYXNzPSJzdDAiIHdpZHRoPSI3LjIiIGhlaWdodD0iNy4yIi8+DQo8cmVjdCB4PSI4NTIuNiIgeT0iODciIGNsYXNzPSJzdDAiIHdpZHRoPSI4LjkiIGhlaWdodD0iOC45Ii8+DQo8cmVjdCB4PSI3MzUuNCIgeT0iMTgyLjgiIGNsYXNzPSJzdDAiIHdpZHRoPSIxOS43IiBoZWlnaHQ9IjE5LjciLz4NCjxyZWN0IHg9IjgyNi4zIiB5PSIyMDQuNiIgY2xhc3M9InN0MCIgd2lkdGg9IjE0LjEiIGhlaWdodD0iMTQuMSIvPg0KPC9zdmc+DQo=';
    }

    /**
	 * Get SVG Icons of Element Pack
	 *
	 * @access public
	 * @return array
	 */

	public function get_settings_sections() {
		$sections = [
			[
				'id' => 'ultimate_post_kit_active_modules',
				'title' => esc_html__('Core Widgets', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'ultimate_post_kit_elementor_extend',
				'title' => esc_html__('Extensions', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'ultimate_post_kit_other_settings',
				'title' => esc_html__('Special Features', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'ultimate_post_kit_api_settings',
				'title' => esc_html__('API Settings', 'ultimate-post-kit'),
				'icon' => 'dashicons dashicons-admin-settings',
			],
		];

		return $sections;
	}

    /**
     * Merge Admin Settings
     *
     * @access protected
     * @return array
     */

    protected function ultimate_post_kit_admin_settings() {

        return ModuleService::get_widget_settings(function ($settings) {
            $settings_fields    = $settings['settings_fields'];

            self::$modules_list = $settings_fields['ultimate_post_kit_active_modules'];
            self::$modules_list_only_widgets  = $settings_fields['ultimate_post_kit_active_modules'];

            return $settings_fields;
        });
    }

    /**
	 * Get Welcome Panel
	 *
	 * @access public
	 * @return void
	 */

	public function ultimate_post_kit_welcome() {

		?>

		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="upk-dashboard-welcome-container">

				<div class="upk-dashboard-item upk-dashboard-welcome bdt-card bdt-card-body">
					<h1 class="upk-feature-title upk-dashboard-welcome-title">
						<?php esc_html_e('Welcome to Ultimate Post Kit!', 'ultimate-post-kit'); ?>
					</h1>
					<p class="upk-dashboard-welcome-desc">
						<?php esc_html_e('Empower your web creation with powerful widgets, advanced extensions, ready templates and more.', 'ultimate-post-kit'); ?>
					</p>
					<a href="<?php echo admin_url('?upk_setup_wizard=show'); ?>"
						class="bdt-button bdt-welcome-button bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Setup Ultimate Post Kit', 'ultimate-post-kit'); ?></a>

					<div class="upk-dashboard-compare-section">
						<h4 class="upk-feature-sub-title">
							<?php printf(esc_html__('Unlock %sPremium Features%s', 'ultimate-post-kit'), '<strong class="upk-highlight-text">', '</strong>'); ?>
						</h4>
						<h1 class="upk-feature-title upk-dashboard-compare-title">
							<?php esc_html_e('Create Your Sleek Website with Ultimate Post Kit Pro!', 'ultimate-post-kit'); ?>
						</h1>
						<p><?php esc_html_e('Don\'t need more plugins. This pro addon helps you build complex or professional websites—visually stunning, functional and customizable.', 'ultimate-post-kit'); ?>
						</p>
						<ul>
							<li><?php esc_html_e('Dynamic Content and Integrations', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Live Copy Paste', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Template Builder', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Custom Meta Fields - Category Image, Audio Link, Video Link', 'ultimate-post-kit'); ?></li>
							<li><?php esc_html_e('Powerful Widgets and Advanced Extensions', 'ultimate-post-kit'); ?>
							</li>
						</ul>
						<div class="upk-dashboard-compare-section-buttons">
							<a href="https://postkit.pro/pricing/"
								class="bdt-button bdt-welcome-button bdt-margin-small-right"
								target="_blank"><?php esc_html_e('Compare Free Vs Pro', 'ultimate-post-kit'); ?></a>
							<a href="https://store.bdthemes.com/ultimate-post-kit?utm_source=UltimatePostKit&utm_medium=PluginPage&utm_campaign=UltimatePostKit&coupon=FREETOPRO"
								class="bdt-button bdt-dashboard-sec-btn"
								target="_blank"><?php esc_html_e('Get Premium at 30% OFF', 'ultimate-post-kit'); ?></a>
						</div>
					</div>
				</div>

				<div class="upk-dashboard-item upk-dashboard-template-quick-access bdt-card bdt-card-body">
					<div class="upk-dashboard-template-section">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/template.jpg'; ?>"
							alt="Ultimate Post Kit Dashboard Template">
						<h1 class="upk-feature-title ">
							<?php esc_html_e('Faster Web Creation with Sleek and Ready-to-Use Templates!', 'ultimate-post-kit'); ?>
						</h1>
						<p><?php esc_html_e('Build your wordpress websites of any niche—not from scratch and in a single click.', 'ultimate-post-kit'); ?>
						</p>
						<a href="https://postkit.pro/"
							class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
							target="_blank"><?php esc_html_e('View Templates', 'ultimate-post-kit'); ?></a>
					</div>

					<div class="upk-dashboard-quick-access bdt-margin-medium-top">
						<img src="<?php echo BDTUPK_ADMIN_URL . 'assets/images/support.jpg'; ?>"
							alt="Ultimate Post Kit Dashboard Template">
						<h1 class="upk-feature-title">
							<?php esc_html_e('Getting Started with Quick Access', 'ultimate-post-kit'); ?>
						</h1>
						<ul>
							<li><a href="https://postkit.pro/contact/"
									target="_blank"><?php esc_html_e('Contact Us', 'ultimate-post-kit'); ?></a></li>
							<li><a href="https://bdthemes.com/support/"
									target="_blank"><?php esc_html_e('Help Centre', 'ultimate-post-kit'); ?></a></li>
							<li><a href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/idea/new"
									target="_blank"><?php esc_html_e('Request a Feature', 'ultimate-post-kit'); ?></a>
							</li>
						</ul>
						<div class="upk-dashboard-support-section">
							<h1 class="upk-feature-title">
								<i class="dashicons dashicons-phone"></i>
								<?php esc_html_e('24/7 Support', 'ultimate-post-kit'); ?>
							</h1>
							<p><?php esc_html_e('Helping you get real-time solutions related to web creation with WordPress, Elementor, and Ultimate Post Kit.', 'ultimate-post-kit'); ?>
							</p>
							<a href="https://bdthemes.com/support/" class="bdt-margin-small-top"
								target="_blank"><?php esc_html_e('Get Your Support', 'ultimate-post-kit'); ?></a>
						</div>
					</div>
				</div>

				<div class="upk-dashboard-item upk-dashboard-request-feature bdt-card bdt-card-body">
					<h1 class="upk-feature-title upk-dashboard-template-quick-title">
						<?php esc_html_e('What\'s Stacking You?', 'ultimate-post-kit'); ?>
					</h1>
					<p><?php esc_html_e('We are always here to help you. If you have any feature request, please let us know.', 'ultimate-post-kit'); ?>
					</p>
					<a href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/idea/new"
						class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Request Your Features', 'ultimate-post-kit'); ?></a>
				</div>

				<a href="https://www.youtube.com/watch?v=zNeoRz94cPw&list=PLP0S85GEw7DNBnZCb4RtJzlf38GCJ7z1b" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-video-tutorial bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-video-alt3"></i>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Watch Video Tutorials', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('An invaluable resource for mastering WordPress, Elementor, and Web Creation', 'ultimate-post-kit'); ?>
					</p>
				</a>
				<a href="https://bdthemes.com/all-knowledge-base-of-ultimate-post-kit/" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-documentation bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-tools"></i>
					</span>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Read Easy Documentation', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('A way to eliminate the challenges you might face', 'ultimate-post-kit'); ?></p>
				</a>
				<a href="https://www.facebook.com/bdthemes" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-community bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-users"></i>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Join Our Community', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('A platform for the opportunity to network, collaboration and innovation', 'ultimate-post-kit'); ?>
					</p>
				</a>
				<a href="https://wordpress.org/plugins/ultimate-post-kit/#reviews" target="_blank"
					class="upk-dashboard-item upk-dashboard-footer-item upk-dashboard-review bdt-card bdt-card-body bdt-card-small">
					<span class="upk-dashboard-footer-item-icon">
						<i class="dashicons dashicons-star-filled"></i>
					</span>
					<h1 class="upk-feature-title"><?php esc_html_e('Show Your Love', 'ultimate-post-kit'); ?></h1>
					<p><?php esc_html_e('A way of the assessment of code', 'ultimate-post-kit'); ?></p>
				</a>
			</div>

		</div>

		<?php
	}

    /**
     * Get Pro
     *
     * @access public
     * @return void
     */

    function ultimate_post_kit_get_pro() {
    ?>
        <div class="upk-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card" style="max-width: 800px; margin-left: auto; margin-right: auto;">
                <div class="bdt-width-1-1@m upk-comparision bdt-text-center">

                    <div class="bdt-flex bdt-flex-between bdt-flex-middle">
                        <div class="bdt-text-left">
                            <h1 class="bdt-text-bold">
                                <?php echo esc_html_x('WHY GO WITH PRO?', 'Frontend', 'ultimate-post-kit'); ?>
                            </h1>
                            <h2>
                                <?php echo esc_html_x('Just Compare With Ultimate Post Kit Free Vs Pro', 'Frontend', 'ultimate-post-kit'); ?>
                            </h2>

                        </div>
                        <?php if (true !== _is_upk_pro_activated()) : ?>
                            <div class="upk-purchase-button">
                                <a href="https://postkit.pro/#a851ca7" target="_blank">
                                    <?php echo esc_html_x('Purchase Now', 'Frontend', 'ultimate-post-kit'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div>

                        <ul class="bdt-list bdt-list-divider bdt-text-left bdt-text-normal" style="font-size: 15px;">


                            <li class="bdt-text-bold">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Features', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m">
                                        <?php echo esc_html_x('Free', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m">
                                        <?php echo esc_html_x('Pro', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m"><span bdt-tooltip="pos: top-left; title: Lite have 35+ Widgets but Pro have 100+ core widgets">
                                            <?php echo esc_html_x('Core Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                                        </span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Theme Compatibility', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Dynamic Content & Custom Fields Capabilities', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Proper Documentation', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Updates & Support', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Ready Made Pages', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Ready Made Blocks', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Elementor Extended Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Live Copy or Paste', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Duplicator', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Video Link Meta', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Category Image', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Rooten Theme Pro Features', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>
                            <li class="">
                                <div class="bdt-grid">
                                    <div class="bdt-width-expand@m">
                                        <?php echo esc_html_x('Priority Support', 'Frontend', 'ultimate-post-kit'); ?>
                                    </div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
                                    <div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
                                </div>
                            </li>

                        </ul>


                        <!-- <div class="upk-dashboard-divider"></div> -->


                        <div class="upk-more-features bdt-card bdt-card-body bdt-margin-medium-top bdt-padding-large">
                            <ul class="bdt-list bdt-list-divider bdt-text-left" style="font-size: 15px;">
                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Incredibly Advanced', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Refund or Cancel Anytime', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Dynamic Content', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Super-Flexible Widgets', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('24/7 Premium Support', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Third Party Plugins', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Special Discount!', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Custom Field Integration', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('With Live Chat Support', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="bdt-grid bdt-grid-small">
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Trusted Payment Methods', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Interactive Effects', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                        <div class="bdt-width-1-3@m">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo esc_html_x('Video Tutorial', 'Frontend', 'ultimate-post-kit'); ?>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <!-- <div class="upk-dashboard-divider"></div> -->

                            <?php if (true !== _is_upk_pro_activated()) : ?>
                                <div class="upk-purchase-button bdt-margin-medium-top">
                                    <a href="https://postkit.pro/#a851ca7" target="_blank">
                                        <?php echo esc_html_x('Purchase Now', 'Frontend', 'ultimate-post-kit'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    <?php
    }

    /**
	 * Display Plugin Page
	 *
	 * @access public
	 * @return void
	 */

	public function plugin_page() {

		?>

		<div class="wrap ultimate-post-kit-dashboard">
			<h1></h1> <!-- don't remove this div, it's used for the notice container -->
		
			<div class="upk-dashboard-wrapper bdt-margin-top">
				<div class="upk-dashboard-header bdt-flex bdt-flex-wrap bdt-flex-between bdt-flex-middle"
					bdt-sticky="offset: 32; animation: bdt-animation-slide-top-small; duration: 300">

					<div class="bdt-flex bdt-flex-wrap bdt-flex-middle">
						<!-- Header Shape Elements -->
						<div class="upk-header-elements">
							<span class="upk-header-element upk-header-circle"></span>
							<span class="upk-header-element upk-header-dots"></span>
							<span class="upk-header-element upk-header-line"></span>
							<span class="upk-header-element upk-header-square"></span>
							<span class="upk-header-element upk-header-wave"></span>
						</div>

						<div class="upk-logo">
							<?php 
							$white_label_enabled = get_option( 'upk_white_label_enabled', false );
							$white_label_logo 	 = get_option( 'upk_white_label_logo', '' );
							$white_label_title 	 = get_option( 'upk_white_label_title', '' );

							if ($white_label_enabled && !empty($white_label_logo)) {

								$alt_text = !empty($white_label_title) ? $white_label_title . ' Logo' : 'Custom Logo';
								echo '<img src="' . esc_url($white_label_logo) . '" alt="' . esc_attr($alt_text) . '" style="max-height: 40px;">';
							} else {
								echo '<img src="' . BDTUPK_URL  . 'assets/images/logo-with-text.svg" alt="Ultimate Post Kit Logo">';
							}
							?>
						</div>
					</div>

					<div class="upk-dashboard-new-page-wrapper bdt-flex bdt-flex-wrap bdt-flex-middle">
						

						<!-- Always render save button, JavaScript will control visibility -->
						<div class="upk-dashboard-save-btn" style="display: none;">
							<button class="bdt-button bdt-button-primary ultimate-post-kit-settings-save-btn" type="submit">
								<?php esc_html_e('Save Settings', 'ultimate-post-kit'); ?>
							</button>
						</div>

						<!-- Custom Code Save Button Section -->
						<div class="upk-code-save-section" style="display: none;">
							<button type="button" id="upk-save-custom-code" class="bdt-button bdt-button-primary ultimate-post-kit-custom-code-save-btn">
								<?php esc_html_e('Save Custom Code', 'ultimate-post-kit'); ?>
							</button>
							<button type="button" id="upk-reset-custom-code" class="bdt-button bdt-button-primary ultimate-post-kit-custom-code-reset-btn">
								<?php esc_html_e('Reset Code', 'ultimate-post-kit'); ?>
							</button>
						</div>

						<!--  White Label Save Button Section -->
						<?php if (self::is_white_label_license()): ?>
							<div class="upk-white-label-save-section" style="display: none;">
								<button type="button" 
										id="upk-save-white-label" 
										class="bdt-button bdt-button-primary ultimate-post-kit-white-label-save-btn">
										<?php esc_html_e('Save White Label Settings', 'ultimate-post-kit'); ?>
								</button>
							</div>
						<?php endif; ?>

						<div class="upk-dashboard-new-page">
							<a class="bdt-flex bdt-flex-middle" href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>" class=""><i class="dashicons dashicons-admin-page"></i>
								<?php echo esc_html__('Create New Page', 'ultimate-post-kit') ?>
							</a>
						</div>
					</div>
				</div>

				<div class="upk-dashboard-container bdt-flex">
					<div class="upk-dashboard-nav-container-wrapper">
						<div class="upk-dashboard-nav-container-inner" bdt-sticky="end: !.upk-dashboard-container; offset: 115; animation: bdt-animation-slide-top-small; duration: 300">

							<!-- Navigation Shape Elements -->
							<div class="upk-nav-elements">
								<span class="upk-nav-element upk-nav-circle"></span>
								<span class="upk-nav-element upk-nav-dots"></span>
								<span class="upk-nav-element upk-nav-line"></span>
								<span class="upk-nav-element upk-nav-square"></span>
								<span class="upk-nav-element upk-nav-triangle"></span>
								<span class="upk-nav-element upk-nav-plus"></span>
								<span class="upk-nav-element upk-nav-wave"></span>
							</div>

						<?php $this->settings_api->show_navigation(); ?>
						</div>
					</div>


					<div class="bdt-switcher bdt-tab-container bdt-container-xlarge bdt-flex-1">
						<div id="ultimate_post_kit_welcome_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_welcome(); ?>
						</div>

						<?php $this->settings_api->show_forms(); ?>

						<div id="ultimate_post_kit_extra_options_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_extra_options(); ?>
						</div>

						<div id="ultimate_post_kit_analytics_system_req_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_analytics_system_req_content(); ?>
						</div>

						<div id="ultimate_post_kit_other_plugins_page" class="upk-option-page group">
							<?php $this->ultimate_post_kit_others_plugin(); ?>
						</div>

						<!-- <div id="ultimate_post_kit_affiliate_page" class="upk-option-page group">
							<?php //$this->ultimate_post_kit_affiliate_content(); ?>
						</div> -->

						<?php if (true == _is_upk_pro_activated()) : ?>
							<div id="ultimate_post_kit_rollback_version_page" class="upk-option-page group">
								<?php $this->upk_rollback_version_content(); ?>
							</div>
						<?php endif; ?>

                        <?php if (_is_upk_pro_activated() !== true) : ?>
                            <div id="ultimate_post_kit_get_pro" class="upk-option-page group">
                                <?php $this->ultimate_post_kit_get_pro(); ?>
                            </div>
                        <?php endif; ?>

                        <div id="ultimate_post_kit_license_settings_page" class="upk-option-page group">

                            <?php
                            if (_is_upk_pro_activated() == true) {
                                apply_filters('upk_license_page', '');
                            }

                            ?>
                        </div>

					</div>
				</div>

				<?php if (!defined('BDTUPK_WL') || false == self::license_wl_status()) {
					$this->footer_info();
				} ?>
			</div>

		</div>

		<?php

		$this->script();

	}




    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    function script() {
    	?>
        <script>
            jQuery(document).ready(function() {
                jQuery('.upk-no-result').removeClass('bdt-animation-shake');
            });

            function filterSearch(e) {
                var parentID = '#' + jQuery(e).data('id');
                var search = jQuery(parentID).find('.bdt-search-input').val().toLowerCase();

                jQuery(".upk-options .upk-option-item").filter(function() {
                    jQuery(this).toggle(jQuery(this).attr('data-widget-name').toLowerCase().indexOf(search) > -1)
                });

                if (!search) {
                    jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "");
                    jQuery(parentID).find('.upk-widget-all').trigger('click');
                } else {
                    jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "filter: [data-widget-name*='" + search + "']");
                    jQuery(parentID).find('.bdt-search-input').removeClass('bdt-active'); // Thanks to Bar-Rabbas
                    jQuery(parentID).find('.bdt-search-input').trigger('click');
                }
            }

            jQuery('.upk-options-parent').each(function(e, item) {
                var eachItem = '#' + jQuery(item).attr('id');
                jQuery(eachItem).on("beforeFilter", function() {
                    jQuery(eachItem).find('.upk-no-result').removeClass('bdt-animation-shake');
                });

                jQuery(eachItem).on("afterFilter", function() {

                    var isElementVisible = false;
                    var i = 0;

                    if (jQuery(eachItem).closest(".upk-options-parent").eq(i).is(":visible")) {} else {
                        isElementVisible = true;
                    }

                    while (!isElementVisible && i < jQuery(eachItem).find(".upk-option-item").length) {
                        if (jQuery(eachItem).find(".upk-option-item").eq(i).is(":visible")) {
                            isElementVisible = true;
                        }
                        i++;
                    }

                    if (isElementVisible === false) {
                        jQuery(eachItem).find('.upk-no-result').addClass('bdt-animation-shake');
                    }
                });


            });


            jQuery('.upk-widget-filter-nav li a').on('click', function(e) {
                jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('');
                jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('').attr('bdt-filter-control', '');
            });


            jQuery(document).ready(function($) {
                'use strict';

                function hashHandler() {
                    var $tab = jQuery('.ultimate-post-kit-dashboard .bdt-tab');
                    if (window.location.hash) {
                        var hash = window.location.hash.substring(1);
                        bdtUIkit.tab($tab).show(jQuery('#bdt-' + hash).data('tab-index'));
                    }
                }

                function onWindowLoad() {
                    hashHandler();
                }

                if (document.readyState === 'complete') {
					onWindowLoad();
				} else {
					jQuery(window).on('load', onWindowLoad);
				}

                window.addEventListener("hashchange", hashHandler, true);

                jQuery('.toplevel_page_ultimate_post_kit_options > ul > li > a ').on('click', function(event) {
                    jQuery(this).parent().siblings().removeClass('current');
                    jQuery(this).parent().addClass('current');
                });

                jQuery('#ultimate_post_kit_active_modules_page a.upk-active-all-widget').on('click', function(e) {
                    e.preventDefault();

                    jQuery('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_active_modules_page a.upk-deactive-all-widget').on('click', function(e) {
                    e.preventDefault();
                    jQuery('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-active-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_elementor_extend_page a.upk-active-all-widget').on('click', function(e) {
                    e.preventDefault();

                    jQuery('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#ultimate_post_kit_elementor_extend_page a.upk-deactive-all-widget').on('click', function(e) {
                    e.preventDefault();
                    jQuery('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.upk-active-all-widget').removeClass('bdt-active');
                });

                // Activate/Deactivate all widgets functionality
				$('#ultimate_post_kit_active_modules_page a.upk-active-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_active_modules_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function () {
						$(this).attr('checked', 'checked').prop("checked", true);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_active_modules_page a.upk-deactive-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

				$('#ultimate_post_kit_active_modules_page a.upk-deactive-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_active_modules_page .checkbox:visible').each(function () {
						$(this).removeAttr('checked').prop("checked", false);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_active_modules_page a.upk-active-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

				$('#ultimate_post_kit_elementor_extend_page a.upk-active-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_elementor_extend_page .upk-option-item:not(.upk-pro-inactive) .checkbox:visible').each(function () {
						$(this).attr('checked', 'checked').prop("checked", true);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_elementor_extend_page a.upk-deactive-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

				$('#ultimate_post_kit_elementor_extend_page a.upk-deactive-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#ultimate_post_kit_elementor_extend_page .checkbox:visible').each(function () {
						$(this).removeAttr('checked').prop("checked", false);
					});

					$(this).addClass('bdt-active');
					$('#ultimate_post_kit_elementor_extend_page a.upk-active-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.upk-dashboard-save-btn').show();
					}, 100);
				});

                jQuery('#ultimate_post_kit_active_modules_page .upk-pro-inactive .checkbox').each(function() {
                    jQuery(this).removeAttr('checked');
                    jQuery(this).attr("disabled", true);
                });

            });

            jQuery(document).ready(function ($) {
                const getProLink = $('a[href="admin.php?page=ultimate_post_kit_options_get_pro"]');
                if (getProLink.length) {
                    getProLink.attr('target', '_blank');
                }
            });

            // License Renew Redirect
            jQuery(document).ready(function ($) {
                const renewalLink = $('a[href="admin.php?page=ultimate_post_kit_options_license_renew"]');
                if (renewalLink.length) {
                    renewalLink.attr('target', '_blank');
                }
            });

			// Dynamic Save Button Control
			jQuery(document).ready(function ($) {
				// Define pages that need save button - only specific settings pages
				const pagesWithSave = [
					'ultimate_post_kit_active_modules',        // Core widgets
					'ultimate_post_kit_elementor_extend',      // Extensions
					'ultimate_post_kit_other_settings',        // Special features
					'ultimate_post_kit_api_settings'           // API settings
				];

				function toggleSaveButton() {
					const currentHash = window.location.hash.substring(1);
					const saveButton = $('.upk-dashboard-save-btn');
					
					// Check if current page should have save button
					if (pagesWithSave.includes(currentHash)) {
						saveButton.fadeIn(200);
					} else {
						saveButton.fadeOut(200);
					}
				}

				// Force save button to be visible for settings pages
				function forceSaveButtonVisible() {
					const currentHash = window.location.hash.substring(1);
					const saveButton = $('.upk-dashboard-save-btn');
					
					if (pagesWithSave.includes(currentHash)) {
						saveButton.show();
					}
				}

				// Initial check
				toggleSaveButton();

				// Listen for hash changes
				$(window).on('hashchange', function() {
					toggleSaveButton();
				});

				// Listen for tab clicks
				$('.bdt-dashboard-navigation a').on('click', function() {
					setTimeout(toggleSaveButton, 100);
				});

				// Also listen for navigation menu clicks (from show_navigation())
				$(document).on('click', '.bdt-tab a, .bdt-subnav a, .upk-dashboard-nav a, [href*="#ultimate_post_kit"]', function() {
					setTimeout(toggleSaveButton, 100);
				});

				// Listen for bulk active/deactive button clicks to maintain save button visibility
				$(document).on('click', '.upk-active-all-widget, .upk-deactive-all-widget', function() {
					setTimeout(forceSaveButtonVisible, 50);
				});

				// Listen for individual checkbox changes to maintain save button visibility
				$(document).on('change', '#ultimate_post_kit_elementor_extend_page .checkbox, #ultimate_post_kit_active_modules_page .checkbox', function() {
					setTimeout(forceSaveButtonVisible, 50);
				});

				// Update URL when navigation items are clicked
				$(document).on('click', '.bdt-tab a, .bdt-subnav a, .upk-dashboard-nav a', function(e) {
					const href = $(this).attr('href');
					if (href && href.includes('#')) {
						const hash = href.substring(href.indexOf('#'));
						if (hash && hash.length > 1) {
							// Update browser URL with the hash
							const currentUrl = window.location.href.split('#')[0];
							const newUrl = currentUrl + hash;
							window.history.pushState(null, null, newUrl);
							
							// Trigger hash change event for other listeners
							$(window).trigger('hashchange');
						}
					}
				});

				// Handle save button click
				$(document).on('click', '.ultimate-post-kit-settings-save-btn', function(e) {
					e.preventDefault();
					
					// Find the active form in the current tab
					const currentHash = window.location.hash.substring(1);
					let targetForm = null;
					
					// Look for forms in the active tab content
					if (currentHash) {
						// Try to find form in the specific tab page
						targetForm = $('#' + currentHash + '_page form.settings-save');
						
						// If not found, try without _page suffix
						if (!targetForm || targetForm.length === 0) {
							targetForm = $('#' + currentHash + ' form.settings-save');
						}
						
						// Try to find any form in the active tab content
						if (!targetForm || targetForm.length === 0) {
							targetForm = $('#' + currentHash + '_page form');
						}
					}
					
					// Fallback to any visible form with settings-save class
					if (!targetForm || targetForm.length === 0) {
						targetForm = $('form.settings-save:visible').first();
					}
					
					// Last fallback - any visible form
					if (!targetForm || targetForm.length === 0) {
						targetForm = $('.bdt-switcher .group:visible form').first();
					}
					
					if (targetForm && targetForm.length > 0) {
						// Show loading notification
						// bdtUIkit.notification({
						// 	message: '<div bdt-spinner></div> <?php //esc_html_e('Please wait, Saving settings...', 'ultimate-post-kit') ?>',
						// 	timeout: false
						// });

						// Submit form using AJAX (same logic as existing form submission)
						targetForm.ajaxSubmit({
							success: function () {
								// Show success message using UIkit notification (same as main settings)
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'ultimate-post-kit') ?>',
									status: 'primary',
									pos: 'top-center'
								});
							},
							error: function (data) {
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'ultimate-post-kit') ?>',
									status: 'warning'
								});
							}
						});
					} else {
						// Show error if no form found
						bdtUIkit.notification({
							message: '<span bdt-icon="icon: warning"></span> <?php esc_html_e('No settings form found to save.', 'ultimate-post-kit') ?>',
							status: 'warning'
						});
					}
				});

				//White Label Settings Functionality
				//Check if upk_admin_ajax is available
				if (typeof upk_admin_ajax === 'undefined') {
					window.upk_admin_ajax = {
						ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
						white_label_nonce: '<?php echo wp_create_nonce('upk_white_label_nonce'); ?>'
					};
				}				
				
				// Initialize CodeMirror editors for custom code
				var codeMirrorEditors = {};
				
				function initializeCodeMirrorEditors() {
					// CSS Editor 1
					if (document.getElementById('upk-custom-css')) {
						codeMirrorEditors['upk-custom-css'] = wp.codeEditor.initialize('upk-custom-css', {
							type: 'text/css',
							codemirror: {
								lineNumbers: true,
								mode: 'css',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// JavaScript Editor 1
					if (document.getElementById('upk-custom-js')) {
						codeMirrorEditors['upk-custom-js'] = wp.codeEditor.initialize('upk-custom-js', {
							type: 'application/javascript',
							codemirror: {
								lineNumbers: true,
								mode: 'javascript',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// CSS Editor 2
					if (document.getElementById('upk-custom-css-2')) {
						codeMirrorEditors['upk-custom-css-2'] = wp.codeEditor.initialize('upk-custom-css-2', {
							type: 'text/css',
							codemirror: {
								lineNumbers: true,
								mode: 'css',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// JavaScript Editor 2
					if (document.getElementById('upk-custom-js-2')) {
						codeMirrorEditors['upk-custom-js-2'] = wp.codeEditor.initialize('upk-custom-js-2', {
							type: 'application/javascript',
							codemirror: {
								lineNumbers: true,
								mode: 'javascript',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// Refresh all editors after a short delay to ensure proper rendering
					setTimeout(function() {
						refreshAllCodeMirrorEditors();
					}, 100);
				}
				
				// Function to refresh all CodeMirror editors
				function refreshAllCodeMirrorEditors() {
					Object.keys(codeMirrorEditors).forEach(function(editorKey) {
						if (codeMirrorEditors[editorKey] && codeMirrorEditors[editorKey].codemirror) {
							codeMirrorEditors[editorKey].codemirror.refresh();
						}
					});
				}
				
				// Function to refresh editors when tab becomes visible
				function refreshEditorsOnTabShow() {
					// Listen for tab changes (UIkit tab switching)
					if (typeof bdtUIkit !== 'undefined' && bdtUIkit.tab) {
						// When tab becomes active, refresh editors
						bdtUIkit.util.on(document, 'shown', '.bdt-tab', function() {
							setTimeout(function() {
								refreshAllCodeMirrorEditors();
							}, 50);
						});
					}
					
					// Also listen for direct tab clicks
					$('.bdt-tab a').on('click', function() {
						setTimeout(function() {
							refreshAllCodeMirrorEditors();
						}, 100);
					});
					
					// Listen for switcher changes (UIkit switcher)
					if (typeof bdtUIkit !== 'undefined' && bdtUIkit.switcher) {
						bdtUIkit.util.on(document, 'shown', '.bdt-switcher', function() {
							setTimeout(function() {
								refreshAllCodeMirrorEditors();
							}, 50);
						});
					}
				}
				
				// Initialize editors when page loads - with delay for better rendering
				setTimeout(function() {
					initializeCodeMirrorEditors();
				}, 100);
				
				// Setup tab switching handlers
				setTimeout(function() {
					refreshEditorsOnTabShow();
				}, 100);
				
				// Handle window resize events
				$(window).on('resize', function() {
					setTimeout(function() {
						refreshAllCodeMirrorEditors();
					}, 100);
				});
				
				// Handle page visibility changes (when switching browser tabs)
				document.addEventListener('visibilitychange', function() {
					if (!document.hidden) {
						setTimeout(function() {
							refreshAllCodeMirrorEditors();
						}, 200);
					}
				});
				
				// Force refresh when clicking on the Custom CSS & JS tab specifically
				$('a[href="#"]').on('click', function() {
					var tabText = $(this).text().trim();
					if (tabText === 'Custom CSS & JS') {
						setTimeout(function() {
							refreshAllCodeMirrorEditors();
						}, 150);
					}
				});

				//Toggle white label fields visibility
				$('#upk-white-label-enabled').on('change', function() {
					if ($(this).is(':checked')) {
						$('.upk-white-label-fields').slideDown(300);
					} else {
						$('.upk-white-label-fields').slideUp(300);
					}
				});

				//WordPress Media Library Integration for Icon Upload
				var mediaUploader;
				
				$('#upk-upload-icon').on('click', function(e) {
					e.preventDefault();
					
					// If the uploader object has already been created, reopen the dialog
					if (mediaUploader) {
						mediaUploader.open();
						return;
					}
					
					// Create the media frame
					mediaUploader = wp.media.frames.file_frame = wp.media({
						title: 'Select Icon',
						button: {
							text: 'Use This Icon'
						},
						library: {
							type: ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml']
						},
						multiple: false
					});
					
					// When an image is selected, run a callback
					mediaUploader.on('select', function() {
						var attachment = mediaUploader.state().get('selection').first().toJSON();
						
						// Set the hidden inputs
						$('#upk-white-label-icon').val(attachment.url);
						$('#upk-white-label-icon-id').val(attachment.id);
						
						// Update preview
						$('#upk-icon-preview-img').attr('src', attachment.url);
						$('.upk-icon-preview-container').show();
					});
					
					// Open the uploader dialog
					mediaUploader.open();
				});
				
				//Remove icon functionality
				$('#upk-remove-icon').on('click', function(e) {
					e.preventDefault();
					
					// Clear the hidden inputs
					$('#upk-white-label-icon').val('');
					$('#upk-white-label-icon-id').val('');
					
					// Hide preview
					$('.upk-icon-preview-container').hide();
					$('#upk-icon-preview-img').attr('src', '');
				});

				// WordPress Media Library Integration for Logo Upload
				var logoUploader;

				$('#upk-upload-logo').on('click', function(e) {
					e.preventDefault();

					// If the uploader object has already been created, reopen the dialog
					if (logoUploader) {
						logoUploader.open();
						return;
					}

					// Create the media frame
					logoUploader = wp.media.frames.file_frame = wp.media({
						title: 'Select Logo',
						button: {
							text: 'Use This Logo'
						},
						library: {
							type: ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml']
						},
						multiple: false
					});

					// When an image is selected, run a callback
					logoUploader.on('select', function() {
						var attachment = logoUploader.state().get('selection').first().toJSON();

						// Set the hidden inputs
						$('#upk-white-label-logo').val(attachment.url);
						$('#upk-white-label-logo-id').val(attachment.id);

						// Update preview
						$('#upk-logo-preview-img').attr('src', attachment.url);
						$('.upk-logo-preview-container').show();
					});

					// Open the uploader dialog
					logoUploader.open();
				});

				// Remove logo functionality
				$('#upk-remove-logo').on('click', function(e) {
					e.preventDefault();

					// Clear the hidden inputs
					$('#upk-white-label-logo').val('');
					$('#upk-white-label-logo-id').val('');

					// Hide preview
					$('.upk-logo-preview-container').hide();
					$('#upk-logo-preview-img').attr('src', '');
				});

				//BDTUPK_HIDE Warning when checkbox is enabled
				$('#upk-white-label-bdtupk-hide').on('change', function() {
					if ($(this).is(':checked')) {
						// Show warning modal/alert
						var warningMessage = '⚠️ WARNING: ADVANCED FEATURE\n\n' +
							'Enabling BDTUPK_HIDE will activate advanced white label mode that:\n\n' +
							'• Hides ALL Element Pack branding and menus\n' +
							'• Makes these settings difficult to access later\n' +
							'• Requires the special access link to return\n' +
							'• Is intended for client/agency use only\n\n' +
							'An email with access instructions will be sent if you proceed.\n\n' +
							'Are you sure you want to enable this advanced mode?';
						
						if (!confirm(warningMessage)) {
							// User cancelled, uncheck the box
							$(this).prop('checked', false);
							return false;
						}
						
						// Show additional info message
						if ($('#upk-bdtupk-hide-info').length === 0) {
							$(this).closest('.upk-option-item').after(
								'<div id="upk-bdtupk-hide-info" class="bdt-alert bdt-alert-warning bdt-margin-small-top">' +
								'<p><strong>BDTUPK_HIDE Mode Enabled</strong></p>' +
								'<p>When you save these settings, an email will be sent with instructions to access white label settings in the future.</p>' +
								'</div>'
							);
						}
					} else {
						// Remove info message when unchecked
						$('#upk-bdtupk-hide-info').remove();
					}
				});

				// Save white label settings with confirmation
				$('#upk-save-white-label').on('click', function(e) {
					e.preventDefault();
					
					// Check if button is disabled (no license or no white label eligible license)
					if ($(this).prop('disabled')) {
						var buttonText = $(this).text().trim();
						var alertMessage = '';
						
						if (buttonText.includes('License Not Activated')) {
							alertMessage = '<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p><strong>License Not Activated</strong><br>You need to activate your Ultimate Post Kit license to access White Label functionality. Please activate your license first.</p>' +
								'</div>';
						} else {
							alertMessage = '<div class="bdt-alert bdt-alert-warning" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p><strong>Eligible License Required</strong><br>White Label functionality is available for Agency, Extended, Developer, AppSumo Lifetime, and other eligible license holders. Please upgrade your license to access these features.</p>' +
								'</div>';
						}
						
						$('#upk-white-label-message').html(alertMessage).show();
						return false;
					}
					
					// Check if white label mode is being enabled
					var whiteLabelEnabled = $('#upk-white-label-enabled').is(':checked');
					var bdtupkHideEnabled = $('#upk-white-label-bdtupk-hide').is(':checked');
					
					// Only show confirmation dialog if white label is enabled AND BDTUPK_HIDE is enabled
					if (whiteLabelEnabled && bdtupkHideEnabled) {
						var confirmMessage = '🔒 FINAL CONFIRMATION\n\n' +
							'You are about to save settings with BDTUPK_HIDE enabled.\n\n' +
							'This will:\n' +
							'• Hide Ultimate Post Kit from WordPress admin immediately\n' +
							'• Send access instructions to your email addresses\n' +
							'• Require the special link to modify these settings\n\n' +
							'Email will be sent to:\n' +
							'• License email: <?php echo esc_js(self::get_license_email()); ?>\n' +
							'Are you absolutely sure you want to proceed?';
						
						if (!confirm(confirmMessage)) {
							return false;
						}
					}
					
					var $button = $(this);
					var originalText = $button.html();
					
					// Show loading state
					$button.html('Saving...');
					$button.prop('disabled', true);
					
					// Collect form data
					var formData = {
						action: 'upk_save_white_label',
						nonce: upk_admin_ajax.white_label_nonce,
						upk_white_label_enabled: $('#upk-white-label-enabled').is(':checked') ? 1 : 0,
						upk_white_label_title: $('#upk-white-label-title').val(),
						upk_white_label_icon: $('#upk-white-label-icon').val(),
						upk_white_label_icon_id: $('#upk-white-label-icon-id').val(),
						upk_white_label_logo: $('#upk-white-label-logo').val(),
						upk_white_label_logo_id: $('#upk-white-label-logo-id').val(),
						upk_white_label_hide_license: $('#upk-white-label-hide-license').is(':checked') ? 1 : 0,
						upk_white_label_bdtupk_hide: $('#upk-white-label-bdtupk-hide').is(':checked') ? 1 : 0
					};
					
					// Send AJAX request
					$.post(upk_admin_ajax.ajax_url, formData)
						.done(function(response) {
							if (response.success) {
								// Show success message with countdown
								var countdown = 2;
								var successMessage = response.data.message;
								
								// Add email notification info if BDTUPK_HIDE was enabled
								if (response.data.bdtupk_hide && response.data.email_sent) {
									successMessage += '<br><br><strong>📧 Access Email Sent!</strong><br>Check your email for the access link to modify these settings in the future.';
								} else if (response.data.bdtupk_hide && !response.data.email_sent && response.data.access_url) {
									// Localhost scenario - show the access URL directly
									successMessage += '<br><br><strong>📧 Localhost Email Notice:</strong><br>Email functionality is not available on localhost.<br><strong>Your Access URL:</strong><br><a href="' + response.data.access_url + '" target="_blank">Click here to access white label settings</a><br><small>Save this URL - you\'ll need it to modify settings when BDTUPK_HIDE is active.</small>';
								} else if (response.data.bdtupk_hide && !response.data.email_sent) {
									successMessage += '<br><br><strong>⚠️ Email Notice:</strong><br>There was an issue sending the access email. Please check your email settings or contact support.';
								}
								
								$('#upk-white-label-message').html(
									'<div class="bdt-alert bdt-alert-success" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>' + successMessage + ' <span id="upk-reload-countdown">Reloading in ' + countdown + ' seconds...</span></p>' +
									'</div>'
								).show();
								
								// Update button text
								$button.html('Reloading...');
								
								// Countdown timer
								var countdownInterval = setInterval(function() {
									countdown--;
									if (countdown > 0) {
										$('#upk-reload-countdown').text('Reloading in ' + countdown + ' seconds...');
									} else {
										$('#upk-reload-countdown').text('Reloading now...');
										clearInterval(countdownInterval);
									}
								}, 1000);
								
								// Check if BDTUPK_HIDE is enabled and redirect accordingly
								setTimeout(function() {
									if (response.data.bdtupk_hide) {
										// Redirect to admin dashboard if BDTUPK_HIDE is enabled
										window.location.href = '<?php echo admin_url('index.php'); ?>';
									} else {
										// Reload current page if BDTUPK_HIDE is not enabled
										window.location.reload();
									}
								}, 1500);
							} else {
								// Show error message
								$('#upk-white-label-message').html(
									'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>Error: ' + (response.data.message || 'Unknown error occurred') + '</p>' +
									'</div>'
								).show();
								
								// Restore button state for error case
								$button.html(originalText);
								$button.prop('disabled', false);
							}
						})
						.fail(function(xhr, status, error) {
							// Show error message
							$('#upk-white-label-message').html(
								'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p>Error: Failed to save settings. Please try again. (' + status + ')</p>' +
								'</div>'
							).show();
							
							// Restore button state for failure case
							$button.html(originalText);
							$button.prop('disabled', false);
						});
				});

				// Save custom code functionality (updated for CodeMirror)
				$('#upk-save-custom-code').on('click', function(e) {
					e.preventDefault();
					
					var $button = $(this);
					var originalText = $button.html();
					
					// Check if upk_admin_ajax is available
					if (typeof upk_admin_ajax === 'undefined') {
						$('#upk-custom-code-message').html(
							'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
							'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
							'<p>Error: AJAX configuration not loaded. Please refresh the page and try again.</p>' +
							'</div>'
						).show();
						return;
					}
					
					// Prevent multiple simultaneous saves
					if ($button.prop('disabled') || $button.hasClass('upk-saving')) {
						return;
					}
					
					// Mark as saving
					$button.addClass('upk-saving');
					
					// Get content from CodeMirror editors
					function getCodeMirrorContent(elementId) {
						if (codeMirrorEditors[elementId] && codeMirrorEditors[elementId].codemirror) {
							return codeMirrorEditors[elementId].codemirror.getValue();
						} else {
							// Fallback to textarea value
							return $('#' + elementId).val() || '';
						}
					}
					
					var cssContent = getCodeMirrorContent('upk-custom-css');
					var jsContent = getCodeMirrorContent('upk-custom-js');
					var css2Content = getCodeMirrorContent('upk-custom-css-2');
					var js2Content = getCodeMirrorContent('upk-custom-js-2');
					
					// Show loading state
					$button.prop('disabled', true);
					
					// Timeout safeguard - if AJAX doesn't complete in 30 seconds, restore button
					var timeoutId = setTimeout(function() {
						$button.removeClass('upk-saving');
						$button.html(originalText);
						$button.prop('disabled', false);
						$('#upk-custom-code-message').html(
							'<div class="bdt-alert bdt-alert-warning" bdt-alert>' +
							'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
							'<p>Save operation timed out. Please try again.</p>' +
							'</div>'
						).show();
					}, 30000);
					
					// Collect form data
					var formData = {
						action: 'upk_save_custom_code',
						nonce: upk_admin_ajax.nonce,
						custom_css: cssContent,
						custom_js: jsContent,
						custom_css_2: css2Content,
						custom_js_2: js2Content,
						excluded_pages: $('#upk-excluded-pages').val() || []
					};
					
					
					// Verify we have some content before sending (optional check)
					var totalContentLength = cssContent.length + jsContent.length + css2Content.length + js2Content.length;
					if (totalContentLength === 0) {
						var confirmEmpty = confirm('No content detected in any editor. Do you want to save empty content (this will clear all custom code)?');
						if (!confirmEmpty) {
							// Restore button state
							$button.html(originalText);
							$button.prop('disabled', false);
							return;
						}
					}
					
					// Send AJAX request
					$.post(upk_admin_ajax.ajax_url, formData)
						.done(function(response) {
							console.log('AJAX Response:', response); // Debug log
							
							if (response && response.success) {
								// Show success message
								var successMessage = response.data.message;
								if (response.data.excluded_count) {
									successMessage += ' (' + response.data.excluded_count + ' pages excluded)';
								}
								
								$('#upk-custom-code-message').html(
									'<div class="bdt-alert bdt-alert-success" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>' + successMessage + '</p>' +
									'</div>'
								).show();
								
								// Auto-hide message after 5 seconds
								setTimeout(function() {
									$('#upk-custom-code-message').fadeOut();
								}, 5000);
								
							} else {
								// Show error message
								var errorMessage = 'Unknown error occurred';
								if (response && response.data && response.data.message) {
									errorMessage = response.data.message;
								} else if (response && response.message) {
									errorMessage = response.message;
								}
								
								$('#upk-custom-code-message').html(
									'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>Error: ' + errorMessage + '</p>' +
									'</div>'
								).show();
							}
						})
						.fail(function(xhr, status, error) {
							console.log('AJAX Error:', xhr, status, error); // Debug log
							
							// Try to parse error response
							var errorMessage = 'Failed to save custom code. Please try again.';
							try {
								var errorResponse = JSON.parse(xhr.responseText);
								if (errorResponse.data && errorResponse.data.message) {
									errorMessage = errorResponse.data.message;
								} else if (errorResponse.message) {
									errorMessage = errorResponse.message;
								}
							} catch (e) {
								// Use default error message
							}
							
							// Show error message
							$('#upk-custom-code-message').html(
								'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p>Error: ' + errorMessage + ' (' + status + ')</p>' +
								'</div>'
							).show();
						})
						.always(function() {
							
							// Clear the timeout since AJAX completed
							clearTimeout(timeoutId);
							
							try {
								$button.removeClass('upk-saving');
								$button.html(originalText);
								$button.prop('disabled', false);
							} catch (e) {
								// Fallback: force button restoration
								$('#upk-save-custom-code').removeClass('upk-saving').html('<span class="dashicons dashicons-yes"></span> Save Custom Code').prop('disabled', false);
							}
						});
				});

				// Reset custom code functionality (updated for CodeMirror)
				$('#upk-reset-custom-code').on('click', function(e) {
					e.preventDefault();
					
					if (confirm('Are you sure you want to reset all custom code? This will clear all code.')) {
						var $button = $(this);
						var originalText = $button.html();

						// Clear CodeMirror editors
						function clearCodeMirrorEditor(elementId) {
							if (codeMirrorEditors[elementId] && codeMirrorEditors[elementId].codemirror) {
								codeMirrorEditors[elementId].codemirror.setValue('');
							} else {
								// Fallback to clearing textarea
								$('#' + elementId).val('');
							}
						}
						
						// Clear all editors
						clearCodeMirrorEditor('upk-custom-css');
						clearCodeMirrorEditor('upk-custom-js');
						clearCodeMirrorEditor('upk-custom-css-2');
						clearCodeMirrorEditor('upk-custom-js-2');
						
						// Clear exclusions
						$('#upk-excluded-pages').val([]).trigger('change');
						
						// Show clearing message
						$('#upk-custom-code-message').html(
							'<div class="bdt-alert bdt-alert-primary" bdt-alert>' +
							'<p><span bdt-spinner="ratio: 0.6"></span> Clearing custom code...</p>' +
							'</div>'
						).show();

						// Disable button during save
						$button.prop('disabled', true).html('<span bdt-spinner="ratio: 0.6"></span> Resetting...');

						// Prepare empty data for AJAX save
						var formData = {
							action: 'upk_save_custom_code',
							nonce: upk_admin_ajax.nonce,
							custom_css: '',
							custom_js: '',
							custom_css_2: '',
							custom_js_2: '',
							excluded_pages: []
						};

						// Send AJAX request to save empty values
						$.ajax({
							url: upk_admin_ajax.ajax_url,
							type: 'POST',
							data: formData,
							timeout: 30000,
							success: function(response) {
								if (response.success) {
									// Show success message
									$('#upk-custom-code-message').html(
										'<div class="bdt-alert bdt-alert-success" bdt-alert>' +
										'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
										'<p><span class="dashicons dashicons-yes"></span> All custom code has been reset successfully!</p>' +
										'</div>'
									).show();

									// Auto-hide message after 5 seconds
									setTimeout(function() {
										$('#upk-custom-code-message').fadeOut();
									}, 5000);
								} else {
									// Show error message
									$('#upk-custom-code-message').html(
										'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
										'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
										'<p><span class="dashicons dashicons-warning"></span> ' + (response.data.message || 'Failed to save reset. Please try again.') + '</p>' +
										'</div>'
									).show();
								}

								// Restore button
								$button.prop('disabled', false).html(originalText);
							},
							error: function(xhr, status, error) {
								// Show error message
								$('#upk-custom-code-message').html(
									'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p><span class="dashicons dashicons-warning"></span> Failed to save reset: ' + error + '</p>' +
									'</div>'
								).show();

								// Restore button
								$button.prop('disabled', false).html(originalText);
							}
						});
					}
				});				
			});

			// Chart.js initialization for system status canvas charts
			function initUltimatePostKitCharts() {
				// Wait for Chart.js to be available
				if (typeof Chart === 'undefined') {
					setTimeout(initUltimatePostKitCharts, 500);
					return;
				}

				// Chart instances storage
				window.upkChartInstances = window.upkChartInstances || {};
				window.upkChartsInitialized = false;

				// Function to create a chart
				function createChart(canvasId) {
					var canvas = document.getElementById(canvasId);
					if (!canvas) {
						return;
					}

					var $canvas = jQuery('#' + canvasId);
					var valueStr = $canvas.data('value');
					var labelsStr = $canvas.data('labels');
					var bgStr = $canvas.data('bg');

					if (!valueStr || !labelsStr || !bgStr) {
						return;
					}

					// Parse data
					var values = valueStr.toString().split(',').map(v => parseInt(v.trim()) || 0);
					var labels = labelsStr.toString().split(',').map(l => l.trim());
					var colors = bgStr.toString().split(',').map(c => c.trim());

					// Destroy existing chart using Chart.js built-in method
					var existingChart = Chart.getChart(canvas);
					if (existingChart) {
						existingChart.destroy();
					}

					// Also destroy from our instance storage
					if (window.upkChartInstances && window.upkChartInstances[canvasId]) {
						window.upkChartInstances[canvasId].destroy();
						delete window.upkChartInstances[canvasId];
					}

					// Create new chart
					try {
						var newChart = new Chart(canvas, {
							type: 'doughnut',
							data: {
								labels: labels,
								datasets: [{
									data: values,
									backgroundColor: colors,
									borderWidth: 0
								}]
							},
							options: {
								responsive: true,
								maintainAspectRatio: false,
								plugins: {
									legend: { display: false },
									tooltip: { enabled: true }
								},
								cutout: '60%'
							}
						});
						
						// Store in our instance storage
						if (!window.upkChartInstances) window.upkChartInstances = {};
						window.upkChartInstances[canvasId] = newChart;
					} catch (error) {
						// Do nothing
					}
				}

				// Update total widgets status
				function updateTotalStatus() {
					var coreCount = jQuery('#ultimate_post_kit_active_modules_page input:checked').length;
					var extensionsCount = jQuery('#ultimate_post_kit_elementor_extend_page input:checked').length;

					jQuery('#bdt-total-widgets-status-core').text(coreCount);
					jQuery('#bdt-total-widgets-status-extensions').text(extensionsCount);
					jQuery('#bdt-total-widgets-status-heading').text(coreCount + extensionsCount);
					
					jQuery('#bdt-total-widgets-status').attr('data-value', [coreCount, extensionsCount].join(','));
				}

				// Initialize all charts once
				function initAllCharts() {
					// Check if charts already exist and are properly rendered
					if (window.upkChartInstances && Object.keys(window.upkChartInstances).length >= 4) {
						return;
					}
					
					// Update total status first
					updateTotalStatus();
					
					// Create all charts
					var chartCanvases = [
						'bdt-db-total-status',
						'bdt-db-only-widget-status', 
						'bdt-total-widgets-status'
					];

					var successfulCharts = 0;
					chartCanvases.forEach(function(canvasId) {
						var canvas = document.getElementById(canvasId);
						if (canvas && canvas.offsetParent !== null) { // Check if canvas is visible
							createChart(canvasId);
							if (window.upkChartInstances && window.upkChartInstances[canvasId]) {
								successfulCharts++;
							}
						}
					});
				}

				// Check if we're currently on system status tab and initialize
				function checkAndInitIfOnSystemStatus() {
					if (window.location.hash === '#ultimate_post_kit_analytics_system_req') {
						setTimeout(initAllCharts, 300);
					}
				}

				// Initialize charts when DOM is ready
				jQuery(document).ready(function() {
					// Only initialize if we're on the system status tab
					setTimeout(checkAndInitIfOnSystemStatus, 500);
				});

				// Add click handler for System Status tab to create/refresh charts
				jQuery(document).on('click', 'a[href="#ultimate_post_kit_analytics_system_req"], a[href*="ultimate_post_kit_analytics_system_req"]', function() {
					setTimeout(function() {
						// Always recreate charts when tab is clicked to ensure they're visible
						initAllCharts();
					}, 200);
				});
			}

			// Start the chart initialization
			setTimeout(initUltimatePostKitCharts, 1000);

			// Handle plugin installation via AJAX
			jQuery(document).on('click', '.upk-install-plugin', function(e) {
				e.preventDefault();
				
				var $button = jQuery(this);
				var pluginSlug = $button.data('plugin-slug');
				var nonce = $button.data('nonce');
				var originalText = $button.text();
				
				// Disable button and show loading state
				$button.prop('disabled', true)
					   .text('<?php echo esc_js(__('Installing...', 'ultimate-post-kit')); ?>')
					   .addClass('bdt-installing');
				
				// Perform AJAX request
				jQuery.ajax({
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					type: 'POST',
					data: {
						action: 'upk_install_plugin',
						plugin_slug: pluginSlug,
						nonce: nonce
					},
					success: function(response) {
						if (response.success) {
							// Show success message
							$button.text('<?php echo esc_js(__('Installed!', 'ultimate-post-kit')); ?>')
								   .removeClass('bdt-installing')
								   .addClass('bdt-installed');
							
							// Show success notification
							if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-yes"></span> ' + response.data.message,
									status: 'success'
								});
							}
							
							// Reload the page after 2 seconds to update button states
							setTimeout(function() {
								window.location.reload();
							}, 2000);
							
						} else {
							// Show error message
							$button.prop('disabled', false)
								   .text(originalText)
								   .removeClass('bdt-installing');
							
							// Show error notification
							if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-warning"></span> ' + response.data.message,
									status: 'danger'
								});
							}
						}
					},
					error: function() {
						// Handle network/server errors
						$button.prop('disabled', false)
							   .text(originalText)
							   .removeClass('bdt-installing');
						
						// Show error notification
						if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
							bdtUIkit.notification({
								message: '<span class="dashicons dashicons-warning"></span> <?php echo esc_js(__('Installation failed. Please try again.', 'ultimate-post-kit')); ?>',
								status: 'danger'
							});
						}
					}
				});
			});

			// Show/hide white label & custom code save button based on active tab
			function toggleWhiteLabelSaveButton() {
				
				// Check if we're on the extra options page
				if (window.location.hash === '#ultimate_post_kit_extra_options') {
					// Target specifically the tabs within the Extra Options section
					var extraOptionsTabs = jQuery('.upk-extra-options-tabs .bdt-tab li.bdt-active');
					var activeTab = extraOptionsTabs.index();
					
					if (activeTab === 1) { // White Label tab is the second tab (index 1)
						jQuery('.upk-white-label-save-section').show();
						jQuery('.upk-code-save-section').hide();
					} else {
						jQuery('.upk-white-label-save-section').hide();
						jQuery('.upk-code-save-section').show();
					}
				} else {
					jQuery('.upk-white-label-save-section').hide();
					jQuery('.upk-code-save-section').hide();
				}
			}

			// Wait for jQuery to be ready
			jQuery(document).ready(function($) {
				
				// Check if we should automatically switch to White Label tab
				var urlParams = new URLSearchParams(window.location.search);
				if (urlParams.get('white_label_tab') === '1') {
					// Wait a bit for UIkit to be ready, then switch to White Label tab
					setTimeout(function() {
						// Use UIkit's API to switch to the second tab (index 1)
						var tabElement = document.querySelector('.upk-extra-options-tabs [bdt-tab]');
						if (tabElement && typeof UIkit !== 'undefined') {
							UIkit.tab(tabElement).show(1); // Show tab at index 1 (White Label tab)
						} else {
							// Fallback: simply click the White Label tab link
							var whiteLabelTab = $('.upk-extra-options-tabs .bdt-tab li').eq(1);
							if (whiteLabelTab.length > 0) {
								whiteLabelTab.find('a')[0].click(); // Use native click
							}
						}
						
						// Check button visibility after tab switch
						setTimeout(function() {
							toggleWhiteLabelSaveButton();
						}, 300);
					}, 800);
				} else {
					toggleWhiteLabelSaveButton();
				}
				
				// Check on hash change (when navigating to extra options page)
				$(window).on('hashchange', function() {
					toggleWhiteLabelSaveButton();
				});

				// Listen for UIkit tab changes using multiple methods
				$(document).on('click', '.bdt-tab li a', function() {
					setTimeout(function() {
						toggleWhiteLabelSaveButton();
					}, 200);
				});

				// Listen for UIkit's internal tab change events
				$(document).on('shown', '[bdt-tab]', function() {
					setTimeout(function() {
						toggleWhiteLabelSaveButton();
					}, 200);
				});

				// Also listen for the specific tab content changes
				$(document).on('show', '#upk-extra-options-tab-content > div', function() {
					setTimeout(function() {
						toggleWhiteLabelSaveButton();
					}, 200);
				});

				// Alternative: Check periodically for tab changes
				setInterval(function() {
					if (window.location.hash === '#ultimate_post_kit_extra_options') {
						var currentActiveTab = $('.bdt-tab li.bdt-active').index();
						if (typeof window.lastActiveTab === 'undefined') {
							window.lastActiveTab = currentActiveTab;
						} else if (window.lastActiveTab !== currentActiveTab) {
							window.lastActiveTab = currentActiveTab;
							toggleWhiteLabelSaveButton();
						}
					}
				}, 500);
			});
			
        </script>
    	<?php
    }

    /**
     * Display Footer
     *
     * @access public
     * @return void
     */

    function footer_info() {
    ?>

        <div class="ultimate-post-kit-footer-info bdt-margin-medium-top">

            <div class="bdt-grid ">

                <div class="bdt-width-auto@s upk-setting-save-btn">



                </div>

                <div class="bdt-width-expand@s bdt-text-right">
                    <p class="">
                        Ultimate Post Kit Pro plugin made with love by <a target="_blank" href="https://bdthemes.com">BdThemes</a> Team.
                        <br>All rights reserved by <a target="_blank" href="https://bdthemes.com">BdThemes.com</a>.
                    </p>
                </div>
            </div>

        </div>

<?php
    }
    
    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages         = get_pages();
        $pages_options = [];
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

	/**
	 * Check if current license supports white label features
	 * Now includes other_param checking for AppSumo WL flag
	 * 
	 * @access public static
	 * @return bool
	 */
	public static function is_white_label_license() {
		// Check if pro version is activated first
		if (!function_exists('_is_upk_pro_activated') || !_is_upk_pro_activated()) {
			return false;
		}
		
		// Since UltimatePostKitPro\Base doesn't exist, return false for now
		// This should be replaced with actual pro license checking logic when available
		$license_info = UltimatePostKitPro\Base\Ultimate_Post_Kit_Base::GetRegisterInfo();
		
		// Security: Validate license info structure
		if (empty($license_info) || 
			!is_object($license_info) || 
			empty($license_info->license_title) || 
			empty($license_info->is_valid)) {
			return false;
		}
		
		// Sanitize license title to prevent any potential issues
		$license_title = sanitize_text_field(strtolower($license_info->license_title));
		
		// Check for other_param WL flag FIRST (for AppSumo and other special licenses)
		if (!empty($license_info->other_param)) {
			// Check if other_param contains WL flag
			if (is_array($license_info->other_param)) {
				if (in_array('WL', $license_info->other_param, true)) {
					return true;
				}
			} elseif (is_string($license_info->other_param)) {
				if (strpos($license_info->other_param, 'WL') !== false) {
					return true;
				}
			}
		}
		
		// Check standard license types (but NOT AppSumo - AppSumo requires WL flag)
		$allowed_types = self::get_white_label_allowed_license_types();
		$allowed_hashes = array_values($allowed_types);
		
		// Split license title into words and check each word
		$words = preg_split('/\s+/', $license_title, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($words as $word) {
			$word = trim($word);
			if (empty($word) || strlen($word) > 50) { // Prevent extremely long strings
				continue;
			}
			
			// Use SHA-256 for enhanced security
			$hash = hash('sha256', $word);
			if (in_array($hash, $allowed_hashes, true)) { // Strict comparison
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Render White Label Section
	 * 
	 * @access public
	 * @return void
	 */
	public function render_white_label_section() {
		//// Safely check if helper functions exist
		$is_pro_installed = function_exists('_is_upk_pro_installed') ? _is_upk_pro_installed() : false;
		$is_pro_activated = function_exists('_is_upk_pro_activated') ? _is_upk_pro_activated() : false;
	
		// Define plugin slug (adjust if needed)
		$plugin_slug = 'ultimate-post-kit-pro/ultimate-post-kit-pro.php';
	
		// Case 1: Pro not installed
		if ( ! $is_pro_installed ) : ?>
			<div class="bdt-alert bdt-alert-danger bdt-margin-medium-top" bdt-alert>
				<p><?php esc_html_e( 'Ultimate Post Kit Pro is not installed. Please install it to access White Label functionality.', 'ultimate-post-kit' ); ?></p>
				<div class="bdt-margin-small-top">
					<a href="https://postkit.pro/pricing/" target="_blank" class="bdt-button bdt-btn-blue">
						<?php esc_html_e( 'Get Pro', 'ultimate-post-kit' ); ?>
					</a>
				</div>
			</div>
			<?php
			return;
		endif;
	
		// Case 2: Installed but not active
		if ( $is_pro_installed && ! $is_pro_activated ) :
			// Generate secure activation link
			$activate_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'activate',
						'plugin' => $plugin_slug,
					),
					admin_url( 'plugins.php' )
				),
				'activate-plugin_' . $plugin_slug
			);
			?>
			<div class="bdt-alert bdt-alert-warning bdt-margin-medium-top" bdt-alert>
				<p><?php esc_html_e( 'Ultimate Post Kit Pro is installed but not activated. Please activate it to access White Label functionality.', 'ultimate-post-kit' ); ?></p>
				<div class="bdt-margin-small-top">
					<a href="<?php echo esc_url( $activate_url ); ?>" class="bdt-button bdt-btn-blue">
						<?php esc_html_e( 'Activate Pro', 'ultimate-post-kit' ); ?>
					</a>
				</div>
			</div>
			<?php
			return;
		endif;
		?>
		<div class="upk-white-label-section">
			<h1 class="upk-feature-title"><?php esc_html_e('White Label Settings', 'ultimate-post-kit'); ?></h1>
			<p><?php esc_html_e('Enable white label mode to hide Ultimate Post Kit branding from the admin interface and widgets.', 'ultimate-post-kit'); ?></p>

			<?php 

			$is_license_active = false;
			if ( function_exists( 'upk_license_validation' ) && true === upk_license_validation() ) {
				$is_license_active = true;
			}
			$is_white_label_eligible = self::is_white_label_license();
			
			// Show appropriate notices based on license status
			if (!$is_license_active): ?>
				<div class="bdt-alert bdt-alert-danger bdt-margin-medium-top" bdt-alert>
					<p><strong><?php esc_html_e('License Not Activated', 'ultimate-post-kit'); ?></strong></p>
					<p><?php esc_html_e('You need to activate your Ultimate Post Kit license to access White Label functionality. Please activate your license first.', 'ultimate-post-kit'); ?></p>
					<div class="bdt-margin-small-top">
						<a href="<?php echo esc_url(admin_url('admin.php?page=ultimate_post_kit_options#ultimate_post_kit_license_settings')); ?>" class="bdt-button bdt-btn-blue bdt-margin-small-right">
							<?php esc_html_e('Activate License', 'ultimate-post-kit'); ?>
						</a>
						<a href="https://postkit.pro/pricing/" target="_blank" class="bdt-button bdt-btn-blue">
							<?php esc_html_e('Get License', 'ultimate-post-kit'); ?>
						</a>
					</div>
				</div>
			<?php elseif ($is_license_active && !$is_white_label_eligible): ?>
				<div class="bdt-alert bdt-alert-warning bdt-margin-medium-top" bdt-alert>
					<p><strong><?php esc_html_e('Eligible License Required', 'ultimate-post-kit'); ?></strong></p>
					<p><?php esc_html_e('White Label functionality is available for Agency, Extended, Developer, AppSumo Lifetime, and other eligible license holders. Some licenses may include special white label permissions.', 'ultimate-post-kit'); ?></p>
					<a href="https://postkit.pro/pricing/" target="_blank" class="bdt-button bdt-btn-blue bdt-margin-small-top">
						<?php esc_html_e('Upgrade License', 'ultimate-post-kit'); ?>
					</a>
				</div>
			<?php endif; ?>

			<div class="upk-white-label-options <?php echo (!$is_license_active || !$is_white_label_eligible) ? 'upk-white-label-locked' : ''; ?>">
				<div class="upk-option-item ">
					<div class="upk-option-item-inner bdt-card">
						<div class="bdt-flex bdt-flex-between bdt-flex-middle">
							<div>
								<h3 class="upk-option-title"><?php esc_html_e('Enable White Label Mode', 'ultimate-post-kit'); ?></h3>
								<p class="upk-option-description">
									<?php if ($is_license_active && $is_white_label_eligible): ?>
										<?php esc_html_e('When enabled, Ultimate Post Kit branding will be hidden from the admin interface and widgets.', 'ultimate-post-kit'); ?>
									<?php elseif (!$is_license_active): ?>
										<?php esc_html_e('This feature requires an active Ultimate Post Kit license. Please activate your license first.', 'ultimate-post-kit'); ?>
									<?php else: ?>
										<?php esc_html_e('This feature requires an eligible license (Agency, Extended, Developer, AppSumo Lifetime, etc.). Upgrade your license to access white label functionality.', 'ultimate-post-kit'); ?>
									<?php endif; ?>
								</p>
							</div>
							<div class="upk-option-switch">
								<?php
								$white_label_enabled = ($is_license_active && $is_white_label_eligible) ? get_option('upk_white_label_enabled', false) : false;
								// Convert to boolean to ensure proper comparison
								$white_label_enabled = (bool) $white_label_enabled;
								?>
								<label class="switch">
									<input type="checkbox" 
										   id="upk-white-label-enabled" 
										   name="upk_white_label_enabled" 
										   <?php checked($white_label_enabled, true); ?>
										   <?php disabled(!$is_license_active || !$is_white_label_eligible); ?>>
									<span class="slider"></span>
								</label>
							</div>
						</div>
					</div>
				</div>

				<!-- White Label Title Field (conditional) -->
				<div class="upk-option-item upk-white-label-fields" style="<?php echo ($white_label_enabled && $is_license_active && $is_white_label_eligible) ? '' : 'display: none;'; ?>">
					<div class="upk-option-item-inner bdt-card">
						<div class="upk-white-label-title-section bdt-margin-medium-bottom">
							<h3 class="upk-option-title"><?php esc_html_e('White Label Title', 'ultimate-post-kit'); ?></h3>
							<p class="upk-option-description"><?php esc_html_e('Enter a custom title to replace "Ultimate Post Kit" branding throughout the plugin.', 'ultimate-post-kit'); ?></p>
							<div class="upk-white-label-input-wrapper bdt-margin-small-top">
								<input type="text" 
									   id="upk-white-label-title" 
									   name="upk_white_label_title" 
									   class="upk-white-label-input" 
									   placeholder="<?php esc_attr_e('Enter your custom title...', 'ultimate-post-kit'); ?>"
									   value="<?php echo esc_attr(get_option('upk_white_label_title', '')); ?>"
									   <?php disabled(!$is_license_active || !$is_white_label_eligible); ?>>
							</div>
						</div>

						<hr class="bdt-divider-small">
						
						<!-- White Label Title Icon Field -->
						<div class="upk-white-label-icon-section bdt-margin-medium-top">
							<h3 class="upk-option-title"><?php esc_html_e('White Label Title Icon', 'ultimate-post-kit'); ?></h3>
							<p class="upk-option-description"><?php esc_html_e('Upload a custom icon to replace the Ultimate Post Kit menu icon. Supports JPG, PNG, and SVG formats.', 'ultimate-post-kit'); ?></p>
							
							<div class="upk-icon-upload-wrapper bdt-margin-small-top">
								<?php 
								$icon_url = get_option('upk_white_label_icon', '');
								$icon_id = get_option('upk_white_label_icon_id', '');
								?>
								<div class="upk-icon-preview-container" style="<?php echo $icon_url ? '' : 'display: none;'; ?>">
									<div class="upk-icon-preview">
										<img id="upk-icon-preview-img" src="<?php echo esc_url($icon_url); ?>" alt="Icon Preview" style="max-width: 64px; max-height: 64px; border: 1px solid #ddd; border-radius: 4px; padding: 8px; background: #fff;">
									</div>
									<button type="button" id="upk-remove-icon" class="bdt-button bdt-btn-grey bdt-flex bdt-flex-middle bdt-margin-small-top" style="padding: 8px 12px; font-size: 12px;">
										<span class="dashicons dashicons-trash"></span>
										<?php esc_html_e('Remove', 'ultimate-post-kit'); ?>
									</button>
								</div>
								
								<div class="upk-icon-upload-container">
									<button type="button" id="upk-upload-icon" class="bdt-button bdt-btn-blue bdt-margin-small-top" <?php disabled(!$is_license_active || !$is_white_label_eligible); ?>>
										<span class="dashicons dashicons-cloud-upload"></span>
										<?php esc_html_e('Upload Icon', 'ultimate-post-kit'); ?>
									</button>
									<input type="hidden" id="upk-white-label-icon" name="upk_white_label_icon" value="<?php echo esc_attr($icon_url); ?>">
									<input type="hidden" id="upk-white-label-icon-id" name="upk_white_label_icon_id" value="<?php echo esc_attr($icon_id); ?>">
								</div>
							</div>

							<p class="upk-input-help">
								<?php esc_html_e('Recommended size: 20x20 pixels. The icon will be automatically resized to fit the WordPress admin menu. Supported formats: JPG, PNG, SVG.', 'ultimate-post-kit'); ?>
							</p>
						</div>

						<!-- White Label Plugin Logo Field -->
						<div class="upk-white-label-logo-section bdt-margin-medium-top">
							<h3 class="upk-option-title"><?php esc_html_e('Plugin Logo', 'ultimate-post-kit'); ?></h3>
							<p class="upk-option-description"><?php esc_html_e('Upload a custom logo to replace the Ultimate Post Kit logo in the admin header. Supports JPG, PNG, and SVG formats.', 'ultimate-post-kit'); ?></p>
							<div class="upk-logo-upload-wrapper-inner">
								<div class="upk-logo-upload-wrapper bdt-margin-small-top">
									<?php 
									$logo_url = get_option('upk_white_label_logo', '');
									$logo_id = get_option('upk_white_label_logo_id', '');
									?>
									<div class="upk-logo-preview-container" style="<?php echo $logo_url ? '' : 'display: none;'; ?>">
										<div class="upk-logo-preview">
											<img id="upk-logo-preview-img" src="<?php echo esc_url($logo_url); ?>" alt="Logo Preview" style="max-width: 200px; max-height: 64px; border: 1px solid #ddd; border-radius: 4px; padding: 8px; background: #fff;">
										</div>
										<button type="button" id="upk-remove-logo" class="bdt-button bdt-btn-grey bdt-flex bdt-flex-middle bdt-margin-small-top" style="padding: 8px 12px; font-size: 12px;">
											<span class="dashicons dashicons-trash"></span>
										</button>
									</div>
									
									<div class="upk-logo-upload-container">
										<button type="button" id="upk-upload-logo" class="bdt-button bdt-btn-blue bdt-margin-small-top" <?php disabled(!$is_license_active || !$is_white_label_eligible); ?>>
											<span class="dashicons dashicons-cloud-upload"></span>
											<?php esc_html_e('Upload Logo', 'ultimate-post-kit'); ?>
										</button>
										<input type="hidden" id="upk-white-label-logo" name="upk_white_label_logo" value="<?php echo esc_attr($logo_url); ?>">
										<input type="hidden" id="upk-white-label-logo-id" name="upk_white_label_logo_id" value="<?php echo esc_attr($logo_id); ?>">
									</div>
								</div>
								<p class="upk-input-help">
									<?php esc_html_e('Recommended size: 200x40 pixels. The logo will be displayed in the admin header. Supported formats: JPG, PNG, SVG.', 'ultimate-post-kit'); ?>
								</p>
							</div>
						</div>
					</div>
				</div>

				<!-- License Hide Option (conditional) -->
				<div class="upk-option-item upk-white-label-fields" style="<?php echo ($white_label_enabled && $is_license_active && $is_white_label_eligible) ? '' : 'display: none;'; ?>">
					<div class="upk-option-item-inner bdt-card">
						<div class="bdt-flex bdt-flex-between bdt-flex-middle">
							<div>
								<h3 class="upk-option-title"><?php esc_html_e('Hide License Menu', 'ultimate-post-kit'); ?></h3>
								<p class="upk-option-description"><?php esc_html_e('Hide the license menu from the admin sidebar when white label mode is enabled.', 'ultimate-post-kit'); ?></p>
							</div>
							<div class="upk-option-switch">
								<?php
								$hide_license = get_option('upk_white_label_hide_license', false);
								// Convert to boolean to ensure proper comparison
								$hide_license = (bool) $hide_license;
								?>
								<label class="switch">
									<input type="checkbox" 
										   id="upk-white-label-hide-license" 
										   name="upk_white_label_hide_license" 
										   <?php checked($hide_license, true); ?>
										   <?php disabled(!$is_license_active || !$is_white_label_eligible); ?>>
									<span class="slider"></span>
								</label>
							</div>
						</div>
					</div>
				</div>

				<!-- BDTUPK_HIDE Option (conditional) -->
				<div class="upk-option-item upk-white-label-fields" style="<?php echo ($white_label_enabled && $is_license_active && $is_white_label_eligible) ? '' : 'display: none;'; ?>">
					<div class="upk-option-item-inner bdt-card">
						<div class="bdt-flex bdt-flex-between bdt-flex-middle">
							<div>
								<h3 class="upk-option-title"><?php esc_html_e('Enable BDTUPK_HIDE Constant', 'ultimate-post-kit'); ?></h3>
								<p class="upk-option-description"><?php esc_html_e('Define the BDTUPK_HIDE constant to hide additional Ultimate Post Kit branding and features throughout the plugin.', 'ultimate-post-kit'); ?></p>
								<?php 
								$bdtupk_hide = get_option('upk_white_label_bdtupk_hide', false);
								if ($bdtupk_hide): ?>
									<div class="bdt-alert bdt-alert-warning bdt-margin-small-top">
										<p><strong>⚠️ BDTUPK_HIDE Currently Active</strong></p>
										<p>Advanced white label mode is currently enabled. Ultimate Post Kit menus are hidden from the admin interface.</p>
									</div>
								<?php endif; ?>
							</div>
							<div class="upk-option-switch">
								<?php
								// Convert to boolean to ensure proper comparison
								$bdtupk_hide = (bool) $bdtupk_hide;
								?>
								<label class="switch">
									<input type="checkbox" 
										   id="upk-white-label-bdtupk-hide" 
										   name="upk_white_label_bdtupk_hide" 
										   <?php checked($bdtupk_hide, true); ?>
										   <?php disabled(!$is_license_active || !$is_white_label_eligible); ?>>
									<span class="slider"></span>
								</label>
							</div>
						</div>
					</div>
				</div>
				
				<?php if (!$bdtupk_hide && $is_license_active && $is_white_label_eligible): ?>
				<div class="bdt-margin-small-top">
					<div class="bdt-alert bdt-alert-danger">
						<h4>📧 Email Access System</h4>
						<p>When you enable BDTUPK_HIDE, an email will be automatically sent to:</p>
						<ul style="margin: 10px 0;">
							<li><strong>License Email:</strong> <?php echo esc_html(self::get_license_email()); ?></li>
							<?php if (get_bloginfo('admin_email') !== self::get_license_email()): ?>
							<li><strong>Admin Email:</strong> <?php echo esc_html(get_bloginfo('admin_email')); ?></li>
							<?php endif; ?>
						</ul>
						<p>This email will contain a special access link that allows you to return to these settings even when BDTUPK_HIDE is active.</p>
					</div>
				</div>
				<?php endif; ?>

				<!-- Success/Error Messages -->
				<div id="upk-white-label-message" class="upk-white-label-message bdt-margin-small-top" style="display: none;">
					<div class="bdt-alert bdt-alert-success" bdt-alert>
						<a href class="bdt-alert-close" bdt-close></a>
						<p><?php esc_html_e('White label settings saved successfully!', 'ultimate-post-kit'); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

    public static function license_wl_status() {
		$status = get_option('ultimate_post_kit_license_title_status');
		
		if ($status) {
			return true;
		}
		
		return false;
	}



    /**
	 * Display Analytics and System Requirements
	 *
	 * @access public
	 * @return void
	 */

	public function ultimate_post_kit_analytics_system_req_content() {
		?>
		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="upk-dashboard-analytics-system">

				<?php $this->ultimate_post_kit_widgets_status(); ?>

				<div class="bdt-grid bdt-grid-medium bdt-margin-medium-top" bdt-grid
					bdt-height-match="target: > div > .bdt-card">
					<div class="bdt-width-1-1">
						<div class="bdt-card bdt-card-body upk-system-requirement">
							<h1 class="upk-feature-title bdt-margin-small-bottom">
								<?php esc_html_e('System Requirement', 'ultimate-post-kit'); ?>
							</h1>
							<?php $this->ultimate_post_kit_system_requirement(); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

    /**
	 * Widgets Status
	 */

	public function ultimate_post_kit_widgets_status() {
		$track_nw_msg = '';
		if (!Tracker::is_allow_track()) {
			$track_nw = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'ultimate-post-kit');
			$track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
		}
		?>
		<div class="upk-dashboard-widgets-status">
			<div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="upk-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_widgets = count(self::get_used_widgets());
						$un_used_widgets = count(self::get_unused_widgets());
						?>

						<div class="upk-count-canvas-wrap">
							<h1 class="upk-feature-title"><?php esc_html_e('All Widgets', 'ultimate-post-kit'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="upk-count-wrap">
									<div class="upk-widget-count"><?php esc_html_e('Used:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($used_widgets); ?>
										</b></div>
									<div class="upk-widget-count"><?php esc_html_e('Unused:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($un_used_widgets); ?>
										</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Total:', 'ultimate-post-kit'); ?>
										<b>
											<?php echo esc_html($used_widgets + $un_used_widgets); ?>
										</b>
									</div>
								</div>

								<div class="upk-canvas-wrap">
									<canvas id="bdt-db-total-status" style="height: 100px; width: 100px;"
										data-label="Total Widgets Status - (<?php echo esc_html($used_widgets + $un_used_widgets); ?>)"
										data-labels="<?php echo esc_attr('Used, Unused'); ?>"
										data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>"
										data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="upk-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_widgets = count(self::get_used_only_widgets());
						$unused_only_widgets = count(self::get_unused_only_widgets());
						?>


						<div class="upk-count-canvas-wrap">
							<h1 class="upk-feature-title"><?php esc_html_e('Core', 'ultimate-post-kit'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="upk-count-wrap">
									<div class="upk-widget-count"><?php esc_html_e('Used:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($used_only_widgets); ?>
										</b></div>
									<div class="upk-widget-count"><?php esc_html_e('Unused:', 'ultimate-post-kit'); ?> <b>
											<?php echo esc_html($unused_only_widgets); ?>
										</b></div>
									<div class="upk-widget-count"><?php esc_html_e('Total:', 'ultimate-post-kit'); ?>
										<b>
											<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>
										</b>
									</div>
								</div>

								<div class="upk-canvas-wrap">
									<canvas id="bdt-db-only-widget-status" style="height: 100px; width: 100px;"
										data-label="Core Widgets Status - (<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>)"
										data-labels="<?php echo esc_attr('Used, Unused'); ?>"
										data-value="<?php echo esc_attr($used_only_widgets) . ',' . esc_attr($unused_only_widgets); ?>"
										data-bg="#EF476F, #ffcdd9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="upk-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<div class="upk-count-canvas-wrap">
							<h1 class="upk-feature-title"><?php esc_html_e('Active', 'ultimate-post-kit'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="upk-count-wrap">
									<div class="upk-widget-count"><?php esc_html_e('Core:', 'ultimate-post-kit'); ?> 
										<b id="bdt-total-widgets-status-core">0</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Extensions:', 'ultimate-post-kit'); ?>
										<b id="bdt-total-widgets-status-extensions">0</b>
									</div>
									<div class="upk-widget-count"><?php esc_html_e('Total:', 'ultimate-post-kit'); ?> <b
											id="bdt-total-widgets-status-heading">0</b></div>
								</div>

								<div class="upk-canvas-wrap">
									<canvas id="bdt-total-widgets-status" style="height: 100px; width: 100px;"
										data-label="Total Active Widgets Status"
										data-labels="<?php echo esc_attr('Core, Extensions'); ?>"
										data-value="0,0,0"
										data-bg="#0680d6, #B0EBFF" data-bg-hover="#0673e1, #B0EBFF">
									</canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<?php if (!Tracker::is_allow_track()): ?>
			<div class="bdt-border-rounded bdt-box-shadow-small bdt-alert-warning" bdt-alert>
				<a href class="bdt-alert-close" bdt-close></a>
				<div class="bdt-text-default">
				<?php
					printf(
						esc_html__('To view widgets analytics, Elementor %1$sUsage Data Sharing%2$s feature by Elementor needs to be activated. Please activate the feature to get widget analytics instantly ', 'ultimate-post-kit'),
						'<b>', '</b>'
					);

					echo ' <a href="' . esc_url(admin_url('admin.php?page=elementor-settings')) . '">' . esc_html__('from here.', 'ultimate-post-kit') . '</a>';
				?>
				</div>
			</div>
		<?php endif; ?>

		<?php
	}

    /**
	 * Display System Requirement
	 *
	 * @access public
	 * @return void
	 */

	public function ultimate_post_kit_system_requirement() {
		$php_version = phpversion();
		$max_execution_time = ini_get('max_execution_time');
		$memory_limit = ini_get('memory_limit');
		$post_limit = ini_get('post_max_size');
		$uploads = wp_upload_dir();
		$upload_path = $uploads['basedir'];
		$yes_icon = '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
		$no_icon = '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';

		$environment = Utils::get_environment_info();

		?>
		<ul class="check-system-status bdt-grid bdt-child-width-1-2@m  bdt-grid-small ">
			<li>
				<div>
					<span class="label1"><?php esc_html_e('PHP Version:', 'ultimate-post-kit'); ?></span>

					<?php
					if (version_compare($php_version, '7.4.0', '<')) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__('Min: 7.4 Recommended', 'ultimate-post-kit') . '" bdt-tooltip>' . esc_html__('Currently:', 'ultimate-post-kit') . ' ' . esc_html($php_version) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently:', 'ultimate-post-kit') . ' ' . esc_html($php_version) . '</span>';
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max execution time:', 'ultimate-post-kit'); ?> </span>
					<?php
					if ($max_execution_time < '90') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="Min: 90 Recommended" bdt-tooltip>Currently: ' . esc_html($max_execution_time) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . esc_html($max_execution_time) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1"><?php esc_html_e('Memory Limit:', 'ultimate-post-kit'); ?> </span>

					<?php
					if (intval($memory_limit) < '512') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="Min: 512M Recommended" bdt-tooltip>Currently: ' . esc_html($memory_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . esc_html($memory_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max Post Limit:', 'ultimate-post-kit'); ?> </span>

					<?php
					if (intval($post_limit) < '32') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="Min: 32M Recommended" bdt-tooltip>Currently: ' . wp_kses_post($post_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . wp_kses_post($post_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Uploads folder writable:', 'ultimate-post-kit'); ?></span>

					<?php
					if (!is_writable($upload_path)) {
						echo wp_kses_post($no_icon);
					} else {
						echo wp_kses_post($yes_icon);
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('MultiSite:', 'ultimate-post-kit'); ?></span>

					<?php
					if ($environment['wp_multisite']) {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('MultiSite Enabled', 'ultimate-post-kit') . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Single Site', 'ultimate-post-kit') . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('GZip Enabled:', 'ultimate-post-kit'); ?></span>

					<?php
					if ($environment['gzip_enabled']) {
						echo wp_kses_post($yes_icon);
					} else {
						echo wp_kses_post($no_icon);
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Debug Mode:', 'ultimate-post-kit'); ?></span>
					<?php
					if ($environment['wp_debug_mode']) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned On', 'ultimate-post-kit') . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned Off', 'ultimate-post-kit') . '</span>';
					}
					?>
				</div>

			</li>

		</ul>

		<div class="bdt-admin-alert">
			<strong><?php esc_html_e('Note:', 'ultimate-post-kit'); ?></strong>
			<?php
			/* translators: %s: Plugin name 'Ultimate Post Kit' */
			printf(
				esc_html__('If you have multiple addons like %s so you may need to allocate additional memory for other addons as well.', 'ultimate-post-kit'),
				'<b>Ultimate Post Kit</b>'
			);
			?>
		</div>

		<?php
	}

    /**
	 * Others Plugin
	 */

	public function ultimate_post_kit_others_plugin() {
		// Include the Plugin Integration Helper and API Fetcher
		require_once BDTUPK_INC_PATH . 'setup-wizard/class-plugin-api-fetcher.php';
		require_once BDTUPK_INC_PATH . 'setup-wizard/class-plugin-integration-helper.php';

		// Define plugin slugs to fetch data for (same as integration view)
		$plugin_slugs = array(
			'bdthemes-element-pack-lite',
			'bdthemes-prime-slider-lite/bdthemes-prime-slider.php',
			'ultimate-store-kit',
			'zoloblocks',
			'pixel-gallery',
			'live-copy-paste',
			'spin-wheel',
			'ai-image',
			'dark-reader',
			'ar-viewer',
			'smart-admin-assistant',
			'website-accessibility',
		);

		// Get plugin data using the helper (same as integration view)
		$upk_plugins = \UltimatePostKit\SetupWizard\Plugin_Integration_Helper::build_plugin_data($plugin_slugs);

		// Helper function for time formatting (same as integration view)
		if (!function_exists('format_last_updated')) {
			function format_last_updated($date_string) {
				if (empty($date_string)) {
					return __('Unknown', 'ultimate-post-kit');
				}
				
				$date = strtotime($date_string);
				if (!$date) {
					return __('Unknown', 'ultimate-post-kit');
				}
				
				$diff = current_time('timestamp') - $date;
				
				if ($diff < 60) {
					return __('Just now', 'ultimate-post-kit');
				} elseif ($diff < 3600) {
					$minutes = floor($diff / 60);
					return sprintf(_n('%d minute ago', '%d minutes ago', $minutes, 'ultimate-post-kit'), $minutes);
				} elseif ($diff < 86400) {
					$hours = floor($diff / 3600);
					return sprintf(_n('%d hour ago', '%d hours ago', $hours, 'ultimate-post-kit'), $hours);
				} elseif ($diff < 2592000) { // 30 days
					$days = floor($diff / 86400);
					return sprintf(_n('%d day ago', '%d days ago', $days, 'ultimate-post-kit'), $days);
				} elseif ($diff < 31536000) { // 1 year
					$months = floor($diff / 2592000);
					return sprintf(_n('%d month ago', '%d months ago', $months, 'ultimate-post-kit'), $months);
				} else {
					$years = floor($diff / 31536000);
					return sprintf(_n('%d year ago', '%d years ago', $years, 'ultimate-post-kit'), $years);
				}
			}
		}

		// Helper function for fallback URLs (same as integration view)
		if (!function_exists('get_plugin_fallback_urls')) {
			function get_plugin_fallback_urls($plugin_slug) {
				// Handle different plugin slug formats
				if (strpos($plugin_slug, '/') !== false) {
					// If it's a file path like 'plugin-name/plugin-name.php', extract directory
					$plugin_slug_clean = dirname($plugin_slug);
				} else {
					// If it's just the plugin directory name, use it directly
					$plugin_slug_clean = $plugin_slug;
				}
				
				// Custom icon URLs for specific plugins that might not be on WordPress.org
				$custom_icons = [
					'ar-viewer' => [
						'https://ps.w.org/ar-viewer/assets/icon-256x256.gif',
						'https://ps.w.org/ar-viewer/assets/icon-128x128.gif',
					],
				];
				
				// Return custom icons if available, otherwise use default WordPress.org URLs
				if (isset($custom_icons[$plugin_slug_clean])) {
					return $custom_icons[$plugin_slug_clean];
				}
				
				return [
					"https://ps.w.org/{$plugin_slug_clean}/assets/icon-256x256.png",  // Then PNG
					"https://ps.w.org/{$plugin_slug_clean}/assets/icon-128x128.png",  // Medium PNG
					"https://ps.w.org/{$plugin_slug_clean}/assets/icon-128x128.gif",  // Medium GIF
					"https://ps.w.org/{$plugin_slug_clean}/assets/icon-256x256.gif",  // Try GIF first
				];
			}
		}
		?>
		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="upk-dashboard-others-plugin">
				
				<?php foreach ($upk_plugins as $plugin) : 
					$is_active = is_plugin_active($plugin['slug']);
					// $is_recommended = $plugin['recommended'] && !$is_active;
					
					// Get plugin logo with fallback
					$logo_url = $plugin['logo'] ?? '';
					$plugin_name = $plugin['name'] ?? '';
					$plugin_slug = $plugin['slug'] ?? '';
					
					if (empty($logo_url) || !filter_var($logo_url, FILTER_VALIDATE_URL)) {
						// Generate fallback URLs for WordPress.org
						// Extract the directory name from the file path format
						// For 'plugin-name/plugin-file.php', use dirname to get 'plugin-name'
						$actual_slug = (strpos($plugin_slug, '/') !== false) ? dirname($plugin_slug) : $plugin_slug;
						$fallback_urls = get_plugin_fallback_urls($actual_slug);
						$logo_url = $fallback_urls[0];
					}
				?>
				
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle">
						<div class="bdt-plugin-logo-wrap bdt-flex bdt-flex-middle">
							<div class="bdt-plugin-logo-container">
								<img src="<?php echo esc_url($logo_url); ?>" 
									alt="<?php echo esc_attr($plugin_name); ?>" 
									class="bdt-plugin-logo"
									onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
								<div class="default-plugin-icon" style="display:none;">📦</div>
							</div>

							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="upk-feature-title"><?php echo esc_html($plugin_name); ?></h1>
								
								<!-- <?php //if ($is_active) : ?>
									<span class="bdt-others-plugin-active"><?php //esc_html_e('ACTIVE', 'ultimate-post-kit'); ?></span>
								<?php //endif; ?> -->
								
							</div>
						</div>	
						<div class="bdt-others-plugin-content-text">
							
							
							
							
							
							<?php if (!empty($plugin['description'])) : ?>
								<p><?php echo esc_html($plugin['description']); ?></p>
							<?php endif; ?>

							<span class="active-installs bdt-margin-small-top">
								<?php esc_html_e('Active Installs: ', 'ultimate-post-kit'); 
								// echo wp_kses_post($plugin['active_installs'] ?? '0'); 
								if (isset($plugin['active_installs_count']) && $plugin['active_installs_count'] > 0) {
									echo ' <span class="installs-count">' . number_format($plugin['active_installs_count']) . '+' . '</span>';
								} else {
									echo ' <span class="installs-count">Fewer than 10' . '</span>';
								}
								?>
							</span>

							<?php if (isset($plugin['downloaded_formatted']) && !empty($plugin['downloaded_formatted'])): ?>
								<div class="downloads bdt-margin-small-top">
									<span><?php esc_html_e('Downloads: ', 'ultimate-post-kit'); ?><?php echo esc_html($plugin['downloaded_formatted']); ?></span>
								</div>
							<?php endif; ?>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<?php 
									$rating = floatval($plugin['rating'] ?? 0);
									$full_stars = floor($rating);
									$has_half_star = ($rating - $full_stars) >= 0.5;
									$empty_stars = 5 - $full_stars - ($has_half_star ? 1 : 0);
									
									// Full stars
									for ($i = 0; $i < $full_stars; $i++) {
										echo '<i class="dashicons dashicons-star-filled"></i>';
									}
									
									// Half star
									if ($has_half_star) {
										echo '<i class="dashicons dashicons-star-half"></i>';
									}
									
									// Empty stars
									for ($i = 0; $i < $empty_stars; $i++) {
										echo '<i class="dashicons dashicons-star-empty"></i>';
									}
									?>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php echo esc_html($plugin['rating'] ?? '0'); ?> <?php esc_html_e('out of 5 stars.', 'ultimate-post-kit'); ?>
									<?php if (isset($plugin['num_ratings']) && $plugin['num_ratings'] > 0): ?>
										<span class="rating-count">(<?php echo number_format($plugin['num_ratings']); ?> <?php esc_html_e('ratings', 'ultimate-post-kit'); ?>)</span>
									<?php endif; ?>
								</span>
							</div>
							
							<?php if (isset($plugin['last_updated']) && !empty($plugin['last_updated'])): ?>
								<div class="bdt-others-plugin-updated bdt-margin-small-top">
									<span><?php esc_html_e('Last Updated: ', 'ultimate-post-kit'); ?><?php echo esc_html(format_last_updated($plugin['last_updated'])); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
						<?php echo $this->get_plugin_action_button($plugin['slug'], 'https://wordpress.org/plugins/' . dirname($plugin['slug']) . '/'); ?>
						<?php if (!empty($plugin['homepage'])) : ?>
							<a class="bdt-button bdt-dashboard-sec-btn" target="_blank" href="<?php echo esc_url($plugin['homepage']); ?>">
								<?php esc_html_e('Learn More', 'ultimate-post-kit'); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
				
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

    /**
	 * Check plugin status (installed, active, or not installed)
	 * 
	 * @param string $plugin_path Plugin file path
	 * @return string 'active', 'installed', or 'not_installed'
	 */
	private function get_plugin_status($plugin_path) {
		// Check if plugin is active
		if (is_plugin_active($plugin_path)) {
			return 'active';
		}
		
		// Check if plugin is installed but not active
		$installed_plugins = get_plugins();
		if (isset($installed_plugins[$plugin_path])) {
			return 'installed';
		}
		
		// Plugin is not installed
		return 'not_installed';
	}

	/**
	 * AJAX handler for saving custom code
	 * 
	 * @access public
	 * @return void
	 */
	public function save_custom_code_ajax() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'upk_custom_code_nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Invalid security token.' ] );
		}

		// Check user capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Insufficient permissions.' ] );
		}

		// Sanitize and save the custom code
		$custom_css = isset( $_POST['custom_css'] ) ? wp_unslash( $_POST['custom_css'] ) : '';
		$custom_js = isset( $_POST['custom_js'] ) ? wp_unslash( $_POST['custom_js'] ) : '';
		$custom_css_2 = isset( $_POST['custom_css_2'] ) ? wp_unslash( $_POST['custom_css_2'] ) : '';
		$custom_js_2 = isset( $_POST['custom_js_2'] ) ? wp_unslash( $_POST['custom_js_2'] ) : '';

		// Handle excluded pages - ensure we get proper array format
		$excluded_pages = array();
		if ( isset( $_POST['excluded_pages'] ) ) {
			if ( is_array( $_POST['excluded_pages'] ) ) {
				$excluded_pages = $_POST['excluded_pages'];
			} elseif ( is_string( $_POST['excluded_pages'] ) && ! empty( $_POST['excluded_pages'] ) ) {
				// Handle case where it might be a single value
				$excluded_pages = [ $_POST['excluded_pages'] ];
			}
		}
		
		// Sanitize excluded pages - convert to integers and remove empty values
		$excluded_pages = array_map( 'intval', $excluded_pages );
		$excluded_pages = array_filter( $excluded_pages, function( $page_id ) {
			return $page_id > 0;
		} );

		// Save to database
		update_option( 'upk_custom_css', $custom_css );
		update_option( 'upk_custom_js', $custom_js );
		update_option( 'upk_custom_css_2', $custom_css_2 );
		update_option( 'upk_custom_js_2', $custom_js_2 );
		update_option( 'upk_excluded_pages', $excluded_pages );

		wp_send_json_success( [ 
			'message' => 'Custom code saved successfully!',
			'excluded_count' => count( $excluded_pages )
		] );
	}

	/**
	 * Handle AJAX plugin installation
	 * 
	 * @access public
	 * @return void
	 */
	public function install_plugin_ajax() {
		// Check nonce
		if (!wp_verify_nonce($_POST['nonce'], 'upk_install_plugin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed', 'ultimate-post-kit')]);
		}

		// Check user capability
		if (!current_user_can('install_plugins')) {
			wp_send_json_error(['message' => __('You do not have permission to install plugins', 'ultimate-post-kit')]);
		}

		$plugin_slug = sanitize_text_field($_POST['plugin_slug']);

		if (empty($plugin_slug)) {
			wp_send_json_error(['message' => __('Plugin slug is required', 'ultimate-post-kit')]);
		}

		// Include necessary WordPress files
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

		// Get plugin information
		$api = plugins_api('plugin_information', [
			'slug' => $plugin_slug,
			'fields' => [
				'sections' => false,
			],
		]);

		if (is_wp_error($api)) {
			wp_send_json_error(['message' => __('Plugin not found: ', 'ultimate-post-kit') . $api->get_error_message()]);
		}

		// Install the plugin
		$skin = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader($skin);
		$result = $upgrader->install($api->download_link);

		if (is_wp_error($result)) {
			wp_send_json_error(['message' => __('Installation failed: ', 'ultimate-post-kit') . $result->get_error_message()]);
		} elseif ($skin->get_errors()->has_errors()) {
			wp_send_json_error(['message' => __('Installation failed: ', 'ultimate-post-kit') . $skin->get_error_messages()]);
		} elseif (is_null($result)) {
			wp_send_json_error(['message' => __('Installation failed: Unable to connect to filesystem', 'ultimate-post-kit')]);
		}

		// Get installation status
		$install_status = install_plugin_install_status($api);
		
		wp_send_json_success([
			'message' => __('Plugin installed successfully!', 'ultimate-post-kit'),
			'plugin_file' => $install_status['file'],
			'plugin_name' => $api->name
		]);
	}

    /**
	 * Extract plugin slug from plugin path
	 * 
	 * @param string $plugin_path Plugin file path
	 * @return string Plugin slug
	 */
	private function extract_plugin_slug_from_path($plugin_path) {
		$parts = explode('/', $plugin_path);
		return isset($parts[0]) ? $parts[0] : '';
	}

    /**
	 * Get plugin action button HTML based on plugin status
	 * 
	 * @param string $plugin_path Plugin file path
	 * @param string $install_url Plugin installation URL
	 * @param string $plugin_slug Plugin slug for activation
	 * @return string Button HTML
	 */
	private function get_plugin_action_button($plugin_path, $install_url, $plugin_slug = '') {
		$status = $this->get_plugin_status($plugin_path);
		
		switch ($status) {
			case 'active':
				return '';
				
			case 'installed':
				$activate_url = wp_nonce_url(
					add_query_arg([
						'action' => 'activate',
						'plugin' => $plugin_path
					], admin_url('plugins.php')),
					'activate-plugin_' . $plugin_path
				);
				return '<a class="bdt-button bdt-welcome-button" href="' . esc_url($activate_url) . '">' . 
				       __('Activate', 'ultimate-post-kit') . '</a>';
				
			case 'not_installed':
			default:
				$plugin_slug = $this->extract_plugin_slug_from_path($plugin_path);
				$nonce = wp_create_nonce('upk_install_plugin_nonce');
				return '<a class="bdt-button bdt-welcome-button upk-install-plugin" 
				          data-plugin-slug="' . esc_attr($plugin_slug) . '" 
				          data-nonce="' . esc_attr($nonce) . '" 
				          href="#">' . 
				       __('Install', 'ultimate-post-kit') . '</a>';
		}
	}

    /**
	 * Extra Options Start Here
	 */

	/**
	 * Render Custom CSS & JS Section
	 * 
	 * @access public
	 * @return void
	 */
	public function render_custom_css_js_section() {
		?>
		<div class="upk-custom-code-section">
			<!-- Header Section -->
			<div class="upk-code-section-header">
				<h2 class="upk-section-title"><?php esc_html_e('Header Code Injection', 'ultimate-post-kit'); ?></h2>
				<p class="upk-section-description"><?php esc_html_e('Code added here will be injected into the &lt;head&gt; section of your website.', 'ultimate-post-kit'); ?></p>
			</div>
			<div class="upk-code-row bdt-grid bdt-grid-small" bdt-grid>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('CSS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw CSS code without &lt;style&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-css" name="upk_custom_css" class="upk-code-editor" data-mode="css" placeholder=".example {&#10;    background: red;&#10;    border-radius: 5px;&#10;    padding: 15px;&#10;}&#10;&#10;"><?php echo esc_textarea(get_option('upk_custom_css', '')); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('JS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw JavaScript code without &lt;script&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-js" name="upk_custom_js" class="upk-code-editor" data-mode="javascript" placeholder="alert('Hello, Ultimate Post Kit!');"><?php echo esc_textarea(get_option('upk_custom_js', '')); ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Footer Section -->
			<div class="upk-code-section-header bdt-margin-medium-top">
				<h2 class="upk-section-title"><?php esc_html_e('Footer Code Injection', 'ultimate-post-kit'); ?></h2>
				<p class="upk-section-description"><?php esc_html_e('Code added here will be injected before the closing &lt;/body&gt; tag of your website.', 'ultimate-post-kit'); ?></p>
			</div>
			<div class="upk-code-row bdt-grid bdt-grid-small bdt-margin-small-top" bdt-grid>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('CSS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw CSS code without &lt;style&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-css-2" name="upk_custom_css_2" class="upk-code-editor" data-mode="css" placeholder=".example {&#10;    background: green;&#10;}&#10;&#10;"><?php echo esc_textarea(get_option('upk_custom_css_2', '')); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bdt-width-1-2@m">
					<div class="upk-code-editor-wrapper">
						<h3 class="upk-code-editor-title"><?php esc_html_e('JS', 'ultimate-post-kit'); ?></h3>
						<p class="upk-code-editor-description"><?php esc_html_e('Enter raw JavaScript code without &lt;script&gt; tags.', 'ultimate-post-kit'); ?></p>
						<div class="upk-codemirror-editor-container">
							<textarea id="upk-custom-js-2" name="upk_custom_js_2" class="upk-code-editor" data-mode="javascript" placeholder="console.log('Hello, Ultimate Post Kit!');"><?php echo esc_textarea(get_option('upk_custom_js_2', '')); ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Page Exclusion Section -->
			<div class="upk-code-section-header bdt-margin-medium-top">
				<h2 class="upk-section-title"><?php esc_html_e('Page & Post Exclusion Settings', 'ultimate-post-kit'); ?></h2>
				<p class="upk-section-description"><?php esc_html_e('Select pages and posts where you don\'t want any custom code to be injected. This applies to all sections above.', 'ultimate-post-kit'); ?></p>
			</div>
			<div class="upk-page-exclusion-wrapper">
				<label for="upk-excluded-pages" class="upk-exclusion-label">
					<?php esc_html_e('Exclude Pages & Posts:', 'ultimate-post-kit'); ?>
				</label>
				<select id="upk-excluded-pages" name="upk_excluded_pages[]" multiple class="upk-page-select">
					<option value=""><?php esc_html_e('-- Select pages/posts to exclude --', 'ultimate-post-kit'); ?></option>
					<?php
					$excluded_pages = get_option('upk_excluded_pages', array());
					if (!is_array($excluded_pages)) {
						$excluded_pages = array();
					}
					
					// Get all published pages
					$pages = get_pages(array(
						'sort_order' => 'ASC',
						'sort_column' => 'post_title',
						'post_status' => 'publish'
					));
					
					// Get recent posts (last 50)
					$posts = get_posts(array(
						'numberposts' => 50,
						'post_status' => 'publish',
						'post_type' => 'post',
						'orderby' => 'date',
						'order' => 'DESC'
					));
					
					// Display pages first
					if (!empty($pages)) {
						echo '<optgroup label="' . esc_attr__('Pages', 'ultimate-post-kit') . '">';
						foreach ($pages as $page) {
							$selected = in_array($page->ID, $excluded_pages) ? 'selected' : '';
							echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
						}
						echo '</optgroup>';
					}
					
					// Then display posts
					if (!empty($posts)) {
						echo '<optgroup label="' . esc_attr__('Recent Posts', 'ultimate-post-kit') . '">';
						foreach ($posts as $post) {
							$selected = in_array($post->ID, $excluded_pages) ? 'selected' : '';
							$post_date = date('M j, Y', strtotime($post->post_date));
							echo '<option value="' . esc_attr($post->ID) . '" ' . $selected . '>' . esc_html($post->post_title) . ' (' . $post_date . ')</option>';
						}
						echo '</optgroup>';
					}
					?>
				</select>
				<p class="upk-exclusion-help">
					<?php esc_html_e('Hold Ctrl (or Cmd on Mac) to select multiple items. Selected pages and posts will not load any custom CSS or JavaScript code. The list shows all pages and the 50 most recent posts.', 'ultimate-post-kit'); ?>
				</p>
			</div>

			<!-- Success/Error Messages -->
			<div id="upk-custom-code-message" class="upk-code-message bdt-margin-small-top" style="display: none;">
				<div class="bdt-alert bdt-alert-success" bdt-alert>
					<a href class="bdt-alert-close" bdt-close></a>
					<p><?php esc_html_e('Custom code saved successfully!', 'ultimate-post-kit'); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

    /**
	 * Extra Options Start Here
	 */

	public function ultimate_post_kit_extra_options() {
		?>
		<div class="upk-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="upk-dashboard-extra-options">
				<div class="bdt-card bdt-card-body">
					<h1 class="upk-feature-title"><?php esc_html_e('Extra Options', 'ultimate-post-kit'); ?></h1>

					<div class="upk-extra-options-tabs">
						<ul class="bdt-tab" bdt-tab="connect: #upk-extra-options-tab-content; animation: bdt-animation-fade">
							<li class="bdt-active"><a
									href="#"><?php esc_html_e('Custom CSS & JS', 'ultimate-post-kit'); ?></a></li>
							<li><a href="#"><?php esc_html_e('White Label', 'ultimate-post-kit'); ?></a></li>
						</ul>

						<div id="upk-extra-options-tab-content" class="bdt-switcher">
							<!-- Custom CSS & JS Tab -->
							<div>
								<?php $this->render_custom_css_js_section(); ?>
							</div>
							
							<!-- White Label Tab -->
							<div>
								<?php $this->render_white_label_section(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}


    /**
	 * Rollback Version Content
	 *
	 * @access public
	 * @return void
	 */
	public function upk_rollback_version_content() {
		// Use the already initialized rollback version instance
		$this->rollback_version->upk_rollback_version_content();
	}

	/**
	 * Get allowed white label license types (SHA-256 hashes)
	 * This centralized method makes it easy to add new license types in the future
	 * Note: AppSumo and Lifetime licenses require WL flag in other_param instead of automatic access
	 * 
	 * @access public static
	 * @return array Array of SHA-256 hashes for allowed license types
	 */
	public static function get_white_label_allowed_license_types() {
		$allowed_types = [
			'agency' => 'c4b2af4722ee54e317672875b2d8cf49aa884bf5820ec6091114fea5ec6560e4',
			'extended' => '4d7120eb6c796b04273577476eb2e20c34c51d7fa1025ec19c3414448abc241e',
			'developer' => '88fa0d759f845b47c044c2cd44e29082cf6fea665c30c146374ec7c8f3d699e3',
			// Note: AppSumo and Lifetime licenses removed from automatic access
			// They require WL flag in other_param for white label functionality
		];

		return $allowed_types;
	}

	/**
	 * Revoke white label access token
	 * 
	 * @access public
	 * @return bool
	 */
	public function revoke_white_label_access_token() {
		$token_data = get_option( 'upk_white_label_access_token', [] );
		
		if ( ! empty( $token_data ) ) {
			delete_option( 'upk_white_label_access_token' );
			return true;
		}
		
		return false;
	}

	/**
	 * Validate white label access token
	 * 
	 * @access public
	 * @param string $token
	 * @return bool
	 */
	public function validate_white_label_access_token( $token ) {
		$stored_token_data = get_option( 'upk_white_label_access_token', [] );
		
		if ( empty( $stored_token_data ) || ! isset( $stored_token_data['token'] ) ) {
			return false;
		}
		
		// Check token match
		if ( $stored_token_data['token'] !== $token ) {
			return false;
		}
		
		// Check if token was generated for current license
		$current_license_key = self::get_license_key();
		if ( $stored_token_data['license_key'] !== $current_license_key ) {
			return false;
		}
		
		return true;
	}

	/**
	 * AJAX handler for revoking white label access token
	 * 
	 * @access public
	 * @return void
	 */
	public function revoke_white_label_token_ajax() {
		// Check nonce and permissions
		if (!wp_verify_nonce($_POST['nonce'], 'upk_white_label_nonce')) {
			wp_send_json_error(['message' => __('Security check failed', 'ultimate-post-kit')]);
		}

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('You do not have permission to manage white label settings', 'ultimate-post-kit')]);
		}

		// Check license eligibility
		if (!self::is_white_label_license()) {
			wp_send_json_error(['message' => __('Your license does not support white label features', 'ultimate-post-kit')]);
		}

		// Revoke the token
		$revoked = $this->revoke_white_label_access_token();

		if ($revoked) {
			wp_send_json_success([
				'message' => __('White label access token has been revoked successfully', 'ultimate-post-kit')
			]);
		} else {
			wp_send_json_error([
				'message' => __('No active access token found to revoke', 'ultimate-post-kit')
			]);
		}
	}

	/**
	 * Get License Key
	 *
	 * @access public
	 * @return string
	 */

	public static function get_license_key() {
		$license_key = get_option('ultimate_post_kit_license_key');
		return trim($license_key);
	}

	/**
	 * Get License Email
	 *
	 * @access public
	 * @return string
	 */

	 public static function get_license_email() {
		return trim(get_option('ultimate_post_kit_license_email', get_bloginfo('admin_email')));
	}

}

new UltimatePostKit_Admin_Settings();
