<?php
namespace UltimatePostKit\Modules\TinyList;
use UltimatePostKit\Traits\Global_Widget_Functions;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_nopriv_upk_tiny_list_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
		add_action( 'wp_ajax_upk_tiny_list_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
	}

	public function get_name() {
		return 'tiny-list';
	}

	public function get_widgets() {

		$widgets = [
			'Tiny_List',
		];
		
		return $widgets;
	}

	public function callback_ajax_loadmore_posts() {

		$settings = [];

		if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
			$settings = map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' );
		}

		// $post_type = $settings['posts_source'] ?? 'post';

		$settings = array_merge(
			[
				'posts_source'                   => 'post',
				'posts_orderby'                  => 'date',
				'posts_order'                    => 'DESC',
				'posts_ignore_sticky_posts'      => 'no',
				'posts_only_with_featured_image' => 'no',
				'posts_select_date'              => '',
				'posts_exclude_by'               => [],
				'posts_include_by'               => [],
				'posts_per_page'                 => isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 0,
				'posts_offset'                   => isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0,
			],
			$settings
		);

		$ajaxposts = $this->query_args( $settings );

		ob_start();
		$found_posts = false;

		if ( $ajaxposts->have_posts() ) {
			$placeholder = \Elementor\Utils::get_placeholder_image_src();

			while ( $ajaxposts->have_posts() ) {
				$ajaxposts->the_post();
				$found_posts = true;

				$title       = get_the_title();
				$post_link   = esc_url( get_permalink() );

				$image_src   = wp_get_attachment_image_src( get_post_thumbnail_id(), $settings['primary_thumbnail_size'] ?? 'thumbnail' );
				$image_src   = $image_src ? $image_src[0] : $placeholder;

				$title_tag   = Utils::get_valid_html_tag($settings['title_tags']);

				$onclick = '';
				if ( ! empty( $settings['global_link'] ) && $settings['global_link'] === 'yes' ) {
					$onclick = 'onclick="window.open(\'' . esc_url( $post_link ) . '\', \'_self\')"';
				}

				?>
				<div <?php echo $onclick; ?> class="upk-item">
					<div class="upk-content upk-flex upk-flex-middle">

						<?php if ( $settings['show_counter_number'] === 'yes' ) : ?>
							<div class="upk-counter"></div>
						<?php endif; ?>

						<?php if ( $settings['show_image'] === 'yes' ) : ?>
							<img class="upk-img" src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $title ); ?>">
						<?php endif; ?>

						<?php if ( ! empty( $settings['show_item_icon']['value'] ) ) : ?>
							<div class="upk-title-icon">
								<?php \Elementor\Icons_Manager::render_icon( $settings['show_item_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( ! isset( $settings['show_title'] ) || $settings['show_title'] === 'yes' ) : ?>
							<<?php echo esc_attr( $title_tag ); ?> class="upk-title">
								<a 
									href="<?php echo esc_url($post_link); ?>"
									title="<?php echo esc_attr( $title ); ?>" 
									class="title-animation-<?php echo esc_attr( $settings['title_style'] ?? '' ); ?>"
								>
									<?php echo esc_html( $title ); ?>
								</a>
							</<?php echo esc_attr( $title_tag ); ?>>
						<?php endif; ?>
					</div>
				</div>
				<?php
			}
		}

		wp_reset_postdata();
		$markup = ob_get_clean();

		wp_send_json(
			[
				'success' => $found_posts,
				'markup'  => $found_posts ? $markup : esc_html__( 'No more found', 'ultimate-post-kit' ),
			]
		);

	}
}
