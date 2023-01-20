<?php

namespace UltimatePostKit\Modules\RambleGrid\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ramble_Grid extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-ramble-grid';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Ramble Grid', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-ramble-grid';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'ramble'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-ramble-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/mKdxqk3M2qI';
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
			'columns',
			[
				'label' => __('Columns', 'ultimate-post-kit'),
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
					'{{WRAPPER}} .upk-ramble-grid .upk-post-wrap' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => esc_html__('Row Gap', 'ultimate-post-kit') . BDTUPK_NC,
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-post-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-post-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_height',
			[
				'label'   => esc_html__('Item Height(px)', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item' => 'height: {{SIZE}}{{UNIT}};',
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
				'default' => 'yes',
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
			'show_author_avatar',
			[
				'label'   => esc_html__('Show Author Avatar', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_name',
			[
				'label'   => esc_html__('Show Author Name', 'ultimate-post-kit'),
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
				'default'     => '/',
				'label_block' => false,
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
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
			'show_readmore',
			[
				'label'     => esc_html__('Show Read More', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => esc_html__('Read More Text', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'ultimate-post-kit'),
				'placeholder' => esc_html__('Read More', 'ultimate-post-kit'),
				'condition'   => [
					'show_readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_icon',
			[
				'label'       => esc_html__('Icon', 'ultimate-post-kit'),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'condition'   => [
					'show_readmore' => 'yes',
				]
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__('Icon Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__('Left', 'ultimate-post-kit'),
					'right' => esc_html__('Right', 'ultimate-post-kit'),
				],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label'   => esc_html__('Icon Spacing', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-flex-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-ramble-grid .upk-flex-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Show Pagination', 'ultimate-post-kit'),
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

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'overlay_background',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-image-wrap:before' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __('Border', 'ultimate-post-kit'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-ramble-grid .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' 	 => __('Content Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-default-show, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-default-hide, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
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
			'overlay_background_hover',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item:hover .upk-image-wrap:before' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'item_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow_hover',
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item:hover',
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
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title',
			]
		);

		$this->add_control(
			'title_advanced_style',
			[
				'label' => esc_html__('Advanced Style', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'title_background',
			[
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 	   => 'title_border',
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'		 => __('Border Radius', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 	   => 'title_box_shadow',
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_text_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_text_margtin',
			[
				'label' 	 => __('Margtin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-title' => 'margtin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label'     => esc_html__('Text', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes'
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'text_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_author_date',
			[
				'label'     => esc_html__('Author/Date', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_author_avatar',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_author_name',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_date',
							'value'    => 'yes'
						]
					]
				],
			]
		);

		$this->add_control(
			'author_image_heading',
			[
				'label'     => esc_html__('Author Avatar', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_author_avatar' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'author_iamge_border',
				'label'       => __('Border', 'ultimate-post-kit'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-meta .upk-author-image img',
				'condition' => [
					'show_author_avatar' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'author_iamge_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-meta .upk-author-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_author_avatar' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'author_iamge_size',
			[
				'label' => esc_html__('Size', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-meta .upk-author-image img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_author_avatar' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'author_iamge_spacing',
			[
				'label' => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-meta .upk-author-image' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_author_avatar' => 'yes'
				]
			]
		);

		$this->add_control(
			'author_heading',
			[
				'label'     => esc_html__('Author Name', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_author_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'author_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-author-name a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_author_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'author_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-author-name a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_author_name' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-author-name a',
				'condition' => [
					'show_author_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'date_heading',
			[
				'label'     => esc_html__('Date/Time', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
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
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-default-hide .upk-date, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-default-hide .upk-post-time, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-reading-time' => 'color: {{VALUE}};',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'date_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-default-hide .upk-date, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-default-hide .upk-post-time, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-reading-time',
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
			'date_spacing',
			[
				'label' => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-default-hide .upk-date' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'meta_space_between',
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
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-reading-time > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__('Category/Date', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_category',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_date',
							'value'    => 'yes'
						]
					]
				],
			]
		);

		$this->add_control(
			'category_date_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-date, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-category a, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-post-time' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_date_divider_color',
			[
				'label'     => esc_html__('Divider Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-category:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'category_date_spacing',
			[
				'label' => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-category' => 'margin-left: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'category_spacing',
			[
				'label'   => esc_html__('Category Space Beween', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_date_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-date, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-category a, {{WRAPPER}} .upk-ramble-grid .upk-item .upk-date-cetagory-wrap .upk-post-time',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_readmore',
			[
				'label'     => __('Read More', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_readmore'       => 'yes',
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
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_background',
				'selector'  => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'readmore_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore'
			]
		);

		$this->add_responsive_control(
			'readmore_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-readmore',
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
					'{{WRAPPER}} .upk-ramble-grid .upk-item:hover .upk-btn-comments-wrap .upk-readmore' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-ramble-grid .upk-item:hover .upk-btn-comments-wrap .upk-readmore svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_hover_background',
				'selector'  => '{{WRAPPER}} .upk-ramble-grid .upk-item:hover .upk-btn-comments-wrap .upk-readmore',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item:hover .upk-btn-comments-wrap .upk-readmore' => 'border-color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .upk-ramble-grid .upk-item:hover .upk-btn-comments-wrap .upk-readmore',
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => __('Hover Animation', 'ultimate-post-kit'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_comments_style',
			[
				'label'     => esc_html__('Comments', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_comments' => 'yes'
				],
			]
		);

		$this->add_control(
			'comments_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-comments' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comments_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-ramble-grid .upk-item:hover .upk-btn-comments-wrap .upk-comments' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'comments_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-ramble-grid .upk-item .upk-btn-comments-wrap .upk-comments',
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

	public function render_post_meta() {
		$settings = $this->get_settings_for_display();

?>
		<?php if ($settings['show_author_avatar'] or $settings['show_author_name'] or $settings['show_date']) : ?>
			<div class="upk-meta">

				<?php if ($settings['show_author_avatar']) : ?>
					<div class="upk-author-image">
						<?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
					</div>
				<?php endif; ?>

				<div class="upk-author-name-date-wrap">

					<?php if ($settings['show_author_name']) : ?>
						<div class="upk-author-name">
							<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>"><?php echo get_the_author() ?></a>
						</div>
					<?php endif; ?>

					<?php if ($settings['show_date'] or $settings['show_reading_time']) : ?>
						<div class="upk-date-reading-time upk-flex upk-flex-middle">
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

				</div>

			</div>
		<?php endif; ?>

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

	public function render_readmore() {
		$settings        = $this->get_settings_for_display();

		if (!$this->get_settings('show_readmore')) {
			return;
		}

		$animation = ($this->get_settings('readmore_hover_animation')) ? ' elementor-animation-' . $this->get_settings('readmore_hover_animation') : '';

	?>
		<a href="<?php echo esc_url(get_permalink()); ?>" class="upk-readmore <?php echo esc_html($animation); ?>">
			<span class="upk-flex upk-flex-middle">
				<?php echo esc_html($this->get_settings('readmore_text')); ?>

				<?php if ($settings['readmore_icon']['value']) : ?>
					<span class="upk-readmore-btn-icon upk-flex-align-<?php echo esc_html($this->get_settings('icon_align')); ?>">

						<?php Icons_Manager::render_icon($settings['readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>

					</span>
				<?php endif; ?>
			</span>
		</a>
	<?php
	}

	public function render_post_grid_item($post_id, $image_size, $excerpt_length) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}
		$this->add_render_attribute('grid-item', 'class', 'upk-item', true);

	?>
		<div <?php $this->print_render_attribute_string('grid-item'); ?>>
			<div class="upk-image-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>

				<div class="upk-content">

					<div class="upk-default-show">
						<div class="upk-date-cetagory-wrap">
							<?php $this->render_date(); ?>
							<?php $this->render_category(); ?>
						</div>

						<?php $this->render_title(substr($this->get_name(), 4)); ?>
					</div>

					<div class="upk-default-hide">
						<?php $this->render_post_meta(); ?>
						<?php $this->render_excerpt($excerpt_length); ?>
					</div>

				</div>
				<div class="upk-btn-comments-wrap">
					<div class="upk-btn-wrap upk-flex">
						<?php $this->render_readmore(); ?>
					</div>
					<?php $this->render_comments($post_id); ?>
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

		$this->add_render_attribute('grid-wrap', 'class', 'upk-post-wrap');

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>
		<div class="upk-ramble-grid">
			<div <?php $this->print_render_attribute_string('grid-wrap'); ?>>

				<?php while ($wp_query->have_posts()) :
					$wp_query->the_post();

					$thumbnail_size = $settings['primary_thumbnail_size'];

				?>

					<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']); ?>

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
