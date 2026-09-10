<?php

namespace UltimatePostKit\Traits;

use UltimatePostKit\Utils;

defined('ABSPATH') || die();

trait Global_Widget_Functions {

	/**
	 * Render Ajax Qery Posts
	 */
	function mapGroupControlQuery($term_ids = []) {
		$terms = get_terms(
			[
				'term_taxonomy_id' => $term_ids,
				'hide_empty' => false,
			]
		);

		$tax_terms_map = [];

		foreach ($terms as $term) {
			$taxonomy = $term->taxonomy;
			$tax_terms_map[$taxonomy][] = $term->term_id;
		}

		return $tax_terms_map;
	}
	function query_args() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in the load-more AJAX handler (check_ajax_referer 'upk-site') before this runs.
		if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in the load-more AJAX handler (check_ajax_referer 'upk-site') before this runs.
			$request_settings = map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' );

			unset( $request_settings['this'], $request_settings['GLOBALS'] );

			extract( $request_settings, EXTR_SKIP );
		}

		// This handler is reachable unauthenticated (wp_ajax_nopriv_*). Clamp the
		// page size to a sane positive maximum so a request cannot ask for -1
		// ("all posts") or a huge value and turn load-more into a DoS amplifier.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in the load-more AJAX handler (check_ajax_referer 'upk-site') before this runs.
		$per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 0;
		$per_page = max( 1, min( 100, $per_page ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in the load-more AJAX handler (check_ajax_referer 'upk-site') before this runs.
		$offset   = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;

		// setmeta args
		$args = [
			'posts_per_page' => $per_page,
			'post_status' => 'publish',
			'suppress_filters' => false,
			'orderby' => $posts_orderby,
			'order' => $posts_order,
			'offset' => $offset,
		];
		/**
		 * set feature image
		 *
		 */
		if (isset($posts_only_with_featured_image) && $posts_only_with_featured_image === 'yes') {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Elementor widget query built from user-configured controls; expected behaviour.
			$args['meta_query'] = [
				[
					'key' => '_thumbnail_id',
					'compare' => 'EXISTS',
				],
			];
		}

		/**
		 * set taxonomy query
		 */

		/**
		 * set date query
		 */

		$selected_date = $posts_select_date;

		if (!empty($selected_date)) {
			$date_query = [];

			switch ($selected_date) {
				case 'today':
					$date_query['after'] = '-1 day';
					break;

				case 'week':
					$date_query['after'] = '-1 week';
					break;

				case 'month':
					$date_query['after'] = '-1 month';
					break;

				case 'quarter':
					$date_query['after'] = '-3 month';
					break;

				case 'year':
					$date_query['after'] = '-1 year';
					break;

				case 'exact':
					$after_date = $posts_date_after;
					if (!empty($after_date)) {
						$date_query['after'] = $after_date;
					}

					$before_date = $posts_date_before;
					if (!empty($before_date)) {
						$date_query['before'] = $before_date;
					}
					$date_query['inclusive'] = true;
					break;
			}

			if (!empty($date_query)) {
				$args['date_query'] = $date_query;
			}
		}

		$exclude_by = isset($posts_exclude_by) ? $posts_exclude_by : [];
		$include_by = isset($posts_include_by) ? $posts_include_by : [];
		$exclude_by = is_array($exclude_by) ? $exclude_by : ( '' === $exclude_by || null === $exclude_by ? [] : [ $exclude_by ] );
		$include_by = is_array($include_by) ? $include_by : ( '' === $include_by || null === $include_by ? [] : [ $include_by ] );
		$include_users = [];
		$exclude_users = [];
		// print_r($exclude_by);
		/**
		 * ignore sticky post
		 */
		if (!empty($exclude_by) && $posts_source === 'post' && $posts_ignore_sticky_posts === 'yes') {
			$args['ignore_sticky_posts'] = true;
			if (in_array('current_post', $exclude_by)) {
				// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- Elementor widget query built from user-configured controls; expected behaviour.
				$args['post__not_in'] = [get_the_ID()];
			}
		}

		/**
		 * set post type
		 */

		if ($posts_source === 'manual_selection') {
			/**
			 * Set Including Manually
			 */
			$selected_ids = $posts_selected_ids;
			$selected_ids = wp_parse_id_list($selected_ids);
			$args['post_type'] = 'any';
			if (!empty($selected_ids)) {
				$args['post__in'] = $selected_ids;
			}
			$args['ignore_sticky_posts'] = 1;
		} elseif ('current_query' === $posts_source) {
			/**
			 * Make Current Query
			 */
			$args = $GLOBALS['wp_query']->query_vars;
			$args = apply_filters('ultimate_post_kit/query/get_query_args/current_query', $args);
		} elseif ('_related_post_type' === $posts_source) {
			/**
			 * Set Related Query
			 */
			$post_id = get_queried_object_id();
			$related_post_id = is_singular() && (0 !== $post_id) ? $post_id : null;
			$args['post_type'] = get_post_type($related_post_id);

			// $include_by = $this->getGroupControlQueryParamBy('include');
			if (in_array('authors', $include_by) && isset($posts_include_author_ids)) {
				$args['author__in'] = wp_parse_id_list($posts_include_author_ids);
			} else {
				$args['author__in'] = get_post_field('post_author', $related_post_id);
			}

			// $exclude_by = $this->getGroupControlQueryParamBy('exclude');
			if (in_array('authors', $exclude_by)) {
				$args['author__not_in'] = wp_parse_id_list($posts_exclude_author_ids);
			}

			if (in_array('current_post', $exclude_by)) {
				// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- Elementor widget query built from user-configured controls; expected behaviour.
				$args['post__not_in'] = [get_the_ID()];
			}

			$args['ignore_sticky_posts'] = 1;
			$args = apply_filters('ultimate_post_kit/query/get_query_args/related_query', $args);
		} else {
			// This runs on wp_ajax_nopriv_* and $posts_source comes straight from $_POST,
			// so restrict it to post types this site already exposes to anonymous visitors.
			// Without this a request can enumerate published entries of post types that are
			// deliberately hidden from anonymous access (e.g. Elementor's elementor_library).
			$args['post_type'] = ultimate_post_kit_sanitize_public_post_type( $posts_source );
			$current_post = [];

			/**
			 * Set Taxonomy && Set Authors
			 */
			$include_terms = [];
			$exclude_terms = [];
			$terms_query = [];

			/**
			 * Set exclude Terms
			 */
			if (!empty($exclude_by)) {
				if (in_array('authors', $exclude_by)) {
					$exclude_users = wp_parse_id_list($posts_exclude_author_ids);
					$include_users = array_diff($include_users, $exclude_users);
				}
				if (!empty($exclude_users)) {
					$args['author__not_in'] = $exclude_users;
				}
				if (in_array('current_post', $exclude_by) && is_singular()) {
					$current_post[] = get_the_ID();
				}
				if (in_array('manual_selection', $exclude_by)) {
					$exclude_ids = $posts_exclude_ids;
					// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- Elementor widget query built from user-configured controls; expected behaviour.
					$args['post__not_in'] = array_merge($current_post, wp_parse_id_list($exclude_ids));
				}
				if (in_array('terms', $exclude_by)) {
					$exclude_terms = wp_parse_id_list($posts_exclude_term_ids);
					$include_terms = array_diff($include_terms, $exclude_terms);
				}
				if (!empty($exclude_terms)) {
					$tax_terms_map = $this->mapGroupControlQuery($exclude_terms);

					foreach ($tax_terms_map as $tax => $terms) {
						$terms_query[] = [
							'taxonomy' => $tax,
							'field' => 'term_id',
							'terms' => $terms,
							'operator' => 'NOT IN',
						];
					}
				}
			}

			/**
			 * Set include Terms
			 */
			if (!empty($include_by)) {

				if (in_array('authors', $include_by)) {
					$include_users = wp_parse_id_list($posts_include_author_ids);
				}
				if (!empty($include_users)) {
					$args['author__in'] = $include_users;
				}
				if (in_array('terms', $include_by)) {
					$include_terms = wp_parse_id_list($posts_include_term_ids);
				}
				$tax_terms_map = $this->mapGroupControlQuery($include_terms);
				foreach ($tax_terms_map as $tax => $terms) {
					$terms_query[] = [
						'taxonomy' => $tax,
						'field' => 'term_id',
						'terms' => $terms,
						'operator' => 'IN',
					];
				}
			}


			if (!empty($terms_query)) {
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Elementor widget query built from user-configured controls; expected behaviour.
				$args['tax_query'] = $terms_query;
				$args['tax_query']['relation'] = 'AND';
			}
		}

		if ( empty( $args['post_status'] ) || 'publish' !== $args['post_status'] ) {
			$args['post_status'] = 'publish';
		}

		$ajaxposts = new \WP_Query($args);
		return $ajaxposts;
	}

	/**
	 * Render Ajax LoadMore
	 */
	function render_ajax_loadmore() {
		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('ajax_loadmore_enable')) {
			return;
		}
		?>
		<div class="upk-loadmore-container">
			<?php if ($settings['ajax_loadmore_btn'] == 'yes') : ?>
				<span class="upk-loadmore-btn"><?php esc_html_e('Load More', 'ultimate-post-kit'); ?></span>
			<?php elseif ($settings['ajax_loadmore_infinite_scroll'] == 'yes') : ?>
				<div class="upk-ajax-loading" style="display: none;"></div>
			<?php endif; ?>
		</div>
		<?php 
	}

	/**
	 * New Image Render Method With Lazy Load Support
	 *
	 * @return void
	 */

	function render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src             = wp_get_attachment_image_src($image_id, $size);

		if (!$image_src) {
			printf('<img class="upk-img" src="%1$s" alt="%2$s">', esc_url($placeholder_image_src), esc_attr(get_the_title()));
		} else {
			print(wp_get_attachment_image(
				$image_id,
				$size,
				false,
				[
					'class' => 'upk-img',
					'alt'   => esc_attr(get_the_title())
				]
			));
		}
	}

	/**
	 * Old Image Render Method
	 * Not Using Anymore
	 * @return void
	 */
	function __render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src = wp_get_attachment_image_src($image_id, $size);
		if (!$image_src) {
			$image_src = $placeholder_image_src;
		} else {
			$image_src = $image_src[0];
		}
