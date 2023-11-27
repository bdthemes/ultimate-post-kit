<?php

namespace UltimatePostKit\Modules\HazelCarousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use UltimatePostKit\Utils;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Swiper_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Hazel_Carousel extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Swiper_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-hazel-carousel';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Hazel Carousel', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-hazel-carousel';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'hazel'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-hazel-carousel'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-hazel-carousel'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/N1f6AanD3gM';
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
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);

		$this->add_responsive_control(
			'default_item_height',
			[
				'label'     => esc_html__('Item Height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid .upk-post-grid-item' => 'height: {{SIZE}}px;',
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
			'global_link',
			[
				'label'        => __('Item Wrapper Link', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'upk-global-link-',
				'description'  => __('Be aware! When Item Wrapper Link activated then title link and read more link will not work', 'ultimate-post-kit'),
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//Navigaiton Global Controls
		$this->register_navigation_controls('hazel');

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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-blog-content-style-2 .upk-blog-box-content' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-blog-content-style-1 .upk-post-grid-item-box:before, {{WRAPPER}} .upk-hazel-carousel .upk-blog-content-style-2 .upk-blog-box-content, {{WRAPPER}} .upk-hazel-carousel .upk-blog-content-style-3 .upk-post-grid-item .upk-post-grid-item-box:before' => 'background-color: {{VALUE}};'
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
				'selector'    => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item',
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
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item:hover' => 'border-color: {{VALUE}};'
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
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item:hover',
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
				'default'     => [
					'size' => 10
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
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title-wrap',
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
				'selector'  => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title',
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
				'selector'  => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border',
				'selector'  => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'  => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-title-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'  => '{{WRAPPER}} .upk-hazel-carousel .upk-blog-content-style-3 .upk-blog-box-content .upk-meta',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_hover_color',
			[
				'label'     => esc_html__('Hover Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-meta .upk-blog-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'date_divider_color',
		// 	[
		// 		'label'     => esc_html__('Divider Color', 'ultimate-post-kit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-meta .upk-blog-date::before' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-meta',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge span' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a:hover',
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
					'{{WRAPPER}} .upk-hazel-carousel .upk-post-grid-item .upk-post-grid-item-box .upk-blog-box-content .upk-blog-badge a:hover' => 'border-color: {{VALUE}};',
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

		printf('<%1$s class="upk-blog-title-wrap"><a href="%2$s" title="%3$s" class="upk-blog-title">%3$s</a></%1$s>', Utils::get_valid_html_tag($settings['title_tags']), get_permalink(), get_the_title());
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}

	?>
		<div class="upk-blog-author">
			<span class="by"><?php echo esc_html('by', 'ultimate-post-kit') ?></span>
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

	public function render_header() {
		$settings = $this->get_settings_for_display();

		//Global Function
		$this->render_header_attribute('hazel');

	?>
		<div <?php $this->print_render_attribute_string('carousel'); ?>>
			<div class="upk-post-grid upk-pg-text-position-<?php echo esc_html($settings['content_position']) ?> upk-blog-content-style-<?php echo esc_html($settings['content_style']) ?>">
				<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
					<div class="swiper-wrapper">
					<?php
				}

				public function render_post($post_id, $image_size) {
					$settings = $this->get_settings_for_display();
					global $post;

					if ('yes' == $settings['global_link']) {

						$this->add_render_attribute('grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
					}
					$this->add_render_attribute('grid-item', 'class', 'upk-post-grid-item swiper-slide upk-transition-toggle', true);

					?>
						<div <?php $this->print_render_attribute_string('grid-item'); ?>>
							<div class="upk-post-grid-item-box">
								<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>

								<div class="upk-blog-box-content">
									<div class="upk-cetagory">
										<?php $this->render_category(); ?>
									</div>

									<div class="upk-blog-title-wrapper">
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

				public function render() {
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

						$this->render_post(get_the_ID(), $thumbnail_size);
					}

					wp_reset_postdata();

					$this->render_footer();
				}
			}
