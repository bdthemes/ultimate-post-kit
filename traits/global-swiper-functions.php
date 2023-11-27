<?php
	
namespace UltimatePostKit\Traits;

use Elementor\Plugin;

defined( 'ABSPATH' ) || die();
	
trait Global_Swiper_Functions {
	
	function render_header_attribute( $name ) {
		$id              = 'upk-' . $name . '-carousel-' . $this->get_id();
		$settings        = $this->get_settings_for_display();
		$elementor_vp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_vp_md = get_option( 'elementor_viewport_md' );
		$viewport_lg     = ! empty( $elementor_vp_lg ) ? $elementor_vp_lg - 1 : 1023;
		$viewport_md     = ! empty( $elementor_vp_md ) ? $elementor_vp_md - 1 : 767;
		
		$this->add_render_attribute( 'carousel', 'id', $id );
		$this->add_render_attribute( 'carousel', 'class', [ 'upk-' . $name . '-carousel' ] );
		
		if ( 'arrows' == $settings['navigation'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'upk-arrows-align-' . $settings['arrows_position'] );
		} elseif ( 'dots' == $settings['navigation'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'upk-dots-align-' . $settings['dots_position'] );
		} elseif ( 'both' == $settings['navigation'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'upk-arrows-dots-align-' . $settings['both_position'] );
		} elseif ( 'arrows-fraction' == $settings['navigation'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'upk-arrows-dots-align-' . $settings['arrows_fraction_position'] );
		}
		
		if ( 'arrows-fraction' == $settings['navigation'] ) {
			$pagination_type = 'fraction';
		} elseif ( 'both' == $settings['navigation'] or 'dots' == $settings['navigation'] ) {
			$pagination_type = 'bullets';
		} elseif ( 'progressbar' == $settings['navigation'] ) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}
		
