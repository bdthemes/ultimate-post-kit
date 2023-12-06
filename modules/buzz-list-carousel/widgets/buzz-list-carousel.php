<?php

namespace UltimatePostKit\Modules\BuzzListCarousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Traits\Global_Swiper_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Buzz_List_Carousel extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;
	use Global_Swiper_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-buzz-list-carousel';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Buzz List Carousel', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-buzz-list-carousel';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'buzz', 'list'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-buzz-list-carousel'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-buzz-list-carousel'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/fxjL-ugL_Ls';
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
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => __('Column Gap', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
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
			'show_author',
			[
				'label'     => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
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
			'item_match_height',
			[
				'label'        => __('Item Match Height', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'upk-item-match-height--',
				'separator'    => 'before'
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

		$this->add_responsive_control(
			'content_vertical_align',
			[
				'label'   => esc_html__('Vertical Align', 'ultimate-post-kit') . BDTUPK_NC,
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start'  => [
						'title' => esc_html__('Top', 'ultimate-post-kit'),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => esc_html__('Bottom', 'ultimate-post-kit'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-content' => 'align-items: {{VALUE}};',
				],
				'separator' => 'before'
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

		//Navigaiton Global Controls
		$this->register_navigation_controls('buzz-list');

		//Style
		$this->start_controls_section(
			'upk_section_style',
			[
				'label' => esc_html__('Item', 'ultimate-post-kit'),
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-img-wrap::before' => 'background: {{VALUE}};'
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
				'selector'    => '{{WRAPPER}} .upk-buzz-list-carousel .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'overlay_background_hover',
			[
				'label'     => esc_html__('Overlay Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-img-wrap::before' => 'background: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'item_border_border!' => ''
				]
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-title a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'label'    => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-title a',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-meta, {{WRAPPER}} .upk-buzz-list-carousel .upk-meta .upk-author-wrap .upk-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-meta, {{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-meta .upk-author-wrap .upk-author-name' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-meta' => 'padding-top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-meta',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-category a',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-category a',
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
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-category a' => 'border-color: {{VALUE}};',
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
			]
		);

		$this->start_controls_tabs('tabs_counter_number_style');
		
		$this->start_controls_tab(
			'tab_counter_number_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'counter_number_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-count' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'counter_number_background',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-count',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'counter_number_border',
				'label'    => esc_html__('Border', 'ultimate-post-kit'),  
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-count',
			]
		);

		$this->add_responsive_control(
			'counter_number_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),  
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'counter_number_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),  
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'counter_number_shadow',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-count',
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'counter_number_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-count',
			]
		);

		$this->add_control(
			'counter_line_color',
			[
				'label'     => esc_html__('Line Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-inner' => 'border-left-color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'counter_number_spacing',
			[
				'label'     => esc_html__('Line Space Between', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-count' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-inner' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_tab();
		
		$this->start_controls_tab(
			'tab_counter_number_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'counter_number_hover_color',
			[
				'label'     => esc_html__('Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'counter_number_background_hover',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-count',
			]
		);

		$this->add_control(
			'counter_number_border_hover_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),  
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'counter_number_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-count' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'counter_number_shadow_hover',
				'selector' => '{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-count',
			]
		);

		$this->add_control(
			'counter_line_hover_color',
			[
				'label'     => esc_html__('Line Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-buzz-list-carousel .upk-item:hover .upk-inner' => 'border-left-color: {{VALUE}};',
				],
				'separator' => 'before'
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

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}
?>
		<div class="upk-author-wrap">
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

		<div class="upk-comments">
			<?php echo get_comments_number($id) ?>
			<?php echo esc_html_x('Comments', 'Frontend', 'ultimate-post-kit') ?>
		</div>

	<?php
	}

	public function render_header() {
		//Global Function
		$this->render_header_attribute('buzz-list');

	?>
		<div <?php $this->print_render_attribute_string('carousel'); ?>>
			<div class="upk-buzz-wrapper">
				<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
					<div class="swiper-wrapper">
					<?php
				}

				public function render_post_grid_item($post_id, $image_size, $slide_index) {
					$settings = $this->get_settings_for_display();

					if ('yes' == $settings['global_link']) {

						$this->add_render_attribute('list-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
					}
					$this->add_render_attribute('list-item', 'class', 'upk-item swiper-slide upk-flex upk-flex-middle', true);

					?>
						<div <?php $this->print_render_attribute_string('list-item'); ?>>
							<div class="upk-img-wrap">
								<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
							</div>
							<div class="upk-content">
								<div class="upk-count"><?php printf("%2d", $slide_index); ?></div>
								<div class="upk-inner">
									<?php $this->render_category(); ?>
									<?php $this->render_title(substr($this->get_name(), 4)); ?>
									<?php if ($settings['show_author'] or $settings['show_comments'] or $settings['show_date'] or $settings['show_reading_time']) : ?>
										<div class="upk-meta upk-flex upk-flex-middle">
											<?php if ($settings['show_author']) : ?>
												<?php $this->render_author(); ?>
											<?php endif; ?>

											<?php if ($settings['show_date']) : ?>
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

											<?php if ($settings['show_comments']) : ?>
												<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
													<?php $this->render_comments($post_id); ?>
												</div>
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

					$slide_index = 1;
					while ($wp_query->have_posts()) {
						$wp_query->the_post();
						$thumbnail_size = $settings['primary_thumbnail_size'];
						$this->render_post_grid_item(get_the_ID(), $thumbnail_size, $slide_index);
						$slide_index++;
					}

					wp_reset_postdata();

					$this->render_footer();
				}
			}
