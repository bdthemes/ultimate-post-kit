<?php

namespace UltimatePostKit\Modules\PostAccordion\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use UltimatePostKit\Utils;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_Accordion extends Group_Control_Query {

	use Global_Widget_Controls;

	private $_query = null;

	public function get_name() {
		return 'upk-post-accordion';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Post Accordion', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-post-accordion';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'accordion'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-post-accordion'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/lxGeTthE_lA';
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
			'layout_style',
			[
				'label'   => __('Layout Style', 'ultimate-post-kit') .BDTUPK_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => 'Horizontal',
					'style-2' => 'Vertical',
				],
			]
		);

		$this->add_control(
			'layout_content_style',
			[
				'label'   => __('Content Style', 'ultimate-post-kit') .BDTUPK_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => '01',
					'style-2' => '02',
				],
			]
		);

		$this->add_responsive_control(
			'default_item_height',
			[
				'label'   => esc_html__('Height(px)', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'default_item_gap',
			[
				'label'   => esc_html__('Item Gap', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'default_item_expand',
			[
				'label'   => esc_html__('Item Hover Expand', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					    'px' => [
					        'min' => 2,
					        'max' => 10,
					    ],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item:hover' => 'flex: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'primary_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'large',
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
						'min' => 1,
						'max' => 12,
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
			'section_content_additional',
			[
				'label' => esc_html__('Additional', 'ultimate-post-kit'),
			]
		);

		//Global Title Controls
		$this->register_title_controls();

		$this->add_control(
			'show_excerpt',
			[
				'label'   => esc_html__('Show Text', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition'   => [
					'layout_style' => 'style-1'
				],
			]	
			
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'       => esc_html__('Text Limit', 'ultimate-post-kit'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'ultimate-post-kit'),
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
				'label'   => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Show Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_comments',
			[
				'label'   => esc_html__('Show Comments', 'ultimate-post-kit'),
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

		$this->add_responsive_control (
			'content_width',
			[
				'label'       => __('Content Width', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
				],
				'selectors'   => [
					'(desktop){{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content' => 'width: {{SIZE}}px;',
					'(tablet){{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content' => 'width: 100%;',
					'(mobile){{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content' => 'width: 100%;'
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __('Border', 'ultimate-post-kit') . BDTUPK_NC,
				'selector'    => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit') . BDTUPK_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-item-box::before' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-item-box::before' => 'background: {{VALUE}};',
				],
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item:hover .upk-accordion-item-box::before' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'overlay_color_hover',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item:hover .upk-accordion-item-box::before' => 'background: {{VALUE}};',
				],
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
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'       => __('Spacing', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-title' => 'margin-bottom: {{SIZE}}px;'
				],
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-text',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label'       => __('Spacing', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-text' => 'margin-bottom: {{SIZE}}px;'
				],
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a::before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a::before',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_space_between',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a::before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a::before',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a:hover ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a:hover::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a:hover ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a:hover::before',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a:hover ,{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-category a:hover::before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_author',
			[
				'label'     => esc_html__('Author', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->add_control(
			'author_image_heading',
			[
				'label'     => esc_html__('Avatar', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'author_image_size',
			[
				'label'   => esc_html__('Size', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 20,
						'max'  => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-img img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'author_image_spacing',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-img img' => 'margin-right: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'author_name_heading',
			[
				'label'     => esc_html__('Name', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'author_name_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-name a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_name_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-name a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_name_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-name a',
			]
		);

		$this->add_control(
			'author_role_heading',
			[
				'label'     => esc_html__('Role', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'author_role_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-role' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_role_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-role',
			]
		);

		$this->add_responsive_control(
			'author_role_spacing',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-author-role' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'     => esc_html__('Meta', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-meta',
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
					'{{WRAPPER}} .upk-accordion-wrapper .upk-accordion-item .upk-accordion-content .upk-accordion-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
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

	public function render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		$image_src = wp_get_attachment_image_src($image_id, $size);

		if (!$image_src) {
			$image_src = $placeholder_image_src;
		} else {
			$image_src = $image_src[0];
		}

?>

		<img class="upk-img" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_html(get_the_title()); ?>">

	<?php
	}

	protected function _upk_get_category($post_type) {
		switch ($post_type) {
			case 'campaign':
				$taxonomy = 'campaign_category';
				break;
			case 'lightbox_library':
				$taxonomy = 'ngg_tag';
				break;
			case 'give_forms':
				$taxonomy = 'give_forms_category';
				break;
			case 'tribe_events':
				$taxonomy = 'tribe_events_cat';
				break;
			case 'product':
				$taxonomy = 'product_cat';
				break;
			default:
				$taxonomy = 'category';
				break;
		}
	
		$categories = get_the_terms(get_the_ID(), $taxonomy);
		$_categories = [];
		if ($categories) {
			foreach ($categories as $category) {
				$link = '<a data-hover="' . $category->name . '" href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
				$_categories[$category->slug] = $link;
			}
		}
		return implode(' ', $_categories);
	}
	


	public function render_title() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_title')) {
			return;
		}

		printf('<%1$s class="upk-accordion-title"><a  data-hover="%3$s" href="%2$s" title="%3$s">%3$s</a></%1$s>', esc_attr(Utils::get_valid_html_tag($settings['title_tags'])), get_permalink(), get_the_title());
	}

	public function render_excerpt($excerpt_length) {

		if (!$this->get_settings('show_excerpt')) {
			return;
		}

		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');
	?>
		<div class="upk-accordion-text" data-hover="<?php echo wp_trim_words(get_the_excerpt(), $excerpt_length, ''); ?>">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo ultimate_post_kit_custom_excerpt ($excerpt_length, $strip_shortcode);
			}
			?>
		</div>

	<?php
	}

	function render_category() {
		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-accordion-category">
			<?php echo $this->_upk_get_category($this->get_settings('posts_source')); ?>
		</div>
	<?php
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_date')) {
			return;
		}

		$date_for_hover = false;
		if ($settings['human_diff_time'] == 'yes') {
			$date_for_hover = ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			$date_for_hover = get_the_date();
		}

	?>
		<div class="upk-accordion-date">
			<i class="upk-icon-calendar" aria-hidden="true"></i>
			<span <?php 
			if($date_for_hover){ 
				printf('data-hover="%s"', $date_for_hover); 
				} 
				?>>
				<?php
				echo $date_for_hover;
				?>
			</span>
		</div>
		<?php if ($settings['show_time']) : ?>
			<div class="upk-post-time">
				<i class="upk-icon-clock" aria-hidden="true"></i>
				<?php echo get_the_time(); ?>
			</div>
		<?php endif; ?>

	<?php
	}

	public function render_comments($id = 0) {

		if (!$this->get_settings('show_comments')) {
			return;
		}
	?>

		<div class="upk-accordion-comments">
			<i class="upk-icon-bubbles" aria-hidden="true"></i>
			<span <?php printf('data-hover="%s"', get_comments_number($id) . ' ' . esc_html('Comments', 'ultimate-post-kit')); ?>>
				<?php echo get_comments_number($id); ?>
				<?php echo esc_html('Comments', 'ultimate-post-kit'); ?>
			</span>
		</div>

	<?php
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}

	?>
		<div class="upk-author-wrapper">
			<div class="upk-author-img">
				<?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
			</div>
			<div class="upk-author-info-warp upk-flex">
				<div class="upk-author-name">
					<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
						<?php echo get_the_author() ?>
					</a>
				</div>
				<div class="upk-author-role">
					<?php
					$aid = get_the_author_meta('ID');
					echo ucwords(get_user_role($aid));
					?>
				</div>
			</div>
		</div>
	<?php
	}

	public function render_post_accordion_item($post_id, $image_size, $excerpt_length) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('accordion-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}

		$this->add_render_attribute('accordion-item', 'class', 'upk-accordion-item', true);

	?>

		<div <?php $this->print_render_attribute_string('accordion-item'); ?>>
			<div class="upk-accordion-item-box">

				<div class="upk-accordion-image">
					<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
				</div>
				<?php $this->render_author(); ?>

				<div class="upk-accordion-content">
					<?php $this->render_category(); ?>
					<?php $this->render_title(substr($this->get_name(), 4)); ?>
					<?php $this->render_excerpt($excerpt_length); ?>

					<?php if ($settings['show_comments'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
					<div class="upk-accordion-meta">
						<?php $this->render_date(); ?>

						<?php if ($settings['show_comments'] == 'yes') : ?>
						<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
						<?php $this->render_comments($post_id); ?>
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

	?>
		<div class="upk-accordion">
			<div class="upk-accordion-wrapper upk-accordion-content<?php echo esc_attr($settings['layout_content_style'])?> upk-accordion-<?php echo esc_attr($settings['layout_style'])?>">

				<?php while ($wp_query->have_posts()) :
					$wp_query->the_post();

					$thumbnail_size = $settings['primary_thumbnail_size'];

				?>

					<?php $this->render_post_accordion_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']); ?>

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
