<?php
namespace UltimatePostKit\Modules\AlterGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Traits\Global_Widget_Functions;
use \Elementor\Icons_Manager;
use UltimatePostKit\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_nopriv_upk_alter_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
		add_action( 'wp_ajax_upk_alter_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
	}

	public function get_name() {
		return 'alter-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Alter_Grid',
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
			$readmore_text = $settings['readmore_text'] ?? 'Read More';
	
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
						<div class="upk-img-wrap">
							<div class="upk-main-img">
								<img 
									class="upk-img" 
									src="<?php echo esc_url( $image_src ); ?>" 
									alt="<?php echo esc_attr( $title ); ?>"
								>
								<?php if ( $settings['readmore_type'] === 'on_image' ) : ?>
									<a href="<?php echo $post_link; ?>" class="upk-readmore-on-image">
										<span class="upk-readmore-icon"><span></span></span>
									</a>
								<?php endif; ?>

								<?php if ( $settings['show_post_format'] === 'yes' ) : ?>
									<div class="upk-post-format">
										<a href="<?php echo $post_link; ?>">
											<?php
											if ( has_post_format( 'aside' ) ) :
												echo '<i class="upk-icon-aside" aria-hidden="true"></i>';
											elseif ( has_post_format( 'gallery' ) ) :
												echo '<i class="upk-icon-gallery" aria-hidden="true"></i>';
											elseif ( has_post_format( 'link' ) ) :
												echo '<i class="upk-icon-link" aria-hidden="true"></i>';
											elseif ( has_post_format( 'image' ) ) :
												echo '<i class="upk-icon-image" aria-hidden="true"></i>';
											elseif ( has_post_format( 'quote' ) ) :
												echo '<i class="upk-icon-quote" aria-hidden="true"></i>';
											elseif ( has_post_format( 'status' ) ) :
												echo '<i class="upk-icon-status" aria-hidden="true"></i>';
											elseif ( has_post_format( 'video' ) ) :
												echo '<i class="upk-icon-video" aria-hidden="true"></i>';
											elseif ( has_post_format( 'audio' ) ) :
												echo '<i class="upk-icon-music" aria-hidden="true"></i>';
											elseif ( has_post_format( 'chat' ) ) :
												echo '<i class="upk-icon-chat" aria-hidden="true"></i>';
											else :
												echo '<i class="upk-icon-post" aria-hidden="true"></i>';
											endif;
											?>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="upk-content">
							<div>
								<?php if ( $settings['show_category'] === 'yes' ) : ?>
									<div class="upk-category">
										<?php echo upk_get_category( $post_type ); ?>
									</div>
								<?php endif; ?>

								<?php if ( ! isset( $settings['show_title'] ) || $settings['show_title'] === 'yes' ) : ?>
									<<?php echo esc_attr( $title_tag ); ?> class="upk-title">
										<a 
											href="<?php echo $post_link; ?>" 
											title="<?php echo esc_attr( $title ); ?>"
											class="title-animation-<?php echo esc_attr( $settings['title_style'] ); ?>"
											<?php echo $settings['upk_link_new_tab'] === 'yes' ? 'target="_blank"' : ''; ?>
										>
											<?php echo esc_html( $title ); ?>
										</a>
									</<?php echo esc_attr( $title_tag ); ?>>
								<?php endif; ?>

								<?php if ( $settings['show_excerpt'] === 'yes' ) : ?>
									<div class="upk-text-wrap">
										<div class="upk-text">
											<?php
											echo esc_html(
												wp_trim_words(
													get_the_excerpt(),
													absint( $settings['excerpt_length'] ?? 20 ),
													'...'
												)
											);
											?>
										</div>

										<?php if ( $settings['readmore_type'] === 'classic' ) : ?>
											<a href="<?php echo $post_link; ?>" class="upk-readmore upk-display-inline-block">
												<?php echo esc_html( $readmore_text ); ?>
												<?php if ( $settings['readmore_icon']['value'] ) : ?>
													<span class="upk-button-icon-align-<?php echo esc_attr( $settings['readmore_icon_align'] ); ?>">
														<?php Icons_Manager::render_icon( $settings['readmore_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
													</span>
												<?php endif; ?>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if (
									$settings['show_author'] === 'yes' ||
									$settings['show_date'] === 'yes' ||
									$settings['show_reading_time'] === 'yes'
								) : ?>
									<div class="upk-meta">

										<?php if ( $settings['show_author'] === 'yes' ) : ?>
											<div class="upk-blog-author" data-separator="<?php echo esc_attr( $meta_sep ); ?>">
												<a class="author-name" href="<?php echo $author_url; ?>">
													<?php echo $author_name; ?>
												</a>
											</div>
										<?php endif; ?>

										<?php if ( $settings['show_date'] === 'yes' ) : ?>
											<div data-separator="<?php echo esc_attr( $meta_sep ); ?>">
												<div class="upk-date">
													<?php
													if ( $settings['human_diff_time'] === 'yes' ) {
														echo esc_html(
															ultimate_post_kit_post_time_diff(
																( $settings['human_diff_time_short'] == 'yes' ) ? 'short' : ''
															)
														);
													} else {
														echo get_the_date();
													}
													?>
												</div>
												
												<?php if ( $settings['show_time'] === 'yes' && $settings['human_diff_time'] !== 'yes' ) : ?>
													<div class="upk-post-time">
														<i class="upk-icon-clock" aria-hidden="true"></i>
														<?php echo esc_html( get_the_time() ); ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endif; ?>

										<?php if ( function_exists( 'ultimate_post_kit_reading_time' ) && $settings['show_reading_time'] === 'yes' ) : ?>
											<?php $speed = (int) ( $settings['avg_reading_speed'] ?? 200 ); ?>
											<div class="upk-reading-time" data-separator="<?php echo esc_attr( $meta_sep ); ?>">
												<?php echo esc_html( ultimate_post_kit_reading_time( get_the_content(), $speed ) ); ?>
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
