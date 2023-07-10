<?php

namespace UltimatePostKit\Modules\PholoxSlider\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Plugin;
use UltimatePostKit\Utils;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Pholox_Slider extends Group_Control_Query
{
	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name()
	{
		return 'upk-pholox-slider';
	}

	public function get_title()
	{
		return esc_html__('Pholox Slider', 'ultimate-post-kit');
	}

	public function get_icon()
	{
		return 'upk-widget-icon upk-icon-pholox-slider';
	}

	public function get_categories()
	{
		return ['ultimate-post-kit'];
	}

	public function get_keywords()
	{
		return ['pholox', 'slider', 'post', 'blog', 'recent', 'news', 'soft', 'video', 'gallery', 'youtube'];
	}

	public function get_style_depends()
	{
		if ($this->upk_is_edit_mode()) {
			return ['elementor-icons-fa-solid', 'upk-all-styles'];
		} else {
			return ['elementor-icons-fa-solid', 'upk-pholox-slider'];
		}
	}

	public function get_script_depends()
	{
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-pholox-slider'];
		}
	}

	public function get_custom_help_url()
	{
		return 'https://youtu.be/Et_-wFgOn0M';
	}

	public function get_query()
	{
		return $this->_query;
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__('Layout', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label'     => __('Content Max Width', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'       => __('Content Position', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'default' 	  => 'left',
				'options'     => [
					'left'  => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'      => false,
				'label_block' => false,
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'       => __('Text Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left'  => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'      => true,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'primary_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'full',
				'separator' => 'after',
			]
		);

		//Global Title Controls

		$this->register_title_controls();

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'   => esc_html__('Author', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_meta',
			[
				'label'   => esc_html__('Meta', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
			'thumb_show_category',
			[
				'label'   => esc_html__('Thumbs Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'thumb_show_title',
			[
				'label'   => esc_html__('Thumbs Title', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'thumb_title_tags',
			[
				'label'     => __('Thumbs Title HTML Tag', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => ultimate_post_kit_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->end_controls_section();
		//New Query Builder Settings
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
				'label'     => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 20,
						'step' => 5
					],
				],
				'default'   => [
					'size' => 5,
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

		$this->start_controls_section(
			'style_content_section',
			[
				'label' => esc_html__('Slider', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'main_slider_image_heading',
			[
				'label'     => esc_html__('Image', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'main_slider_image_border',
				'selector'    => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-img-wrap',
			]
		);

		$this->add_responsive_control(
			'main_slider_image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-img-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->start_controls_tabs(
			'style_slider_tabs'
		);

		$this->start_controls_tab(
			'style_main_slider_items_tab',
			[
				'label' => esc_html__('Items', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_background',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-content',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'content_border',
				'selector'    => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-content',
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-content',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_main_slider_play_button_tab',
			[
				'label' => esc_html__('Play Button', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'play_button_size',
			[
				'label'     => __('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 40,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-play-btn a' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'play_button_icon_size',
			[
				'label'     => __('Icon Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-play-btn' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'play_button_color',
			[
				'label'     => __('Icon Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-play-btn a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'play_button_bg',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-play-btn a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'play_button_border',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-play-btn a',
			]
		);

		$this->add_control(
			'main_slider_play_button_hover_heading',
			[
				'label'     => esc_html__('Hover', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'play_button_color_hover',
			[
				'label'     => __('Icon Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-main-slider .upk-play-btn:hover a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'play_button_bg_hover',
				'selector'  => '{{WRAPPER}} .upk-main-slider .upk-play-btn:hover a',
			]
		);

		$this->add_control(
			'play_button_border_color_hover',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-main-slider .upk-play-btn:hover a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'play_button_border_border!' => ''
				]
			]
		);

		$this->end_controls_tab();
		// main slider title tab
		$this->start_controls_tab(
			'style_main_slider_title_tab',
			[
				'label' => esc_html__('Title', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-title',
			]
		);

		$this->add_responsive_control(
			'title_bottom_spacing',
			[
				'label' => esc_html__('Bottom Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		//main slider category tab

		$this->start_controls_tab(
			'style_main_slider_category_tab',
			[
				'label' => esc_html__('Category', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'category_bottom_spacing',
			[
				'label' => esc_html__('Bottom Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing',
			[
				'label' => esc_html__('Spacing Between', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a',
			]
		);

		$this->add_control(
			'main_slider_category_hover_heading',
			[
				'label'     => esc_html__('Hover', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a:hover',
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
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		// main slider meta tab

		$this->start_controls_tab(
			'style_main_slider_meta_tab',
			[
				'label' => esc_html__('Meta', 'ultimate-post-kit'),
			]
		);


		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-meta, {{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-author a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-author a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-main-slider .upk-meta',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// start thumbnail style

		$this->start_controls_section(
			'style_playlist',
			[
				'label' => esc_html__('Thumbs', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_thumbs_slider_tabs'
		);

		// thumbs slider item

		$this->start_controls_tab(
			'tab_thumbs_slider_item',
			[
				'label' => __('Item', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'thumbs_item_background',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'thumbs_item_width_border',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-item',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumbs_item_width_border_radius',
			[
				'label'		 => __('Border Radius', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_item_padding',
			[
				'label'		 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_item_margin',
			[
				'label'		 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbs_slider_active_line',
			[
				'label'     => esc_html__('Line', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'thumbs_line_color',
			[
				'label'     => __('Line Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-item:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_line_height',
			[
				'label'   => esc_html__('Line Height', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-item:after' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		// thumbs slider image tab

		$this->start_controls_tab(
			'style_thumbs_slide_image_tab',
			[
				'label' => esc_html__('Image', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'playlist_thumb_width',
			[
				'label'   => esc_html__('Width', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 60,
						'max' => 100,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-img-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'playlist_thumb_width_border',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-img-wrap',
			]
		);

		$this->add_responsive_control(
			'playlist_thumb_width_border_radius',
			[
				'label'		 => __('Border Radius', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-img-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		// thumbs slider play button tab

		$this->start_controls_tab(
			'style_thumbs_slider_play-button_tab',
			[
				'label' => esc_html__('Play Button', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'playlist_play_button_size',
			[
				'label'   => esc_html__('Play Button Size', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 12,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-play-btn a' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'playlist_play_icon_size',
			[
				'label'   => esc_html__('Play Icon Size', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 8,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-play-btn' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'play_button_thumbs_color',
			[
				'label'     => __('Icon Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-play-btn a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'play_button_thumbs_bg',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-play-btn a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'play_button_thumbs_border',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-play-btn a',
			]
		);

		$this->add_responsive_control(
			'play_button_thumbs_border_radius',
			[
				'label'		 => __('Border Radius', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-play-btn a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbs_slider_play_butoon_hover',
			[
				'label'     => esc_html__('Hover', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'play_button_thumbs_color_hover',
			[
				'label'     => __('Icon Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-thumbs-slider .upk-play-btn:hover a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'play_button_thumbs_bg_hover',
				'selector'  => '{{WRAPPER}} .upk-thumbs-slider .upk-play-btn:hover a',
			]
		);

		$this->add_control(
			'play_button_thumbs_border_color_hover',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-thumbs-slider .upk-play-btn:hover a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'play_button_thumbs_border_border!' => ''
				]
			]
		);


		$this->end_controls_tab();

		// thumbs slider title tab

		$this->start_controls_tab(
			'style_thumbs_slider_title_tab',
			[
				'label' => esc_html__('Title', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'playlist_title_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'playlist_title_typo',
				'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-title',
			]
		);

		$this->add_control(
			'playlist_title_color_hover',
			[
				'label'     => __('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-thumbs-slider .upk-content:hover .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'playlist_title_color_active',
			[
				'label'     => __('Active Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .swiper-slide-active .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// thumbs slider thumbs tab

		$this->start_controls_tab(
			'style_thumbs_slider_category_tab',
			[
				'label' => esc_html__('Category', 'ultimate-post-kit'),
			]
		);

		// $this->add_control(
		// 	'playlist_category_color',
		// 	[
		// 		'label'     => __('Color', 'ultimate-post-kit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a' => 'color: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => 'playlist_category_typo',
		// 		'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a',
		// 	]
		// );

		// $this->add_control(
		// 	'playlist_category_color_hover',
		// 	[
		// 		'label'     => __('Hover Color', 'ultimate-post-kit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider:hover .upk-category a' => 'color: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_control(
		// 	'playlist_category_color_active',
		// 	[
		// 		'label'     => __('Active Color', 'ultimate-post-kit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider  .swiper-slide-active .upk-category a' => 'color: {{VALUE}};',
		// 		],
		// 	]
		// );


		// category control thumbs start
			
		$this->add_responsive_control(
			'playlist_category_bottom_spacing',
			[
				'label' => esc_html__('Bottom Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'playlist_category_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'playlist_category_background',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'playlist_category_border',
				'selector'    => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a',
			]
		);

		$this->add_responsive_control(
			'playlist_category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'playlist_category_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'playlist_category_spacing',
			[
				'label' => esc_html__('Spacing Between', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'playlist_category_shadow',
				'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'playlist_category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a',
			]
		);

		$this->add_control(
			'thumbs_slider_category_hover_heading',
			[
				'label'     => esc_html__('Hover', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'playlist_category_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'playlist_category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a:hover',
			]
		);

		$this->add_control(
			'playlist_category_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-pholox-slider .upk-thumbs-slider .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
		// category control thumbs end


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Main query render for this widget
	 * @param $posts_per_page number item query limit
	 */
	public function query_posts($posts_per_page)
	{

		$default = $this->getGroupControlQueryArgs();
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
		}

		$args         = array_merge($default, $args);
		$this->_query = new WP_Query($args);
	}

	public function render_image($image_id, $size)
	{
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

	public function render_playlist_title()
	{
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('thumb_show_title')) {
			return;
		}

		printf('<%1$s class="upk-title"><a href="%2$s" title="%3$s">%3$s</a></%1$s>', Utils::get_valid_html_tag($settings['title_tags']), 'javascript:void(0);', get_the_title());
	}


	function thumbs_render_category()
	{
		if (!$this->get_settings('thumb_show_category')) {
			return;
		}
	?>
		<div class="upk-category">
			<?php echo upk_get_category($this->get_settings('posts_source')); ?>
		</div>
	<?php
	}


	public function render_title()
	{
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_title')) {
			return;
		}

		printf('<%1$s class="upk-title" data-swiper-parallax-x="100"><a href="%2$s" title="%3$s">%3$s</a></%1$s>', Utils::get_valid_html_tag($settings['title_tags']), get_permalink(), get_the_title());
	}


	public function render_date()
	{
		$settings = $this->get_settings_for_display();


		if (!$this->get_settings('show_date')) {
			return;
		}

	?>
		<div class="upk-flex upk-flex-middle">
			<div class="upk-date">
				<i class="eicon-calendar upk-author-icon" aria-hidden="true"></i>
				<span>
					<?php if ($settings['human_diff_time'] == 'yes') {
						echo ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
					} else {
						echo get_the_date();
					} ?>
				</span>
			</div>
			<?php if ($settings['show_time']) : ?>
				<div class="upk-post-time">
					<i class="upk-icon-clock" aria-hidden="true"></i>
					<?php echo get_the_time(); ?>
				</div>
			<?php endif; ?>
		</div>

	<?php
	}

	public function render_author()
	{

		if (!$this->get_settings('show_author')) {
			return;
		}
	?>
		<div class="upk-author">
			<i class="eicon-user-circle-o upk-author-icon" aria-hidden="true"></i>
			<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>"><?php echo get_the_author() ?></a>
		</div>

	<?php
	}

	public function render_excerpt($excerpt_length)
	{

		if (!$this->get_settings('show_excerpt')) {
			return;
		}
		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');
	?>
		<div class="upk-text">
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




	public function video_link_render($video)
	{
		$youtube_id = (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $match)) ? $match[1] : false;

		$vimeo_id = (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $video, $match)) ? $match[3] : false;

		if ($youtube_id) {
			$video_source    = 'https://www.youtube.com/embed/' . $youtube_id;
		} elseif ($vimeo_id) {
			$video_source    = 'https://vimeo.com/' . $vimeo_id;
		} else {
			$video_source = false;
		}
		return $video_source;
	}

	public function render_playlist_item($post_id, $image_size, $video_link)
	{
		$video_link = $this->video_link_render($video_link);
	?>
		<div class="upk-item swiper-slide">
			<div class="upk-img-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
				<?php if ($video_link !== false) : ?>
					<div class="upk-play-btn">
						<a href="javascript:void(0);" data-src="<?php echo esc_url($video_link); ?>">
							<i class="fas fa-play"></i>
						</a>
					</div>
				<?php endif; ?>
			</div>
			<div class="upk-content">
				<?php $this->thumbs_render_category(); ?>
				<?php $this->render_playlist_title(); ?>
			</div>
		</div>
	<?php
	}

	public function render_item($post_id, $image_size, $video_link)
	{
		$settings = $this->get_settings_for_display();

		if ($video_link !== false) {
			$video_link = $this->video_link_render($video_link);
		}
	?>
		<div class="upk-item swiper-slide">
			<div class="upk-img-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
				<?php if ($video_link !== false) : ?>
					<div class="upk-play-btn">
						<a class="upk-pholox-video-trigger" data-src="<?php echo esc_url($video_link); ?>" href="javascript:void(0);">
							<i class="upk-play-btn fas fa-play"></i>
						</a>
					</div>
					<div class="upk-video-wrap">
						<iframe src="" class="upk-video-iframe" allow="autoplay;">
						</iframe>
					</div>
				<?php endif; ?>
			</div>
			<div class="upk-content">
				<div data-swiper-parallax="-350" data-swiper-parallax-duration="800">
					<?php $this->render_category(); ?>
				</div>
				<div data-swiper-parallax="-350" data-swiper-parallax-duration="900">
					<?php $this->render_title(substr($this->get_name(), 4));  ?>
				</div>

				<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
				<div class="upk-meta" data-swiper-parallax="-350" data-swiper-parallax-duration="1000">
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

			</div>
		</div>
	<?php
	}


	public function render_header()
	{
		$settings = $this->get_settings_for_display();

		$id              = 'upk-pholox-slider-' . $this->get_id();
		$settings        = $this->get_settings_for_display();

		$this->add_render_attribute('pholox-slide', 'id', $id);
		$this->add_render_attribute('pholox-slide', 'class', [
			'upk-pholox-slider',
			'upk-pholox-slide-style-' . $settings['content_position']
		]);

		$this->add_render_attribute(
			[
				'pholox-slide' => [
					'data-widget' => [
						wp_json_encode(array_filter([
							"id" => '#' . $id
						]))
					],
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
							"loop"           => ($settings["loop"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"effect"         => 'fade',
							"fadeEffect"     => ['crossFade' => true],
							"parallax"       => true,
							"grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
							"pauseOnHover"   => true,
							"slidesPerView"  => 1,
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"loopedSlides" => 4,
							"lazy" => [
								"loadPrevNext"  => "true",
							],
						]))
					]
				]
			]
		);

	?>
		<div <?php $this->print_render_attribute_string('pholox-slide'); ?>>
		<?php
	}
	public function render_footer()
	{
		?>
		</div>
	<?php
	}
	public function render()
	{
		$settings = $this->get_settings_for_display();

		$this->query_posts($settings['item_limit']['size']);
		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);
		$this->add_render_attribute('swiper-thumbs', 'class', 'swiper-thumbs ' . $swiper_class);

		$this->render_header();
	?>
		<div class="upk-main-slider">
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div class="swiper-wrapper">
					<?php
					while ($wp_query->have_posts()) {
						$wp_query->the_post();
						$video_link = get_post_meta(get_the_ID(), '_upk_video_link_meta_key', true);
						$thumbnail_size = $settings['primary_thumbnail_size'];

						if ($video_link) {
							$this->render_item(get_the_ID(), $thumbnail_size, $video_link);
						} else {
							$this->render_item(get_the_ID(), $thumbnail_size, false);
						}
					}
					?>
				</div>
			</div>
		</div>

		<div class="upk-thumbs-slider">
			<div thumbsSlider="" <?php echo $this->get_render_attribute_string('swiper-thumbs'); ?>>
				<div class="swiper-wrapper">
					<?php
					while ($wp_query->have_posts()) {
						$wp_query->the_post();
						$video_link = get_post_meta(get_the_ID(), '_upk_video_link_meta_key', true);
						$thumbnail_size = $settings['primary_thumbnail_size'];
						if ($video_link) {
							$this->render_playlist_item(get_the_ID(), $thumbnail_size, $video_link);
						} else {
							$this->render_playlist_item(get_the_ID(), $thumbnail_size, false);
						}
					}

					?>
				</div>
			</div>
		</div>



<?php
		$this->render_footer();
		wp_reset_postdata();
	}
}
