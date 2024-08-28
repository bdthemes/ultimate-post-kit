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
		$settings = $_POST['settings'];
		$post_type =  isset($settings['post_type']) ? sanitize_text_field($$settings['post_type']) : 'post';
		$ajaxposts = $this->query_args();
		$markup = '';
		if ($ajaxposts->have_posts()) {
			$item_index = 1;
			while ($ajaxposts->have_posts()) :
				$ajaxposts->the_post();
				// $title                 = wp_trim_words(get_the_title(), $title_text_limit, '...');
				$title = get_the_title();
				$post_link = esc_url(get_permalink());
				$image_src = wp_get_attachment_image_url(get_post_thumbnail_id(), 'full');
				$category = upk_get_category($post_type);
				$author_url = get_author_posts_url(get_the_author_meta('ID'));
				$author_name = get_the_author();
				// $reading_time = ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']);

				if ($settings['human_diff_time'] == 'yes') {
					$date =  ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
				} else {
					$date =  get_the_date();
				}
				$placeholder_image_src = \Elementor\Utils::get_placeholder_image_src();
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
				if (!$image_src) {
					$image_src = $placeholder_image_src;
				} else {
					$image_src = $image_src[0];
				}
				$markup .= '<div class="upk-item">';
				$markup .= '<div class="upk-image-wrap">';
				$markup .= '<img class="upk-img" src="' . $image_src . '" alt="' . $title . '">';
				
				if (($settings['show_author'] === 'yes') or ($settings['show_date'] == 'yes') or ($settings['show_time'] == 'yes') or ($settings['show_reading_time'] == 'yes')) :
					$markup .= '<div class="upk-meta">';
					if ($settings['show_author'] == 'yes') :
						$markup .= '<div class="upk-author-img">';
						$markup .= get_avatar(get_the_author_meta('ID'), 48);
						$markup .= '</div>';
					endif;
					$markup .= '<div>';
					if ($settings['show_author'] == 'yes') :
						$markup .= '<div class="upk-author-name">';
						$markup .= '<a href="' . $author_url . '">';
						$markup .= $author_name;
						$markup .= '</a>';
						$markup .= '</div>';
					endif;
					$markup .= '<div class="upk-flex upk-flex-middle upk-date-reading-wrap">';
					if ('yes' === $settings['show_date']) :
						$markup .= '<div data-separator="' . esc_html($settings['meta_separator']) . '">';
						$markup .= '<div class="upk-date">';
						$markup .= $date;
						$markup .= '</div>';
						if ($settings['show_time'] == 'yes') :
							$markup .= '<div class="upk-post-time">';
							$markup .= '<i class="upk-icon-clock" aria-hidden="true"></i>';
							$markup .= get_the_time();
							$markup .= '</div>';
						endif;
						$markup .= '</div>';
					endif;
					if (_is_upk_pro_activated()) :
						if ('yes' === $settings['show_reading_time']) :
							$markup .= '<div class="upk-reading-time" data-separator="' . esc_html($settings['meta_separator']) . '">';
							$markup .= ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']);
							$markup .= '</div>';
						endif;
					endif;
					$markup .= '</div>';
					$markup .= '</div>';
					$markup .= '</div>';
				endif;
				if ($settings['show_post_format'] == 'yes') :
					$markup .= '<div class="upk-post-format">';
					$markup .= '<a href="' . esc_url(get_permalink()) . '">';
					if (has_post_format('aside')) :
						$markup .= '<i class="upk-icon-aside" aria-hidden="true"></i>';
					elseif (has_post_format('gallery')) :
						$markup .= '<i class="upk-icon-gallery" aria-hidden="true"></i>';
					elseif (has_post_format('link')) :
						$markup .= '<i class="upk-icon-link" aria-hidden="true"></i>';
					elseif (has_post_format('image')) :
						$markup .= '<i class="upk-icon-image" aria-hidden="true"></i>';
					elseif (has_post_format('quote')) :
						$markup .= '<i class="upk-icon-quote" aria-hidden="true"></i>';
					elseif (has_post_format('status')) :
						$markup .= '<i class="upk-icon-status" aria-hidden="true"></i>';
					elseif (has_post_format('video')) :
						$markup .= '<i class="upk-icon-video" aria-hidden="true"></i>';
					elseif (has_post_format('audio')) :
						$markup .= '<i class="upk-icon-music" aria-hidden="true"></i>';
					elseif (has_post_format('chat')) :
						$markup .= '<i class="upk-icon-chat" aria-hidden="true"></i>';
					else :
						$markup .= '<i class="upk-icon-post" aria-hidden="true"></i>';
					endif;
					$markup .= '</a>';
					$markup .= '</div>';
				endif;
				$markup .= '<div class="upk-content-wrap">';
				$markup .= '<div class="upk-content">';
				if ($settings['show_category'] == 'yes') :
					$markup .= '<div class="upk-category">';
					$markup .= $category;
					$markup .= '</div>';
				endif;
				if ($settings['show_title'] == 'yes') :
					$markup .= '<h3 class="upk-title">';
					$markup .= '<a class="title-animation-'. esc_attr($settings['title_style']) .'" href="' . $post_link . '" title=' . $title . '>' . $title . '</a>';
					$markup .= '</h3>';
				endif;
				$markup .= '</div>';
				if ($settings['show_readmore'] == 'yes') :
					$markup .= '<div class="upk-button-wrap">';
					$markup .= '<a href="' . $post_link . '" class="upk-readmore">';
					$markup .= '<span class="upk-readmore-icon"><span></span></span>';
					$markup .= '</a>';
					$markup .= '</div>';
				endif;
				$markup .= '</div>';
				$markup .= '</div>';
				$markup .= '</div>';
				$item_index++;
			endwhile;
		}

		wp_reset_postdata();
		$result = [
			'markup' => $markup,
		];
		wp_send_json($result);
		exit;
	}
}
