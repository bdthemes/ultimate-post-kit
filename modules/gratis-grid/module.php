<?php
namespace UltimatePostKit\Modules\GratisGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_nopriv_upk_gratis_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
		add_action( 'wp_ajax_upk_gratis_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
	}

	public function get_name() {
		return 'gratis-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Gratis_Grid',
		];
		
		return $widgets;
	}

	public function callback_ajax_loadmore_posts() {

		$settings = [];

		if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
			$settings = map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' );
		}

		$post_type = $settings['posts_source'] ?? 'post';
	
		$settings = array_merge(
			[
				'posts_source'                 => 'post',
				'posts_orderby'                => 'date',
				'posts_order'                  => 'DESC',
				'posts_ignore_sticky_posts'    => 'no',
				'posts_only_with_featured_image' => 'no',
				'posts_select_date'            => '',
				'posts_exclude_by'             => [],
				'posts_include_by'             => [],
				'posts_per_page'               => isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 0,
				'posts_offset'                 => isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0,
			],
			$settings
		);
	
		$ajaxposts = $this->query_args($settings);
	
		ob_start();
		$found_posts = false;
	
		if ( $ajaxposts->have_posts() ) {

			$placeholder = \Elementor\Utils::get_placeholder_image_src();
			$meta_sep    = $settings['meta_separator'] ?? '/';
	
			while ( $ajaxposts->have_posts() ) {

				$ajaxposts->the_post();
				$found_posts = true;
				$title       = get_the_title();
				$post_link   = get_permalink();
				$image_src   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
				$image_src   = $image_src ? $image_src[0] : $placeholder;
				$title_tag   = Utils::get_valid_html_tag($settings['title_tags']);

				$onclick = '';
				if ( ! empty( $settings['global_link'] ) && $settings['global_link'] === 'yes' ) {
					$onclick = 'onclick="window.open(\'' . esc_url( $post_link ) . '\', \'_self\')"';
				}
				
				?>
				<div <?php echo $onclick; ?> class="upk-item">
					<div class="upk-img-wrap">
						<img 
							class="upk-img" 
							src="<?php echo esc_url( $image_src ); ?>" 
							alt="<?php echo esc_attr( $title ); ?>"
						>
					</div>

					<div class="upk-content-wrap">
						<?php if ( $settings['show_category'] === 'yes' ) : ?>
							<div class="upk-category">
								<?php echo upk_get_category( $post_type ); ?>
							</div>
						<?php endif; ?>

						<?php if ( ! isset( $settings['show_title'] ) || $settings['show_title'] === 'yes' ) : ?>
							<<?php echo esc_attr( $title_tag ); ?> class="upk-title">
								<a 
									href="<?php echo esc_url( $post_link ); ?>" 
									title="<?php echo esc_attr( $title ); ?>"
									class="title-animation-<?php echo esc_attr( $settings['title_style'] ); ?>"
									<?php echo ( $settings['upk_link_new_tab'] === 'yes' ) ? 'target="_blank"' : ''; ?>
								>
									<?php echo esc_html( $title ); ?>
								</a>
							</<?php echo esc_attr( $title_tag ); ?>>
						<?php endif; ?>

						<?php if ( $settings['show_meta'] === 'yes' ) : ?>			
							<div class="upk-meta">
								<div class="upk-author">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
										class="bi bi-person-circle" viewBox="0 0 16 16">
										<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
										<path fill-rule="evenodd"
											d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
									</svg>
									<span><?php echo esc_html__( 'by', 'ultimate-post-kit' ); ?></span>
									<a class="upk-author-name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
										<span><?php echo esc_html( get_the_author() ); ?></span>
									</a>
								</div>

								<?php if ( $settings['show_date'] || $settings['show_reading_time'] ) : ?>
									<div class="upk-date-reading-time upk-flex upk-flex-middle">
										<?php if ( $settings['show_date'] ) : ?>
											<div class="upk-date">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
													class="bi bi-calendar" viewBox="0 0 16 16">
													<path
														d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
												</svg>
												<?php
												if ( $settings['human_diff_time'] == 'yes' ) {
													echo esc_html(
														ultimate_post_kit_post_time_diff(
															( $settings['human_diff_time_short'] == 'yes' ) ? 'short' : ''
														)
													);
												} else {
													echo esc_html( get_the_date() );
												}
												?>
											</div>

											<?php if ( $settings['show_time'] ) : ?>
												<div class="upk-post-time">
													<i class="upk-icon-clock" aria-hidden="true"></i>
													<?php echo esc_html( get_the_time() ); ?>
												</div>
											<?php endif; ?>
										<?php endif; ?>

										<?php if ( function_exists( 'ultimate_post_kit_reading_time' ) && $settings['show_reading_time'] === 'yes' ) : ?>
											<div class="upk-reading-time">
												<?php echo esc_html( ultimate_post_kit_reading_time( get_the_content(), $settings['avg_reading_speed'] ?? 200 ) ); ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<div class="upk-gratis-line"></div>

						<?php if ( $settings['show_readmore']  === 'yes' ) : ?>
							<div class="upk-link-button">
								<a href="<?php echo esc_url( $post_link ); ?>">
									<span><?php echo esc_html( $settings['readmore_text'] ); ?></span>
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
										class="bi bi-arrow-right" viewBox="0 0 16 16">
										<path fill-rule="evenodd"
											d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
									</svg>
								</a>
							</div>
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
