<?php

namespace UltimatePostKit;

defined( 'ABSPATH' ) || exit;

/**
 * Global author promotional offer preference on the Ultimate Post Kit plugin dashboard.
 */
class Promo_Offer_Settings {

	const OPTIONS_PAGE = 'ultimate_post_kit_options';
	const NONCE_ACTION = 'upk_promo_offer_settings';
	const AJAX_ACTION  = 'upk_save_promo_offer_settings';
	const OPTION_NAME  = 'bdt_hide_promotional_offers';

	public function __construct() {
		add_action( 'upk_after_dashboard_request_feature', [ $this, 'render_control' ] );
		add_action( 'admin_init', [ $this, 'register_scripts' ], 20 );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'save_ajax' ] );
	}

	private function dashboard_page() {
		return (string) apply_filters( 'upk_promo_offer_settings_page', self::OPTIONS_PAGE );
	}

	private function is_plugin_dashboard() {
		$page = $this->dashboard_page();

		return '' !== $page
			&& isset( $_GET['page'] )
			&& $page === sanitize_text_field( wp_unslash( $_GET['page'] ) );
	}

	private function option_name() {
		if ( class_exists( 'Bdt_Admin_Api_Biggopti_Helper', false ) ) {
			return \Bdt_Admin_Api_Biggopti_Helper::global_author_suppression_option();
		}

		return self::OPTION_NAME;
	}

	private function is_hidden() {
		if ( class_exists( 'Bdt_Admin_Api_Biggopti_Helper', false ) ) {
			return \Bdt_Admin_Api_Biggopti_Helper::is_promo_author_suppression_effective();
		}

		$data = get_option( $this->option_name(), [] );

		return is_array( $data ) && ! empty( $data['duration'] );
	}

	private function save_hidden( $hidden ) {
		$hidden = (bool) $hidden;

		if ( class_exists( 'Bdt_Admin_Api_Biggopti_Helper', false ) ) {
			\Bdt_Admin_Api_Biggopti_Helper::set_promo_author_suppression_hidden( $hidden );
		} elseif ( $hidden ) {
			update_option(
				$this->option_name(),
				[
					'duration'   => 'lifetime',
					'enabled_at' => time(),
					'expires_at' => 0,
				],
				false
			);
		} else {
			delete_option( $this->option_name() );
		}

		return $this->is_hidden() === $hidden;
	}

	public function register_scripts() {
		if ( ! $this->is_plugin_dashboard() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_script( 'jquery' );

		wp_add_inline_script(
			'jquery',
			$this->get_control_script(),
			'after'
		);
	}

	private function get_control_script() {
		$ajax_url = admin_url( 'admin-ajax.php' );
		$nonce    = wp_create_nonce( self::NONCE_ACTION );
		$action   = self::AJAX_ACTION;
		$saved    = esc_js( __( 'Saved', 'ultimate-post-kit' ) );
		$error    = esc_js( __( 'Could not save.', 'ultimate-post-kit' ) );

		return <<<JS
document.addEventListener('change', function (event) {
	var input = event.target;

	if (!input || input.id !== 'upk-show-promotional-offers') {
		return;
	}

	var chip = document.getElementById('upk-promo-offer-settings-chip');
	var status = chip ? chip.querySelector('.upk-dashboard-promo-prefs-status') : null;

	if (status) {
		status.classList.remove('is-error', 'is-success');
		status.textContent = '';
	}

	var body = new URLSearchParams();
	body.append('action', '{$action}');
	body.append('nonce', '{$nonce}');
	body.append('show_promotional_offers', input.checked ? '1' : '0');

	fetch('{$ajax_url}', {
		method: 'POST',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
		},
		body: body.toString()
	})
		.then(function (response) {
			return response.json();
		})
		.then(function (response) {
			if (response && response.success) {
				if (status) {
					status.classList.add('is-success');
					status.textContent = '{$saved}';
				}

				if (response.data && response.data.reload) {
					window.location.reload();
				}

				return;
			}

			throw new Error(
				response && response.data && response.data.message
					? response.data.message
					: '{$error}'
			);
		})
		.catch(function (err) {
			if (status) {
				status.classList.add('is-error');
				status.textContent = err && err.message ? err.message : '{$error}';
			}

			input.checked = !input.checked;
		});
});
JS;
	}

	public function render_control() {
		if ( ! $this->is_plugin_dashboard() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$checked  = ! $this->is_hidden();
		$input_id = 'upk-show-promotional-offers';
		$label    = __( 'Show promotional offers', 'ultimate-post-kit' );
		?>
		<div class="upk-dashboard-promo-prefs bdt-margin-medium-top" id="upk-promo-offer-settings-chip">
			<label class="upk-dashboard-promo-prefs-label" for="<?php echo esc_attr( $input_id ); ?>">
				<input
					type="checkbox"
					class="upk-dashboard-promo-prefs-checkbox"
					id="<?php echo esc_attr( $input_id ); ?>"
					name="upk_show_promotional_offers"
					value="1"
					<?php checked( $checked ); ?>
				/>
				<span class="upk-dashboard-promo-prefs-checkmark" aria-hidden="true"></span>
				<span class="upk-dashboard-promo-prefs-text"><?php echo esc_html( $label ); ?></span>
			</label>
			<span class="upk-dashboard-promo-prefs-status" aria-live="polite"></span>
		</div>
		<?php
	}

	public function save_ajax() {
		if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'ultimate-post-kit' ) ] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'ultimate-post-kit' ) ] );
		}

		$show = ! isset( $_POST['show_promotional_offers'] )
			|| '1' === (string) wp_unslash( $_POST['show_promotional_offers'] );
		$hide = ! $show;

		if ( ! $this->save_hidden( $hide ) ) {
			wp_send_json_error( [ 'message' => __( 'Could not save settings.', 'ultimate-post-kit' ) ] );
		}

		wp_send_json_success(
			[
				'message' => __( 'Saved', 'ultimate-post-kit' ),
				'show'    => $show,
				'hidden'  => $hide,
				'reload'  => true,
			]
		);
	}
}

new Promo_Offer_Settings();
