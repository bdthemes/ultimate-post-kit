<?php

namespace UltimatePostKit;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

/**
 * Admin_Feeds class
 */

class Ultimate_Post_Kit_Admin_Feeds {

	public function __construct() {
		add_action('admin_enqueue_scripts', [$this, 'enqueue_product_feeds_styles']);
		add_action('wp_dashboard_setup', [$this, 'ultimate_post_kit_register_rss_feeds']);
	}

	/**
	 * Enqueue Admin Style Files
	 */
	function enqueue_product_feeds_styles($hook) {
		if ('index.php' != $hook) {
			return;
		}
		$direction_suffix = is_rtl() ? '.rtl' : '';
		wp_enqueue_style('upk-product-feed', BDTUPK_ADMIN_URL . 'assets/css/upk-product-feed' . $direction_suffix . '.css', [], BDTUPK_VER);
	}


	/**
	 * Ultimate Post Kit Feeds Register
	 */

	public function ultimate_post_kit_register_rss_feeds() {
		wp_add_dashboard_widget('bdt-upk-dashboard-overview', esc_html__('Ultimate Post Kit News &amp; Updates', 'ultimate-post-kit'), [
			$this,
			'ultimate_post_kit_rss_feeds_content_data'
		], null, null, 'column4', 'core');
	}

	/**
	 * Ultimate Post Kit dashboard overview fetch content data
	 */
	public function ultimate_post_kit_rss_feeds_content_data() {
		echo '<div class="bdt-upk-dashboard-widget">';
		$feeds = array();
		$feeds = $this->ultimate_post_kit_get_feeds_remote_data();
		if (is_array($feeds)) :
			foreach ($feeds as $key => $feed) {
				printf('<div class="bdt-product-feeds-content activity-block"><a href="%s" target="_blank"><img class="bdt-upk-promo-image" src="%s"></a> <p>%s</p></div>', $feed->demo_link, $feed->image, $feed->content);
			}
		endif;
		echo $this->ultimate_post_kit_get_feeds_posts_data();
	}

	/**
	 * Ultimate Post Kit dashboard overview fetch remote data
	 */
	public function ultimate_post_kit_get_feeds_remote_data() {
		$source      = wp_remote_get('https://dashboard.bdthemes.io/wp-json/bdthemes/v1/product-feed/?product_category=ultimate-post-kit');
		$reponse_raw = wp_remote_retrieve_body($source);
		$reponse     = json_decode($reponse_raw);

		return $reponse;
	}

	/**
	 * Ultimate Post Kit dashboard overview fetch posts data
	 */
	public function ultimate_post_kit_get_feeds_posts_data() {
		// Get RSS Feed(s)
		include_once(ABSPATH . WPINC . '/feed.php');
		$rss = fetch_feed('https://bdthemes.com/feed');
		if (!is_wp_error($rss)) {
			$maxitems  = $rss->get_item_quantity(5);
			$rss_items = $rss->get_items(0, $maxitems);
		} else {
			$maxitems = 0;
		}
		?>
		<!-- // Display the container -->
		<div class="bdt-upk-overview__feed">
			<ul class="bdt-upk-overview__posts">
				<?php
				// Check items
				if ($maxitems == 0) {
					echo '<li class="bdt-upk-overview__post">' . esc_html__('No item', 'ultimate-post-kit') . '.</li>';
				} else {
					foreach ($rss_items as $item) :
						$feed_url = $item->get_permalink();
						$feed_title = $item->get_title();
						$feed_date = human_time_diff($item->get_date('U'), current_time('timestamp')) . ' ' . esc_html__('ago', 'ultimate-post-kit');
						$content = $item->get_content();
						$feed_content = wp_html_excerpt($content, 120) . ' [...]';
				?>
						<li class="bdt-upk-overview__post">
							<?php printf('<a class="bdt-upk-overview__post-link" href="%1$s" title="%2$s">%3$s</a>', $feed_url, $feed_date, $feed_title);
							printf('<span class="bdt-upk-overview__post-date">%1$s</span>', $feed_date);
							printf('<p class="bdt-upk-overview__post-description">%1$s</p>', $feed_content); ?>

						</li>
				<?php
					endforeach;
				}
				?>
			</ul>
			<div class="bdt-upk-overview__footer bdt-upk-divider_top">
				<ul>
					<?php
					$footer_link = [
						[
							'url'   => 'https://bdthemes.com/blog/',
							'title' => esc_html__('Blog', 'ultimate-post-kit'),
						],
						[
							'url'   => 'https://bdthemes.com/knowledge-base/',
							'title' => esc_html__('Docs', 'ultimate-post-kit'),
						],
						[
							'url'   => 'https://www.ultimatepostkit.pro/pricing/',
							'title' => esc_html__('Get Pro', 'ultimate-post-kit'),
						],
						[
							'url'   => 'https://feedback.ultimatepostkit.pro/announcements/',
							'title' => esc_html__('Changelog', 'ultimate-post-kit'),
						],
					];
					foreach ($footer_link as $key => $link) {
						printf('<li><a href="%1$s" target="_blank">%2$s<span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>', esc_url($link['url']), esc_html($link['title']));
					}
					?>
				</ul>
			</div>
		</div>
		</div>
<?php
	}
}

new Ultimate_Post_Kit_Admin_Feeds();
