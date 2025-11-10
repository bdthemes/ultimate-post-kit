<?php
namespace UltimatePostKit\Modules\HaroldList;
use UltimatePostKit\Traits\Global_Widget_Functions;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();
		
		add_action( 'wp_ajax_nopriv_upk_harold_list_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
		add_action( 'wp_ajax_upk_harold_list_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
	}

	public function get_name() {
		return 'harold-list';
	}

	public function get_widgets() {

		$widgets = [
			'Harold_List',
		];
		
		return $widgets;
	}

	public function callback_ajax_loadmore_posts() {

		$settings = [];

		if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
			$settings = map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' );
		}

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
				$post_id     = get_the_ID();

				$image_src   = wp_get_attachment_image_src( get_post_thumbnail_id(), $settings['primary_thumbnail_size'] ?? 'medium' );
				$image_src   = $image_src ? $image_src[0] : $placeholder;

				$title_tag   = Utils::get_valid_html_tag($settings['title_tags']);

				$onclick = '';
				if ( ! empty( $settings['global_link'] ) && $settings['global_link'] === 'yes' ) {
					$onclick = 'onclick="window.open(\'' . esc_url( $post_link ) . '\', \'_self\')"';
				}

				?>
				<div <?php echo $onclick; ?> class="upk-item">
					<div class="upk-item-box">
						<?php if ( $settings['show_image'] === 'yes' ) : ?>
							<div class="upk-image-wrap">
								<img class="upk-img" src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $title ); ?>">
							</div>
						<?php endif; ?>
						<div class="upk-content">
							<div>
								<?php if ( $settings['show_category'] === 'yes' ) : ?>
									<div class="upk-category">
										<?php echo upk_get_category($settings['posts_source'] ?? 'post'); ?>
									</div>
								<?php endif; ?>

								<?php if ( ! isset( $settings['show_title'] ) || $settings['show_title'] === 'yes' ) : ?>
									<<?php echo esc_attr( $title_tag ); ?> class="upk-title">
										<a 
											href="<?php echo esc_url($post_link); ?>"
											title="<?php echo esc_attr( $title ); ?>" 
											class="title-animation-<?php echo esc_attr( $settings['title_style'] ?? '' ); ?>"
											<?php echo $settings['upk_link_new_tab'] === 'yes' ? 'target="_blank"' : '' ?>
										>
											<?php echo esc_html( $title ); ?>
										</a>
									</<?php echo esc_attr( $title_tag ); ?>>
								<?php endif; ?>

								<?php if ( $settings['show_author'] === 'yes' || $settings['show_date'] === 'yes' || $settings['show_reading_time'] === 'yes' ) : ?>
									<div class="upk-meta">
										<?php if ( $settings['show_author'] === 'yes' ) : ?>
											<div class="upk-author">
												<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
													<i class="upk-icon-user"></i>
													<span class="upk-author-name"><?php echo esc_html( get_the_author() ); ?></span>
												</a>
											</div>
										<?php endif; ?>

										<?php if ( $settings['show_date'] === 'yes' ) : ?>
											<div data-separator="<?php echo esc_attr( $settings['meta_separator'] ?? '.' ); ?>">
												<div class="upk-flex">
													<div class="upk-date">
														<i class="upk-icon-calendar"></i>
														<span class="upk-harold-date">
															<?php
																if ($settings['show_date'] === 'yes' && $settings['human_diff_time'] !== 'yes') {
																	echo esc_html(get_the_date());
																}
																if ($settings['human_diff_time'] === 'yes') {
																	echo esc_html(ultimate_post_kit_post_time_diff($settings['human_diff_time_short'] === 'yes' ? 'short' : ''));
																}
															?>
														</span>
													</div>
													<?php if ($settings['show_time'] === 'yes') : ?>
														<div class="upk-post-time">
															<i class="upk-icon-clock" aria-hidden="true"></i>
															<?php echo esc_html( get_the_time() ); ?>
														</div>
													<?php endif; ?>
												</div>
											</div>
										<?php endif; ?>

										<?php if ( _is_upk_pro_activated() && $settings['show_reading_time'] === 'yes' ) : ?>
											<div class="upk-reading-time" data-separator="<?php echo esc_attr( $settings['meta_separator'] ?? '.' ); ?>">
												<?php echo esc_html( ultimate_post_kit_reading_time( get_the_content(), $settings['avg_reading_speed'] ?? 200 ) ); ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
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
