<?php
/**
 * Keeps paginated post widgets on archive templates from ending in a 404.
 *
 * WordPress counts the pages of a blog, category, tag, author, date, search or
 * post type archive with its own query, which uses Settings > Reading > "Blog
 * pages show at most". A post widget placed on the archive template (Element
 * Pack or Elementor Pro Theme Builder) runs its own query with its own posts
 * per page, so its pagination can link to more pages than WordPress has. For
 * those pages the main query finds no posts and WordPress answers with a 404
 * before the template runs.
 *
 * Before WordPress decides, this checks the archive template the request will
 * use. When one of its Ultimate Post Kit widgets with pagination has posts for
 * the requested page, the 404 is skipped and the template renders as usual.
 *
 * @package UltimatePostKit
 */

namespace UltimatePostKit\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pagination_404 {

	/**
	 * Widget type prefix of the Ultimate Post Kit (and Pro) widgets.
	 */
	const WIDGET_PREFIX = 'upk-';

	/**
	 * Templates nested deeper than this are not inspected.
	 */
	const MAX_DEPTH = 20;

	public static function init() {
		add_filter( 'pre_handle_404', [ __CLASS__, 'maybe_allow_widget_page' ], 10, 2 );
	}

	/**
	 * @param bool      $handled  Whether another callback already handled the 404 decision.
	 * @param \WP_Query $wp_query The main query.
	 *
	 * @return bool
	 */
	public static function maybe_allow_widget_page( $handled, $wp_query ) {
		if ( false !== $handled || ! $wp_query instanceof \WP_Query || ! $wp_query->is_main_query() || is_admin() ) {
			return $handled;
		}

		// Only the case WordPress is about to answer with a 404: a paged archive
		// whose own query has no posts on this page. Singular pages are not
		// affected; their /page/N/ sets `paged`, which WordPress does not check there.
		if ( $wp_query->is_singular() || ! $wp_query->is_paged() || ! empty( $wp_query->posts ) ) {
			return $handled;
		}

		if ( ! $wp_query->is_home() && ! $wp_query->is_archive() && ! $wp_query->is_search() ) {
			return $handled;
		}

		// A term, author or post type archive must still exist.
		if ( ( $wp_query->is_category() || $wp_query->is_tag() || $wp_query->is_tax() || $wp_query->is_author() || $wp_query->is_post_type_archive() ) && ! $wp_query->get_queried_object() ) {
			return $handled;
		}

		// A date archive with no posts at all is a 404 on its first page too, so keep
		// it one. (found_posts is not computed when the page itself has no posts.)
		if ( $wp_query->is_date() && ! self::archive_has_posts( $wp_query ) ) {
			return $handled;
		}

		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			return $handled;
		}

		$page = max( 1, (int) $wp_query->get( 'paged' ) );

		foreach ( self::get_template_ids() as $template_id ) {
			if ( self::template_has_page( $template_id, $page ) ) {
				return true;
			}
		}

		return $handled;
	}

	/**
	 * Whether the archive's own query has any post on its first page.
	 *
	 * @param \WP_Query $wp_query The main query.
	 *
	 * @return bool
	 */
	protected static function archive_has_posts( \WP_Query $wp_query ) {
		$args = array_merge(
			$wp_query->query_vars,
			[
				'paged'                  => 1,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			]
		);
		unset( $args['offset'] );

		$probe = new \WP_Query( $args );

		return ! empty( $probe->posts );
	}

	/**
	 * Archive templates that will render the current request.
	 *
	 * @return int[]
	 */
	protected static function get_template_ids() {
		$ids = [];

		// Element Pack Theme Builder.
		if ( class_exists( '\ElementPack\Includes\Builder\Builder_Integration' )
			&& method_exists( '\ElementPack\Includes\Builder\Builder_Integration', 'instance' ) ) {
			$builder = \ElementPack\Includes\Builder\Builder_Integration::instance();

			if ( is_object( $builder ) && method_exists( $builder, 'set_builder_template' ) && property_exists( $builder, 'current_template_id' ) ) {
				// Resolve the template the same way template_include will, then leave
				// the builder as it was. For archive requests this lookup only sets
				// current_template_id.
				$previous = $builder->current_template_id;

				try {
					$builder->current_template_id = null;
					$builder->set_builder_template( '' );
					if ( $builder->current_template_id ) {
						$ids[] = (int) $builder->current_template_id;
					}
				} catch ( \Throwable $e ) {
					// Fall through: no Element Pack template for this request.
				}

				$builder->current_template_id = $previous;
			}
		}

		// Elementor Pro Theme Builder.
		if ( class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
			try {
				$documents = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location( 'archive' );
				foreach ( (array) $documents as $document_id => $document ) {
					$ids[] = (int) $document_id;
				}
			} catch ( \Throwable $e ) {
				// Fall through: no Elementor Pro template for this request.
			}
		}

		/**
		 * Filters the Elementor template IDs inspected for paginated Ultimate Post Kit widgets
		 * before WordPress answers a paged archive request with a 404.
		 *
		 * @param int[] $ids Template (Elementor document) IDs.
		 */
		$ids = apply_filters( 'ultimate_post_kit/pagination/archive_template_ids', $ids );

		return array_values( array_unique( array_filter( array_map( 'absint', (array) $ids ) ) ) );
	}

	/**
	 * Whether a paginated Ultimate Post Kit widget in the template has posts on $page.
	 *
	 * @param int $template_id Elementor document ID.
	 * @param int $page        Requested page.
	 *
	 * @return bool
	 */
	protected static function template_has_page( $template_id, $page ) {
		$document = \Elementor\Plugin::$instance->documents->get( $template_id );

		if ( ! $document || ! $document->is_built_with_elementor() ) {
			return false;
		}

		$elements = $document->get_elements_data();

		return is_array( $elements ) && self::elements_have_page( $elements, $page, 0 );
	}

	/**
	 * @param array $elements Elementor elements data.
	 * @param int   $page     Requested page.
	 * @param int   $depth    Nesting depth.
	 *
	 * @return bool
	 */
	protected static function elements_have_page( array $elements, $page, $depth ) {
		if ( $depth > self::MAX_DEPTH ) {
			return false;
		}

		foreach ( $elements as $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}

			if ( isset( $element['elType'], $element['widgetType'] ) && 'widget' === $element['elType']
				&& 0 === strpos( (string) $element['widgetType'], self::WIDGET_PREFIX )
				&& isset( $element['settings']['show_pagination'] ) && 'yes' === $element['settings']['show_pagination']
				&& self::widget_has_page( $element, $page ) ) {
				return true;
			}

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] )
				&& self::elements_have_page( $element['elements'], $page, $depth + 1 ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Run the widget's own query (by rendering it, output discarded) and compare its page count.
	 *
	 * @param array $element Elementor element data of the widget.
	 * @param int   $page    Requested page.
	 *
	 * @return bool
	 */
	protected static function widget_has_page( array $element, $page ) {
		try {
			$widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element );

			if ( ! $widget || ! method_exists( $widget, 'get_query' ) ) {
				return false;
			}

			$level = ob_get_level();
			ob_start();
			try {
				$widget->render_content();
			} finally {
				while ( ob_get_level() > $level ) {
					ob_end_clean();
				}
			}

			$query = $widget->get_query();
		} catch ( \Throwable $e ) {
			return false;
		}

		wp_reset_postdata();

		return $query instanceof \WP_Query && $page <= (int) $query->max_num_pages && $query->have_posts();
	}
}
