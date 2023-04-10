<?php

namespace UltimatePostKit\Modules\AlexGrid\Widgets;

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

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Alex_Grid extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-alex-grid';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Alex Grid', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-alex-grid';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'alex'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-alex-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/criKI7Mm-5g';
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
					'1' => esc_html__('Style 01', 'ultimate-post-kit'),
					'2' => esc_html__('Style 02', 'ultimate-post-kit'),
					'3' => esc_html__('Style 03', 'ultimate-post-kit'),
				],
			]
		);

		// if (_is_upk_pro_activated()) {
		// 	$column_size = 'grid-template-columns: repeat({{SIZE}}, 1fr);';

		// } else {
		// 	$column_size = '';
		// }

		$column_size = apply_filters('upk_column_size', '');

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'ultimate-post-kit') . BDTUPK_PC,
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
					'{{WRAPPER}} .upk-alex-grid .upk-style-2' => $column_size,
				],
				'condition'      => [
					'grid_style' => ['2']
				],
				'classes'        => BDTUPK_IS_PC
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Row Gap', 'ultimate-post-kit') . BDTUPK_NC,
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-alex-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alex-grid .upk-alex-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'default_item_height',
			[
				'label'     => esc_html__('Item Height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-style-2 .upk-item, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5)' => 'height: {{SIZE}}{{UNIT}};',
					
				],
				'condition' => [
					'grid_style' => ['2', '3']
				]
			]
		);

		$this->add_responsive_control(
			'primary_item_height',
			[
				'label'     => esc_html__('Primary Item Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+2), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+2)' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'grid_style!' => ['2', '3']
				]
			]
		);

		$this->add_responsive_control(
			'secondary_item_height',
			[
				'label'     => esc_html__('Secondary Item Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5)' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'grid_style!' => ['2', '3']
				]
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'   => esc_html__('Content Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::CHOOSE,
				'toggle'  => false,
				'default' => 'right',
				'options' => [
					'left'  => [
						'title' => esc_html__('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-h-align-right',
					],
				],
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

		//New Query Builder Settings
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
				'label'     => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 20,
						'step' => 5
					],
				],
				'default'   => [
					'size' => 5,
				],
				'condition' => [
					'grid_style' => ['1', '3']
				]
			]
		);

		$this->add_control(
			'item_limit_2',
			[
				'label'     => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 21,
						'step' => 1
					],
				],
				'default'   => [
					'size' => 6,
				],
				'condition' => [
					'grid_style' => ['2']
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
			'show_readmore',
			[
				'label'   => esc_html__('Read More', 'ultimate-post-kit'),
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
				'default'     => '|',
				'label_block' => false,
			]
		);

		$this->add_control(
			'show_post_format',
			[
				'label'   => esc_html__('Post Format', 'ultimate-post-kit') . BDTUPK_NC,
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'     => esc_html__('Pagination', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
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

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item',
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
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_hover',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item:hover',
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
			]
		);

		$this->add_control(
			'overlay_blur_effect',
			[
				'label'       => esc_html__('Glassmorphism', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'ultimate-post-kit'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'default'     => 'yes',
			]
		);

		$this->add_control(
			'overlay_blur_level',
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
					'{{WRAPPER}} .upk-alex-grid .upk-content-wrap' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-content-wrap',
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label'     => __('Width(%)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-content-wrap' => 'width: {{SIZE}}%;'
				],
			]
		);

		$this->add_responsive_control(
			'secondary_content_width',
			[
				'label'     => __('Secondary Width(%)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3) .upk-content-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4) .upk-content-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5) .upk-content-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1) .upk-content-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-content-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-content-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-content-wrap' => 'width: {{SIZE}}%;'
				],
				'condition' => [
					'grid_style!' => '2'
				]
			]
		);

		$this->add_control(
			'hr_32',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'content_height',
			[
				'label'     => __('Height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-content' => 'height: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'secondary_content_height',
			[
				'label'     => __('Secondary height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-content' => 'height: {{SIZE}}px;'
				],
				'condition' => [
					'grid_style!' => '2'
				]
			]
		);

		$this->add_control(
			'hr_2',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'content_inner_padding',
			[
				'label'      => __('Inner Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_secondary_inner_padding',
			[
				'label'      => __('Secondary Inner Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-content, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'grid_style!' => '2'
				]
			]
		);

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
				'default' => '',
				'options' => [
					''                 => esc_html__('Default', 'ultimate-post-kit'),
					'underline'        => esc_html__('Underline', 'ultimate-post-kit'),
					'middle-underline' => esc_html__('Middle Underline', 'ultimate-post-kit'),
					'overline'         => esc_html__('Overline', 'ultimate-post-kit'),
					'middle-overline'  => esc_html__('Middle Overline', 'ultimate-post-kit'),
				],
			]
		);

		$this->start_controls_tabs('tabs_title_style');

		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'secondary_title_typography',
				'label'     => esc_html__('Secondary Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3) .upk-title, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4) .upk-title, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5) .upk-title, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1) .upk-title, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-title, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-title, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-title',
				'condition' => [
					'grid_style!' => '2'
				]
			]
		);

		$this->add_control(
			'title_advanced_style',
			[
				'label' => esc_html__('Advanced Style', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'title_background',
				'selector'  => '{{WRAPPER}} .upk-alex-grid .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'      => 'title_text_shadow',
				'label'     => __('Text Shadow', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-alex-grid .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border',
				'selector'  => '{{WRAPPER}} .upk-alex-grid .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'title_box_shadow',
				'selector'  => '{{WRAPPER}} .upk-alex-grid .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_text_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_border_hover_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes',
					'title_border_border!' => '',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'title_hover_background',
				'selector'  => '{{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__('Category', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_category' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'category_bottom_spacing',
			[
				'label'     => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_space_between',
			[
				'label'     => esc_html__('Space Between', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_category_hover',
			[
				'label'     => esc_html__('Hover', 'ultimate-post-kit'),
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a:hover',
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
					'{{WRAPPER}} .upk-alex-grid .upk-item .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_readmore',
			[
				'label'     => __('Read More', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_readmore' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_readmore_style');

		$this->start_controls_tab(
			'tab_readmore_normal',
			[
				'label' => __('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'readmore_text_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-readmore .upk-readmore-icon:before, {{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-readmore .upk-readmore-icon span:before, {{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-readmore .upk-readmore-icon span:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'readmore_background',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-readmore',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'readmore_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-alex-grid .upk-readmore'
			]
		);

		$this->add_responsive_control(
			'readmore_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_primary_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .upk-alex-grid .upk-button-wrap'        => 'margin-bottom: calc(-{{TOP}}px * 2);',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_secondary_padding',
			[
				'label'      => __('Secondary Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3) .upk-readmore, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4) .upk-readmore, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5) .upk-readmore, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1) .upk-readmore, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-readmore, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-readmore, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+3) .upk-button-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+4) .upk-button-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-1 .upk-item:nth-child(5n+5) .upk-button-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+1) .upk-button-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-button-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-button-wrap, {{WRAPPER}} .upk-alex-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-button-wrap'                                                  => 'margin-bottom: calc(-{{TOP}}px * 2);',
				],
				'condition'  => [
					'grid_style!' => '2'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-readmore',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_readmore_hover',
			[
				'label' => __('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'readmore_hover_text_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-readmore .upk-readmore-icon:before, {{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-readmore .upk-readmore-icon span:before, {{WRAPPER}} .upk-alex-grid .upk-item:hover .upk-readmore .upk-readmore-icon span:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'readmore_hover_background',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-readmore:hover',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-readmore:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'readmore_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_hover_shadow',
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-readmore:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_author',
			[
				'label'      => esc_html__('Meta', 'ultimate-post-kit') . BDTUPK_UC,
				'tab'        => Controls_Manager::TAB_STYLE,
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
						],
						[
							'name'  => 'show_reading_time',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->start_controls_tabs('tabs_author_date_style');

		$this->start_controls_tab(
			'tab_avatar_normal',
			[
				'label' => __('Avatar', 'ultimate-post-kit'),
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'author_image_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-meta .upk-author-img img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'author_image_spacing',
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
					'{{WRAPPER}} .upk-alex-grid .upk-meta .upk-author-img img' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'author_image_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-meta .upk-author-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'show_author' => 'yes',
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_author_name_normal',
			[
				'label' => __('Author Name', 'ultimate-post-kit'),
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->add_control(
			'author_name_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-meta .upk-author-name a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->add_control(
			'author_name_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-meta .upk-author-name a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_name_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-meta .upk-author-name a',
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_date_normal',
			[
				'label' => __('Date', 'ultimate-post-kit'),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_date',
							'value' => 'yes'
						],
						[
							'name'  => 'show_reading_time',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-meta .upk-date, {{WRAPPER}} .upk-alex-grid .upk-meta .upk-post-time, {{WRAPPER}} .upk-alex-grid .upk-meta .upk-reading-time' => 'color: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_date',
							'value' => 'yes'
						],
						[
							'name'  => 'show_reading_time',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
			[
				'label'     => esc_html__('Space Between', 'ultimate-post-kit') . BDTUPK_NC,
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-meta .upk-date-reading-wrap > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_reading_time' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'date_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-meta .upk-date, {{WRAPPER}} .upk-alex-grid .upk-meta .upk-post-time, {{WRAPPER}} .upk-alex-grid .upk-meta .upk-reading-time',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_date',
							'value' => 'yes'
						],
						[
							'name'  => 'show_reading_time',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_post_format',
			[
				'label' => esc_html__('Post Format', 'ultimate-post-kit') . BDTUPK_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_format' => 'yes'
				]
			]
		);

		$this->add_control(
			'post_format_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-post-format a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_blur_effect_post_format',
			[
				'label'       => esc_html__('Glassmorphism', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'ultimate-post-kit'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'default'     => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'overlay_blur_level_post_format',
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
					'{{WRAPPER}} .upk-alex-grid .upk-post-format a' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect_post_format' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'post_format_background',
				'label' => esc_html__('Background', 'bdthemes-prime-slider'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .upk-alex-grid .upk-post-format a',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(43, 45, 66, 0.3)',
					],
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'post_format_border',
				'selector'    => '{{WRAPPER}} .upk-alex-grid .upk-post-format a',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'post_format_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-post-format a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_format_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-post-format a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_format_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alex-grid .upk-post-format' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_format_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alex-grid .upk-post-format a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

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

	public function render_readmore() {
		if (!$this->get_settings('show_readmore')) {
			return;
		}

?>
		<div class="upk-button-wrap">
			<a href="<?php echo esc_url(get_permalink()); ?>" class="upk-readmore">
				<span class="upk-readmore-icon"><span></span></span>
			</a>
		</div>
	<?php
	}

	public function render_author_date() {
		$settings = $this->get_settings_for_display();

	?>
		<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_time'] or $settings['show_reading_time']) : ?>
			<div class="upk-meta">
				<?php if ($settings['show_author']) : ?>
					<div class="upk-author-img">
						<?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
					</div>
				<?php endif; ?>
				<div>
					<?php if ($settings['show_author']) : ?>
						<div class="upk-author-name">
							<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
								<?php echo get_the_author() ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="upk-flex upk-flex-middle upk-date-reading-wrap">
						<?php if ('yes' === $settings['show_date']) : ?>
							<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
								<?php $this->render_date(); ?>
							</div>
						<?php endif; ?>

						<?php if (_is_upk_pro_activated()) :
							if ('yes' === $settings['show_reading_time']) : ?>
								<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>

				</div>
			</div>
		<?php endif; ?>

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
			<div class="upk-image-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>

				<?php $this->render_author_date(); ?>

				<?php $this->render_post_format(); ?>

				<div class="upk-content-wrap">

					<div class="upk-content">
						<?php $this->render_category(); ?>
						<?php $this->render_title(substr($this->get_name(), 4)); ?>
					</div>

					<?php $this->render_readmore(); ?>

				</div>
			</div>
		</div>
	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ($settings['grid_style'] == '2') {
			$this->query_posts($settings['item_limit_2']['size']);
		} else {
			$this->query_posts($settings['item_limit']['size']);
		}

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->add_render_attribute('grid-wrap', 'class', 'upk-alex-wrap');
		$this->add_render_attribute('grid-wrap', 'class', 'upk-content-' . $settings['content_position']);
		$this->add_render_attribute('grid-wrap', 'class', 'upk-style-' . $settings['grid_style']);


		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>

		<div class="upk-alex-grid">
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
