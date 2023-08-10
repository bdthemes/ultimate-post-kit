<?php

namespace UltimatePostKit\Modules\CategoryCarousel\Widgets;

use UltimatePostKit\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Plugin;
use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Traits\Global_Swiper_Functions;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Category_Carousel extends Module_Base {
	use Global_Widget_Controls;
	use Global_Widget_Functions;
	use Global_Swiper_Functions;
	private $_query = null;

	public function get_name() {
		return 'upk-category-carousel';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Category Carousel', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-category-carousel';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'elite'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-category-carousel'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-category-carousel'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/kNA3mYIMkLM';
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
			'skin_layout',
			[
				'label'      => __('Skin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'style-1',
				'options'    => [
					'style-1'  => __('Style 1', 'ultimate-post-kit'),
					'style-2'  => __('Style 2', 'ultimate-post-kit'),
					'style-3'  => __('Style 3', 'ultimate-post-kit'),
					'style-4'  => __('Style 4', 'ultimate-post-kit'),
					'style-5'  => __('Style 5', 'ultimate-post-kit'),
					// 'style-6'  => __('Style 6', 'ultimate-post-kit'),
				],
			]
		);
		$this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'ultimate-post-kit-pro'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options'        => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => __('Item Gap', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'tablet_default' => [
					'size' => 20,
				],
				'mobile_default' => [
					'size' => 20,
				],
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);
		$this->add_responsive_control(
			'item_height',
			[
				'label'   => esc_html__('Item Height(px)', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-category-carousel .upk-category-carousel-image' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'skin_layout!' => 'style-3'
				]
			]
		);
		$this->add_responsive_control(
			'item_height_skin_3',
			[
				'label'   => esc_html__('Item Height(px)', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-category-carousel-image' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'skin_layout' => 'style-3'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'primary_thumbnail',
				'exclude' => ['custom'],
				'default' => 'medium',
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_post_grid_query',
			[
				'label' => esc_html__('Query', 'ultimate-post-kit-pro'),
			]
		);

		// $this->add_control(
		// 	'item_limit',
		// 	[
		// 		'label' => esc_html__('Item Limit', 'ultimate-post-kit-pro'),
		// 		'type'  => Controls_Manager::SLIDER,
		// 		'range' => [
		// 			'px' => [
		// 				'min' => 1,
		// 				'max' => 50,
		// 			],
		// 		],
		// 		'default' => [
		// 			'size' => 6,
		// 		],
		// 	]
		// );

		$this->add_control(
			'taxonomy',
			[
				'label'   => esc_html__('Taxonomy', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => ultimate_post_kit_get_taxonomies(),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__('Order By', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'       => esc_html__('Name', 'ultimate-post-kit-pro'),
					'post_date'  => esc_html__('Date', 'ultimate-post-kit-pro'),
					'post_title' => esc_html__('Title', 'ultimate-post-kit-pro'),
					'menu_order' => esc_html__('Menu Order', 'ultimate-post-kit-pro'),
					'rand'       => esc_html__('Random', 'ultimate-post-kit-pro'),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__('Order', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => [
					'asc'  => esc_html__('ASC', 'ultimate-post-kit-pro'),
					'desc' => esc_html__('DESC', 'ultimate-post-kit-pro'),
				],
			]
		);
		// $this->add_control(
		// 	'include',
		// 	[
		// 		'label'       => esc_html__('Include', 'ultimate-post-kit-pro'),
		// 		'type'        => Controls_Manager::TEXT,
		// 		'placeholder' => __('Category ID: 12,3,1', 'ultimate-post-kit-pro'),
		// 	]
		// );
		$this->add_control(
			'exclude',
			[
				'label'       => esc_html__('Exclude', 'ultimate-post-kit-pro'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Category ID: 12,3,1', 'ultimate-post-kit-pro'),
			]
		);
		$this->add_control(
			'parent',
			[
				'label'       => esc_html__('Parent', 'ultimate-post-kit-pro'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Category ID: 12', 'ultimate-post-kit-pro'),
			]
		);
		// $this->add_control(
		// 	'hide_empty',
		// 	[
		// 		'label'         => esc_html__('Hide Empty', 'ultiamte-post-kit-pro'),
		// 		'type'          => Controls_Manager::SWITCHER,
		// 	]
		// );


		$this->end_controls_section();
		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__('Additional', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'     => esc_html__('Show Image', 'ultimate-post-kit-pro'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'     => esc_html__('Show Count', 'ultimate-post-kit-pro'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);
		$this->add_control(
			'show_text',
			[
				'label'   => esc_html__('Show Text', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);
		$this->add_control(
			'count_text_label',
			[
				'label'       => esc_html__('Count Label', 'ultimate-post-kit-pro'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__('Posts', 'ultimate-post-kit-pro'),
				'default' => esc_html__('Posts', 'ultimate-post-kit-pro'),
				'condition' => [
					'show_text' => 'yes'
				]
			]
		);
		$this->add_control(
			'item_match_height',
			[
				'label'        => __('Item Match Height', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'upk-item-match-height--',
				'separator'    => 'before'
			]
		);

		$this->end_controls_section();

		//Navigaation Global Controls
		$this->register_navigation_controls('category');

		//Style
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__('Item', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'item_tabs'
		);
		$this->start_controls_tab(
			'item_tab_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'items_background',
				'label'     => esc_html__('Backgrund', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-category-carousel .upk-category-carousel-image',
			]
		);
		$this->add_control(
			'item_overlay',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item-overlay' => 'background: {{VALUE}}',
				],
				'condition' => [
					'skin_layout!' => ['style-3']
				]
			]
		);
		$this->add_control(
			'item_overlay_blur_effect',
			[
				'label'       => esc_html__('Glassmorphism', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'ultimate-post-kit'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'default'     => 'yes',
				'condition' => [
					'skin_layout' => [
						'style-5',
					]
				]
			]
		);

		$this->add_control(
			'item_overlay_blur_level',
			[
				'label'     => __('Blur Level', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'   => [
					'size' => 10
				],
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel.style-5 .upk-category-carousel-image:before' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'item_overlay_blur_effect' => 'yes',
					'skin_layout' => [
						'style-5'
					]
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .upk-category-carousel.style-5 .upk-category-carousel-image:before',
				'condition' => [
					'skin_layout' => [
						'style-5'
					]
				]
			]
		);
		$this->add_responsive_control(
			'item_padding',
			[
				'label'                 => esc_html__('Padding', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-category-carousel .upk-item'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'item_margin',
			[
				'label'                 => esc_html__('Margin', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-category-carousel .upk-item'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'label'     => esc_html__('Border', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-category-carousel .upk-item',
			]
		);
		$this->add_responsive_control(
			'item_radius',
			[
				'label'                 => esc_html__('Radius', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-category-carousel .upk-item'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Box_Shadow::get_type(),
		// 	[
		// 		'name'     => 'item_shadow',
		// 		'selector' => '{{WRAPPER}} .upk-category-carousel .upk-item',
		// 	]
		// );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'item_tab_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'items_hover_background',
				'label'     => esc_html__('Backgrund', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-category-carousel .upk-item:hover .upk-category-carousel-image',
				'condition' => [
					'skin_layout!' => 'style-5'
				]
			]
		);
		$this->add_control(
			'item_overlay_hover',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item:hover .upk-item-overlay' => 'background: {{VALUE}}',
				],
				'condition' => [
					'skin_layout!' => ['style-3']
				]
			]
		);
		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item:hover' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin_layout' => [
						'style-1'
					]
				]
			]
		);
		$this->start_controls_tabs(
			'content_tabs'
		);
		$this->start_controls_tab(
			'content_tab_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .upk-category-carousel .upk-item .upk-content',
				'condition' => [
					'skin_layout' => 'style-1'
				]
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'                 => esc_html__('Padding', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_margin',
			[
				'label'                 => esc_html__('Margin', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'content_border',
				'label'     => esc_html__('Border', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-category-carousel .upk-item .upk-content',
			]
		);
		$this->add_responsive_control(
			'content_radius',
			[
				'label'                 => esc_html__('Radius', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .upk-category-carousel .upk-item .upk-content',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'content_tab_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);
		$this->add_control(
			'content_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item:hover .upk-content' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_hover_shadow',
				'selector' => '{{WRAPPER}} .upk-category-carousel .upk-item:hover .upk-content',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_category',
			[
				'label' => esc_html__('Category', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		// $this->add_responsive_control(
		// 	'category_title_spacing',
		// 	[
		// 		'label'         => esc_html__('Bottom Spacing', 'ultimate-post-kit'),
		// 		'type'          => Controls_Manager::SLIDER,
		// 		'size_units'    => ['px'],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-cateogry-carousel .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );
		$this->start_controls_tabs(
			'category_tabs'
		);
		$this->start_controls_tab(
			'category_tab_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);
		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'style_5_category_bg',
				'label'     => esc_html__('Backgorund', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .title',
				'condition' => [
					'skin_layout' => [
						'style-5'
					]
				]
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'style_6_category_bg',
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-category-carousel.style-5 .upk-category-carousel-image:before',
				'condition' => [
					'skin_layout' => [
						'style-6'
					]
				]
			]
		);
		$this->add_responsive_control(
			'category_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'skin_layout' => [
						'style-4'
					]
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'exclude' => ['line_height'],
				'selector' => '{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .title',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'category_tab_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
				'condition' => [
					'skin_layout!' => 'style-5'
				]
			]
		);
		$this->add_control(
			'hover_category_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item:hover .upk-content .title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_count',
			[
				'label' => esc_html__('Count', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'count_tabs'
		);
		$this->start_controls_tab(
			'count_tab_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
				'condition' => [
					'skin_layout!' => [
						'style-5'
					]
				]
			]
		);
		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .upk-category-count > *' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'count_background',
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .upk-category-count > *',
				'condition' => [
					'skin_layout' => [
						'style-2'
					]
				]
			]
		);
		$this->add_responsive_control(
			'count_number_size',
			[
				'label'         => esc_html__('Size', 'ultimate-post-kit'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'default'       => [
					'unit'      => 'px',
					'size'      => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .upk-category-count > *' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin_layout' => [
						'style-2'
					]
				]
			]
		);
		// $this->add_responsive_control(
		// 	'count_padding',
		// 	[
		// 		'label'      => __('Padding', 'ultimate-post-kit'),
		// 		'type'       => Controls_Manager::DIMENSIONS,
		// 		'size_units' => ['px', 'em', '%'],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .upk-category-count > *' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-category-carousel .upk-item .upk-content .upk-category-count > *',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'count_tab_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);
		$this->add_control(
			'count_color_hover',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-category-carousel .upk-item:hover .upk-content .upk-category-count > *' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'count_hover_background',
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-category-carousel .upk-item:hover .upk-content .upk-category-count > *',
				'condition' => [
					'skin_layout' => [
						'style-2'
					]
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->register_navigation_style('swiper');
	}

	public function get_taxonomies() {
		$taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

		$options = ['' => ''];

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label;
		}

		return $options;
	}

	public function render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src = wp_get_attachment_image_src($image_id, $size);
		if (!$image_src) {
			return;
		} else {
			$image_src = $image_src[0];
		}

?>
		<img class="upk-category-carousel-img" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_html(get_the_title()); ?>">
	<?php
	}

	public function render_header() {
		$id       		 = 'upk-category-carousel-' . $this->get_id();
		$settings        = $this->get_settings_for_display();
		$this->add_render_attribute('carousel', 'class', ['upk-category-carousel', $settings['skin_layout']]);
		$this->render_header_attribute('category');

		$skin_layout = $settings['skin_layout'];
		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel upk-category-carousel-wrap upk-category-carousel-' . $skin_layout .' ' . $swiper_class );
		?>
		<div <?php $this->print_render_attribute_string('carousel'); ?>>
			<div class="upk-category-carousel-wrapper">
				<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
					<div class="swiper-wrapper">
						<?php
					}

					public function render_carousel_category_item() {
						$settings = $this->get_settings_for_display();
						$categories     = get_categories(
							[
								'taxonomy'   => $settings["taxonomy"],
								'orderby'    => $settings["orderby"],
								'order'      => $settings["order"],
								// 'include'    => explode(',', esc_attr($settings["include"])),
								'exclude'    => explode(',', esc_attr($settings["exclude"])),
								'parent'     => $settings["parent"],
								'hide_empty' => 0,
							]
						);
						if (!empty($categories)) :
							foreach ($categories as $item_index => $cat) :
								$this->add_render_attribute('category-item', 'class', ['swiper-slide', 'upk-item', 'category-link'], true);
								$this->add_render_attribute('category-item', 'href', get_category_link($cat->cat_ID), true);
								$category_image_id = get_term_meta($cat->cat_ID, 'upk-category-image-id', true); ?>
								<a <?php $this->print_render_attribute_string('category-item'); ?>>
									<div class="upk-category-carousel-image">
										<?php $this->render_image($category_image_id, $settings['primary_thumbnail_size']); ?>
									</div>
									<div class="upk-content">
										<h3 class="title"><?php echo esc_html($cat->cat_name); ?></h3>
										<?php if ($settings['show_count'] === 'yes') : ?>
											<p class="upk-category-count">
											<?php printf('<span class="upk-count-number">%s</span>', $cat->category_count);
											if ($settings['show_text'] === 'yes') :
												printf('<span class="upk-count-text">%s</span>', $settings['count_text_label']);
											endif;
										endif; ?>
											</p>
									</div>
									<div class="upk-item-overlay"></div>
								</a>
				<?php
							endforeach;
						endif;
					}
					public function render() {
						$this->render_header();
						$this->render_carousel_category_item();
						$this->render_footer();
					}
				}
