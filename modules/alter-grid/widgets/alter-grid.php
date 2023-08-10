<?php

namespace UltimatePostKit\Modules\AlterGrid\Widgets;

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

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Alter_Grid extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-alter-grid';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Alter Grid', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-alter-grid';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'alter'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-alter-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/lJdoW-aPAe8';
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
					'1' => esc_html__('Style 01', 'ultimate-post-kit'),
					'2' => esc_html__('Style 02', 'ultimate-post-kit'),
					'3' => esc_html__('Style 03', 'ultimate-post-kit'),
				],
			]
		);

		// if (_is_upk_pro_activated()) {
		// 	$column_size = 'grid-template-columns: repeat({{SIZE}}, 1fr);';
		// } else {
		// 	$column_size = '';
		// }

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
					'{{WRAPPER}} .upk-alter-grid .upk-style-1' => $column_size,
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
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'secondary_image_height',
			[
				'label'     => esc_html__('Secondary Item Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 100,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+2), {{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+3), {{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+4), {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+2) .upk-img-wrap .upk-main-img .upk-img, {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-img-wrap .upk-main-img .upk-img, {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-img-wrap .upk-main-img .upk-img, {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-img-wrap .upk-main-img .upk-img' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'grid_style' => ['2', '3']
				]
			]
		);

		$this->add_responsive_control(
			'secondary_image_width',
			[
				'label'     => esc_html__('Secondary Image Width', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 100,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+2) .upk-img-wrap .upk-main-img, {{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+3) .upk-img-wrap .upk-main-img, {{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+4) .upk-img-wrap .upk-main-img' => 'width: {{SIZE}}px;',
				],
				'condition' => [
					'grid_style' => ['2']
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
						'step' => 4
					],
				],
				'default' => [
					'size' => 4,
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
						'step' => 5
					],
				],
				'default' => [
					'size' => 5,
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
				'separator' => 'before'
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'label'       => __('Separator', 'ultimate-post-kit') . BDTUPK_NC,
				'type'        => Controls_Manager::TEXT,
				'default'     => '|',
				'label_block' => false,
			]
		);

		//Global Date Controls
		$this->register_date_controls();

		// if (_is_upk_pro_activated()) :
		// endif;
		//Global Reading Time Controls
		$this->register_reading_time_controls();

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
			'primary_meta_end_position',
			[
				'label'   => esc_html__('Primary Meta End Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'upk-primary-meta-end-position--',
			]
		);

		$this->add_control(
			'secondary_meta_end_position',
			[
				'label'   => esc_html__('Secondary Meta End Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'upk-secondary-meta-end-position--',
				'condition' => [
					'grid_style!' => '1'
				]
			]
		);

		$this->add_control(
			'readmore_type',
			[
				'label'   => esc_html__('Read More Type', 'ultimate-post-kit') . BDTUPK_PC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'     => esc_html__('None', 'ultimate-post-kit'),
					'classic'  => esc_html__('Classic', 'ultimate-post-kit'),
					'on_image' => esc_html__('On Image', 'ultimate-post-kit'),
				],
				'separator' => 'before',
				'classes'   => BDTUPK_IS_PC
			]
		);

		$this->add_control(
			'show_post_format',
			[
				'label'   => esc_html__('Post Format', 'ultimate-post-kit') . BDTUPK_NC,
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before'
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

		$this->start_controls_section(
			'section_content_readmore',
			[
				'label' => esc_html__('Read More', 'ultimate-post-kit') . BDTUPK_NC,
				'condition'   => [
					'readmore_type' => 'classic',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => esc_html__('Read More Text', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'ultimate-post-kit'),
				'placeholder' => esc_html__('Read More', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'readmore_icon',
			[
				'label'       => esc_html__('Icon', 'ultimate-post-kit'),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline'
			]
		);

		$this->add_control(
			'readmore_icon_align',
			[
				'label'   => esc_html__('Icon Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'right',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_icon_indent',
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
					'{{WRAPPER}} .upk-alter-grid .upk-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-alter-grid .upk-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __('Border', 'ultimate-post-kit'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-alter-grid .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item',
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
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item:hover',
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-item:hover' => 'border-color: {{VALUE}};'
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
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item:hover',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'secondary_title_typography',
				'label'     => esc_html__('Secondary Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+2) .upk-title, {{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+3) .upk-title, {{WRAPPER}} .upk-alter-grid .upk-style-2 .upk-item:nth-child(4n+4) .upk-title, {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+2) .upk-title, {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+3) .upk-title, {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+4) .upk-title, {{WRAPPER}} .upk-alter-grid .upk-style-3 .upk-item:nth-child(5n+5) .upk-title',
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
			'title_background',
			[
				'label'     => esc_html__('Background Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a' => 'background-color: {{VALUE}};',
				],
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
				'selector'  => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border',
				'selector'  => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'  => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-text-wrap .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-text-wrap .upk-text',
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-text-wrap .upk-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
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

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-meta .upk-blog-author a, {{WRAPPER}} .upk-alter-grid .upk-item .upk-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-meta .upk-blog-author a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-alter-grid .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-meta',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a:hover',
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
					'{{WRAPPER}} .upk-alter-grid .upk-item .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		if (_is_upk_pro_activated()) {
			$this->start_controls_section(
				'section_style_readmore',
				[
					'label'     => esc_html__('Read More', 'bdthemes-element-pack') . BDTUPK_NC,
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'readmore_type' => 'classic',
					],
				]
			);

			$this->start_controls_tabs('tabs_readmore_style');

			$this->start_controls_tab(
				'tab_readmore_normal',
				[
					'label' => esc_html__('Normal', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'readmore_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore' => 'color: {{VALUE}};',
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore svg *' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'readmore_background',
					'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'readmore_border',
					'label'       => esc_html__('Border', 'bdthemes-element-pack'),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore',
				]
			);

			$this->add_responsive_control(
				'readmore_border_radius',
				[
					'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'readmore_padding',
				[
					'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'readmore_shadow',
					'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'readmore_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_readmore_hover',
				[
					'label' => esc_html__('Hover', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'readmore_hover_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore:hover' => 'color: {{VALUE}};',
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore:hover svg *' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'readmore_hover_background',
					'selector' => '{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore:hover',
				]
			);

			$this->add_control(
				'readmore_hover_border_color',
				[
					'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'readmore_border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .upk-alter-grid .upk-item .upk-readmore:hover' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'readmore_hover_animation',
				[
					'label' => esc_html__('Animation', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_readmore_on_image',
				[
					'label'     => esc_html__('Read More On Image', 'bdthemes-element-pack') . BDTUPK_NC,
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'readmore_type' => 'on_image',
					],
				]
			);

			$this->add_control(
				'readmore_on_image_color',
				[
					'label'     => __('Color', 'ultimate-post-kit'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-alter-grid .upk-readmore-on-image .upk-readmore-icon:before, {{WRAPPER}} .upk-alter-grid .upk-item:hover .upk-readmore-on-image .upk-readmore-icon span:before, {{WRAPPER}} .upk-alter-grid .upk-item:hover .upk-readmore-on-image .upk-readmore-icon span:after' => 'background: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'item_image_overlay_color',
				[
					'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-alter-grid .upk-readmore-on-image:before' => 'background: linear-gradient(0deg, {{VALUE}} 0, rgba(141, 153, 174, 0.1) 100%);',
					],
				]
			);

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'section_style_post_format',
			[
				'label'     => esc_html__('Post Format', 'ultimate-post-kit') . BDTUPK_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_format' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_post_format_style');

		$this->start_controls_tab(
			'tab_post_format_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'post_format_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-format a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'post_format_background',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-post-format a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'post_format_border',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-post-format a',
			]
		);

		$this->add_responsive_control(
			'post_format_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-format a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_format_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-format a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_format_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-format a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'post_format_shadow',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-post-format a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_format_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-post-format a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_post_format_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'post_format_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-format a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'post_format_hover_background',
				'selector' => '{{WRAPPER}} .upk-alter-grid .upk-post-format a:hover',
			]
		);

		$this->add_control(
			'post_format_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'post_format_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alter-grid .upk-post-format a:hover' => 'border-color: {{VALUE}};',
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

	public function render_readmore() {
		$settings = $this->get_settings_for_display();

		$animation = ($this->get_settings('readmore_hover_animation')) ? ' elementor-animation-' . $this->get_settings('readmore_hover_animation') : '';

?>

		<a href="<?php echo esc_url(get_permalink()); ?>" class="upk-readmore upk-display-inline-block <?php echo esc_attr($animation); ?>">
			<?php echo esc_html($this->get_settings('readmore_text')); ?>

			<?php if ($settings['readmore_icon']['value']) : ?>
				<span class="upk-button-icon-align-<?php echo esc_attr($this->get_settings('readmore_icon_align')); ?>">
					<?php Icons_Manager::render_icon($settings['readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
				</span>
			<?php endif; ?>
		</a>
	<?php
	}

	public function render_readmore_on_image() {

	?>
		<a href="<?php echo esc_url(get_permalink()); ?>" class="upk-readmore-on-image">
			<span class="upk-readmore-icon"><span></span></span>
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
			<div class="upk-item-box">
				<div class="upk-img-wrap">
					<div class="upk-main-img">
						<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
						<?php if (_is_upk_pro_activated()) : ?>
							<?php if ($settings['readmore_type'] == 'on_image') : ?>
								<?php $this->render_readmore_on_image(); ?>
							<?php endif; ?>
						<?php endif; ?>
						<?php $this->render_post_format(); ?>
					</div>
				</div>

				<div class="upk-content">
					<div>

						<?php $this->render_category(); ?>

						<?php $this->render_title(substr($this->get_name(), 4)); ?>
						<div class="upk-text-wrap">
							<?php $this->render_excerpt($excerpt_length); ?>
							<?php if (_is_upk_pro_activated()) : ?>
								<?php if ($settings['readmore_type'] == 'classic') : ?>
									<?php $this->render_readmore(); ?>
								<?php endif; ?>
							<?php endif; ?>
						</div>

						<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
							<div class="upk-meta">
								<?php if ($settings['show_author']) : ?>
									<div class="upk-blog-author" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
										<a class="author-name" href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
											<?php echo get_the_author() ?>
										</a>
									</div>
								<?php endif; ?>

								<?php if ('yes' === $settings['show_date']) : ?>
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

		$this->add_render_attribute('grid-wrap', 'class', 'upk-post-grid');
		$this->add_render_attribute('grid-wrap', 'class', 'upk-style-' . $settings['grid_style']);

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

	?>

		<div class="upk-alter-grid">
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
