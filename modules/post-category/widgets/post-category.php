<?php

namespace UltimatePostKit\Modules\PostCategory\Widgets;

use UltimatePostKit\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Post_Category extends Module_Base {
	private $_query = null;

	public function get_name() {
		return 'upk-post-category';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Category', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-post-category';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'list', 'blog', 'recent', 'news', 'category'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-post-category'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/3S5hRqxTDTo';
	}

	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {

		$image_settings = ultimate_post_kit_option('category_image', 'ultimate_post_kit_other_settings', 'off');

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => __('Style', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => 'Style 1',
					'2' => 'Style 2',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'ultimate-post-kit'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors'      => [
					'{{WRAPPER}} .upk-post-category' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-post-category' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_height',
			[
				'label'     => esc_html__('Item Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'     => __('Alignment', 'ultimate-post-kit'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item' => 'text-align: {{VALUE}};',
				],
			]
		);
		if ($image_settings == 'on') :
			$this->add_control(
				'show_image',
				[
					'label'     => esc_html__('Show Image', 'ultimate-post-kit'),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'no',
					'separator' => 'before'
				]
			);
		endif;
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'cat_image_size',
				'exclude'   => ['custom'],
				'include'   => [],
				'default'   => 'thumbnail',
				'condition' => [
					'show_image' => 'yes'
				]
			]
		);
		$this->add_control(
			'show_count',
			[
				'label'   => esc_html__('Show Count', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_text',
			[
				'label' => esc_html__('Show Text', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'text_length',
			[
				'label'       => esc_html__('Text Limit', 'ultimate-post-kit'),
				'description' => esc_html__('If you set 0 so you will get full main content.', 'ultimate-post-kit'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'condition'   => [
					'show_text' => 'yes'
				],
			]
		);

		$this->add_control(
			'view_all_button',
			[
				'label' => esc_html__('View All Button', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_grid_query',
			[
				'label' => esc_html__('Query', 'ultimate-post-kit'),
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
						'max' => 50,
					],
				],
				'default' => [
					'size' => 6,
				],
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'   => esc_html__('Taxonomy', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => ultimate_post_kit_get_taxonomies(),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__('Order By', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'       => esc_html__('Name', 'ultimate-post-kit'),
					'post_date'  => esc_html__('Date', 'ultimate-post-kit'),
					'post_title' => esc_html__('Title', 'ultimate-post-kit'),
					'menu_order' => esc_html__('Menu Order', 'ultimate-post-kit'),
					'rand'       => esc_html__('Random', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__('Order', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => [
					'asc'  => esc_html__('ASC', 'ultimate-post-kit'),
					'desc' => esc_html__('DESC', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'exclude',
			[
				'label'       => esc_html__('Exclude', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Category ID: 12,3,1', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'parent',
			[
				'label'       => esc_html__('Parent', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Category ID: 12', 'ultimate-post-kit'),
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'upk_section_style',
			[
				'label' => esc_html__('Items', 'ultimate-post-kit'),
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

		$this->add_control(
			'single_background',
			[
				'label' => esc_html__('Single Background', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_background',
				'selector'  => '{{WRAPPER}} .upk-post-category .upk-category-item',
				'condition' => [
					'single_background' => 'yes'
				]
			]
		);

		$this->add_control(
			'multiple_background',
			[
				'label'       => esc_html__('Multiple Background', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => '#000000, #f5f5f5, #999999',
				'condition'   => [
					'single_background' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-post-category .upk-category-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-post-category .upk-category-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item',
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
				'name'     => 'itam_background_hover',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item:hover',
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'item_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_hover',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item:hover',
			]
		);

		$this->add_control(
			'item_hover_opacity',
			[
				'label'     => esc_html__('Opacity', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category_image',
			[
				'label'     => esc_html__('Image', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'cat_image_background',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-image img',
			]
		);
		$this->add_responsive_control(
			'cat_image_width',
			[
				'label'      => esc_html__('Image Size', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'style' => '1'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'cat_image_border',
				'label'    => esc_html__('Border', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-image img',
			]
		);

		$this->add_responsive_control(
			'cat_image_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cat_image_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'cat_image_bottom_spacing',
			[
				'label'     => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => '1'
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cat_image_box_shadow',
				'label'    => esc_html__('Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-image img',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category_name',
			[
				'label' => esc_html__('Name', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'category_name_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_name_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item:hover .upk-category-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_name_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-name',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category_text',
			[
				'label' => esc_html__('Description', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'category_text_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_text_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item:hover .upk-category-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_text_spacing',
			[
				'label'     => __('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-text' => 'padding-top: {{SIZE}}px;'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_count',
			[
				'label' => esc_html__('Count', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'count_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-post-category .upk-category-item:hover .upk-category-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'count_background',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'count_border',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count',
			]
		);

		$this->add_responsive_control(
			'count_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'count_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'count_box_shadow',
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count',
			]
		);

		$this->add_control(
			'count_offset_toggle',
			[
				'label'        => __('Offset', 'ultimate-post-kit'),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __('None', 'ultimate-post-kit'),
				'label_on'     => __('Custom', 'ultimate-post-kit'),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'count_horizontal_offset',
			[
				'label'       => __('Horizontal Offset', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'condition'   => [
					'count_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors'   => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count' => 'right: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'count_vertical_offset',
			[
				'label'       => __('Vertical Offset', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'condition'   => [
					'count_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors'   => [
					'{{WRAPPER}} .upk-post-category .upk-category-item .upk-category-count' => 'top: {{SIZE}}px;',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	public function render() {
		$settings       = $this->get_settings_for_display();
		$image_settings = ultimate_post_kit_option('category_image', 'ultimate_post_kit_other_settings', 'off');
		$categories     = get_categories(
			[
				'taxonomy'   => $settings["taxonomy"],
				'orderby'    => $settings["orderby"],
				'order'      => $settings["order"],
				'hide_empty' => 0,
				'exclude'    => explode(',', esc_attr($settings["exclude"])),
				'parent'     => $settings["parent"],
			]
		);

		$this->add_render_attribute('category-container', 'class', 'upk-post-category');
		$this->add_render_attribute('category-container', 'class', 'upk-category-style-' . $settings['style']);

		if ($settings['view_all_button'] == 'yes') {
			$this->add_render_attribute('category-container', 'class', 'upk-category-view-all');
		}


		if (!empty($categories)) :

?>
			<div <?php $this->print_render_attribute_string('category-container'); ?>>
				<?php
				$multiple_bg    = explode(',', rtrim($settings['multiple_background'], ','));
				$total_category = count($categories);

				// re-creating array for the multiple colors
				$jCount = count($multiple_bg);
				$j      = 0;
				for ($i = 0; $i < $total_category; $i++) {
					if ($j == $jCount) {
						$j = 0;
					}
					$multiple_bg_create[$i] = $multiple_bg[$j];
					$j++;
				}

				foreach ($categories as $index => $cat) :

					$this->add_render_attribute('category-item', 'class', 'upk-category-item', true);

					$this->add_render_attribute('category-item', 'href', get_category_link($cat->cat_ID), true);

					$bg_color = strToHex($cat->cat_name);
					//===============================
					// 		CATEGORY IMAGE
					$category_image_id = get_term_meta($cat->cat_ID, 'upk-category-image-id', true);

					if (!empty($category_image_id)) {
						$category_url   = wp_get_attachment_image_url($category_image_id, $settings['cat_image_size_size']);
						$category_image = '<div class="upk-category-image"><img src="' . $category_url . '" alt=""></div>';
					} else {
						$category_image = '';
					}
					//===============================

					if (!empty($settings['multiple_background'])) {
						$bg_color = $multiple_bg_create[$index];
						if (!preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $multiple_bg_create[$index])) {
							$bg_color = strToHex($cat->cat_name);
						}
					}

					if ($settings['single_background'] == '') {
						$this->add_render_attribute('category-item', 'style', "background-color: $bg_color", true);
					}

				?>

					<a <?php $this->print_render_attribute_string('category-item'); ?>>
						<!-- display image  -->
						<?php if ($image_settings == 'on' && $settings['show_image'] == 'yes') :
							echo  $category_image;
						endif; ?>
						<div class="upk-content">
							<span class="upk-category-name"><?php echo esc_html($cat->cat_name); ?></span>

							<?php if (!empty($cat->category_description) and $settings['show_text'] == 'yes') : ?>
								<span class="upk-category-text"><?php echo wp_trim_words($cat->category_description, $settings['text_length']); ?></span>
							<?php endif; ?>

							<?php if ($settings['show_count'] == 'yes') : ?>

								<span class="upk-category-count"><?php echo esc_html($cat->category_count); ?></span>
							<?php endif; ?>
						</div>

						<?php if ($settings['view_all_button'] == 'yes') : ?>
							<div class="upk-category-btn">
								<span><?php esc_html_e('view all', 'ultimate-post-kit'); ?></span>
							</div>
						<?php endif; ?>

					</a>
				<?php

					if (!empty($settings['item_limit']['size'])) {
						if ($index == ($settings['item_limit']['size'] - 1)) {
							break;
						}
					}
				endforeach;
				?>
			</div>
<?php
		else :

			echo '<div class="upk-alert">' . __('Category Not Found!', 'ultimate-post-kit') . '</div>';

		endif;
	}
}
