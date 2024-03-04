<?php

namespace UltimatePostKit\Modules\CrystalSlider\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

use UltimatePostKit\Utils;
use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Crystal_Slider extends Group_Control_Query {

	use Global_Widget_Controls;

	private $_query = null;

	public function get_name() {
		return 'upk-crystal-slider';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Crystal Slider', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-crystal-slider';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'slider', 'crystal'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-crystal-slider'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-crystal-slider'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/wZNw_prt-uI';
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

		$this->add_responsive_control(
			'item_height',
			[
				'label' => esc_html__('Height', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1080,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-crystal-slider .swiper-carousel' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slide_spacing',
			[
				'label'      => esc_html__('Slide Bottom Spacing', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .swiper-carousel' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'     => esc_html__('Content Alignment', 'ultimate-post-kit'),
				'type'      => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options'   => [
					'left'   => [
						'title' => esc_html__('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'primary_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'full',
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		//Global Title Controls
		$this->register_title_controls();

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Show Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'strip_shortcode',
			[
				'label'     => esc_html__('Strip Shortcode', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'     => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'label'       => __('Separator', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '//',
				'label_block' => false,
			]
		);

		//Global Date Controls
		$this->register_date_controls();

		//Global Reading Time Controls
		$this->register_reading_time_controls();

		$this->add_control(
			'show_readmore',
			[
				'label' => esc_html__('Read more', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'no',
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
				'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'default' => [
					'size' => 3,
				],
			]
		);

		$this->register_query_builder_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => __('Slider Settings', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => __('Autoplay', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', 'ultimate-post-kit'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[
				'label' => esc_html__('Pause on Hover', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'grab_cursor',
			[
				'label'   => __('Grab Cursor', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => __('Loop', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);


		$this->add_control(
			'speed',
			[
				'label'   => __('Animation Speed (ms)', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'observer',
			[
				'label'       => __('Observer', 'ultimate-post-kit'),
				'description' => __('When you use carousel in any hidden place (in tabs, accordion etc) keep it yes.', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_navigation',
			[
				'label' => esc_html__('Show Navigation', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'     => esc_html__('Arrows Icon', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => '0',
				'options'   => [
					'0'        => esc_html__('Default', 'bdthemes-element-pack'),
					'1'        => esc_html__('Style 1', 'ultimate-post-kit'),
					'2'        => esc_html__('Style 2', 'ultimate-post-kit'),
					'3'        => esc_html__('Style 3', 'ultimate-post-kit'),
					'4'        => esc_html__('Style 4', 'ultimate-post-kit'),
					'5'        => esc_html__('Style 5', 'ultimate-post-kit'),
					'6'        => esc_html__('Style 6', 'ultimate-post-kit'),
					'7'        => esc_html__('Style 7', 'ultimate-post-kit'),
					'8'        => esc_html__('Style 8', 'ultimate-post-kit'),
					'9'        => esc_html__('Style 9', 'ultimate-post-kit'),
					'10'       => esc_html__('Style 10', 'ultimate-post-kit'),
					'11'       => esc_html__('Style 11', 'ultimate-post-kit'),
					'12'       => esc_html__('Style 12', 'ultimate-post-kit'),
					'13'       => esc_html__('Style 13', 'ultimate-post-kit'),
					'14'       => esc_html__('Style 14', 'ultimate-post-kit'),
					'15'       => esc_html__('Style 15', 'ultimate-post-kit'),
					'16'       => esc_html__('Style 16', 'ultimate-post-kit'),
					'17'       => esc_html__('Style 17', 'ultimate-post-kit'),
					'18'       => esc_html__('Style 18', 'ultimate-post-kit'),
					'circle-1' => esc_html__('Style 19', 'ultimate-post-kit'),
					'circle-2' => esc_html__('Style 20', 'ultimate-post-kit'),
					'circle-3' => esc_html__('Style 21', 'ultimate-post-kit'),
					'circle-4' => esc_html__('Style 22', 'ultimate-post-kit'),
					'square-1' => esc_html__('Style 23', 'ultimate-post-kit'),
				],
				'condition' => [
					'show_navigation' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Show Pagination', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'Content_background',
				'selector'  => '{{WRAPPER}} .upk-crystal-slider .upk-content',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'content_border',
				'selector'    => '{{WRAPPER}} .upk-crystal-slider .upk-content',
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label' => esc_html__('Max Width', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_vertical_offset',
			[
				'label'      => esc_html__('Vertical Offset', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min'  => -500,
						'max'  => 500,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'%' => [
					'min' => -0,
					'max' => 100,
				],
				'default' => [
					'unit' => '%',
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-content' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-content',
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
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-title a:hover' => 'color: {{VALUE}};',
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
						'max'  => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-crystal-slider .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-title a',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'      => 'title_text_stroke',
				'label'     => __('Text Stroke', 'ultimate-post-kit') . BDTUPK_NC,
				'selector'  => '{{WRAPPER}} .upk-crystal-slider .upk-title a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'      => esc_html__('Meta', 'ultimate-post-kit'),
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
							'name'  => 'show_comments',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-meta,
					{{WRAPPER}} .upk-crystal-slider .upk-meta .upk-author a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-meta .upk-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_space_between',
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
					'{{WRAPPER}} .upk-crystal-slider .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-meta',
			]
		);

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
				'label'   => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-crystal-slider .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-crystal-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-crystal-slider .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-crystal-slider .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-category a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_category_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
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
					'{{WRAPPER}} .upk-crystal-slider .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-crystal-slider .upk-category a:hover',
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
					'{{WRAPPER}} .upk-crystal-slider .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Read More Css
		$this->start_controls_section(
			'section_style_btn',
			[
				'label'     => __('Read More', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_readmore' => 'yes'
				]
			]
		);

		$this->start_controls_tabs('tabs_read_more_style');

		$this->start_controls_tab(
			'tabs_read_more_normal',
			[
				'label'     => __('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'button_icon_color',
			[
				'label'     => __('Icon Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-link-btn a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-link-btn a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-link-btn a',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-link-btn a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'read_more_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-link-btn a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_size',
			[
				'label'     => esc_html__('Icon Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-link-btn a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'button_vertical_offset',
			[
				'label'      => esc_html__('Vertical Offset', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'%' => [
					'min' => -0,
					'max' => 100,
				],
				'default' => [
					'unit' => '%',
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-link-btn' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_horizontal_offset',
			[
				'label'      => esc_html__('Horizontal Offset', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'%' => [
					'min' => -0,
					'max' => 100,
				],
				'default' => [
					'unit' => '%',
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-link-btn' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-link-btn a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_button_hover',
			[
				'label'     => __('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-content:hover .upk-link-btn a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_hover_background',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-content:hover .upk-link-btn a',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'arrows_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-content:hover .upk-link-btn a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}}.upk-crystal-slider .upk-content:hover .upk-link-btn a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Css
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => __('Navigation', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_navigation_arrows_style');

		$this->start_controls_tab(
			'tabs_nav_arrows_normal',
			[
				'label'     => __('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'arrows_background',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'arrows_border',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev',
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'navigation_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}  .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider
						.upk-navigation-wrap .upk-navigation-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_shadow',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev',
			]
		);

		$this->add_responsive_control(
			'vertical_offset',
			[
				'label' => esc_html__('Vertical Offset', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev' => 'top: {{SIZE}}%;',
				],
			]
		);

		$this->add_responsive_control(
			'horizontal_offset_next',
			[
				'label' => esc_html__('Horizontal Offset', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_arrows_hover',
			[
				'label'     => __('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next:hover, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'arrows_hover_background',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next:hover, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev:hover',
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'arrows_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next:hover, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_hover_shadow',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-next:hover, {{WRAPPER}} .upk-crystal-slider .upk-navigation-wrap .upk-navigation-prev:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Pagination Css
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => __('Pagination', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'dots_nny_position',
			[
				'label'          => __('Dots Vertical Offset', 'ultimate-post-kit'),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap' => 'bottom: {{SIZE}}px;'
				],
			]
		);

		$this->start_controls_tabs('tabs_navigation_dots_style');

		$this->start_controls_tab(
			'tabs_nav_dots_normal',
			[
				'label'     => __('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'dots_space_between',
			[
				'label'     => __('Space Between', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .upk-pagination ' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_size',
			[
				'label'     => __('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => ''
				],
			]
		);

		$this->add_control(
			'advanced_dots_size',
			[
				'label'     => __('Advanced Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'advanced_dots_width',
			[
				'label'     => __('Width(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_height',
			[
				'label'     => __('Height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dots_box_shadow',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_dots_active',
			[
				'label'     => __('Active', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'active_dots_size',
			[
				'label'     => __('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--ps-swiper-dots-active-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => ''
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_width',
			[
				'label'     => __('Width(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_height',
			[
				'label'     => __('Height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--ps-swiper-dots-active-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_align',
			[
				'label'   => __('Alignment', 'ultimate-post-kit'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __('Top', 'ultimate-post-kit'),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __('Bottom', 'ultimate-post-kit'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ps-swiper-dots-align: {{VALUE}};',
				],
				'condition' => [
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dots_active_box_shadow',
				'selector' => '{{WRAPPER}} .upk-crystal-slider .upk-pagination-wrap .swiper-pagination-bullet-active',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
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

	public function render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		$image_src = wp_get_attachment_image_src($image_id, $size);

		if (!$image_src) {
			$image_src = $placeholder_image_src;
		} else {
			$image_src = $image_src[0];
		}

?>

		<img class="upk-img swiper-lazy" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">

	<?php
	}

	public function render_title() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_title')) {
			return;
		}

		printf('<%1$s class="upk-title"><a href="%2$s" title="%3$s">%3$s</a></%1$s>', esc_attr(Utils::get_valid_html_tag($settings['title_tags'])), get_permalink(), get_the_title());
	}

	public function render_category() {

		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-category" data-swiper-parallax-x="-150" data-swiper-parallax-duration="1000">
			<?php echo upk_get_category($this->get_settings('posts_source')); ?>
		</div>
	<?php
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();


		if (!$this->get_settings('show_date')) {
			return;
		}

	?>
		<div class="upk-date-and-time upk-flex upk-flex-middle">
			<div class="upk-date">
				<i class="upk-icon-calendar" aria-hidden="true"></i>
				<span><?php if ($settings['human_diff_time'] == 'yes') {
							echo ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
						} else {
							echo get_the_date();
						} ?>
				</span>

			</div>
			<?php if ($settings['show_time']) : ?>
				<div class="upk-post-time">
					<i class="upk-icon-clock" aria-hidden="true"></i>
					<?php echo get_the_time(); ?>
				</div>
			<?php endif; ?>
		</div>

	<?php
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}
	?>
		<div class="upk-author">
			<i class="upk-icon-user" aria-hidden="true"></i>
			<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
				<?php echo get_the_author() ?>
			</a>
		</div>
	<?php
	}

	public function render_header() {
		$id              = 'upk-crystal-slider-' . $this->get_id();
		$settings        = $this->get_settings_for_display();

		$this->add_render_attribute('crystal-slider', 'id', $id);
		$this->add_render_attribute('crystal-slider', 'class', ['upk-crystal-slider']);

		$this->add_render_attribute(
			[
				'crystal-slider' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
							"loop"           => ($settings["loop"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"effect"         => 'slide',
							"lazy"           => true,
							"parallax"       => true,
							"grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"  => 1,
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"navigation" => [
								"nextEl" => "#" . $id . " .upk-navigation-next",
								"prevEl" => "#" . $id . " .upk-navigation-prev",
							],
							"pagination" => [
								"el"             => "#" . $id . " .upk-pagination",
								"clickable"      => "true",
							],
							"lazy" => [
								"loadPrevNext"  => "true",
							],
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);

	?>
		<div <?php $this->print_render_attribute_string('crystal-slider'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div class="swiper-wrapper">
				<?php
			}

			public function render_footer() {
				$settings = $this->get_settings_for_display();

				?>
				</div>
				<?php if ($settings['show_navigation']) : ?>
					<div class="upk-navigation-wrap">
						<div class="upk-navigation-prev">
							<i class="upk-icon-arrow-right-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
						<div class="upk-navigation-next">
							<i class="upk-icon-arrow-left-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_pagination']) : ?>
					<div class="upk-pagination-wrap">
						<div class="upk-pagination"></div>
					</div>
				<?php endif; ?>
			</div>
		</div>

	<?php
			}

			public function render_post_grid_item($post_id, $image_size) {
				$settings = $this->get_settings_for_display();

				$this->add_render_attribute('slider-item', 'class', 'upk-item swiper-slide', true);

	?>

		<div <?php $this->print_render_attribute_string('slider-item'); ?>>
			<div class="upk-img-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
			</div>
			<div class="upk-content">
				<div class="bdt-inner-content" data-swiper-parallax-x="-250" data-swiper-parallax-duration="1000">
					<?php $this->render_category(); ?>

					<div data-swiper-parallax-x="-250" data-swiper-parallax-duration="1200">
						<?php $this->render_title(substr($this->get_name(), 4)); ?>
					</div>

					<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
						<div class="upk-meta upk-flex upk-flex-middle" data-swiper-parallax-x="-300" data-swiper-parallax-duration="1400">
							<?php if ($settings['show_author']) : ?>
								<?php $this->render_author(); ?>
							<?php endif; ?>
							<?php if ($settings['show_date']) : ?>
							<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
								<?php $this->render_date(); ?>
							</div>
							<?php endif; ?>
							<?php if (_is_upk_pro_activated()) :
							if ('yes' === $settings['show_reading_time']) : ?>
								<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php echo ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ($settings['show_readmore'] === 'yes') : ?>
						<div class="upk-link-btn">
							<a href="<?php echo esc_url(get_permalink()); ?>">
								<i class="eicon-plus"></i>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
<?php
			}

			public function render() {
				$settings = $this->get_settings_for_display();

				$this->query_posts($settings['item_limit']['size']);
				$wp_query = $this->get_query();

				if (!$wp_query->found_posts) {
					return;
				}

				$this->render_header();

				while ($wp_query->have_posts()) {
					$wp_query->the_post();
					$thumbnail_size = $settings['primary_thumbnail_size'];

					$this->render_post_grid_item(get_the_ID(), $thumbnail_size);
				}

				$this->render_footer();

				wp_reset_postdata();
			}
		}
