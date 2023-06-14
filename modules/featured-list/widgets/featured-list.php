<?php

namespace UltimatePostKit\Modules\FeaturedList\Widgets;

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

class Featured_List extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-featured-list';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Featured List', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-featured-list';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'featured', 'list'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-featured-list'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/Q-Pm-6Kkmr4';
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
				'label'   => esc_html__('Layout Style', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__('Style 01', 'ultimate-post-kit'),
					'style-2'  => esc_html__('Style 02', 'ultimate-post-kit'),
					'style-3'  => esc_html__('Style 03', 'ultimate-post-kit'),
				],
			]
		);


		$this->add_responsive_control(
			'item_image_height',
			[
				'label'     => esc_html__('Image Height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-image-wrap .upk-img' => 'height: {{SIZE}}{{UNIT}};',
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

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
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
			'show_counter_number',
			[
				'label'   => esc_html__('Show Counter Number', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'     => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_comments',
			[
				'label' => esc_html__('Show Comments', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
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
			'show_pagination',
			[
				'label'     => esc_html__('Show Pagination', 'ultimate-post-kit'),
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

		$this->add_responsive_control(
			'item_bottom_spacing',
			[
				'label'     => esc_html__('Item Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'featured_item_padding',
			[
				'label'      => esc_html__('Featured Item Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

					// '{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-content, 
					// {{WRAPPER}} .upk-featured-list .upk-item:nth-last-child(1) .upk-content' => 'padding-bottom: calc(2 * {{TOP}}{{UNIT}});',
					// '{{WRAPPER}} .upk-featured-list .upk-item:nth-child(2) .upk-content' => 'padding-top: calc(2 * {{TOP}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'image_heading',
			[
				'label'     => esc_html__('Image', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'item_overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-image-wrap:before' => 'background: linear-gradient(0deg, {{VALUE}} 0, rgba(141, 153, 174, 0.1) 100%);',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_image_border',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-image-wrap',
			]
		);

		$this->add_responsive_control(
			'item_image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-featured-list .upk-image-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
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
					'{{WRAPPER}} .upk-featured-list.upk-featured-style-2 .upk-item-box' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-featured-list.upk-featured-style-3 .upk-item-box' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-title a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_featured_color',
			[
				'label'     => esc_html__('Featured Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-title a' => 'color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_featured_hover_color',
			[
				'label'     => esc_html__('Featured Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-title a:hover' => 'color: {{VALUE}};',
				],
				'separator' => 'after'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'label'    => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-title a',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-featured-meta, {{WRAPPER}} .upk-featured-list .upk-item .upk-featured-meta .upk-author-name-wrap .upk-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_featured_color',
			[
				'label'     => esc_html__('Featured Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-featured-meta, {{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-featured-meta .upk-author-name-wrap .upk-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-featured-meta' => 'padding-top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-featured-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-featured-meta',
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
				'label'     => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'category_style_color',
			[
				'label'     => esc_html__('Dot Style Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category a span' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-category a',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-category a:hover',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_category_featured',
			[
				'label' => esc_html__('Featured', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'category_featured_heading',
			[
				'label'     => esc_html__('NORMAL', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'category_featured_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_featured_background',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-category a',
			]
		);

		$this->add_control(
			'category_featured_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-category a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_category_featured_hover_style',
			[
				'label'     => esc_html__('HOVER', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'category_featured_color_hover',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_featured_background_hover',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-category a:hover',
			]
		);

		$this->add_control(
			'category_featured_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();


		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_counter_number',
			[
				'label'     => esc_html__('Counter Number', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_counter_number' => 'yes',
					'layout_style'        => 'style-1',
				]
			]
		);

		$this->start_controls_tabs(
			'counter_number_style_tabs'
		);
		
		$this->start_controls_tab(
			'conter_number_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'ultimate-post-kit' ),
			]
		);

		$this->add_control(
			'counter_number_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-counter:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'counter_number_background',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-counter',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'counter_number_border',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-counter',
			]
		);

		$this->add_responsive_control(
			'counter_number_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-counter' => 'margin-right: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-featured-list .upk-item .upk-counter' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'counter_number_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item .upk-counter:before',
			]
		);

		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'conter_number__featured_style_normal_tab',
			[
				'label' => esc_html__( 'Featured', 'ultimate-post-kit' ),
			]
		);
		
		$this->add_control(
			'featured_counter_number_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit') . BDTUPK_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-counter:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'featured_counter_number_background',
				'selector' => '{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-counter',
			]
		);

		$this->add_control(
			'featured_counter_number_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'counter_number_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-featured-list .upk-item:nth-child(1) .upk-counter' => 'border-color: {{VALUE}};',
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

	public function print_category_output($output) {


		$tags = [
			'a'     => ['href' => [], 'target' => [], 'class' => []],
			'span'  => ['style' => [], 'class' => []],
		];

		if (isset($output)) {
			echo wp_kses($output, $tags);
		}
	}

	public function render_category() {
		if (!$this->get_settings('show_category')) {
			return;
		}
?>
		<div class="upk-category">

			<?php
			$post_type = $this->get_settings('posts_source');
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
					$bg_color = strToHex($category->cat_name);
					$link = '<a href="' . esc_url(get_category_link($category->term_id)) . '"><span style="background-color:' . $bg_color . '"></span>' . $category->name . '</a>';
					$_categories[$category->slug] = $link;
				}
			}

			$category_link = implode(' ', $_categories);

			$this->print_category_output($category_link);

			?>
		</div>
	<?php
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}
	?>
		<div class="upk-author-name-wrap">
			<span class="upk-by"><?php echo esc_html_x('by', 'Frontend', 'ultimate-post-kit'); ?></span>
			<a class="upk-author-name" href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
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

		<div class="upk-featured-comments">
			<?php echo get_comments_number($id) ?>
			<?php echo esc_html_x('Comments', 'Frontend', 'ultimate-post-kit') ?>
		</div>

	<?php
	}

	public function render_post_grid_item($post_id, $image_size) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('list-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}
		$this->add_render_attribute('list-item', 'class', 'upk-item', true);

	?>
		<div <?php $this->print_render_attribute_string('list-item'); ?>>
			<div class="upk-item-box">

				<div class="upk-image-wrap">
					<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
				</div>
				<div class="upk-content">
					<?php if ($settings['show_counter_number'] == 'yes') : ?>
						<div class="upk-counter"></div>
					<?php endif; ?>

					<div>
						<?php $this->render_category(); ?>
						<?php $this->render_title(substr($this->get_name(), 4)); ?>

						<?php if ($settings['show_author'] or $settings['show_comments'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
							<div class="upk-featured-meta">
								<?php if ($settings['show_author']) : ?>
									<?php $this->render_author(); ?>
								<?php endif; ?>
								<?php if ($settings['show_date']) : ?>
									<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php $this->render_date(); ?>
									</div>
								<?php endif; ?>
								<?php if ($settings['show_comments']) : ?>
									<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php $this->render_comments($post_id); ?>
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

					</div>
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

		$this->add_render_attribute('list-wrap', 'class', 'upk-featured-list upk-featured-' . $settings['layout_style']);

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('list-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('list-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>
		<div <?php $this->print_render_attribute_string('list-wrap'); ?>>
			<?php while ($wp_query->have_posts()) :
				$wp_query->the_post();

				$thumbnail_size = $settings['primary_thumbnail_size'];

			?>

				<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size); ?>

			<?php endwhile; ?>
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
