<?php
namespace UltimatePostKit\Modules\MapleGrid;
use UltimatePostKit\Traits\Global_Widget_Functions;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;
use UltimatePostKit\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	use Global_Widget_Functions;

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_nopriv_upk_maple_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
		add_action( 'wp_ajax_upk_maple_grid_loadmore_posts', [ $this, 'callback_ajax_loadmore_posts' ] );
	}

	public function get_name() {
		return 'maple-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Maple_Grid',
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
        $placeholder = \Elementor\Utils::get_placeholder_image_src();

        while ( $ajaxposts->have_posts() ) {
            $ajaxposts->the_post();
            $found_posts = true;

            $title       = get_the_title();
            $post_link   = esc_url( get_permalink() );
            $author_url  = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
            $author_name = esc_html( get_the_author() );

            $image_src   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
            $image_src   = $image_src ? $image_src[0] : $placeholder;

            $meta_sep    = $settings['meta_separator'] ?? '-';
            $grid_style  = $settings['grid_style'] ?? '1';
			$title_tag   = Utils::get_valid_html_tag($settings['title_tags']);

            $onclick = '';
			if ( ! empty( $settings['global_link'] ) && $settings['global_link'] === 'yes' ) {
					$onclick = 'onclick="window.open(\'' . esc_url( $post_link ) . '\', \'_self\')"';
			}

            ?>
            <div <?php echo $onclick; ?> class="upk-item">
                <div class="upk-item-box">
                    <div class="upk-image-wrap">
                        <div class="upk-main-image">
                            <img class="upk-img" src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                        </div>

                        <?php if ( $settings['show_post_format'] === 'yes' && $grid_style !== '3' ) : ?>
                            <div class="upk-post-format">
                                <a href="<?php echo esc_url($post_link); ?>">
                                    <?php if ( has_post_format( 'aside' ) ) : ?>
                                        <i class="upk-icon-aside" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'gallery' ) ) : ?>
                                        <i class="upk-icon-gallery" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'link' ) ) : ?>
                                        <i class="upk-icon-link" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'image' ) ) : ?>
                                        <i class="upk-icon-image" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'quote' ) ) : ?>
                                        <i class="upk-icon-quote" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'status' ) ) : ?>
                                        <i class="upk-icon-status" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'video' ) ) : ?>
                                        <i class="upk-icon-video" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'audio' ) ) : ?>
                                        <i class="upk-icon-music" aria-hidden="true"></i>
                                    <?php elseif ( has_post_format( 'chat' ) ) : ?>
                                        <i class="upk-icon-chat" aria-hidden="true"></i>
                                    <?php else : ?>
                                        <i class="upk-icon-post" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ( ( $settings['show_category'] ?? 'yes' ) === 'yes' && $grid_style !== '3' ) : ?>
                            <div class="upk-category">
                                <?php echo wp_kses_post(upk_get_category( $post_type )); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="upk-content">
                        <div class="upk-content-inner">
                            <?php if ( ( $settings['show_author_avatar'] ?? 'yes' ) === 'yes' || ( $settings['show_author_name'] ?? 'yes' ) === 'yes' || ( $settings['show_date'] ?? 'yes' ) === 'yes' || ( $settings['show_reading_time'] ?? 'no' ) === 'yes' ) : ?>
                                <div class="upk-meta">
                                    <?php if ( $grid_style !== '3' && ( $settings['show_author_avatar'] == 'yes' || $settings['show_author_name'] == 'yes' ) ) : ?>
                                        <div class="upk-author">
                                            <?php if ($settings['show_author_avatar'] == 'yes') : ?>
                                                <?php echo wp_kses_post(get_avatar(get_the_author_meta('ID'), 36)); ?>
                                            <?php endif; ?>

                                            <?php if ($settings['show_author_name'] == 'yes') : ?>
                                                <a class="author-name" href="<?php echo esc_url( get_author_posts_url(get_the_author_meta('ID')) ); ?>">
                                                    <?php echo esc_html( get_the_author() ); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( ( $settings['show_date'] ?? 'yes' ) === 'yes' ) : ?>
                                        <div class="upk-blog-date" data-separator="<?php echo esc_html( $meta_sep ); ?>">
                                            <div class="upk-blog-date">
                                                <a class="date" href="#">
                                                    <i class="upk-icon-calendar" aria-hidden="true"></i>
                                                    <?php
                                                        if ( ( $settings['human_diff_time'] ?? 'no' ) === 'yes' ) {
                                                            echo esc_html( ultimate_post_kit_post_time_diff( ( ( $settings['human_diff_time_short'] ?? 'no' ) === 'yes' ) ? 'short' : '' ) );
                                                        } else {
                                                            echo get_the_date();
                                                        }
                                                    ?>
                                                </a>
                                            </div>
                                            <?php if ( ( $settings['show_time'] ?? 'no' ) === 'yes' ) : ?>
                                                <div class="upk-post-time">
                                                    <i class="upk-icon-clock" aria-hidden="true"></i>
                                                    <?php echo esc_html( get_the_time() ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( function_exists( '_is_upk_pro_activated' ) && _is_upk_pro_activated() && function_exists( 'ultimate_post_kit_reading_time' ) && ( $settings['show_reading_time'] ?? '' ) === 'yes' ) : ?>
                                        <div class="upk-reading-time" data-separator="<?php echo esc_html( $meta_sep ); ?>">
                                            <?php echo esc_html( ultimate_post_kit_reading_time( get_the_content(), $settings['avg_reading_speed'] ?? 200 ) ); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( ! isset( $settings['show_title'] ) || $settings['show_title'] === 'yes' ) : ?>
                                <<?php echo esc_attr( $title_tag ); ?> class="upk-title">
                                    <a 
                                        href="<?php echo esc_url($post_link); ?>"
                                        title="<?php echo esc_attr( $title ); ?>" 
                                        class="title-animation-<?php echo esc_attr( $settings['title_style'] ); ?>"
                                        <?php echo $settings['upk_link_new_tab'] === 'yes' ? 'target="_blank"' : ''; ?>
                                    >
                                        <?php echo esc_html( $title ); ?>
                                    </a>
                                </<?php echo esc_attr( $title_tag ); ?>>
                            <?php endif; ?>

                            <?php if ( ( $settings['show_excerpt'] ?? 'yes' ) === 'yes' && $grid_style !== '3' ) : ?>
                                <div class="upk-text">
                                    <?php 
                                        if ( has_excerpt() ) { 
                                            the_excerpt(); 
                                        } else {
                                            if ( function_exists( 'ultimate_post_kit_custom_excerpt' ) ) {
                                                echo wp_kses_post( ultimate_post_kit_custom_excerpt( intval( $settings['excerpt_length'] ?? 20 ), false, '' ) );
                                            } else {
                                                echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), intval( $settings['excerpt_length'] ?? 20 ) ) );
                                            }
                                        } 
                                    ?>
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