?>
		<img class="upk-img" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
	<?php
	}

	function render_title($widget_name) {
		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_title')) {
			return;
		}
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- established hook name relied on across the plugin family; renaming would break integration.
		apply_filters('upk/' . $widget_name . '/before/title', '');
		$title = get_the_title();
		printf(
			'<%1$s class="upk-title"><a href="%2$s" title="%5$s" class="title-animation-%4$s" aria-label="%5$s">%3$s</a></%1$s>',
			esc_attr(Utils::get_valid_html_tag($settings['title_tags'])),
			esc_url( get_permalink() ),
			esc_html( $title ),
			esc_attr($settings['title_style']),
			esc_attr( $title )
		);
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- established hook name relied on across the plugin family; renaming would break integration.
		apply_filters('upk/' . $widget_name . '/after/title', '');
	}



	function render_category() {
		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-category">
			<?php echo wp_kses_post( upk_get_category($this->get_settings('posts_source')) ); ?>
		</div>
	<?php
	}

	function render_date() {
		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_date')) {
			return;
		}
		$meta_separator = isset( $settings['meta_separator'] ) ? $settings['meta_separator'] : '.';
	?>
		<div class="upk-date">
			<?php if ($settings['human_diff_time'] == 'yes') {
				echo esc_html( ultimate_post_kit_post_time_diff( ($settings['human_diff_time_short'] == 'yes') ? 'short' : '' ) );
			} else {
				echo get_the_date();
			} ?>
		</div>

		<?php if ($settings['show_time']) : ?>
			<div class="upk-post-time" data-separator="<?php echo esc_attr( $meta_separator ); ?>">
				<i class="upk-icon-clock" aria-hidden="true"></i>
				<?php echo esc_html( get_the_time() ); ?>
			</div>
		<?php endif; ?>
	<?php
	}

	function render_excerpt($excerpt_length) {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_excerpt')) {
			return;
		}
		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

		$ellipsis = isset($settings['ellipsis']) ? $settings['ellipsis'] : '' ;
		?>
		<div class="upk-text">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo wp_kses_post( ultimate_post_kit_custom_excerpt($excerpt_length, $strip_shortcode, $ellipsis) );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Localized comment count with label. Echo with esc_html().
	 *
	 * @param int $post_id Post ID, or 0 for current post in The Loop.
	 * @return string
	 */
	protected function upk_get_formatted_comments_count( $post_id = 0 ) {
		return Utils::get_formatted_comments_count( $post_id );
	}

	/**
	 * Localized comment count number only. Echo with esc_html().
	 *
	 * @param int $post_id Post ID, or 0 for current post in The Loop.
	 * @return string
	 */
	protected function upk_get_localized_comment_count( $post_id = 0 ) {
		return Utils::get_localized_comment_count( $post_id );
	}

	function render_post_format() {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_post_format']) {
			return;
		}
	?>
		<div class="upk-post-format">
			<a href="<?php echo esc_url(get_permalink()) ?>">
				<?php if (has_post_format('aside')) : ?>
					<i class="upk-icon-aside" aria-hidden="true"></i>
				<?php elseif (has_post_format('gallery')) : ?>
					<i class="upk-icon-gallery" aria-hidden="true"></i>
				<?php elseif (has_post_format('link')) : ?>
					<i class="upk-icon-link" aria-hidden="true"></i>
				<?php elseif (has_post_format('image')) : ?>
					<i class="upk-icon-image" aria-hidden="true"></i>
				<?php elseif (has_post_format('quote')) : ?>
					<i class="upk-icon-quote" aria-hidden="true"></i>
				<?php elseif (has_post_format('status')) : ?>
					<i class="upk-icon-status" aria-hidden="true"></i>
				<?php elseif (has_post_format('video')) : ?>
					<i class="upk-icon-video" aria-hidden="true"></i>
				<?php elseif (has_post_format('audio')) : ?>
					<i class="upk-icon-music" aria-hidden="true"></i>
				<?php elseif (has_post_format('chat')) : ?>
					<i class="upk-icon-chat" aria-hidden="true"></i>
				<?php else : ?>
					<i class="upk-icon-post" aria-hidden="true"></i>
				<?php endif; ?>
			</a>
		</div>
<?php
	}
}
