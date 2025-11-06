<?php

namespace UltimatePostKit\Modules\TinyList\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Icons_Manager;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Tiny_List extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-tiny-list';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Tiny List', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-tiny-list';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'tiny', 'blog', 'recent', 'news', 'tiny', 'list'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-tiny-list'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-ajax-loadmore'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/PZlXofIOy68';
	}

	public function get_query() {
		return $this->_query;
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }
	

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Columns', 'ultimate-post-kit'),
				'type' => Controls_Manager::SELECT,
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options' => [
					'1' => esc_html__('1', 'ultimate-post-kit'),
					'2' => esc_html__('2', 'ultimate-post-kit'),
					'3' => esc_html__('3', 'ultimate-post-kit'),
					'4' => esc_html__('4', 'ultimate-post-kit'),
					'5' => esc_html__('5', 'ultimate-post-kit'),
					'6' => esc_html__('6', 'ultimate-post-kit'),
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => esc_html__('Row Gap', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'     => __('Title HTML Tag', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => ultimate_post_kit_title_tags(),
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'   => esc_html__('Show Image', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'primary_thumbnail',
				'exclude' => ['custom'],
				'default' => 'thumbnail',
				'condition' => [
					'show_image' => 'yes'
				]
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'show_image' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_counter_number',
			[
				'label'   => esc_html__('Show Counter Number', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				// 'default' => 'yes',
			]
		);

		$this->add_control(
			'show_item_icon',
			[
				'label'       => esc_html__('Icon', 'ultimate-post-kit'),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'     => esc_html__('Show Pagination', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		//Global Ajax Controls
		$this->register_ajax_loadmore_controls();

		$this->add_control(
			'global_link',
			[
				'label'        => __('Item Wrapper Link', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'upk-global-link-',
				'description'  => __('Be aware! When Item Wrapper Link activated then title link and read more link will not work', 'ultimate-post-kit'),
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		// Query Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'item_limit',
			[
				'label'   => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'default' => [
					'size' => 4,
				],
			]
		);

		$this->register_query_builder_controls();

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'upk_section_style',
			[
				'label' => esc_html__('Item', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_hover_background',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item:hover',
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__('Image', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'item_image_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_image_border',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-img',
			]
		);

		$this->add_responsive_control(
			'item_image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_image_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_image_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_image_shadow',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-img',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__('Icon', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_item_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'item_icon_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_icon_background',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_icon_border',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon',
			]
		);

		$this->add_responsive_control(
			'item_icon_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_icon_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_icon_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_icon_shadow',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon',
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_style',
			[
				'label'   => esc_html__('Style', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => [
					''        => esc_html__('Default', 'ultimate-post-kit'),
					'underline'        => esc_html__('Underline', 'ultimate-post-kit'),
					'middle-underline' => esc_html__('Middle Underline', 'ultimate-post-kit'),
					'overline'         => esc_html__('Overline', 'ultimate-post-kit'),
					'middle-overline'  => esc_html__('Middle Overline', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title a' => 'color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'label'    => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'title_text_stroke',
				'label'    => esc_html__('Text Stroke', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-title a',
			]
		);

		$this->add_control(
			'title_hover_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'ultimate-post-kit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 's',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-title a' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_counter_number',
			[
				'label'     => esc_html__('Counter Number', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_counter_number' => 'yes',
				]
			]
		);

		$this->add_control(
			'counter_number_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-counter:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'counter_number_background',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-counter',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'counter_number_border',
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-counter',
			]
		);

		$this->add_responsive_control(
			'counter_number_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'counter_number_spacing',
			[
				'label'     => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-counter' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'counter_number_size',
			[
				'label'     => esc_html__('Counter Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tiny-list .upk-item .upk-counter' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'counter_number_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-tiny-list .upk-item .upk-counter:before',
			]
		);

		$this->end_controls_section();

		//Global Pagination Controls
		$this->register_pagination_controls();

		//Global Ajax Loadmore Style Controls
		$this->register_ajax_loadmore_style_controls();
	}

	/**
	 * Main query render for this widget
	 * @param $posts_per_page number item query limit
	 */
	public function query_posts($posts_per_page) {

		$default = $this->getGroupControlQueryArgs();
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
		}
		$args         = array_merge($default, $args);
		$this->_query = new WP_Query($args);
	}

	public function render_post_grid_item($post_id, $image_size) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('list-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}
		$this->add_render_attribute('list-item', 'class', 'upk-item', true);

?>
		<div <?php $this->print_render_attribute_string('list-item'); ?>>
			<div class="upk-content upk-flex upk-flex-middle">

				<?php if ($settings['show_counter_number'] == 'yes') : ?>
					<div class="upk-counter"></div>
				<?php endif; ?>

				<?php if ($settings['show_image'] == 'yes') : ?>
					<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
				<?php endif; ?>

				<?php if ($settings['show_item_icon']['value']) : ?>
					<div class="upk-title-icon">
						<?php Icons_Manager::render_icon($settings['show_item_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
					</div>
				<?php endif; ?>

				<?php $this->render_title(substr($this->get_name(), 4)); ?>
			</div>
		</div>

	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->query_posts($settings['item_limit']['size']);
		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->add_render_attribute('list-wrap', 'class', 'upk-tiny-list upk-ajax-grid-wrap');

		$this->add_render_attribute(
			[
				'upk-tiny-list' => [
					'class' => 'upk-tiny-list-container upk-ajax-grid',
					'data-loadmore' => [
						wp_json_encode(
							array_filter([
								'loadmore_enable'   => $settings['ajax_loadmore_enable'],
								'loadmore_btn'      => $settings['ajax_loadmore_btn'],
								'infinite_scroll'   => $settings['ajax_loadmore_infinite_scroll'],
							])
						),
					],
				],
			]
		);

		if ($settings['ajax_loadmore_enable'] == 'yes') {
			$ajax_settings = [
				'posts_source'                  => isset($settings['posts_source']) ? $settings['posts_source'] : 'post',
				'posts_per_page'                => isset($settings['item_limit']['size']) ? $settings['item_limit']['size'] : 4,
				'ajax_item_load'                => isset($settings['ajax_loadmore_items']) ? $settings['ajax_loadmore_items'] : 3,
				'posts_selected_ids'            => isset($settings['posts_selected_ids']) ? $settings['posts_selected_ids'] : '',
				'posts_include_by'              => isset($settings['posts_include_by']) ? $settings['posts_include_by'] : [],
				'posts_include_author_ids'      => isset($settings['posts_include_author_ids']) ? $settings['posts_include_author_ids'] : '',
				'posts_include_term_ids'        => isset($settings['posts_include_term_ids']) ? $settings['posts_include_term_ids'] : '',
				'posts_exclude_by'              => isset($settings['posts_exclude_by']) ? $settings['posts_exclude_by'] : [],
				'posts_exclude_ids'             => isset($settings['posts_exclude_ids']) ? $settings['posts_exclude_ids'] : '',
				'posts_exclude_author_ids'      => isset($settings['posts_exclude_author_ids']) ? $settings['posts_exclude_author_ids'] : '',
				'posts_exclude_term_ids'        => isset($settings['posts_exclude_term_ids']) ? $settings['posts_exclude_term_ids'] : '',
				'posts_offset'                  => isset($settings['posts_offset']) ? $settings['posts_offset'] : 0,
				'posts_select_date'             => isset($settings['posts_select_date']) ? $settings['posts_select_date'] : '',
				'posts_date_before'             => isset($settings['posts_date_before']) ? $settings['posts_date_before'] : '',
				'posts_date_after'              => isset($settings['posts_date_after']) ? $settings['posts_date_after'] : '',
				'posts_orderby'                 => isset($settings['posts_orderby']) ? $settings['posts_orderby'] : 'date',
				'posts_order'                   => isset($settings['posts_order']) ? $settings['posts_order'] : 'DESC',
				'posts_ignore_sticky_posts'     => isset($settings['posts_ignore_sticky_posts']) ? $settings['posts_ignore_sticky_posts'] : 'no',
				'posts_only_with_featured_image'=> isset($settings['posts_only_with_featured_image']) ? $settings['posts_only_with_featured_image'] : 'no',
				// List Settings
				'show_title'                    => isset($settings['show_title']) ? $settings['show_title'] : 'yes',
				'title_tags'                    => isset( $settings['title_tags'] ) ? $settings['title_tags'] : 'h3',
				'title_style'                   => isset($settings['title_style']) ? $settings['title_style'] : '',
				'show_image'                    => isset($settings['show_image']) ? $settings['show_image'] : 'no',
				'primary_thumbnail_size'        => isset($settings['primary_thumbnail_size']) ? $settings['primary_thumbnail_size'] : 'thumbnail',
				'show_counter_number'           => isset($settings['show_counter_number']) ? $settings['show_counter_number'] : 'no',
				'show_item_icon'                => isset($settings['show_item_icon']) ? $settings['show_item_icon'] : [],
				'global_link'                   => isset($settings['global_link']) ? $settings['global_link'] : 'no',
			];
		
			$this->add_render_attribute(
				[
					'upk-tiny-list' => [
						'data-settings' => [
							wp_json_encode($ajax_settings)
						],
					],
				]
			);
		}

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('list-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('list-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>
		<div <?php $this->print_render_attribute_string('upk-tiny-list'); ?>>
			<div <?php $this->print_render_attribute_string('list-wrap'); ?>>
				<?php while ($wp_query->have_posts()) :
					$wp_query->the_post();

					$thumbnail_size = $settings['primary_thumbnail_size'];

				?>

					<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size); ?>

				<?php endwhile; ?>
			</div>
		</div>

		<?php $this->render_ajax_loadmore(); ?>

		<?php

		if ($settings['show_pagination']) { ?>
			<div class="ep-pagination">
				<?php ultimate_post_kit_post_pagination($wp_query, $this->get_id()); ?>
			</div>
<?php
		}
		wp_reset_postdata();
	}
}
