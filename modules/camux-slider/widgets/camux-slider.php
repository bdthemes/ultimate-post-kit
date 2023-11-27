<?php

namespace UltimatePostKit\Modules\CamuxSlider\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

use UltimatePostKit\Utils;
use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Camux_Slider extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-camux-slider';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Camux Slider', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-camux-slider';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'slider', 'camux'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-camux-slider'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-camux-slider'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/3IFFXN69IgQ';
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
				'size_units' => ['px', '%', 'vh'],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1080,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-camux-slider .upk-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => esc_html__('Content Max Width', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'vw'],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-camux-slider .upk-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'   => __('Alignment', 'ultimate-post-kit'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => __('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-camux-slider .upk-content' => 'text-align: {{VALUE}};',
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
			'show_excerpt',
			[
				'label'   => esc_html__('Show Text', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'       => esc_html__('Text Limit', 'ultimate-post-kit'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'ultimate-post-kit'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'condition'   => [
					'show_excerpt' => 'yes'
				],
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
			'show_comments',
			[
				'label' => esc_html__('Show Comments', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_navigation',
			[
				'label' => esc_html__('Show Navigation', 'ultimate-post-kit') . BDTUPK_NC,
				'type'  => Controls_Manager::SWITCHER,
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
					'size' => 12,
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

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_items',
			[
				'label'     => esc_html__('Item', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_type',
			[
				'label'   => esc_html__('Overlay', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'background',
				'options' => [
					'none'       => esc_html__('None', 'ultimate-post-kit'),
					'background' => esc_html__('Background', 'ultimate-post-kit'),
					'blend'      => esc_html__('Blend', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_color',
				'label' => esc_html__('Background', 'ultimate-post-kit'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .upk-camux-slider .upk-img-wrap::before',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(3, 4, 16, 0.4)',
					],
				],
				'condition' => [
					'overlay_type' => ['background', 'blend'],
				],
			]
		);

		$this->add_control(
			'blend_type',
			[
				'label'     => esc_html__('Blend Type', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multiply',
				'options'   => ultimate_post_kit_blend_options(),
				'condition' => [
					'overlay_type' => 'blend',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-camux-slider .upk-img-wrap::before' => 'mix-blend-mode: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' 	 => __('Content Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-slider .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
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
				'type'    => Controls_Manager::HIDDEN,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-camux-slider .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-camux-slider .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'title_background',
				'selector'  => '{{WRAPPER}} .upk-camux-slider .upk-title a',
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-slider .upk-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-slider .upk-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-camux-slider .upk-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-camux-slider .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-camux-slider .upk-title a',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'      => 'title_text_stroke',
				'label'     => __('Text Stroke', 'ultimate-post-kit') . BDTUPK_NC,
				'selector'  => '{{WRAPPER}} .upk-camux-slider .upk-title a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => esc_html__('Text', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-camux-slider .upk-text' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'text_margin',
			[
				'label' 	 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-slider .upk-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_width',
			[
				'label' => esc_html__('Max Width', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 800,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-camux-slider .upk-text' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'text_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-camux-slider .upk-text',
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
					'{{WRAPPER}} .upk-camux-slider .upk-meta, {{WRAPPER}} .upk-camux-slider .upk-meta .upk-author-wrap .upk-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-camux-slider .upk-meta .upk-author-wrap .upk-name:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-camux-slider .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-camux-slider .upk-meta',
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
					'{{WRAPPER}} .upk-camux-slider .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-camux-slider .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-camux-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-camux-slider .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-slider .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-camux-slider .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-camux-slider .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-camux-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-camux-slider .upk-category a',
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
					'{{WRAPPER}} .upk-camux-slider .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-camux-slider .upk-category a:hover',
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
					'{{WRAPPER}} .upk-camux-slider .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbs',
			[
				'label'     => esc_html__('Thumbs', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		//thumbs width
		$this->add_responsive_control(
			'thumbs_width',
			[
				'label'      => esc_html__('Width', 'ultimate-post-kit') . BDTUPK_NC,
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-thumbs' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		//thumbs vertical spacing
		$this->add_responsive_control(
			'thumbs_vertical_spacing',
			[
				'label'      => esc_html__('Vertical Spacing', 'ultimate-post-kit') . BDTUPK_NC,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-slider' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_thumbs_style');

		$this->start_controls_tab(
			'tab_thumbs_comments',
			[
				'label' => esc_html__('Comments', 'ultimate-post-kit'),
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_control(
			'comments_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-comments' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'comments_background',
				'selector'  => '{{WRAPPER}} .upk-camux-thumbs .upk-comments',
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'thumbs_comments_border',
				'selector'    => '{{WRAPPER}} .upk-camux-thumbs .upk-comments',
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_comments_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-comments' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'comments_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-comments' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'comments_margin',
			[
				'label' 	 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-comments' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'comments_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-camux-thumbs .upk-comments',
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbs_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'thumbs_opacity',
			[
				'label'      => esc_html__('Opacity', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step'  => 0.1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-item' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'thumbs_image_border',
				'selector'    => '{{WRAPPER}} .upk-camux-thumbs .upk-item',
			]
		);

		$this->add_responsive_control(
			'thumbs_image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbs_active_item',
			[
				'label' => esc_html__('Active', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'thumbs_active_opacity',
			[
				'label'      => esc_html__('Opacity', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step'  => 0.1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-item.swiper-slide-active' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'item_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'thumbs_image_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-camux-thumbs .upk-item.swiper-slide-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comments_heading',
			[
				'label'     => esc_html__('Comments', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_comments' => 'yes',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'comments_active_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-camux-thumbs .swiper-slide-active .upk-comments' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'comments_active_background',
				'selector'  => '{{WRAPPER}} .upk-camux-thumbs .swiper-slide-active .upk-comments',
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$this->add_control(
			'comments_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'thumbs_comments_border_border!' => '',
					'show_comments' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-camux-thumbs .swiper-slide-active .upk-comments' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __('Navigation', 'ultimate-post-kit') . BDTUPK_NC,
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition'  => [
					'show_navigation' => 'yes',
				],
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
					'{{WRAPPER}} .upk-navigation-prev i, {{WRAPPER}} .upk-navigation-next i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'arrows_background',
				'selector'  => '{{WRAPPER}} .upk-navigation-prev, {{WRAPPER}} .upk-navigation-next',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'nav_arrows_border',
				'selector'  => '{{WRAPPER}} .upk-navigation-prev, {{WRAPPER}} .upk-navigation-next',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-navigation-prev, {{WRAPPER}} .upk-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-navigation-prev, {{WRAPPER}} .upk-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_spacing',
			[
				'label'     => __('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-navigation-prev' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .upk-navigation-next' => 'margin-right: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'     => __('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-navigation-prev i, {{WRAPPER}} .upk-navigation-next i' => 'font-size: {{SIZE || 24}}{{UNIT}};',
				],
			]
		);
		// box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_box_shadow',
				'selector' => '{{WRAPPER}} .upk-navigation-prev, {{WRAPPER}} .upk-navigation-next',
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
					'{{WRAPPER}} .upk-navigation-prev:hover i, {{WRAPPER}} .upk-navigation-next:hover i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'arrows_hover_background',
				'selector'  => '{{WRAPPER}} .upk-navigation-prev:hover, {{WRAPPER}} .upk-navigation-next:hover',
			]
		);

		$this->add_control(
			'nav_arrows_hover_border_color',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-navigation-prev:hover, {{WRAPPER}} .upk-navigation-next:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'nav_arrows_border_border!' => '',
				],
			]
		);
		// box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_hover_box_shadow',
				'selector' => '{{WRAPPER}} .upk-navigation-prev:hover, {{WRAPPER}} .upk-navigation-next:hover',
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

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}
?>
		<div class="upk-author-wrap">
			<span class="upk-by"><?php echo esc_html_x('by', 'Frontend', 'ultimate-post-kit') ?></span>
			<a class="upk-name" href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
				<?php echo get_the_author() ?>
			</a>
		</div>
	<?php
	}

	public function render_comments($id = 0) {

		if (!$this->get_settings('show_comments')) {
			return;
		}
	?>

		<div class="upk-comments">
			<?php echo get_comments_number($id) ?>
			<i class="upk-icon-bubble"></i>
		</div>

	<?php
	}

	public function render_header() {
		$id              = 'upk-camux-slider-' . $this->get_id();
		$settings        = $this->get_settings_for_display();

		$this->add_render_attribute('camux-slider', 'id', $id);
		$this->add_render_attribute('camux-slider', 'class', ['upk-camux-slider']);

		$this->add_render_attribute(
			[
				'camux-slider' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
							"loop"           => ($settings["loop"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"effect"         => 'fade',
							"fadeEffect"     => ['crossFade' => true],
							"lazy"           => true,
							"parallax"       => true,
							"grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"  => 1,
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"loopedSlides" => 4,
							"lazy" => [
								"loadPrevNext"  => "true",
							],
							"navigation" => [
								"nextEl" => "#$id .upk-navigation-next",
								"prevEl" => "#$id .upk-navigation-prev",
							],
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);

	?>
		<div <?php echo $this->get_render_attribute_string('camux-slider'); ?>>
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
						<i class="upk-icon-arrow-left-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</div>
					<div class="upk-navigation-next">
						<i class="upk-icon-arrow-right-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>

	<?php
			}

			public function render_post_grid_item($post_id, $image_size, $excerpt_length) {
				$settings = $this->get_settings_for_display();

				$this->add_render_attribute('slider-item', 'class', 'upk-item swiper-slide', true);

	?>
		<div <?php echo $this->get_render_attribute_string('slider-item'); ?>>
			<div class="upk-img-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
			</div>

			<div class="upk-content">

				<?php if ($settings['show_category']) : ?>
					<div data-swiper-parallax-y="-150" data-swiper-parallax-duration="700">
						<?php $this->render_category(); ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_title']) : ?>
					<div data-swiper-parallax-y="-100" data-swiper-parallax-duration="800">
						<?php $this->render_title(substr($this->get_name(), 4)); ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_excerpt']) : ?>
					<div class="upk-inline-block" data-swiper-parallax-y="-80" data-swiper-parallax-duration="900">
						<?php $this->render_excerpt($excerpt_length); ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
					<div class="upk-meta" data-swiper-parallax-y="-50" data-swiper-parallax-duration="1000">
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

			</div>
		</div>
	<?php
			}

			public function render_thumbnav($post_id, $image_size) {
				$settings        = $this->get_settings_for_display();

				$this->add_render_attribute('thumb-item', 'class', 'upk-item swiper-slide', true);

	?>
		<div <?php echo $this->get_render_attribute_string('thumb-item'); ?>>
			<div class="upk-img-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
			</div>
			<?php $this->render_comments($post_id); ?>
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

				$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper-thumbs', 'class', 'upk-camux-thumbs ' . $swiper_class);

	?>
		<div class="upk-camux-slider-wrap">
			<?php

				$this->render_header();

				while ($wp_query->have_posts()) {
					$wp_query->the_post();
					$thumbnail_size = $settings['primary_thumbnail_size'];

					$this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']);
				}

				$this->render_footer();

			?>
			<div thumbsSlider="" <?php echo $this->get_render_attribute_string('swiper-thumbs'); ?>>
				<div class="swiper-wrapper">
					<?php

					while ($wp_query->have_posts()) {
						$wp_query->the_post();
						$thumbnail_size = $settings['primary_thumbnail_size'];

						$this->render_thumbnav(get_the_ID(), $thumbnail_size);
					}

					?>
				</div>
			</div>
		</div>

<?php
				wp_reset_postdata();
			}
		}
