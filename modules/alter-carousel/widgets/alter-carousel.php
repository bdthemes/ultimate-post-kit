<?php

namespace UltimatePostKit\Modules\AlterCarousel\Widgets;

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
use UltimatePostKit\Traits\Global_Swiper_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Alter_Carousel extends Group_Control_Query
{

	use Global_Widget_Controls;
	use Global_Widget_Functions;
	use Global_Swiper_Functions;

	private $_query = null;

	public function get_name()
	{
		return 'upk-alter-carousel';
	}

	public function get_title()
	{
		return BDTUPK . esc_html__('Alter Carousel', 'ultimate-post-kit');
	}

	public function get_icon()
	{
		return 'upk-widget-icon upk-icon-alter-carousel';
	}

	public function get_categories()
	{
		return ['ultimate-post-kit'];
	}

	public function get_keywords()
	{
		return ['post', 'carousel', 'blog', 'recent', 'news', 'alter'];
	}

	public function get_style_depends()
	{
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-alter-carousel'];
		}
	}

	public function get_script_depends()
	{
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-alter-carousel'];
		}
	}

	public function get_custom_help_url()
	{
		return 'https://youtu.be/KInlL05e_lk';
	}

	public function get_query()
	{
		return $this->_query;
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'ultimate-post-kit'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options'        => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => __('Item Gap', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'tablet_default' => [
					'size' => 20,
				],
				'mobile_default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
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

		$this->add_responsive_control(
			'default_image_height',
			[
				'label' => esc_html__('Image Height(px)', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-img-wrap .upk-main-img' => 'height: {{SIZE}}px;',
				],
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
			'item_match_height',
			[
				'label'        => __('Item Match Height', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'upk-item-match-height--',
				'separator'    => 'before',
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'meta_end_position',
			[
				'label'   => esc_html__('Meta End Position', 'ultimate-post-kit') . BDTUPK_NC,
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'upk-meta-end-position--',
				'condition' => [
					'item_match_height' => 'yes'
				]
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
			'global_link',
			[
				'label'        => __('Item Wrapper Link', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'upk-global-link-',
				'description'  => __('Be aware! When Item Wrapper Link activated then title link and read more link will not work', 'ultimate-post-kit'),
				'separator' => 'before',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-alter-carousel .upk-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Navigaiton Global Controls
		$this->register_navigation_controls('alter');

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
				'label' 	 => __('Content Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-carousel .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __('Border', 'ultimate-post-kit'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-alter-carousel .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-carousel .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-item',
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
				'name'     => 'itam_background_hover',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-item:hover',
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-item:hover' => 'border-color: {{VALUE}};'
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
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-item:hover',
			]
		);

		$this->add_responsive_control(
			'item_shadow_padding',
			[
				'label'       => __('Match Padding', 'ultimate-post-kit'),
				'description' => __('You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'selectors'   => [
					'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
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
					'{{WRAPPER}} .upk-alter-carousel .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-title a',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-title a' => 'background-color: {{VALUE}};',
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
				'selector'  => '{{WRAPPER}} .upk-alter-carousel .upk-title a',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border',
				'selector'  => '{{WRAPPER}} .upk-alter-carousel .upk-title a',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'title_box_shadow',
				'selector'  => '{{WRAPPER}} .upk-alter-carousel .upk-title a',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
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
					'{{WRAPPER}} .upk-alter-carousel .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-text',
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label' 	 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-carousel .upk-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_author',
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
			'author_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-meta .upk-blog-author a, {{WRAPPER}} .upk-alter-carousel .upk-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-meta .upk-blog-author a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-meta',
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
				'label' => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-alter-carousel .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-carousel .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing_between',
			[
				'label' => esc_html__('Space Between', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-alter-carousel .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-category a',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-category a:hover',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

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
					'{{WRAPPER}} .upk-alter-carousel .upk-post-format a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'post_format_background',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-post-format a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'post_format_border',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-post-format a',
			]
		);

		$this->add_responsive_control(
			'post_format_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-alter-carousel .upk-post-format a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-post-format a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-post-format a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'post_format_shadow',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-post-format a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_format_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-post-format a',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-post-format a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'post_format_hover_background',
				'selector' => '{{WRAPPER}} .upk-alter-carousel .upk-post-format a:hover',
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
					'{{WRAPPER}} .upk-alter-carousel .upk-post-format a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Global Controls
		$this->register_navigation_style('swiper');
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

	public function render_header()
	{
		//Global Function
		$this->render_header_attribute('alter');

?>
		<div <?php $this->print_render_attribute_string('carousel'); ?>>
			<div class="upk-post-grid">
				<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
					<div class="swiper-wrapper">
					<?php
				}

				public function render_post_grid_item($post_id, $image_size, $excerpt_length)
				{
					$settings = $this->get_settings_for_display();

					if ('yes' == $settings['global_link']) {

						$this->add_render_attribute('grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
					}
					$this->add_render_attribute('grid-item', 'class', 'upk-item swiper-slide', true);

					?>
						<div <?php $this->print_render_attribute_string('grid-item'); ?>>
							<div class="upk-item-box">
								<div class="upk-img-wrap">
									<div class="upk-main-img">
										<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
										<?php
										if (_is_upk_pro_activated()) {
											if ($settings['readmore_type'] == 'on_image') {
												apply_filters('alter_render_readmore_on_image', '');
											}
										}
										?>
										<?php $this->render_post_format(); ?>
									</div>
								</div>

								<div class="upk-content">
									<div>
										<?php $this->render_category(); ?>
										<?php $this->render_title(substr($this->get_name(), 4)); ?>

										<div class="upk-text-wrap">
											<?php $this->render_excerpt($excerpt_length); ?>
											<?php
											if (_is_upk_pro_activated()) {
												if ($settings['readmore_type'] == 'classic') {
													apply_filters('alter_render_readmore', $this);
												}
											}
											?>
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

				public function render()
				{
					$settings = $this->get_settings_for_display();

					$this->query_posts($settings['item_limit']['size']);
					$wp_query = $this->get_query();

					if (!$wp_query->found_posts) {
						return;
					}

					$this->render_header();

					while ($wp_query->have_posts()) {
						$wp_query->the_post();
						$thumbnail_size = $settings['primary_thumbnail_size'];

						$this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']);
					}

					$this->render_footer();

					wp_reset_postdata();
				}
			}
