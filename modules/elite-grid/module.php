<?php
namespace UltimatePostKit\Modules\EliteGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_nopriv_upk_elite_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
		add_action( 'wp_ajax_upk_elite_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
	}

	public function get_name() {
		return 'elite-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Elite_Grid',
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
			$meta_sep    = $settings['meta_separator'] ?? '|';
	
			while ( $ajaxposts->have_posts() ) {

				$ajaxposts->the_post();
				$found_posts = true;
	
				$title       = get_the_title();
				$post_link   = esc_url( get_permalink() );
				$author_url  = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
				$author_name = esc_html( get_the_author() );
				$title_tag   = Utils::get_valid_html_tag($settings['title_tags']);
	
				$image_src   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
				$image_src   = $image_src ? $image_src[0] : $placeholder;

				$onclick = '';
				if ( ! empty( $settings['global_link'] ) && $settings['global_link'] === 'yes' ) {
					$onclick = 'onclick="window.open(\'' . esc_url( $post_link ) . '\', \'_self\')"';
				}

				?>
				<div <?php echo $onclick; ?> class="upk-item">
					<div class="upk-item-box">
						<div class="upk-image-wrap">
							<div class="upk-main-image">
								<img class="upk-img" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($title); ?>">
							</div>

							<?php if ($settings['show_author'] === 'yes') : ?>
								<div class="upk-author-wrap">
									<div class="upk-author-img-wrap">
										<?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
									</div>
									<div class="upk-author-info-warp">
										<span class="author-name">
											<a class="name" href="<?php echo $author_url; ?>">
												<?php echo $author_name; ?>
											</a>
										</span>
										<span class="author-depertment">
											<?php
											$aid = get_the_author_meta('ID');
											echo ucwords(get_user_role($aid));
											?>
										</span>
									</div>
								</div>
							<?php endif; ?>

							<?php if ($settings['show_category'] === 'yes' || $settings['show_date'] === 'yes') : ?>
								<div class="upk-meta-wrap">
									<?php if ($settings['show_date'] === 'yes' || $settings['show_reading_time'] === 'yes') : ?>
										<div class="upk-flex upk-flex-middle upk-date-reading-wrap">
											<?php if ($settings['show_date'] === 'yes') : ?>
												<div class="upk-date">
													<?php
													if ($settings['human_diff_time'] === 'yes') {
														echo esc_html(
															ultimate_post_kit_post_time_diff(
																($settings['human_diff_time_short'] == 'yes') ? 'short' : ''
															)
														);
													} else {
														echo esc_html(get_the_date());
													}
													?>
												</div>
												<?php if ($settings['show_time'] === 'yes' && $settings['human_diff_time'] !== 'yes') : ?>
													<div class="upk-post-time">
														<i class="upk-icon-clock" aria-hidden="true"></i>
														<?php echo esc_html(get_the_time()); ?>
													</div>
												<?php endif; ?>
											<?php endif; ?>

											<?php if (function_exists('ultimate_post_kit_reading_time') && $settings['show_reading_time'] === 'yes') : ?>
												<?php $speed = (int)($settings['avg_reading_speed'] ?? 200); ?>
												<div class="upk-reading-time" data-separator="<?php echo esc_attr($meta_sep); ?>">
													<?php echo esc_html(ultimate_post_kit_reading_time(get_the_content(), $speed)); ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>

									<?php if ($settings['show_category'] === 'yes') : ?>
										<div class="upk-category"><?php echo upk_get_category($post_type); ?></div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="upk-content">
							<div>
								<?php if (!isset($settings['show_title']) || $settings['show_title'] === 'yes') : ?>
									<<?php echo esc_attr( $title_tag ); ?> class="upk-title">
										<a 
											href="<?php echo $post_link; ?>" 
											title="<?php echo esc_attr($title); ?>"
											class="title-animation-<?php echo esc_attr($settings['title_style']); ?>"
											<?php echo $settings['upk_link_new_tab'] === 'yes' ? 'target="_blank"' : ''; ?>
										>
											<?php echo esc_html($title); ?>
										</a>
									</<?php echo esc_attr( $title_tag ); ?>>
								<?php endif; ?>

								<?php if ($settings['show_excerpt'] === 'yes') : ?>
									<div class="upk-text-wrap">
										<div class="upk-text">
											<?php
											echo esc_html(
												wp_trim_words(
													get_the_excerpt(),
													absint($settings['excerpt_length'] ?? 20)
												)
											);
											?>
										</div>
									</div>
								<?php endif; ?>

								<?php if (
									$settings['show_category'] === 'yes' ||
									$settings['show_date'] === 'yes' ||
									$settings['show_reading_time'] === 'yes'
								) : ?>
									<div class="upk-meta-list upk-flex upk-flex-middle">
										<?php if ($settings['show_category'] === 'yes') : ?>
											<div class="upk-category"><?php echo upk_get_category($post_type); ?></div>
										<?php endif; ?>

										<?php if ($settings['show_date'] === 'yes') : ?>
											<div class="upk-date">
												<?php
												if ($settings['human_diff_time'] === 'yes') {
													echo esc_html(
														ultimate_post_kit_post_time_diff(
															($settings['human_diff_time_short'] == 'yes') ? 'short' : ''
														)
													);
												} else {
													echo esc_html(get_the_date());
												}
												?>
											</div>
											<?php if ($settings['show_time'] === 'yes' && $settings['human_diff_time'] !== 'yes') : ?>
												<div class="upk-post-time">
													<i class="upk-icon-clock" aria-hidden="true"></i>
													<?php echo esc_html(get_the_time()); ?>
												</div>
											<?php endif; ?>
										<?php endif; ?>

										<?php if (function_exists('ultimate_post_kit_reading_time') && $settings['show_reading_time'] === 'yes') : ?>
											<?php $speed = (int)($settings['avg_reading_speed'] ?? 200); ?>
											<div class="upk-reading-time" data-separator="<?php echo esc_attr($meta_sep); ?>">
												<?php echo esc_html(ultimate_post_kit_reading_time(get_the_content(), $speed)); ?>
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
