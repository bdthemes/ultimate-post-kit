<?php

namespace UltimatePostKit\Modules\EliteGrid\Widgets;

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

class Elite_Grid extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-elite-grid';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Elite Grid', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-elite-grid';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'elite'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-elite-grid'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-ajax-loadmore'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/J0AfZvRWClw';
	}

	public function get_query() {
		return $this->_query;
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
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
					'{{WRAPPER}} .upk-elite-grid .upk-style-1' => $column_size,
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
				'label'     => esc_html__('Row Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-post-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-post-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'default_image_height',
			[
				'label'     => esc_html__('Default Image Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-image-wrap .upk-main-image .upk-img' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'grid_style' => ['1']
				]
			]
		);

		$this->add_responsive_control(
			'secondary_item_height',
			[
				'label'     => esc_html__('Secondary Image Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 100,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+2) .upk-item-box .upk-image-wrap .upk-main-image .upk-img, {{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+3) .upk-item-box .upk-image-wrap .upk-main-image .upk-img, {{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+4) .upk-item-box .upk-image-wrap .upk-main-image .upk-img, {{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+5) .upk-item-box .upk-image-wrap .upk-main-image .upk-img, {{WRAPPER}} .upk-elite-grid .upk-style-3 .upk-item:nth-child(4n+2) .upk-item-box .upk-image-wrap .upk-main-image .upk-img, {{WRAPPER}} .upk-elite-grid .upk-style-3 .upk-item:nth-child(4n+3) .upk-item-box .upk-image-wrap .upk-main-image .upk-img, {{WRAPPER}} .upk-elite-grid .upk-style-3 .upk-item:nth-child(4n+4) .upk-item-box .upk-image-wrap .upk-main-image .upk-img' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'grid_style' => ['2', '3']
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
				'label' => __('Query', 'ultimate-post-kit'),
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
						'max' => 21,
						'step' => 3
					],
				],
				'default' => [
					'size' => 6,
				],
				'condition' => [
					'grid_style' => ['1']
				]
			]
		);

		$this->add_control(
			'item_limit_2',
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
					'grid_style' => ['2']
				]
			]
		);

		$this->add_control(
			'item_limit_3',
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
					'grid_style' => ['3']
				]
			]
		);

		$this->register_query_builder_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__('Additional Options', 'ultimate-post-kit'),
			]
		);

		//Global Title Controls
		$this->register_title_controls();
		$this->register_text_controls();

		$this->add_control(
			'show_author',
			[
				'label'   => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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

		//Global Date Controls
		$this->register_date_controls();
		//Global Reading Time Controls 
		$this->register_reading_time_controls();
		$this->add_control(
			'meta_separator',
			[
				'label'       => __('Separator', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '|',
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

		//Global Ajax Controls
		$this->register_ajax_loadmore_controls();

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

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __('Content Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-elite-grid .upk-item .upk-item-box .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'itam_background',
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __('Border', 'ultimate-post-kit'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-elite-grid .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-elite-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-item',
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
			Group_Control_Background::get_type(),
			[
				'name'     => 'itam_background_color_hover',
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-item:hover',
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-item:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'item_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_hover',
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-item:hover',
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
				'default' => '',
				'options' => [
					'' 			  => esc_html__('Default', 'ultimate-post-kit'),
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
					'{{WRAPPER}} .upk-elite-grid .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-title a:hover' => 'color: {{VALUE}};',
				],
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
					'{{WRAPPER}} .upk-elite-grid .upk-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'secondary_title_typography',
				'label'     => esc_html__('Secondary Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+2) .upk-title, {{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+3) .upk-title, {{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+4) .upk-title, {{WRAPPER}} .upk-elite-grid .upk-style-2 .upk-item:nth-child(5n+5) .upk-title, {{WRAPPER}} .upk-elite-grid .upk-style-3 .upk-item:nth-child(4n+2) .upk-title, {{WRAPPER}} .upk-elite-grid .upk-style-3 .upk-item:nth-child(4n+3) .upk-title, {{WRAPPER}} .upk-elite-grid .upk-style-3 .upk-item:nth-child(4n+4) .upk-title',
				'condition' => [
					'grid_style' => ['2', '3']
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

		$this->add_control(
			'title_divider_color',
			[
				'label'     => esc_html__('Line Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-content .upk-title a:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'title_background',
				'label'     => __('Background', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-elite-grid .upk-title a',
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
				'selector'  => '{{WRAPPER}} .upk-elite-grid .upk-title a',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border',
				'selector'  => '{{WRAPPER}} .upk-elite-grid .upk-title a',
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
					'{{WRAPPER}} .upk-elite-grid .upk-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'  => '{{WRAPPER}} .upk-elite-grid .upk-title a',
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
					'{{WRAPPER}} .upk-elite-grid .upk-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'title_advanced_style' => 'yes'
				]
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
					'{{WRAPPER}} .upk-elite-grid .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-text',
			]
		);

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

		$this->add_responsive_control(
			'author_border_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-elite-grid .upk-image-wrap .upk-author-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'author_background',
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-image-wrap .upk-author-wrap',
			]
		);

		$this->add_responsive_control(
			'author_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-elite-grid .upk-image-wrap .upk-author-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'author_image_heading',
			[
				'label'     => esc_html__('Image', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'author_image_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-author-img-wrap img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-elite-grid .upk-item-box .upk-author-wrap .upk-author-info-warp' => 'padding-left: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-elite-grid .upk-author-info-warp .author-name .name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_name_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-author-info-warp .author-name .name:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_name_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-author-info-warp .author-name .name',
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
					'{{WRAPPER}} .upk-elite-grid .upk-author-info-warp .author-depertment' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_role_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-author-info-warp .author-depertment',
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
					'{{WRAPPER}} .upk-elite-grid .upk-author-info-warp .author-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__('Meta', 'ultimate-post-kit') . BDTUPK_UC,
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
						],
						[
							'name'     => 'show_reading_time',
							'value'    => 'yes'
						]
					]
				],
			]
		);

		$this->add_responsive_control(
			'category_top_spacing',
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
					'{{WRAPPER}} .upk-elite-grid .upk-meta-wrap' => 'top: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'category_date_color',
			[
				'label'     => esc_html__('Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-date, {{WRAPPER}} .upk-elite-grid .upk-post-time, {{WRAPPER}} .upk-elite-grid .upk-reading-time' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_date',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_reading_time',
							'value'    => 'yes'
						]
					]
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_date_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-date, {{WRAPPER}} .upk-elite-grid .upk-post-time, {{WRAPPER}} .upk-elite-grid .upk-reading-time',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_date',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_reading_time',
							'value'    => 'yes'
						]
					]
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'date_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-date, {{WRAPPER}} .upk-elite-grid .upk-post-time, {{WRAPPER}} .upk-elite-grid .upk-reading-time',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_date',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_reading_time',
							'value'    => 'yes'
						]
					]
				],
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
					'{{WRAPPER}} .upk-elite-grid .upk-category' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_date',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_reading_time',
							'value'    => 'yes'
						]
					]
				],
			]
		);

		$this->add_control(
			'category_heading',
			[
				'label'     => esc_html__('Category', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->start_controls_tabs('tabs_category_style');

		$this->start_controls_tab(
			'tab_category_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-category a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-elite-grid .upk-category a',
				'condition' => [
					'show_category' => 'yes'
				]
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
						'default' => '#dddfe2',
					],
				],
				'selector'       => '{{WRAPPER}} .upk-elite-grid .upk-category a',
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-elite-grid .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-elite-grid .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
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
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-category a',
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-elite-grid .upk-category a',
				'condition' => [
					'show_category' => 'yes'
				]
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
					'{{WRAPPER}} .upk-elite-grid .upk-category a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-elite-grid .upk-category a:hover',
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_control(
			'category_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
					'show_category' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .upk-elite-grid .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Global Pagination Controls
		$this->register_pagination_controls();

		//Global ajax style controls
		$this->register_ajax_loadmore_style_controls();
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

		<div class="upk-author-img-wrap">
			<?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
		</div>

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
			<div class="upk-item-box">
				<div class="upk-image-wrap">
					<div class="upk-main-image">
						<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
					</div>

					<?php if ($settings['show_author']) : ?>
						<div class="upk-author-wrap">

							<?php $this->render_author(); ?>
							<div class="upk-author-info-warp">
								<span class="author-name">
									<a class="name" href="<?php echo esc_url( get_author_posts_url(get_the_author_meta('ID')) ); ?>">
										<?php echo esc_html( get_the_author() ) ?>
									</a>
								</span>

								<span class="author-depertment">
									<?php
									$aid = get_the_author_meta('ID');
									echo ucwords(get_user_role($aid));
									?>
								</span>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($settings['show_category'] == 'yes' or $settings['show_date'] == 'yes') : ?>
						<div class="upk-meta-wrap">

							<?php if ( 'yes' === $settings['show_date'] or 'yes' === $settings['show_reading_time'] ) : ?>
							<div class="upk-flex upk-flex-middle upk-date-reading-wrap">

								<?php $this->render_date(); ?>
								
								<?php if (_is_upk_pro_activated()) :
									if ('yes' === $settings['show_reading_time']) : ?>
										<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
											<?php echo esc_html( ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']) ); ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							<?php endif; ?>

							<?php $this->render_category(); ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="upk-content">
					<div>
						<?php $this->render_title(substr($this->get_name(), 4)); ?>
						<div class="upk-text-wrap">
							<?php $this->render_excerpt($excerpt_length); ?>
						</div>

						<?php if ($settings['show_category'] == 'yes' or $settings['show_date'] == 'yes') : ?>
							<div class="upk-meta-list upk-flex upk-flex-middle">
								<?php $this->render_category(); ?>

								<?php if ( 'yes' === $settings['show_date'] or 'yes' === $settings['show_reading_time'] ) : ?>
								<div class="upk-flex upk-flex-middle upk-date-reading-wrap">

									<?php $this->render_date(); ?>
									
									<?php if (_is_upk_pro_activated()) :
										if ('yes' === $settings['show_reading_time']) : ?>
											<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
												<?php echo esc_html( ultimate_post_kit_reading_time( get_the_content(), $settings['avg_reading_speed'] ) ); ?>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</div>
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

		if ($settings['grid_style'] == '2') {
			$this->query_posts($settings['item_limit_2']['size']);
		} elseif ($settings['grid_style'] == '3') {
			$this->query_posts($settings['item_limit_3']['size']);
		} else {
			$this->query_posts($settings['item_limit']['size']);
		}

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->add_render_attribute('grid-wrap', 'class', 'upk-post-grid upk-ajax-grid-wrap');
		$this->add_render_attribute('grid-wrap', 'class', 'upk-style-' . $settings['grid_style']);
	
		$this->add_render_attribute(
			[
				'upk-elite-grid' => [
					'class' => 'upk-elite-grid upk-ajax-grid',
					'data-loadmore' => [
						wp_json_encode(
							array_filter([
								'loadmore_enable'  => $settings['ajax_loadmore_enable'],
								'loadmore_btn'     => $settings['ajax_loadmore_btn'],
								'infinite_scroll'  => $settings['ajax_loadmore_infinite_scroll'],
							])
						),
					],
				],
			]
		);		

		if ( $settings['ajax_loadmore_enable'] == 'yes' ) {
			$ajax_settings = [
				'posts_source'                  => isset( $settings['posts_source'] ) ? $settings['posts_source'] : 'post',
				'posts_per_page'                => isset( $posts_load ) ? $posts_load : 6,
				'ajax_item_load'                => isset( $settings['ajax_loadmore_items'] ) ? $settings['ajax_loadmore_items'] : 3,
				'posts_selected_ids'            => isset( $settings['posts_selected_ids'] ) ? $settings['posts_selected_ids'] : '',
				'posts_include_by'              => isset( $settings['posts_include_by'] ) ? $settings['posts_include_by'] : [],
				'posts_include_author_ids'      => isset( $settings['posts_include_author_ids'] ) ? $settings['posts_include_author_ids'] : '',
				'posts_include_term_ids'        => isset( $settings['posts_include_term_ids'] ) ? $settings['posts_include_term_ids'] : '',
				'posts_exclude_by'              => isset( $settings['posts_exclude_by'] ) ? $settings['posts_exclude_by'] : [],
				'posts_exclude_ids'             => isset( $settings['posts_exclude_ids'] ) ? $settings['posts_exclude_ids'] : '',
				'posts_exclude_author_ids'      => isset( $settings['posts_exclude_author_ids'] ) ? $settings['posts_exclude_author_ids'] : '',
				'posts_exclude_term_ids'        => isset( $settings['posts_exclude_term_ids'] ) ? $settings['posts_exclude_term_ids'] : '',
				'posts_offset'                  => isset( $settings['posts_offset'] ) ? $settings['posts_offset'] : 0,
				'posts_select_date'             => isset( $settings['posts_select_date'] ) ? $settings['posts_select_date'] : '',
				'posts_date_before'             => isset( $settings['posts_date_before'] ) ? $settings['posts_date_before'] : '',
				'posts_date_after'              => isset( $settings['posts_date_after'] ) ? $settings['posts_date_after'] : '',
				'posts_orderby'                 => isset( $settings['posts_orderby'] ) ? $settings['posts_orderby'] : 'date',
				'posts_order'                   => isset( $settings['posts_order'] ) ? $settings['posts_order'] : 'DESC',
				'posts_ignore_sticky_posts'     => isset( $settings['posts_ignore_sticky_posts'] ) ? $settings['posts_ignore_sticky_posts'] : 'no',
				'posts_only_with_featured_image'=> isset( $settings['posts_only_with_featured_image'] ) ? $settings['posts_only_with_featured_image'] : 'no',
				// Grid Settings
				'show_title'                    => isset( $settings['show_title'] ) ? $settings['show_title'] : 'yes',
				'title_tags'                    => isset( $settings['title_tags'] ) ? $settings['title_tags'] : 'h3',
				'show_excerpt'                  => isset( $settings['show_excerpt'] ) ? $settings['show_excerpt'] : 'yes',
				'show_author'                   => isset( $settings['show_author'] ) ? $settings['show_author'] : 'yes',
				'show_date'                     => isset( $settings['show_date'] ) ? $settings['show_date'] : 'yes',
				'show_time'                     => isset( $settings['show_time'] ) ? $settings['show_time'] : 'no',
				'show_category'                 => isset( $settings['show_category'] ) ? $settings['show_category'] : 'yes',
				'show_reading_time'             => isset( $settings['show_reading_time'] ) ? $settings['show_reading_time'] : 'no',
				'avg_reading_speed'             => isset( $settings['avg_reading_speed'] ) ? $settings['avg_reading_speed'] : 200,
				'upk_link_new_tab'              => isset( $settings['upk_link_new_tab'] ) ? $settings['upk_link_new_tab'] : 'no',
				'meta_separator'                => isset( $settings['meta_separator'] ) ? $settings['meta_separator'] : '|',
				'human_diff_time'               => isset( $settings['human_diff_time'] ) ? $settings['human_diff_time'] : 'no',
				'human_diff_time_short'         => isset( $settings['human_diff_time_short'] ) ? $settings['human_diff_time_short'] : 'no',
				'excerpt_length'                => isset( $settings['excerpt_length'] ) ? $settings['excerpt_length'] : 20,
				'title_style'                   => isset( $settings['title_style'] ) ? $settings['title_style'] : 'underline',
                'global_link'                   => isset( $settings['global_link'] ) ? $settings['global_link'] : 'no',
			];
		
			$this->add_render_attribute(
				[
					'upk-elite-grid' => [
						'data-settings' => [
							wp_json_encode( $ajax_settings ),
						],
					],
				]
			);
		}		

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>

		<div <?php $this->print_render_attribute_string('upk-elite-grid'); ?>>
			<div <?php $this->print_render_attribute_string('grid-wrap'); ?>>

				<?php while ($wp_query->have_posts()) :
					$wp_query->the_post();

					$thumbnail_size = $settings['primary_thumbnail_size'];

				?>

					<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']); ?>

				<?php endwhile; ?>
			</div>
		</div>

		<?php $this->render_ajax_loadmore(); ?>

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
