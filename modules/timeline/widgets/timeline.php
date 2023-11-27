<?php

namespace UltimatePostKit\Modules\Timeline\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use UltimatePostKit\Utils;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Timeline extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-timeline';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Oras Timeline', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-timeline';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'alter', 'oras'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-timeline'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/2iYuNgP4K0A';
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
			'show_image',
			[
				'label'   => esc_html__('Show Image', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
				'label'   => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'        => esc_html__('Show Date', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'upk-date-hide--',
			]
		);

		$this->add_control(
			'show_inline_date',
			[
				'label'     => esc_html__('Show Inline Date', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_date' => '',
				],
			]
		);

		$this->add_control(
			'human_diff_time',
			[
				'label'   => esc_html__('Human Different Time', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_inline_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'human_diff_time_short',
			[
				'label'   => esc_html__('Time Short Format', 'ultimate-post-kit'),
				'description' => esc_html__('This will work for Hours, Minute and Seconds', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'human_diff_time' => 'yes',
					'show_inline_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'   => esc_html__('Show Time', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'human_diff_time' => '',
					'show_inline_date' => 'yes'
				]
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

		$this->add_control(
			'meta_separator',
			[
				'label'       => __('Separator', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '|',
				'label_block' => false,
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
					'size' => 6,
				],
			]
		);

		$this->register_query_builder_controls();

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
			'item_padding',
			[
				'label'     => esc_html__('Padding', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item:nth-child(2n+1) .upk-image-and-content-wrapper' => 'padding: {{SIZE}}px {{SIZE}}px {{SIZE}}px 0;',
					'{{WRAPPER}} .upk-timeline .upk-item:nth-child(2n+2) .upk-image-and-content-wrapper' => 'padding: {{SIZE}}px 0 {{SIZE}}px {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'item_line_heading',
			[
				'label'     => esc_html__('Line', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'item_line_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--upk-line-color: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'item_border_width',
			[
				'label'     => esc_html__('Border Width', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-border-width: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'     => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-line-right' => 'border-radius: 0 {{SIZE}}px {{SIZE}}px 0;',
					'{{WRAPPER}} .upk-timeline .upk-item .upk-line-left'  => 'border-radius: {{SIZE}}px 0 0 {{SIZE}}px',
				],
			]
		);

		$this->add_control(
			'item_start_end_heading',
			[
				'label'     => esc_html__('Start/End/Date', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'item_start_end_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-start-end-wrap, {{WRAPPER}} .upk-timeline .upk-item .upk-date-wrapper' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'item_start_end_bg_color',
			[
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-start-end-wrap, {{WRAPPER}} .upk-timeline .upk-item .upk-date-wrapper' => 'background: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'item_start_end_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-start-end-wrap, {{WRAPPER}} .upk-timeline .upk-item .upk-date-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => esc_html__('Image', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'item_image_border',
				'label'          => __('Border', 'elementor'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '10',
							'right'    => '10',
							'bottom'   => '10',
							'left'     => '10',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => '#e1e7f0',
					],
				],
				'selector'       => '{{WRAPPER}} .upk-timeline .upk-item .upk-image-wrapper .upk-img',
			]
		);

		$this->add_responsive_control(
			'item_image_border_radius_odd',
			[
				'label'      => esc_html__('Odd Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-timeline .upk-item:nth-child(2n+1) .upk-image-wrapper .upk-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_image_border_radius_even',
			[
				'label'      => esc_html__('Even Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-timeline .upk-item:nth-child(2n+2) .upk-image-wrapper .upk-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_image_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-image-wrapper .upk-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_image_shadow',
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-image-wrapper .upk-img',
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-image-width: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'     => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--upk-image-spacing: {{SIZE}}px;'
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'label'    => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-desc',
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
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
						]
					]
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-meta, {{WRAPPER}} .upk-timeline .upk-item .upk-meta .upk-author-name-wrap .upk-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Text Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-meta .upk-author-name-wrap .upk-author-name:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-meta',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-meta .upk-separator' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_date',
			[
				'label'      => esc_html__('Date', 'ultimate-post-kit'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_date',
							'value' => 'yes'
						],
						[
							'name'  => 'show_inline_date',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label'     => esc_html__('Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-responsive-date' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'date_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-responsive-date',
			]
		);

		$this->add_responsive_control(
			'date_space_between',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-responsive-date .upk-date' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'date_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-responsive-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
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
			'category_spacing',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'category_border',
				'label'          => __('Border', 'ultimate-post-kit'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => '#EF233C',
					],
				],
				'selector'       => '{{WRAPPER}} .upk-timeline .upk-item .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-timeline .upk-item .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing_between',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-category a',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-timeline .upk-item .upk-category a:hover',
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
					'{{WRAPPER}} .upk-timeline .upk-item .upk-category a:hover' => 'border-color: {{VALUE}};',
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

	public function render_image($image_id, $size) {

		if (!$this->get_settings('show_image')) {
			return;
		}

		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src             = wp_get_attachment_image_src($image_id, $size);

		if (!$image_src) {
			$image_src = $placeholder_image_src;
		} else {
			$image_src = $image_src[0];
		}

?>

		<div class="upk-image-wrapper">
			<img class="upk-img" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_html(get_the_title()); ?>">
		</div>
	<?php
	}

	public function render_category() {

		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-category">
			<?php echo upk_get_category($this->get_settings('posts_source')); ?>
		</div>
	<?php
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}
	?>
		<div class="upk-author-name-wrap">
			<span class="upk-by"><?php echo esc_html_x('by', 'Frontend', 'ultimate-post-kit') ?></span>
			<a class="upk-author-name" href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
				<?php echo get_the_author() ?>
			</a>
		</div>
	<?php
	}

	public function render_excerpt($excerpt_length) {

		if (!$this->get_settings('show_excerpt')) {
			return;
		}
		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');
	?>
		<div class="upk-desc">
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

	public function render_comments($id = 0) {

		if (!$this->get_settings('show_comments')) {
			return;
		}
	?>

		<div class="upk-comments">
			<?php echo get_comments_number($id) ?>
			<?php echo esc_html__('Comments', 'ultimate-post-kit') ?>
		</div>

	<?php
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_inline_date')) {
			return;
		}

		if ($settings['human_diff_time'] == 'yes') {
			echo ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			echo get_the_date();
		}
	}

	public function render_post_grid_item($post_id, $image_size, $excerpt_length, $class) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('timeline-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}
		$this->add_render_attribute('timeline-item', 'class', 'upk-item', true);
		$this->add_render_attribute('timeline-item', 'class', $class);

	?>

		<div <?php $this->print_render_attribute_string('timeline-item'); ?>>
			<div class="upk-item-box">
				<span class="upk-start-end-wrap">
					<span class="upk-start"><?php echo esc_html__('start', 'ultimate-post-kit') ?></span>
					<span class="upk-end"><?php echo esc_html__('end', 'ultimate-post-kit') ?></span>
				</span>

				<?php //if($settings['show_date'] == 'yes') : 
				?>
				<div class="upk-date-wrapper">
					<div class="upk-date-inner">
						<span class="upk-month"><?php echo get_the_date('m/d'); ?></span>
						<span class="upk-year"><?php echo get_the_date('Y'); ?></span>
					</div>
				</div>
				<?php ///endif; 
				?>

				<div class="upk-image-and-content-wrapper">
					<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
					<div class="upk-content-wrap">

						<?php $this->render_category(); ?>
						<?php $this->render_title(substr($this->get_name(), 4)); ?>

						<?php if ($settings['show_date'] == 'yes' or $settings['show_inline_date'] == 'yes') : ?>
							<div class="upk-responsive-date">
								<span class="upk-publish"><?php echo esc_html__('publish on', 'ultimate-post-kit') ?></span>
								<span class="upk-date">
									<?php $this->render_date(); ?>
								</span>
								<?php if ($settings['show_time']) : ?>
									<span class="upk-post-time">
										<i class="upk-icon-clock" aria-hidden="true"></i>
										<?php echo get_the_time(); ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php $this->render_excerpt($excerpt_length); ?>

						<div class="upk-meta">
							<?php $this->render_author(); ?>
							<span class="upk-separator"><?php echo esc_html($settings['meta_separator']); ?></span>
							<?php $this->render_comments($post_id); ?>
						</div>
					</div>
				</div>

				<div class="upk-line-right"></div>
				<div class="upk-line-left"></div>
			</div>
		</div>
	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$limit = $settings['item_limit']['size'];

		$this->query_posts($limit);

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

	?>

		<div class="upk-timeline">
			<div class="upk-wrapper">

				<?php

				while ($wp_query->have_posts()) :
					$limit--;
					$class = '';
					$wp_query->the_post();
					$thumbnail_size = $settings['primary_thumbnail_size'];

					if ($limit == 0) {
						$class = 'upk-last-even-item upk-last-odd-item';
					}

				?>

					<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length'], $class); ?>

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
