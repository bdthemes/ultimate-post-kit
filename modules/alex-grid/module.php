<?php
namespace UltimatePostKit\Modules\AlexGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Traits\Global_Widget_Functions;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();

		add_action('wp_ajax_nopriv_upk_alex_grid_loadmore_posts', [$this, 'callback_ajax_loadmore_posts']);
		add_action('wp_ajax_upk_alex_grid_loadmore_posts', [$this, 'callback_ajax_loadmore_posts']);
	}

	public function get_name() {
		return 'alex-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Alex_Grid',
		];
		
		return $widgets;
	}

	public function callback_ajax_loadmore_posts() {

		$settings = [];

		if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
			$settings = map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' );
		}

		$post_type = $settings['post_source'] ?? 'post';

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
	
		if ($ajaxposts->have_posts()) :
			while ($ajaxposts->have_posts()) :
				$ajaxposts->the_post();
				$found_posts = true;
		
				$title       = get_the_title();
				$post_link   = esc_url(get_permalink());
				$image_src   = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
				$image_src   = $image_src ? esc_url($image_src) : esc_url(\Elementor\Utils::get_placeholder_image_src());
				$category    = wp_kses_post(upk_get_category($post_type));
				$author_url  = esc_url(get_author_posts_url(get_the_author_meta('ID')));
				$author_name = esc_html(get_the_author());
		
				$onclick = '';
				if (!empty($settings['global_link']) && $settings['global_link'] === 'yes') {
					$onclick = ' onclick="window.open(\'' . $post_link . '\', \'_self\')"';
				}
		
				$date = '';
				if (!empty($settings['human_diff_time']) && $settings['human_diff_time'] === 'yes') {
					$date = ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] === 'yes') ? 'short' : '');
				} else {
					$date = esc_html(get_the_date());
				}

				$format_icons = [
					'aside'   => 'upk-icon-aside',
					'gallery' => 'upk-icon-gallery',
					'link'    => 'upk-icon-link',
					'image'   => 'upk-icon-image',
					'quote'   => 'upk-icon-quote',
					'status'  => 'upk-icon-status',
					'video'   => 'upk-icon-video',
					'audio'   => 'upk-icon-music',
					'chat'    => 'upk-icon-chat',
				];
		
				$post_format_icon = isset($format_icons[get_post_format()]) ? $format_icons[get_post_format()] : 'upk-icon-post';
				
				?>
				<div class="upk-item"<?php echo $onclick; ?>>
					<div class="upk-image-wrap">
						<img class="upk-img" src="<?php echo $image_src; ?>" alt="<?php echo esc_attr($title); ?>">
		
						<?php if (
							$settings['show_author'] === 'yes' ||
							$settings['show_date'] === 'yes' ||
							$settings['show_time'] === 'yes' ||
							$settings['show_reading_time'] === 'yes'
						) : ?>
							<div class="upk-meta">
								<?php if ($settings['show_author'] === 'yes') : ?>
									<div class="upk-author-img"><?php echo wp_kses_post(get_avatar(get_the_author_meta('ID'), 48)); ?></div>
								<?php endif; ?>
		
								<div>
									<?php if ($settings['show_author'] === 'yes') : ?>
										<div class="upk-author-name">
											<a href="<?php echo $author_url; ?>"><?php echo $author_name; ?></a>
										</div>
									<?php endif; ?>
		
									<div class="upk-flex upk-flex-middle upk-date-reading-wrap">
										<?php if ($settings['show_date'] === 'yes') : ?>
											<div data-separator="<?php echo esc_attr($settings['meta_separator']); ?>">
												<div class="upk-date"><?php echo $date; ?></div>
												<?php if ($settings['show_time'] === 'yes') : ?>
													<div class="upk-post-time">
														<i class="upk-icon-clock" aria-hidden="true"></i><?php echo esc_html(get_the_time()); ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endif; ?>
		
										<?php if (function_exists('_is_upk_pro_activated') && _is_upk_pro_activated() && $settings['show_reading_time'] === 'yes') : ?>
											<div class="upk-reading-time" data-separator="<?php echo esc_attr($settings['meta_separator']); ?>">
												<?php echo ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
		
						<?php if ($settings['show_post_format'] === 'yes') : ?>
							<div class="upk-post-format">
								<a href="<?php echo $post_link; ?>">
									<i class="<?php echo esc_attr($post_format_icon); ?>" aria-hidden="true"></i>
								</a>
							</div>
						<?php endif; ?>
					</div>
		
					<div class="upk-content-wrap">
						<div class="upk-content">
							<?php if ($settings['show_category'] === 'yes') : ?>
								<div class="upk-category"><?php echo $category; ?></div>
							<?php endif; ?>
		
							<?php if ($settings['show_title'] === 'yes') : ?>
								<h3 class="upk-title">
									<a class="title-animation-<?php echo esc_attr($settings['title_style']); ?>"
									   href="<?php echo $post_link; ?>"
									   title="<?php echo esc_attr($title); ?>">
									   <?php echo esc_html($title); ?>
									</a>
								</h3>
							<?php endif; ?>
						</div>
		
						<?php if ($settings['show_readmore'] === 'yes') : ?>
							<div class="upk-button-wrap">
								<a href="<?php echo $post_link; ?>"
								   class="upk-readmore"
								   target="<?php echo ($settings['upk_link_new_tab'] === 'yes') ? '_blank' : '_self'; ?>">
									<span class="upk-readmore-icon"><span></span></span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php
			endwhile;
		endif;
		
		wp_reset_postdata();
		$markup       = ob_get_clean();
	
		wp_send_json(
			[
				'success' => $found_posts,
				'markup'  => $found_posts ? $markup : esc_html__('No more found', 'ultimate-post-kit'),
			]
		);
	}	
}
