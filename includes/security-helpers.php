<?php
/**
 * Small, dependency-free helpers that guard widget-controlled input.
 *
 * Widget settings are saved verbatim by Elementor: a SELECT, URL or ID setting holds
 * whatever was sent to the save endpoint, and text saved by users without
 * unfiltered_html only goes through kses, which keeps entity-encoded markup. These
 * helpers cover the places where such values, or text written by visitors, reach
 * markup that a browser or WordPress reads again.
 *
 * @package UltimatePostKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'ultimate_post_kit_window_open_handler' ) ) {
	/**
	 * Build a safe window.open() call for an onclick attribute.
	 *
	 * Card and list items open their post with an onclick handler. Putting esc_url()
	 * output inside a quoted JS string is not safe: esc_url() turns ' into &#039;,
	 * esc_attr() does not double-encode it, and the browser decodes it back to '
	 * before the handler is compiled, so ?');alert(1);(' breaks out of the string.
	 * Permalinks are filterable (plugins that send a post to a custom URL, such as
	 * external-link plugins, set them per post), so they are treated like any other
	 * URL. Here the URL is JSON-encoded with every quote and markup character
	 * hex-escaped, and only web, mail, phone and relative URLs are accepted.
	 *
	 * @param string $url    Link URL.
	 * @param bool   $is_new Open in a new tab.
	 * @return string The handler, or an empty string when the URL is not allowed.
	 */
	function ultimate_post_kit_window_open_handler( $url, $is_new = false ) {
		// A URL saved by a user without unfiltered_html has been through kses, which
		// stores & as &amp;. The browser used to decode that inside the attribute; the
		// JSON string below is not decoded again, so decode it here, before the scheme
		// check runs on the real value.
		$url = html_entity_decode( (string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$url = esc_url_raw( trim( $url ), [ 'http', 'https', 'mailto', 'tel' ] );

		if ( '' === $url ) {
			return '';
		}

		$flags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP;

		return 'window.open(' . wp_json_encode( $url, $flags ) . ', ' . wp_json_encode( $is_new ? '_blank' : '_self' ) . ')';
	}
}

if ( ! function_exists( 'ultimate_post_kit_escape_shortcode_brackets' ) ) {
	/**
	 * Turn the square brackets in visitor-supplied text into HTML entities.
	 *
	 * Elementor output is part of the_content, and core runs do_shortcode() on it
	 * afterwards, including inside quoted attribute values. Text written by visitors
	 * (a comment, a commenter's name, a user's display name or biography) that is
	 * printed into that output must therefore not keep a literal [ or ], or the
	 * visitor can run any registered shortcode on the page. The entities display as
	 * the same characters, and esc_attr()/esc_html()/wp_kses_post() leave them as
	 * they are.
	 *
	 * @param string $text Text from a visitor or user.
	 * @return string
	 */
	function ultimate_post_kit_escape_shortcode_brackets( $text ) {
		return str_replace( [ '[', ']' ], [ '&#91;', '&#93;' ], (string) $text );
	}
}

if ( ! function_exists( 'ultimate_post_kit_sanitize_public_taxonomy' ) ) {
	/**
	 * Restrict a widget's taxonomy setting to the public taxonomies its control offers.
	 *
	 * Elementor stores a SELECT control's value verbatim, so a saved widget can name
	 * any registered taxonomy (or several, as an array), including private ones
	 * (menus, theme or plugin bookkeeping), whose term names, descriptions and counts
	 * would then be listed to visitors. The controls only offer public taxonomies.
	 * A name that is not a registered taxonomy is returned unchanged: the term query
	 * lists nothing for it, as before.
	 *
	 * @param mixed  $taxonomy Saved setting.
	 * @param string $fallback Taxonomy to use instead of a private one.
	 * @return string
	 */
	function ultimate_post_kit_sanitize_public_taxonomy( $taxonomy, $fallback = 'category' ) {
		if ( ! is_scalar( $taxonomy ) ) {
			return $fallback;
		}

		$taxonomy = (string) $taxonomy;
		$object   = get_taxonomy( $taxonomy );

		return ( $object && empty( $object->public ) ) ? $fallback : $taxonomy;
	}
}

if ( ! function_exists( 'ultimate_post_kit_document_owner_id' ) ) {
	/**
	 * The author of the Elementor document being rendered (the page, or the template
	 * that carries the widget), falling back to the current post's author.
	 *
	 * A widget's settings are chosen by whoever built the page, so a widget that may
	 * list data its builder could not see for themselves checks this user's
	 * capabilities, not the visitor's.
	 *
	 * @return int User ID, or 0 when unknown.
	 */
	function ultimate_post_kit_document_owner_id() {
		$documents = ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->documents ) ) ? \Elementor\Plugin::$instance->documents : null;
		$document  = $documents ? $documents->get_current() : null;

		if ( $document && method_exists( $document, 'get_main_id' ) ) {
			$owner_id = (int) get_post_field( 'post_author', $document->get_main_id() );
			if ( $owner_id ) {
				return $owner_id;
			}
		}

		$post_id = get_the_ID();

		return $post_id ? (int) get_post_field( 'post_author', $post_id ) : 0;
	}
}
