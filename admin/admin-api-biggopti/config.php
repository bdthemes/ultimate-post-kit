<?php

defined( 'ABSPATH' ) || exit;

/**
 * Ultimate Post Kit port settings for admin-api-biggopti.
 *
 * @return array<string, mixed>
 */
return [
	'slug'              => 'ultimate-post-kit',
	'underscore'        => 'upk',
	'options_page'      => 'ultimate_post_kit_options',
	'notice_class'      => 'ultimate-post-kit-biggopti',
	'plugin_id'         => 'ultimate-post-kit',
	'legacy_config_key' => 'UltimatePostKitAdminApiBiggoptiConfig',
	'is_pro_callback'   => '_is_upk_pro_activated',
	'api_url'           => 'https://api.sigmative.io/prod/store/api/biggopti/api-data-all-records',
	'fallback_promo'    => [
		'sub_title' => 'Go Pro',
		'link'      => 'https://postkit.pro/pricing/?utm_source=WordPress_org&utm_medium=bfcm_cta&utm_campaign=ultimate_post_kit',
	],
	'constants'         => [
		'version'         => 'BDTUPK_VER',
		'plugin_basename' => 'BDTUPK_PBNAME',
		'admin_url'       => 'BDTUPK_ADMIN_URL',
		'assets_url'      => 'BDTUPK_ASSETS_URL',
	],
	'legacy_display_id_prefixes'   => [
		'bdt-admin-api-biggopti-',
		'bdt-admin-biggopti-api-biggopti-',
	],
	'legacy_dismiss_ajax_actions'  => [
		'upk_admin_api_biggopti_dismiss',
		'ps_admin_api_biggopti_dismiss',
	],
	'legacy_dismiss_nonce_actions' => [
		'ultimate-post-kit',
		'prime-slider',
		'bdthemes-prime-slider-lite',
	],
	'legacy_script_handles'        => [
		'upk-admin-api-biggopti',
	],
	'legacy_style_handles'         => [
		'upk-admin-api-biggopti',
		'bdt-admin-api-biggopti',
	],
	'promo_suppression_sources'      => [],
	'use_test_api_response'        => false,
	'test_api_response_file'       => 'test-api-response.json',
];
