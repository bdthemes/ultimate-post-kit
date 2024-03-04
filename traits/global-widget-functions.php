<?php

namespace UltimatePostKit\Traits;

use UltimatePostKit\Utils;

defined('ABSPATH') || die();

trait Global_Widget_Functions {

	/**
	 * New Image Render Method With Lazy Load Support
	 *
	 * @return void
	 */

	function render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src             = wp_get_attachment_image_src($image_id, $size);

		if (!$image_src) {
			printf('<img class="upk-img" src="%1$s" alt="%2$s">', esc_url($placeholder_image_src), esc_html(get_the_title()));
		} else {
			print(wp_get_attachment_image(
				$image_id,
				$size,
				false,
				[
					'class' => 'upk-img',
					'alt'   => esc_html(get_the_title())
				]
			));
		}
	}

	/**
	 * Old Image Render Method
	 * Not Using Anymore
	 * @return void
	 */
	function __render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src = wp_get_attachment_image_src($image_id, $size);
		if (!$image_src) {
			$image_src = $placeholder_image_src;
		} else {
			$image_src = $image_src[0];
		}
?>
		<img class="upk-img" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_html(get_the_title()); ?>">
	<?php
	}

	function render_title($widget_name) {
		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_title')) {
			return;
		}
		apply_filters('upk/' . $widget_name . '/before/title', '');
		printf('<%1$s class="upk-title"><a href="%2$s" title="%3$s" class="title-animation-%4$s" >%3$s</a></%1$s>', esc_attr(Utils::get_valid_html_tag($settings['title_tags'])), get_permalink(), get_the_title(), esc_attr($settings['title_style']));
		apply_filters('upk/' . $widget_name . '/after/title', '');
	}



	function render_category() {
		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-category">
			<?php echo upk_get_category($this->get_settings('posts_source')); ?>
		</div>
	<?php
	}

	function render_date() {
		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_date')) {
			return;
		}
	?>
		<div class="upk-date">
			<?php if ($settings['human_diff_time'] == 'yes') {
				echo ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
			} else {
				echo get_the_date();
			} ?>
		</div>

		<?php if ($settings['show_time']) : ?>
			<div class="upk-post-time">
				<i class="upk-icon-clock" aria-hidden="true"></i>
				<?php echo get_the_time(); ?>
			</div>
		<?php endif; ?>
	<?php
	}

	function render_excerpt($excerpt_length) {
		if (!$this->get_settings('show_excerpt')) {
			return;
		}
		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');
	?>
		<div class="upk-text">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo ultimate_post_kit_custom_excerpt($excerpt_length, $strip_shortcode);
			}
			?>
		</div>
	<?php
	}

	function render_post_format() {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_post_format']) {
			return;
		}
	?>
		<div class="upk-post-format">
			<a href="<?php echo esc_url(get_permalink()) ?>">
				<?php if (has_post_format('aside')) : ?>
					<i class="upk-icon-aside" aria-hidden="true"></i>
				<?php elseif (has_post_format('gallery')) : ?>
					<i class="upk-icon-gallery" aria-hidden="true"></i>
				<?php elseif (has_post_format('link')) : ?>
					<i class="upk-icon-link" aria-hidden="true"></i>
				<?php elseif (has_post_format('image')) : ?>
					<i class="upk-icon-image" aria-hidden="true"></i>
				<?php elseif (has_post_format('quote')) : ?>
					<i class="upk-icon-quote" aria-hidden="true"></i>
				<?php elseif (has_post_format('status')) : ?>
					<i class="upk-icon-status" aria-hidden="true"></i>
				<?php elseif (has_post_format('video')) : ?>
					<i class="upk-icon-video" aria-hidden="true"></i>
				<?php elseif (has_post_format('audio')) : ?>
					<i class="upk-icon-music" aria-hidden="true"></i>
				<?php elseif (has_post_format('chat')) : ?>
					<i class="upk-icon-chat" aria-hidden="true"></i>
				<?php else : ?>
					<i class="upk-icon-post" aria-hidden="true"></i>
				<?php endif; ?>
			</a>
		</div>
<?php
	}
}
