<?php

namespace UltimatePostKit\Modules\AliceGrid\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Alice_Grid extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-alice-grid';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Alice Grid', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-alice-grid';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'alice'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-alice-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/E7W5WSAvxbA';
	}


	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'grid_style',
			[
				'label'   => esc_html__('Layout Style', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'  => esc_html__('01', 'ultimate-post-kit'),
					'2'  => esc_html__('02', 'ultimate-post-kit'),
					'3'  => esc_html__('03', 'ultimate-post-kit'),
					'4'  => esc_html__('04', 'ultimate-post-kit'),
				],
			]
		);

		$column_size = apply_filters('upk_column_size', '');

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Columns', 'ultimate-post-kit') . BDTUPK_PC,
				'type' => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-style-1' => $column_size,
				],
				'condition' => [
					'grid_style' => ['1']
				],
				'classes' => BDTUPK_IS_PC
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => esc_html__('Row Gap', 'ultimate-post-kit') . BDTUPK_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-alice-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-alice-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'default_item_height',
			[
				'label'   => esc_html__('Default Item Height', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 200,
						'max'  => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-style-1 .upk-item, {{WRAPPER}} .upk-alice-grid .upk-style-4 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-alice-grid .upk-style-4 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-alice-grid .upk-style-4 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-alice-grid .upk-style-4 .upk-item:nth-child(5n+5)' => 'height: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .upk-alice-grid .upk-style-4 .upk-item:nth-child(5n+2)' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'grid_style' => ['1', '4']
				]
			]
		);

		$this->add_responsive_control(
			'primary_item_height',
			[
				'label'   => esc_html__('Primary Item Height', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 200,
						'max'  => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+2), {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+1)' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'grid_style!' => ['1', '3', '4']
				]
			]
		);

		$this->add_responsive_control(
			'secondary_item_height',
			[
				'label'   => esc_html__('Secondary Item Height', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 200,
						'max'  => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+5), {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+2), {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+3), {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+4), {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+5), {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+6)' => 'height: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+1)' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'grid_style!' => ['1', '4']
				]
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'   => esc_html__('Content Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::CHOOSE,
				'toggle'  => false,
				'default' => 'bottom-center',
				'options' => [
					'bottom-left' => [
						'title' => esc_html__('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-left',
					],
					'bottom-center' => [
						'title' => esc_html__('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-center',
					],
					'bottom-right' => [
						'title' => esc_html__('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'primary_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'medium',
			]
		);

		$this->end_controls_section();

		// Query Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'ultimate-post-kit') . BDTUPK_NC,
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'item_limit',
			[
				'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 21,
						'step' => 3
					],
				],
				'default' => [
					'size' => 6,
				],
				'condition' => [
					'grid_style' => ['1', '3']
				]
			]
		);

		$this->add_control(
			'item_limit_2',
			[
				'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 5
					],
				],
				'default' => [
					'size' => 5,
				],
				'condition' => [
					'grid_style' => ['2', '4']
				]
			]
		);

		$this->register_query_builder_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__('Additional', 'ultimate-post-kit'),
			]
		);

		//Global Title Controls
		$this->register_title_controls();

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'   => esc_html__('Author', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		//Global Date Controls
		$this->register_date_controls();

		//Global Reading Time Controls
		$this->register_reading_time_controls();

		$this->add_control(
			'meta_separator',
			[
				'label'       => __('Separator', 'ultimate-post-kit') . BDTUPK_NC,
				'type'        => Controls_Manager::TEXT,
				'default'     => '.',
				'label_block' => false,
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Pagination', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'global_link',
			[
				'label'        => __('Item Wrapper Link', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'upk-global-link-',
				'description'  => __('Be aware! When Item Wrapper Link activated then title link and read more link will not work', 'ultimate-post-kit'),
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

		$this->add_responsive_control(
			'content_padding',
			[
				'label' 	 => __('Content Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alice-grid .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
			'overlay_blur_effect',
			[
				'label' => esc_html__('Glassmorphism', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'ultimate-post-kit'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
			]
		);

		$this->add_control(
			'overlay_blur_level',
			[
				'label'       => __('Blur Level', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-alice-grid .upk-item-box::before' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'item_overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-item-box::before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'selector'    => '{{WRAPPER}} .upk-alice-grid .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alice-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alice-grid .upk-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 	   => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-alice-grid .upk-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'overlay_blur_level_hover',
			[
				'label'       => __('Blur Level', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 0
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-alice-grid .upk-item:hover .upk-item-box::before' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'item_overlay_hover_color',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-item:hover .upk-item-box::before' => 'background: {{VALUE}};',
				],
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
					'{{WRAPPER}} .upk-alice-grid .upk-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 	   => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .upk-alice-grid .upk-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_style',
			[
				'label'   => esc_html__('Style', 'ultimate-post-kit') . BDTUPK_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => [
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
					'{{WRAPPER}} .upk-alice-grid .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-alice-grid .upk-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-alice-grid .upk-item .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'secondary_title_typography',
				'label'     => esc_html__('Secondary Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+3) .upk-title, {{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+4) .upk-title, {{WRAPPER}} .upk-alice-grid .upk-style-2 .upk-item:nth-child(5n+5) .upk-title, {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+2) .upk-title, {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+3) .upk-title, {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+4) .upk-title, {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+5) .upk-title, {{WRAPPER}} .upk-alice-grid .upk-style-3 .upk-item:nth-child(6n+6) .upk-title',
				'condition' => [
					'grid_style!' => ['1', '4']
				]
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alice-grid .upk-title a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_author',
			[
				'label'     => esc_html__('Meta', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_author',
							'value' => 'yes'
						],
						[
							'name'  => 'show_date',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->add_control(
			'author_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-meta .upk-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'date_divider_color',
		// 	[
		// 		'label'     => esc_html__('Divider Color', 'ultimate-post-kit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-alice-grid .upk-meta .upk-date:before' => 'background: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'date_spacing',
		// 	[
		// 		'label'   => esc_html__('Spacing', 'ultimate-post-kit'),
		// 		'type'    => Controls_Manager::SLIDER,
		// 		'range' => [
		// 			'px' => [
		// 				'min'  => 0,
		// 				'max'  => 50,
		// 				'step' => 2,
		// 			],
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-alice-grid .upk-meta .upk-date' => 'margin-left: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );
		$this->add_responsive_control(
			'date_spacing',
			[
				'label'     => esc_html__('Space Between', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alice-grid .upk-meta *',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__('Category', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'category_bottom_spacing',
			[
				'label'   => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-category' => 'margin: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_category_style');

		$this->start_controls_tab(
			'tab_category_normal',
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
					'{{WRAPPER}} .upk-alice-grid .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-alice-grid .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-alice-grid .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alice-grid .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alice-grid .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing',
			[
				'label'   => esc_html__('Space Between', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-alice-grid .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alice-grid .upk-category a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_category_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-alice-grid .upk-category a:hover',
			]
		);

		$this->add_control(
			'category_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alice-grid .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Global Pagination Controls
		$this->register_pagination_controls();
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

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}

?>

		<div class="upk-author">
			<span><?php echo esc_html_x('by', 'Frontend', 'ultimate-post-kit') ?></span>
			<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
				<?php echo get_the_author() ?>
			</a>
		</div>

	<?php
	}

	public function render_post_grid_item($post_id, $image_size) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}
		$this->add_render_attribute('grid-item', 'class', 'upk-item', true);

	?>
		<div <?php $this->print_render_attribute_string('grid-item'); ?>>
			<div class="upk-item-box">
				<div class="upk-img-wrap">
					<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
				</div>
				<?php $this->render_category(); ?>
				<div class="upk-content">
					<?php $this->render_title(substr($this->get_name(), 4)); ?>

					<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
					<div class="upk-meta">
						<?php $this->render_author(); ?>
						<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
						<?php $this->render_date(); ?>
						</div>
						<?php if (_is_upk_pro_activated()) :
							if ('yes' === $settings['show_reading_time']) : ?>
								<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php echo ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();


		if ($settings['grid_style'] == '2' or $settings['grid_style'] == '4') {
			$this->query_posts($settings['item_limit_2']['size']);
		} else {
			$this->query_posts($settings['item_limit']['size']);
		}

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->add_render_attribute('grid-wrap', 'class', 'upk-alice-wrap');
		$this->add_render_attribute('grid-wrap', 'class', 'upk-content-' . $settings['content_position']);
		$this->add_render_attribute('grid-wrap', 'class', 'upk-style-' . $settings['grid_style']);


		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>
		<div class="upk-alice-grid">
			<div <?php $this->print_render_attribute_string('grid-wrap'); ?>>
				<?php while ($wp_query->have_posts()) :
					$wp_query->the_post();
					$thumbnail_size = $settings['primary_thumbnail_size'];
				?>
					<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size); ?>
				<?php endwhile; ?>
			</div>
		</div>

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
