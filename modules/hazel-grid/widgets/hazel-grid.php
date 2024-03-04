<?php

namespace UltimatePostKit\Modules\HazelGrid\Widgets;

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

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Hazel_Grid extends Group_Control_Query {

	use Global_Widget_Controls;

	private $_query = null;

	public function get_name() {
		return 'upk-hazel-grid';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Hazel Grid', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-hazel-grid';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'hazel'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-hazel-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/Uy_rOg8lQJM';
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
					'1'  => esc_html__('Style 01', 'ultimate-post-kit'),
					'2'  => esc_html__('Style 02', 'ultimate-post-kit'),
					'3'  => esc_html__('Style 03', 'ultimate-post-kit'),
					'4'  => esc_html__('Style 04', 'ultimate-post-kit'),
					'5'  => esc_html__('Style 05', 'ultimate-post-kit'),
					'6'  => esc_html__('Style 06', 'ultimate-post-kit'),
					'7'  => esc_html__('Style 07', 'ultimate-post-kit'),
					'8'  => esc_html__('Style 08', 'ultimate-post-kit'),
					'9'  => esc_html__('Style 09', 'ultimate-post-kit'),
					'10' => esc_html__('Style 10', 'ultimate-post-kit'),
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
					'{{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-1' => $column_size,
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
				'label'     => esc_html__('Row Gap', 'ultimate-post-kit') . BDTUPK_NC,
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-grid .upk-post-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-post-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_style',
			[
				'label'   => esc_html__('Content Style', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => esc_html__('Style 01', 'ultimate-post-kit'),
					'2' => esc_html__('Style 02', 'ultimate-post-kit'),
					'3' => esc_html__('Style 03', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'   => esc_html__('Content Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-left',
				'options' => [
					'top-left'      => esc_html__('Top Left', 'ultimate-post-kit'),
					'top-right'     => esc_html__('Top Right', 'ultimate-post-kit'),
					'center-center' => esc_html__('Center', 'ultimate-post-kit'),
					'bottom-left'   => esc_html__('Bottom Left', 'ultimate-post-kit'),
					'bottom-right'  => esc_html__('Bottom Right', 'ultimate-post-kit'),
					'bottom-center' => esc_html__('Bottom Center', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_responsive_control(
			'default_item_height',
			[
				'label'     => esc_html__('Default Item Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-1 .upk-item, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-8 .upk-item:nth-child(n+1)' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'grid_style' => ['1', '8']
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
					'{{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+2), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+4)' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'grid_style' => ['5']
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
					'{{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+2), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+3), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+4), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+5), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+6), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+2), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+5), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+5), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+3), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+5), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+1), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+2), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+4), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+5), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+2), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+3), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+4), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+6), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+7), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-9 .upk-item:nth-child(4n+2), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-9 .upk-item:nth-child(4n+3), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-9 .upk-item:nth-child(4n+4), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+1), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+2), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+3), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+4), {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+6)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+1)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+1)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+2)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+3)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+1)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+5)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-9 .upk-item:nth-child(4n+1)' => 'height: {{SIZE}}px;',
					'(mobile){{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+5)' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'grid_style!' => ['1', '8']
				]
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
						'min' => 0,
						'max' => 18,
						'step' => 6
					],
				],
				'default' => [
					'size' => 6,
				],
				'condition' => [
					'grid_style' => ['1', '2', '8', '10']
				]
			]
		);

		$this->add_control(
			'item_limit_3_4_5_6',
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
					'grid_style' => ['3', '4', '5', '6']
				]
			]
		);

		$this->add_control(
			'item_limit_7',
			[
				'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 21,
						'step' => 7
					],
				],
				'default' => [
					'size' => 7,
				],
				'condition' => [
					'grid_style' => ['7']
				]
			]
		);

		$this->add_control(
			'item_limit_9',
			[
				'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 4
					],
				],
				'default' => [
					'size' => 4,
				],
				'condition' => [
					'grid_style' => ['9']
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
				'label'   => esc_html__('Show Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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

		//Global Date Controls
		$this->register_date_controls();

		//Global Reading Time Controls
		$this->register_reading_time_controls();

		$this->add_control(
			'meta_separator',
			[
				'label'       => __('Separator', 'ultimate-post-kit') . BDTUPK_NC,
				'type'        => Controls_Manager::TEXT,
				'default'     => '-',
				'label_block' => false,
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

		$this->start_controls_section(
			'upk_section_style',
			[
				'label' => esc_html__('Items', 'ultimate-post-kit'),
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
				'condition'   => [
					'content_style' => '2',
				]
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
					'{{WRAPPER}} .upk-hazel-grid .upk-content-style-2 .upk-content' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes',
					'content_style'       => '2',
				]
			]
		);

		$this->add_control(
			'overlay_background',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-grid .upk-content-style-1 .upk-item-box:before, {{WRAPPER}} .upk-hazel-grid .upk-content-style-2 .upk-content, {{WRAPPER}} .upk-hazel-grid .upk-content-style-3 .upk-item .upk-item-box:before' => 'background-color: {{VALUE}};'
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
				'selector'    => '{{WRAPPER}} .upk-hazel-grid .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-hazel-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __('Content Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-hazel-grid .upk-post-grid .upk-item .upk-item-box .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'secondary_title_typography',
				'label'     => esc_html__('Secondary Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+2) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+3) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+4) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+5) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-2 .upk-item:nth-child(6n+6) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+2) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+3) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+4) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-3 .upk-item:nth-child(5n+5) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+1) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+3) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+4) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-4 .upk-item:nth-child(5n+5) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+2) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+3) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-5 .upk-item:nth-child(5n+5) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+1) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+2) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+4) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-6 .upk-item:nth-child(5n+5) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+2) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+3) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+4) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+6) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-7 .upk-item:nth-child(7n+7) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-9 .upk-item:nth-child(4n+2) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-9 .upk-item:nth-child(4n+3) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-9 .upk-item:nth-child(4n+4) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+1) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+2) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+3) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+4) .upk-content .upk-title, {{WRAPPER}} .upk-hazel-grid .upk-post-grid.upk-style-10 .upk-item:nth-child(6n+6) .upk-content .upk-title',
				'condition' => [
					'grid_style!' => ['1', '8']
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
				'label'     => __('Background', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title',
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
				'selector'  => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border',
				'selector'  => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'  => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_text_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_author_date',
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
						]
					]
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'author_date_background',
				'selector'  => '{{WRAPPER}} .upk-hazel-grid .upk-content-style-3 .upk-content .upk-meta',
				'condition' => [
					'content_style' => '3',
				],
			]
		);

		$this->add_control(
			'author_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'content_style' => '3',
				],
			]
		);

		$this->add_control(
			'author_color',
			[
				'label'     => esc_html__('Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_hover_color',
			[
				'label'     => esc_html__('Hover Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-meta .upk-blog-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'date_divider_color',
		// 	[
		// 		'label'     => esc_html__('Divider Color', 'ultimate-post-kit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-meta .upk-date:before' => 'background: {{VALUE}};',
		// 		],
		// 	]
		// );

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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-meta',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge span' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a:hover',
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
					'{{WRAPPER}} .upk-hazel-grid .upk-item .upk-item-box .upk-content .upk-blog-badge a:hover' => 'border-color: {{VALUE}};',
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
		$placeholder_image_src = Utils::get_placeholder_image_src();

		$image_src = wp_get_attachment_image_src($image_id, $size);

		if (!$image_src) {
			$image_src = $placeholder_image_src;
		} else {
			$image_src = $image_src[0];
		}

?>

		<img class="upk-blog-image" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_html(get_the_title()); ?>">

	<?php
	}

	public function render_title() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_title')) {
			return;
		}

		printf('<%1$s class="upk-title"><a href="%2$s" title="%3$s" class="upk-blog-title">%3$s</a></%1$s>', esc_attr(Utils::get_valid_html_tag($settings['title_tags'])), get_permalink(), get_the_title());
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}

	?>

		<div class="upk-blog-author">
			<span class="by"><?php echo esc_html_x('by', 'Frontend', 'ultimate-post-kit') ?></span>
			<span class="upk-post-grid-author">
				<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
					<?php echo get_the_author() ?>
				</a>
			</span>
		</div>

	<?php
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_date')) {
			return;
		}

		if ($settings['human_diff_time'] == 'yes') {
			echo ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			echo get_the_date();
		}
	}

	public function render_category() {

		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-blog-badge">
			<span>
				<?php echo upk_get_category($this->get_settings('posts_source')); ?>
			</span>
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
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>

				<div class="upk-content">
					<div class="upk-cetagory">
						<?php $this->render_category(); ?>
					</div>

					<div class="upk-title-wrap">
						<?php $this->render_title(substr($this->get_name(), 4)); ?>
					</div>

					<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
						<div class="upk-meta">
							<?php $this->render_author(); ?>

							<?php if ($settings['show_date']) : ?>
								<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<div class="upk-date">
										<i class="upk-icon-calendar" aria-hidden="true"></i><?php $this->render_date(); ?>
									</div>
								
									<?php if ($settings['show_time']) : ?>
									<div class="upk-post-time">
										<i class="upk-icon-clock" aria-hidden="true"></i>
										<?php echo get_the_time(); ?>
									</div>
									<?php endif; ?>
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

		if ($settings['grid_style'] == '9') {
			$this->query_posts($settings['item_limit_9']['size']);
		} elseif ($settings['grid_style'] == '7') {
			$this->query_posts($settings['item_limit_7']['size']);
		} elseif ($settings['grid_style'] == '3' or $settings['grid_style'] == '4' or $settings['grid_style'] == '5' or $settings['grid_style'] == '6') {
			$this->query_posts($settings['item_limit_3_4_5_6']['size']);
		} else {
			$this->query_posts($settings['item_limit']['size']);
		}

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->add_render_attribute('grid-wrap', 'class', 'upk-post-grid');
		$this->add_render_attribute('grid-wrap', 'class', 'upk-style-' . $settings['grid_style']);
		$this->add_render_attribute('grid-wrap', 'class', 'upk-text-position-' . $settings['content_position']);
		$this->add_render_attribute('grid-wrap', 'class', 'upk-content-style-' . $settings['content_style']);

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>
		<div class="upk-hazel-grid">
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
