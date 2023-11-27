<?php

namespace UltimatePostKit\Modules\CarbonSlider\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Plugin;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Carbon_Slider extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-carbon-slider';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Carbon Slider', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-carbon-slider';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'slider', 'carbon', 'horizontal', 'timeline'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-carbon-slider'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-carbon-slider'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/1NNnJRZxxpc';
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
			'content_max_width',
			[
				'label' => esc_html__('Content Max Width', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw', '%'],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1080,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-content, {{WRAPPER}} .upk-carbon-slider-wrap .upk-image-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_text_alignment',
			[
				'label'   => __('Alignment', 'ultimate-post-kit'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-content, {{WRAPPER}} .upk-carbon-slider-wrap .upk-image-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'     => esc_html__('Show Image', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'primary_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'full',
				'condition' => [
					'show_image' => 'yes'
				]
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
			'show_excerpt',
			[
				'label'   => esc_html__('Show Text', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
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
			'show_category',
			[
				'label'   => esc_html__('Show Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'     => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
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

		$this->add_control(
			'show_comments',
			[
				'label' => esc_html__('Show Comments', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		//Global Reading Time Controls
		$this->register_reading_time_controls();

		//Global Date Controls
		$this->register_date_controls();

		$this->add_control(
			'show_navigation',
			[
				'label' => esc_html__('Show Navigation', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
			'upk_section_style_image',
			[
				'label' => esc_html__('Image', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label' => esc_html__('Height', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					]
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-image-wrap .upk-img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => esc_html__('Width', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 800,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-image-wrap .upk-img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'image_border',
				'selector'    => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-image-wrap .upk-img',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-image-wrap .upk-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-image-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_style',
			[
				'label'   => esc_html__('Style', 'ultimate-post-kit'),
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

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit') . BDTUPK_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-title a',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'      => 'title_text_stroke',
				'label'     => __('Text Stroke', 'ultimate-post-kit') . BDTUPK_NC,
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-title a',
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
							'name'  => 'show_comments',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-meta,
					 {{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-meta .upk-author a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-meta .upk-author a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-meta',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category_comments',
			[
				'label'     => esc_html__('Category', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_category' => 'yes'
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
			'category_comments_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category-and-comment' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a:hover',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label'      => esc_html__('Text', 'ultimate-post-kit'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes'
				]
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-main .upk-text',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_thumbs',
			[
				'label'     => esc_html__('Thumbs', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('style_tabs_thumbs_style');

		$this->start_controls_tab(
			'tab_thumbs_date',
			[
				'label' => esc_html__('Date', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'thumbs_date_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-date' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'thumbs_date_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-date',
			]
		);

		$this->add_responsive_control(
			'date_spacing',
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
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-post-time' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbs_line',
			[
				'label' => esc_html__('Line', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'thumbs_line_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs::before' => 'background-image: linear-gradient(to right, transparent, {{VALUE}}, transparent);',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_border_height',
			[
				'label'      => esc_html__('Height', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs::before' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'thumbs_dits_heading',
			[
				'label'     => esc_html__('DOTS', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->start_controls_tabs(
			'style_tabs_thumbs_dots'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'thumbs_dots_background',
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-dot::after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'thumbs_dots_border',
				'selector'    => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-dot::after',
			]
		);

		$this->add_responsive_control(
			'thumbs_dots_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-dot::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_dots_size',
			[
				'label'      => esc_html__('Dots Size', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-dot::after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'thumbs_dots_hover_background',
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-dot:hover::after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'thumbs_dots_hover_border',
				'selector'    => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-dot:hover::after',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_active_tab',
			[
				'label' => esc_html__('Active', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'thumbs_dots_active_background',
				'selector'  => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-item.swiper-slide-active .upk-dot::after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'thumbs_dots_active__border',
				'selector'    => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-carbon-thumbs .upk-item.swiper-slide-active .upk-dot::after',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'thumbs_arrows_heading',
			[
				'label'     => esc_html__('ARROWS', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'     => esc_html__('Arrows Icon', 'ultimate-post-kit') . BDTUPK_NC,
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
					'show_navigation' => 'yes'
				]
			]
		);

		$this->start_controls_tabs('tabs_navigation_arrows_style');

		$this->start_controls_tab(
			'tabs_nav_arrows_normal',
			[
				'label'     => __('Normal', 'ultimate-post-kit'),
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_control(
			'arrows_icon_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'arrows_background',
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button',
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'arrows_border',
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button',
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'arrows_icon_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'navigation_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_arrows_hover',
			[
				'label'     => __('Hover', 'ultimate-post-kit'),
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'arrows_hover_background',
				'selector' => '{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button:hover',
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'arrows_border_border!' => '',
					'show_navigation' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .upk-carbon-slider-wrap .upk-navigation-button:hover' => 'border-color: {{VALUE}};',
				],
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
		<div class="upk-author">
			<i class="upk-icon-user" aria-hidden="true"></i>
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
			<?php echo esc_html_x('Comments', 'Frontend', 'ultimate-post-kit') ?>
		</div>

	<?php
	}

	public function render_header() {
		$id              = 'upk-carbon-slider-' . $this->get_id();
		$settings        = $this->get_settings_for_display();

		$this->add_render_attribute('carbon-slider', 'id', $id);
		$this->add_render_attribute('carbon-slider', 'class', ['upk-carbon-main']);

		$this->add_render_attribute(
			[
				'carbon-slider' => [
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
								"nextEl" => "#$id-wrapper .upk-navigation-next",
								"prevEl" => "#$id-wrapper .upk-navigation-prev",
							],
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);

	?>
		<div <?php $this->print_render_attribute_string('carbon-slider'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div class="swiper-wrapper">
				<?php
			}

			public function render_footer() {
				$settings = $this->get_settings_for_display();

				?>

				</div>
			</div>
		</div>
	<?php
			}

			public function render_post_grid_item($post_id, $image_size, $excerpt_length) {
				$settings = $this->get_settings_for_display();

				$this->add_render_attribute('slider-item', 'class', 'upk-item swiper-slide', true);

	?>
		<div <?php $this->print_render_attribute_string('slider-item'); ?>>
			<?php if ($settings['show_image']) : ?>
				<div class="upk-image-wrap">
					<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
				</div>
			<?php endif; ?>
			<div class="upk-content">
				<?php if ($settings['show_category']) : ?>
					<div class="upk-category-and-comment" data-swiper-parallax-x="-300" data-swiper-parallax-duration="600">
						<?php $this->render_category(); ?>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_title']) : ?>
					<div data-swiper-parallax-x="-300" data-swiper-parallax-duration="800">
						<?php $this->render_title(substr($this->get_name(), 4)); ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_author'] or $settings['show_comments'] or $settings['show_reading_time']) : ?>
					<div class="upk-meta" data-swiper-parallax-x="-300" data-swiper-parallax-duration="1000">
						<?php $this->render_author(); ?>
						<?php if (_is_upk_pro_activated()) :
							if ('yes' === $settings['show_reading_time']) : ?>
								<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php echo ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ($settings['show_comments']) : ?>
							<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
							<?php $this->render_comments($post_id); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_excerpt']) : ?>
					<div data-swiper-parallax-x="-300" data-swiper-parallax-duration="1200">
						<?php $this->render_excerpt($excerpt_length); ?>
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
		<div <?php $this->print_render_attribute_string('thumb-item'); ?>>
			<?php if ($settings['show_date']) : ?>
				<div class="upk-date-wrap upk-flex upk-flex-center">
					<?php $this->render_date(); ?>
				</div>
			<?php endif; ?>
			<div class="upk-dot"></div>
		</div>
	<?php
			}

			public function render() {
				$settings = $this->get_settings_for_display();
				$id_wrapper              = 'upk-carbon-slider-' . $this->get_id() . '-wrapper';

				$this->query_posts($settings['item_limit']['size']);
				$wp_query = $this->get_query();

				if (!$wp_query->found_posts) {
					return;
				}

				$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper-thumbs', 'class', 'upk-carbon-thumbs ' . $swiper_class);

	?>
		<div class="upk-carbon-slider-wrap" id="<?php echo esc_html($id_wrapper); ?>">
			<?php

				$this->render_header();

				while ($wp_query->have_posts()) {
					$wp_query->the_post();
					$thumbnail_size = $settings['primary_thumbnail_size'];

					$this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']);
				}

				$this->render_footer();

			?>
			<div thumbsSlider="" <?php $this->print_render_attribute_string('swiper-thumbs'); ?>>
				<div class="swiper-wrapper">
					<?php

					while ($wp_query->have_posts()) {
						$wp_query->the_post();
						$thumbnail_size = $settings['primary_thumbnail_size'];

						$this->render_thumbnav(get_the_ID(), $thumbnail_size);
					}

					?>
				</div>
				<?php if ($settings['show_navigation']) : ?>
					<div class="upk-navigation-wrap">
						<div class="upk-navigation-button upk-navigation-prev">
							<i class="upk-icon-arrow-left-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
						<div class="upk-navigation-button upk-navigation-next">
							<i class="upk-icon-arrow-right-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>
<?php
				wp_reset_postdata();
			}
		}