		$this->add_render_attribute(
			[
				'carousel' => [
					'data-settings' => [
						wp_json_encode( array_filter( [
							"autoplay"              => ( "yes" == $settings["autoplay"] ) ? [ "delay" => $settings["autoplay_speed"] ] : false,
							"loop"                  => ( $settings["loop"] == "yes" ) ? true : false,
							"speed"                 => $settings["speed"]["size"],
							"pauseOnHover"          => ( "yes" == $settings["pauseonhover"] ) ? true : false,
							"slidesPerView"         => isset($settings["columns_mobile"]) ? (int)$settings["columns_mobile"] : 1,
							"slidesPerGroup"        => isset($settings["slides_to_scroll_mobile"]) ? (int)$settings["slides_to_scroll_mobile"] : 1,
							"spaceBetween"          => !empty($settings["item_gap_mobile"]["size"]) ? (int)$settings["item_gap_mobile"]["size"] : 20,
							"centeredSlides"        => ( $settings["centered_slides"] === "yes" ) ? true : false,
							"grabCursor"            => ( $settings["grab_cursor"] === "yes" ) ? true : false,
							"effect"                => $settings["skin"],
							"observer"              => ( $settings["observer"] ) ? true : false,
							"observeParents"        => ( $settings["observer"] ) ? true : false,
							"direction"             => $settings['direction'],
							"watchSlidesVisibility" => true,
							"watchSlidesProgress"   => true,
							"breakpoints"           => [
								(int) $viewport_md => [
									"slidesPerView"  => isset($settings["columns_tablet"]) ? (int)$settings["columns_tablet"] : 2,
									"spaceBetween"   => !empty($settings["item_gap_tablet"]["size"]) ? (int)$settings["item_gap_tablet"]["size"] : 20,
									"slidesPerGroup" => isset($settings["slides_to_scroll_tablet"]) ? (int)$settings["slides_to_scroll_tablet"] : 1,
								],
								(int) $viewport_lg => [
									"slidesPerView"  => isset($settings["columns"]) ? (int)$settings["columns"] : 3,
									"spaceBetween"   => !empty($settings["item_gap"]["size"]) ? (int)$settings["item_gap"]["size"] : 20,
									"slidesPerGroup" => isset($settings["slides_to_scroll"]) ? (int)$settings["slides_to_scroll"] : 1,
								]
							],
							"navigation"            => [
								"nextEl" => "#" . $id . " .upk-navigation-next",
								"prevEl" => "#" . $id . " .upk-navigation-prev",
							],
							"pagination"            => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'dynamicBullets' => ( "yes" == $settings["dynamic_bullets"] ) ? true : false,
							],
							"scrollbar"             => [
								"el"   => "#" . $id . " .swiper-scrollbar",
								"hide" => "true",
							],
							'coverflowEffect'       => [
								'rotate'       => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_rotate"]["size"] : 50,
								'stretch'      => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_stretch"]["size"] : 0,
								'depth'        => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_depth"]["size"] : 100,
								'modifier'     => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_modifier"]["size"] : 1,
								'slideShadows' => true,
							],
						
						] ) )
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);
	}
	
	function render_navigation() {
		$settings             = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' upk-visible@m' : '';
		
		if ( 'arrows' == $settings['navigation'] ) : ?>
			<div class="upk-position-z-index upk-position-<?php echo esc_html( $settings['arrows_position'] . $hide_arrow_on_mobile ); ?>">
				<div class="upk-arrows-container upk-slidenav-container">
					<div class="upk-navigation-prev">
						<i class="upk-icon-arrow-left-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</div>
					<div class="upk-navigation-next">
						<i class="upk-icon-arrow-right-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</div>
				</div>
			</div>
		<?php endif;
	}
	
	function render_pagination() {
		$settings = $this->get_settings_for_display();
		
		if ( 'dots' == $settings['navigation'] or 'arrows-fraction' == $settings['navigation'] ) : ?>
			<div class="upk-position-z-index upk-position-<?php echo esc_html($settings['dots_position']); ?>">
				<div class="upk-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			</div>
		
		<?php elseif ( 'progressbar' == $settings['navigation'] ) : ?>
			<div class="swiper-pagination upk-position-z-index upk-position-<?php echo esc_html($settings['progress_position']); ?>"></div>
		<?php endif;
	}
	
	function render_both_navigation() {
		$settings             = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'upk-visible@m upk-flex' : 'upk-flex';
		
		?>
		<div class="upk-position-z-index upk-position-<?php echo esc_html($settings['both_position']); ?>">
			<div class="upk-arrows-dots-container upk-slidenav-container ">

				<div class="upk-flex upk-flex-middle">
					<div class="<?php echo esc_html( $hide_arrow_on_mobile ); ?>">
						<div class="upk-navigation-prev">
							<i class="upk-icon-arrow-left-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>
					
					<?php if ('center' !== $settings['both_position']) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>

					<div class="<?php echo esc_html( $hide_arrow_on_mobile ); ?>">
						<div class="upk-navigation-next">
							<i class="upk-icon-arrow-right-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php
	}
	
	function render_arrows_fraction() {
		$settings             = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'upk-visible@m' : '';
		
		?>
		<div class="upk-position-z-index upk-position-<?php echo esc_html($settings['arrows_fraction_position']); ?>">
			<div class="upk-arrows-fraction-container upk-slidenav-container ">

				<div class="upk-flex upk-flex-middle">
					<div class="<?php echo esc_html( $hide_arrow_on_mobile ); ?>">
						<div class="upk-navigation-prev">
							<i class="upk-icon-arrow-left-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>
					
					<?php if ('center' !== $settings['arrows_fraction_position']) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>

					<div class="<?php echo esc_html( $hide_arrow_on_mobile ); ?>">
						<div class="upk-navigation-next">
							<i class="upk-icon-arrow-right-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php
	}
	
	function render_footer() {
		$settings = $this->get_settings_for_display();
		
		?>
		</div>
		<?php if ( 'yes' === $settings['show_scrollbar'] ) : ?>
			<div class="swiper-scrollbar"></div>
		<?php endif; ?>
		</div>
		
		<?php if ('both' == $settings['navigation']) : ?>
			<?php $this->render_both_navigation(); ?>
			<?php if ( 'center' === $settings['both_position'] ) : ?>
				<div class="upk-position-z-index upk-position-bottom">
					<div class="upk-dots-container">
						<div class="swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>
		<?php elseif ('arrows-fraction' == $settings['navigation']) : ?>
			<?php $this->render_arrows_fraction(); ?>
			<?php if ( 'center' === $settings['arrows_fraction_position'] ) : ?>
				<div class="upk-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<?php $this->render_pagination(); ?>
			<?php $this->render_navigation(); ?>
		<?php endif; ?>

		</div>
		</div>
		<?php
	}
}