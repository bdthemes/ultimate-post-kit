<?php
/**
 * Main File
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ultimate_post_kit_reviews_init' ) ) {
	function ultimate_post_kit_reviews_init( $params ) {

		// is_admin() is also true on admin-ajax.php, which fires admin_init before any
		// authentication, so without this the SDK's constructor (and its option writes)
		// would run for anonymous callers. Logged-in requests must still reach it during
		// AJAX, because the constructor is what registers this SDK's own ajax handlers.
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( is_admin() ) :

			$menu_slug    = isset( $params['menu']['slug'] ) ? $params['menu']['slug'] : false;
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only check of the current admin page slug for display routing, no form data processed.
			$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : false;

			/**
			 * Attach SDK to current page
			 */
			$params['current_page'] = $current_page;
			$params['menu_slug']    = $menu_slug;

			/**
			 * Include SDK
			 */
			require_once dirname( __FILE__ ) . '/notice.php';
			ultimate_post_kit_reviews_automate( $params );

		endif;
	}
}
