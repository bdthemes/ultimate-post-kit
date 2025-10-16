<?php
namespace UltimatePostKit\Modules\RambleGrid;
use UltimatePostKit\Traits\Global_Widget_Functions;
use Elementor\Icons_Manager;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_nopriv_upk_ramble_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
		add_action( 'wp_ajax_upk_ramble_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
	}

	public function get_name() {
		return 'ramble-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Ramble_Grid',
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

            while ( $ajaxposts->have_posts() ) {
                $ajaxposts->the_post();
                $found_posts = true;

                $title       = get_the_title();
                $post_link   = esc_url( get_permalink() );
                $author_url  = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
                $author_name = esc_html( get_the_author() );
                $meta_sep    = $settings['meta_separator'] ?? '/';
                $title_tag   = Utils::get_valid_html_tag($settings['title_tags']);

                $onclick = '';
				if ( ! empty( $settings['global_link'] ) && $settings['global_link'] === 'yes' ) {
					$onclick = 'onclick="window.open(\'' . esc_url( $post_link ) . '\', \'_self\')"';
				}

                ?>
                <div <?php echo $onclick; ?> class="upk-item">
                    <div class="upk-image-wrap">
                        <?php $this->render_image(get_post_thumbnail_id(), 'large'); ?>

                        <div class="upk-content">
                            <div class="upk-default-show">
                                <div class="upk-date-cetagory-wrap">
                                    <div class="upk-date">
                                        <?php
                                        if ($settings['show_date'] === 'yes' && $settings['human_diff_time'] !== 'yes') {
                                            echo esc_html(get_the_date());
                                        }
                                        if ($settings['human_diff_time'] === 'yes') {
                                            echo esc_html(ultimate_post_kit_post_time_diff($settings['human_diff_time_short'] !== 'yes' ? 'short' : ''));
                                        }
                                        ?>
                                    </div>
                                    <?php if ($settings['show_time'] === 'yes') : ?>
                                        <div class="upk-post-time">
                                            <i class="upk-icon-clock" aria-hidden="true"></i>
                                            <?php echo esc_html(get_the_time()); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($settings['show_category'] === 'yes') : ?>
                                        <div class="upk-category">
                                            <?php echo wp_kses_post(upk_get_category($post_type)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($settings['show_title'] === 'yes') : ?>
                                    <<?php echo esc_attr( $title_tag ); ?> class="upk-title">
                                        <a 
                                            href="<?php echo esc_url($post_link); ?>" 
                                            title="<?php echo esc_attr($title); ?>" 
                                            class="title-animation-<?php echo esc_attr($settings['title_style']); ?>"
                                        >
                                            <?php echo esc_html($title); ?>
                                        </a>
                                    </<?php echo esc_attr( $title_tag ); ?>>
                                <?php endif; ?>
                            </div>

                            <div class="upk-default-hide">
                                <?php if (
                                    $settings['show_author_avatar'] === 'yes' || 
                                    $settings['show_author_name'] === 'yes' || 
                                    $settings['show_date'] === 'yes' || 
                                    $settings['show_reading_time'] === 'yes'
                                ) : ?>
                                    <div class="upk-meta">
                                        <?php if ($settings['show_author_avatar'] === 'yes') : ?>
                                            <div class="upk-author-image">
                                                <?php echo wp_kses_post(get_avatar(get_the_author_meta('ID'), 48)); ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="upk-author-name-date-wrap">
                                            <?php if ($settings['show_author_name'] === 'yes') : ?>
                                                <div class="upk-author-name">
                                                    <a href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_name); ?></a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($settings['show_date'] === 'yes' || $settings['show_reading_time'] === 'yes') : ?>
                                                <div class="upk-date-reading-time" data-separator="<?php echo esc_html($meta_sep); ?>">
                                                    <?php if ($settings['show_date'] === 'yes') : ?>
                                                        <div class="upk-date">
                                                            <?php
                                                            if ($settings['human_diff_time'] === 'yes') {
                                                                echo esc_html(ultimate_post_kit_post_time_diff(
                                                                    (($settings['human_diff_time_short'] ?? 'no') === 'yes') ? 'short' : ''
                                                                ));
                                                            } else {
                                                                echo get_the_date();
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (function_exists('_is_upk_pro_activated') && _is_upk_pro_activated() && function_exists('ultimate_post_kit_reading_time') && ($settings['show_reading_time'] ?? '') === 'yes') : ?>
                                                        <div class="upk-reading-time" data-separator="<?php echo esc_html($meta_sep); ?>">
                                                            <?php echo esc_html(ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed'] ?? 200)); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($settings['show_excerpt'] === 'yes') : ?>
                                    <div class="upk-text">
                                        <?php 
                                        if (has_excerpt()) {
                                            the_excerpt();
                                        } else {
                                            if (function_exists('ultimate_post_kit_custom_excerpt')) {
                                                echo wp_kses_post(ultimate_post_kit_custom_excerpt(intval($settings['excerpt_length'] ?? 20), false, ''));
                                            } else {
                                                echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_content()), intval($settings['excerpt_length'] ?? 20)));
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="upk-btn-comments-wrap">
                            <div class="upk-btn-wrap upk-flex">
                                <?php if ($settings['show_readmore'] === 'yes') : ?>
                                    <a href="<?php echo esc_url($post_link); ?>" class="upk-readmore" target="<?php echo esc_attr($settings['upk_link_new_tab'] === 'yes' ? '_blank' : '_self'); ?>">
                                        <span class="upk-flex upk-flex-middle">
                                            <?php echo esc_html($settings['readmore_text'] ?? __('Read More', 'ultimate-post-kit')); ?>
                                            <?php if ($settings['readmore_icon']['value']) : ?>
                                                <span class="upk-readmore-btn-icon upk-flex-align-<?php echo esc_attr($settings['icon_align']); ?>">
                                                    <?php Icons_Manager::render_icon($settings['readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($settings['show_comments'] === 'yes') : ?>
                                <div class="upk-comments">
                                    <?php echo absint(get_comments_number()); ?> <?php echo esc_html_x('Comments', 'Frontend', 'ultimate-post-kit'); ?>
                                </div>
                            <?php endif; ?>
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
