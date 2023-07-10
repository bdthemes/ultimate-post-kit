<?php

namespace UltimatePostKit\Modules\SnogSlider\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

use UltimatePostKit\Utils;
use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use UltimatePostKit\Modules\QueryControl\Controls\Group_Control_Posts;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Snog_Slider extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;
	
	private $_query = null;

	public function get_name() {
		return 'upk-snog-slider';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Snog Slider', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-snog-slider';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'slider', 'snog'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-snog-slider'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-snog-slider'];
		}
	}

	// public function get_custom_help_url() {
	// 	return 'https://youtu.be/2ZYnLz__uA4';
	// }


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
				'size_units' => [ 'px', '%', 'vh' ],
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-main-slider, {{WRAPPER}} .upk-snog-slider-wrap .upk-snog-thumbs' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => esc_html__('Content Width(%)', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors'   => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-content-wrap' => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-content-wrap' => 'min-width: {{SIZE}}%;',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'   => __( 'Alignment', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => __( 'Left', 'ultimate-post-kit' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'ultimate-post-kit' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'ultimate-post-kit' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-snog-slider .upk-content-wrap' => 'text-align: {{VALUE}};',
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
				'label'   => esc_html__( 'Show Text', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				// 'default' => 'yes',
			]
		);
		
		$this->add_control(
			'excerpt_length',
			[
				'label'       => esc_html__( 'Text Limit', 'ultimate-post-kit' ),
				'description' => esc_html__( 'It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 15,
				'condition'   => [
					'show_excerpt' => 'yes'
				],
			]
		);
		
		$this->add_control(
			'strip_shortcode',
			[
				'label'     => esc_html__( 'Strip Shortcode', 'ultimate-post-kit' ),
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
				'label'     => esc_html__( 'Show Author', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);
		
		$this->add_control(
			'meta_separator',
			[
				'label'       => __( 'Separator', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '//',
				'label_block' => false,
			]
		);

		$this->add_control(
			'show_readmore',
			[
				'label' => esc_html__('Show Read more', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => __( 'Readmore Text', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Explore', 'ultimate-post-kit'),
				'label_block' => false,
				'condition' => [
					'show_readmore' => 'yes'
				]
			]
		);

		//Global Date Controls
		$this->register_date_controls();

		//Global Reading Time Controls
		$this->register_reading_time_controls();

		$this->add_control(
			'show_navigation',
			[
				'label' => esc_html__('Show Navigation Arrows', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Show Pagination', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		

		$this->end_controls_section();

		// Query Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __( 'Query', 'ultimate-post-kit' ),
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
					'size' => 5,
				],
			]
		);
		
		$this->register_query_builder_controls();
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => __( 'Slider Settings', 'ultimate-post-kit' ),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => __( 'Autoplay', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed', 'ultimate-post-kit' ),
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
				'label' => esc_html__( 'Pause on Hover', 'ultimate-post-kit' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'grab_cursor',
			[
				'label'   => __( 'Grab Cursor', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => __( 'Loop', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				
			]
		);


		$this->add_control(
			'speed',
			[
				'label'   => __( 'Animation Speed (ms)', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
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
				'label'       => __( 'Observer', 'ultimate-post-kit' ),
				'description' => __( 'When you use carousel in any hidden place (in tabs, accordion etc) keep it yes.', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::SWITCHER,				
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_content',
			[
				'label'     => esc_html__('Items', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_type',
			[
				'label'   => esc_html__('Overlay', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'background',
				'options' => [
					'none'       => esc_html__('None', 'ultimate-post-kit-pro'),
					'background' => esc_html__('Background', 'ultimate-post-kit-pro'),
					'blend'      => esc_html__('Blend', 'ultimate-post-kit-pro'),
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-main-slider .upk-image-wrap::before' => 'background-image: linear-gradient(54deg, {{VALUE}}, transparent);',
				],
				'condition' => [
					'overlay_type' => ['background', 'blend'],
				],
			]
		);

		$this->add_control(
			'blend_type',
			[
				'label'     => esc_html__('Blend Type', 'ultimate-post-kit-pro'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multiply',
				'options'   => ultimate_post_kit_blend_options(),
				'condition' => [
					'overlay_type' => 'blend',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-main-slider .upk-image-wrap::before' => 'mix-blend-mode: {{VALUE}};'
				],
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_background',
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-content-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'content_border',
				'selector'    => '{{WRAPPER}} .upk-snog-slider-wrap .upk-content-wrap',
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-inner-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'default' => 'default',
				'options' => [
					'default'        => esc_html__('Default', 'ultimate-post-kit'),
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-title a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}}  .upk-snog-slider-wrap .upk-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' 	 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}  .upk-snog-slider-wrap .upk-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-title a',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'      => esc_html__( 'Meta', 'ultimate-post-kit' ),
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
				'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-meta, {{WRAPPER}} .upk-snog-slider-wrap .upk-meta .upk-author .upk-name' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-meta .upk-author .upk-name:hover' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'meta_spacing',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider .upk-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__( 'Typography', 'ultimate-post-kit' ),
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-meta',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-snog-slider-wrap .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-category a',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-category a:hover',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_link_btn',
			[
				'label'     => esc_html__('Read More', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_link_btn_style');

		$this->start_controls_tab(
			'tab_link_btn_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'link_btn_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'link_btn_background',
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'link_btn_border',
				'selector'    => '{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a',
			]
		);

		$this->add_responsive_control(
			'link_btn_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'link_btn_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'link_btn_shadow',
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_btn_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_btn_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'link_btn_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'link_btn_hover_background',
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a:hover',
			]
		);

		$this->add_control(
			'link_btn_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'link_btn_line_heading',
			[
				'label' => esc_html__( 'LINE', 'ultimate-post-kit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_btn_line_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_btn_line_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-link-btn a:hover::before' => 'background-color: {{VALUE}};',
				],
			]
		);



		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => esc_html__('Navigation', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_navigation' => 'yes'
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn i' => 'color: {{VALUE}}',
				],
				
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'arrows_background',
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'nav_arrows_border',
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn',
				
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

		$this->add_responsive_control(
			'arrows_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn i' => 'font-size: {{SIZE || 18}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn:hover i' => 'color: {{VALUE}}',
				],
				
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'arrows_hover_background',
				'selector' => '{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn:hover',
			]
		);

		$this->add_control(
			'nav_arrows_hover_border_color',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-nav-btn:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'nav_arrows_border_border!' => ''
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		// Pagination Css Control

		$this->start_controls_section (
			'section_style_pagination',
			[
				'label'     => esc_html__('Pagination', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'yes'
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-pagination-wrap .swiper-pagination-bullet' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-pagination-wrap .swiper-pagination-bullet::after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'pagination_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-snog-slider-wrap .upk-pagination-wrap .swiper-pagination-bullet',
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
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
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-pagination-wrap .upk-pagination' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_active_heading',
			[
				'label' => esc_html__( 'A C T I V E', 'ultimate-post-kit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pagination_active_color',
			[
				'label'     => esc_html__('Active Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-pagination-wrap .swiper-pagination-bullet-active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-snog-slider-wrap .upk-pagination-wrap .swiper-pagination-bullet-active::after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Main query render for this widget
	 * @param $posts_per_page number item query limit
	 */
	public function query_posts( $posts_per_page ) {
		
		$default = $this->getGroupControlQueryArgs();
		if ( $posts_per_page ) {
			$args['posts_per_page'] = $posts_per_page;
				$args['paged']  = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}
		$args         = array_merge( $default, $args );
		$this->_query = new WP_Query( $args );
	}

	public function render_author() {
			
		if ( ! $this->get_settings( 'show_author' ) ) {
			return;
		}
		?>
		<div class="upk-author">
			<span class="upk-by"><?php echo esc_html_x( 'by ', 'Frontend', 'ultimate-post-kit' ) ?></span>
			<a class="upk-name" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ) ?>">
				<?php echo get_the_author() ?>
			</a>
		</div>
		<?php
	}
	

	public function render_header() {
		$settings        = $this->get_settings_for_display();
		$id              = 'upk-snog-slider-' . $this->get_id();
		
		$this->add_render_attribute( 'snog-slider', 'class', ['upk-snog-slider'] );

		$this->add_render_attribute(
			[
				'snog-slider' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ( "yes" == $settings["autoplay"] ) ? [ "delay" => $settings["autoplay_speed"] ] : false,
							"loop"           => ($settings["loop"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"effect"         => 'slide',
							"lazy"           => true,
							"grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"  => 1,
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"loopedSlides" => 4,
							// "allowTouchMove" => false,
							"lazy" => [
								"loadPrevNext"  => "true",
							],
				        ]))
						],
						'data-widget-settings' => [
							wp_json_encode(
								[
									'id' => '#' . $id
								]
							)
						]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'upk-main-slider ' . $swiper_class);

		?>
		<div <?php echo $this->get_render_attribute_string( 'snog-slider' ); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
		<?php
	}

	public function render_footer() {
		$settings = $this->get_settings_for_display();
		
		?>
					
				</div>
				<?php if($settings['show_pagination']) : ?>
					<div class="upk-pagination-wrap">
					    <div class="upk-pagination"></div>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php
	}

	function render_excerpt($excerpt_length) {
		if (!$this->get_settings('show_excerpt')) {
			return;
		}
		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');
	    ?>
		<div class="upk-text" data-swiper-parallax-y ="-100" data-swiper-parallax-duration="700">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo ultimate_post_kit_custom_excerpt($excerpt_length, $strip_shortcode);
			}
			?>
		</div>
		<?php
	}

	public function render_post_grid_item($post_id, $image_size) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('slider-item', 'class', 'upk-item swiper-slide', true);

		?>
		<div <?php echo $this->get_render_attribute_string('slider-item'); ?>>
			<div class="upk-image-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
			</div>
		</div>
		<?php
	}

	public function render_content_item($excerpt_length) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('slider-item', 'class', 'upk-item swiper-slide', true);

		?>
		<div <?php echo $this->get_render_attribute_string('slider-item'); ?>>
			<div class="upk-content-wrap">
			   <div class="upk-inner-content">
				<?php if ( $settings['show_category'] ) : ?>
				<div data-swiper-parallax-x ="-60" data-swiper-parallax-duration="500">
					<?php $this->render_category(); ?>
				</div>
				<?php endif; ?>

				<?php if ( $settings['show_author'] or $settings['show_date'] or $settings['show_reading_time'] ) : ?>
				<div class="upk-meta" data-swiper-parallax-x ="-60" data-swiper-parallax-duration="600">
					<?php $this->render_author(); ?>

					<?php if ($settings['show_date'] == 'yes') : ?>
						<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
						<?php $this->render_date();  ?>
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
				<?php endif; ?>

				<?php if ( $settings['show_title'] ) : ?>
				<div data-swiper-parallax-x ="-60" data-swiper-parallax-duration="700">
					<?php $this->render_title(substr($this->get_name(), 4)); ?>
				</div>
				<?php endif; ?>

				<?php $this->render_excerpt($excerpt_length); ?>

				<?php if ($settings['show_readmore'] === 'yes' and !empty($settings['readmore_text'])) : ?>
					<div class="upk-link-btn" data-swiper-parallax-x ="-60" data-swiper-parallax-duration="800">
						<a href="<?php echo esc_url(get_permalink()); ?>">
							<span><?php echo esc_html($settings['readmore_text']); ?></span>
						</a>
					</div>
				<?php endif; ?>

				</div>
			</div>
		</div>
		<?php
	}

	public function render_thumbnav($post_id, $image_size) {
		$settings        = $this->get_settings_for_display();

		$this->add_render_attribute('thumb-item', 'class', 'upk-item swiper-slide', true);

		?>
		<div <?php echo $this->get_render_attribute_string('thumb-item'); ?>>
		    <div class="upk-image-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
			</div>
		</div>
		<?php

	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$id              = 'upk-snog-slider-' . $this->get_id();

		$this->query_posts($settings['item_limit']['size']);
		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper-thumbs', 'class', 'upk-snog-thumbs ' . $swiper_class);
		$this->add_render_attribute('swiper-content', 'class', 'upk-content-slider ' . $swiper_class);

		?>
		<div class="upk-snog-slider-wrap" id="<?php echo esc_attr($id); ?>">
			<?php
		
			$this->render_header();

			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();
				$thumbnail_size = $settings['primary_thumbnail_size'];

				$this->render_post_grid_item( get_the_ID(), $thumbnail_size );
			}

			$this->render_footer();

			?>
			<div thumbsSlider="" <?php echo $this->get_render_attribute_string('swiper-thumbs'); ?>>
				<div class="swiper-wrapper">
					<?php

					while ( $wp_query->have_posts() ) {
						$wp_query->the_post();
						$thumbnail_size = $settings['primary_thumbnail_size'];

						$this->render_thumbnav( get_the_ID(), $thumbnail_size );
					}

				?>
				</div>
			</div>

			<div <?php echo $this->get_render_attribute_string('swiper-content'); ?>>
					<div class="swiper-wrapper">
						<?php 
						while ( $wp_query->have_posts() ) {
							$wp_query->the_post();

							$this->render_content_item( $settings['excerpt_length']);
						}
						?>
					</div>

					<?php if ($settings['show_navigation']) : ?>
					<div class="upk-navigation-wrap"> 
						<div class="upk-nav-btn upk-navigation-prev">
							<i class="upk-icon-arrow-left-<?php echo esc_html($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
						<div class="upk-nav-btn upk-navigation-next">
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
