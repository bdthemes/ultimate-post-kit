<?php

namespace UltimatePostKit\Modules\GratisGrid\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;

use UltimatePostKit\Utils;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Gratis_Grid extends Group_Control_Query
{

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name()
	{
		return 'upk-gratis-grid';
	}

	public function get_title()
	{
		return BDTUPK . esc_html__('Gratis Grid', 'ultimate-post-kit');
	}

	public function get_icon()
	{
		return 'upk-widget-icon upk-icon-gratis-grid';
	}

	public function get_categories()
	{
		return ['ultimate-post-kit'];
	}

	public function get_keywords()
	{
		return ['post', 'grid', 'blog', 'recent', 'news', 'gratis'];
	}

	public function get_style_depends()
	{
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-gratis-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/jLBLAqxEOwQ';
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
					'{{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap' => $column_size,
				],
				// 'condition' => [
				// 	'grid_style' => ['1']
				// ],
				'classes' => BDTUPK_IS_PC
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Row Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		// item height control
		$this->add_responsive_control(
			'item_height',
			[
				'label' => __('Item Height', 'ultimate-post-kit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 400,
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-img-wrap' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
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
            'active_item',
            [
                'label' => __('Active Item', 'bdthemes-element-pack') . BDTUPK_PC,
                'type' => Controls_Manager::NUMBER,
                'default' => 2,
                'description' => __('Be more creative with your design by typing in your item number.', 'ultimate-post-kit'),
                'separator' => 'before',
                'classes' => BDTUPK_IS_PC,
            ]
        );

		$this->end_controls_section();

		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'ultimate-post-kit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'item_limit',
			[
				'label'     => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 20,
					],
				],
				'default'   => [
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
				'label'   => esc_html__('Show Category', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_meta',
			[
				'label'     => esc_html__('Show Meta', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_readmore',
			[
				'label' => esc_html__('Read more', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => __('Readmore Text', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Explore', 'ultimate-post-kit'),
				'label_block' => false,
				'condition' => [
					'show_readmore' => 'yes'
				]
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
				'default'     => '/',
				'label_block' => false,
			]
		);

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
				'label' => esc_html__('Normal', 'pixel-gallery'),
			]
		);

		$this->add_control(
			'overlay_type',
			[
				'label'   => esc_html__('Overlay', 'pixel-gallery'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'background',
				'options' => [
					'none'       => esc_html__('None', 'pixel-gallery'),
					'background' => esc_html__('Background', 'pixel-gallery'),
					'blend'      => esc_html__('Blend', 'pixel-gallery'),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_color',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-img-wrap::before',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => '#fff',
					],
				],
				'condition' => [
					'overlay_type' => ['background', 'blend'],
				],
			]
		);

		$this->add_control(
			'blend_type',
			[
				'label'     => esc_html__('Blend Type', 'pixel-gallery'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multiply',
				'options'   => ultimate_post_kit_blend_options(),
				'condition' => [
					'overlay_type' => 'blend',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-img-wrap::before' => 'mix-blend-mode: {{VALUE}};'
				],
			]
		);

		//border
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'label'    => esc_html__('Border', 'pixel-gallery'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item',
				'separator' => 'before'
			]
		);
		//border radius
		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'pixel-gallery'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		//padding
		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__('Padding', 'pixel-gallery'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-gratis-grid .upk-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		//box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'label'    => esc_html__('Box Shadow', 'pixel-gallery'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item',
			]
		);
		 

		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'pixel-gallery'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_hover_color',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-img-wrap::after',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Overlay Color', 'pixel-gallery'),
						'default' => 'gradient',
					],
					'color' => [
						'default' => 'rgba(32, 34, 37, 0.81)',
					],
					'color_b' => [
						'default' => 'rgba(0, 0, 0, 0)',
					],
					'gradient_type' => [
						'default' => 'linear',
					],
					'gradient_angle' => [
						'default' => [
							'unit' => 'deg',
							'size' => 25,
						],
					],
				],
			]
		);
		//border color
		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'pixel-gallery'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'item_border_border!' => '',
				],
			]
		);
		//box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_hover',
				'label'    => esc_html__('Box Shadow', 'pixel-gallery'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item:hover',
			]
		);
		
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_item_active',
			[
				'label' => esc_html__('Active', 'pixel-gallery'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_active_color',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-img-wrap::after',
			]
		);
		//border color
		$this->add_control(
			'item_border_color_active',
			[
				'label'     => esc_html__('Border Color', 'pixel-gallery'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item.active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'item_border_border!' => '',
				],
				'separator' => 'before'
			]
		);
		//box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_active',
				'label'    => esc_html__('Box Shadow', 'pixel-gallery'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item.active',
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
					'{{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap .upk-title a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap .upk-item.active .upk-title a:hover, {{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap .upk-item:hover .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_item_hover_color',
			[
				'label'     => esc_html__('Item Hover & Active Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap .upk-item.active .upk-title a, {{WRAPPER}} .upk-gratis-grid .upk-gratis-wrap .upk-item:hover .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		//margin
		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .upk-gratis-grid .upk-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'label'    => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'      => esc_html__('Meta', 'ultimate-post-kit'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-meta, {{WRAPPER}} .upk-gratis-grid .upk-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-author-name:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_item_hover_active_color',
			[
				'label'     => esc_html__('Item Hover & Active Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-meta, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-meta, {{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-author-name, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_icon_size',
			[
				'label'     => esc_html__('Icon Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-meta svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
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
					'{{WRAPPER}} .upk-gratis-grid .upk-meta' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-meta',
			]
		);

		$this->add_control(
			'line_heading',
			[
				'label'     => __('LINE', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'meta_line_height_size',
			[
				'label'     => esc_html__('Line Height', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-line' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'line_border_color',
			[
				'label'     => esc_html__('Line Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-line' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'line_border_hover_active_color',
			[
				'label'     => esc_html__('Hover / Active Line Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-item.active .upk-gratis-line, {{WRAPPER}} .upk-item:hover .upk-gratis-line' => 'background-color: {{VALUE}};',
				],
			]
		);

		//margin control
		$this->add_responsive_control(
			'line_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .upk-gratis-grid .upk-gratis-line' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .upk-gratis-grid .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_background',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-gratis-grid .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-gratis-grid .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-gratis-grid .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-category a',
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
					'{{WRAPPER}} .upk-gratis-grid .upk-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_background',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-category a:hover',
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
					'{{WRAPPER}} .upk-gratis-grid .upk-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_category_item_hover',
			[
				'label' => esc_html__('Item Hover & Active', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'category_item_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-category a, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'category_hover_item_background',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-category a, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-category a',
			]
		);

		$this->add_control(
			'category_item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-category a, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-category a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_link_button',
			[
				'label'     => esc_html__('Read More', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_readmore' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_link_button_style');

		$this->start_controls_tab(
			'tab_link_button_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'link_button_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-link-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'link_button_background',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-link-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'link_button_border',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-link-button a',
			]
		);

		$this->add_responsive_control(
			'link_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-gratis-grid .upk-link-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'link_button_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-gratis-grid .upk-link-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'link_button_shadow',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-link-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_button_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-link-button a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_button_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'link_button_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item .upk-link-button a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'link_button_hover_background',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item .upk-link-button a::before',
			]
		);

		$this->add_control(
			'link_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'link_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item .upk-link-button a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_button_item_hover',
			[
				'label' => esc_html__('Item Hover & Active', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'link_button_item_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-link-button a, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-link-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'link_button_item_hover_background',
				'selector' => '{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-link-button a, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-link-button a',
			]
		);

		$this->add_control(
			'link_button_item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'link_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-gratis-grid .upk-item.active .upk-link-button a, {{WRAPPER}} .upk-gratis-grid .upk-item:hover .upk-link-button a' => 'border-color: {{VALUE}};',
				],
			]
		);

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

	public function render_category()
	{

		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-category">
			<?php echo upk_get_category($this->get_settings('posts_source')); ?>
		</div>
	<?php
	}

	public function render_date()
	{
		$settings = $this->get_settings_for_display();


		if (!$this->get_settings('show_date')) {
			return;
		}

	?>
		<div class="upk-date">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
				<path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
			</svg>
			<?php if ($settings['human_diff_time'] == 'yes') {
				echo ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
			} else {
				echo get_the_date();
			} ?>
		</div>

		<?php if ($settings['show_time']) : ?>
			<div class="upk-post-time">
				<i class="upk-icon-clock" aria-hidden="true"></i>
				<?php echo get_the_time(); ?>
			</div>
		<?php endif; ?>

	<?php
	}

	public function render_author()
	{

		if (!$this->get_settings('show_meta')) {
			return;
		}
	?>

		<div class="upk-author">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
				<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
				<path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
			</svg>
			<span><?php echo esc_html('by', 'ultimate-post-kit') ?></span>
			<a class="upk-author-name" href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>">
				<span><?php echo get_the_author() ?></span>
			</a>
		</div>


	<?php
	}

	public function render_post_grid_item($post_id, $image_size, $active_item) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}

		$this->add_render_attribute('grid-item', 'class', 'upk-item ' . $active_item, true);

		?>
		<div <?php $this->print_render_attribute_string('grid-item'); ?>>
			<div class="upk-img-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
			</div>

			<div class="upk-content-wrap">
				<?php $this->render_category(); ?>
				<?php $this->render_title(substr($this->get_name(), 4)); ?>

				<?php if ($settings['show_meta']) : ?>
					<div class="upk-meta">
						<?php $this->render_author(); ?>
						<?php if ($settings['show_date'] or $settings['show_reading_time']) : ?>
							<div class="upk-date-reading upk-flex upk-flex-middle">
								<?php $this->render_date(); ?>
								<?php if ('yes' === $settings['show_reading_time']) : ?>
									<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
										<?php echo ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="upk-gratis-line"></div>

				<?php if ($settings['show_readmore'] === 'yes' and !empty($settings['readmore_text'])) : ?>
					<div class="upk-link-button">
						<a href="<?php echo esc_url(get_permalink()); ?>">
							<span><?php echo esc_html($settings['readmore_text']); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
								<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
							</svg>
						</a>
					</div>
				<?php endif; ?>
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

		$this->add_render_attribute('grid-wrap', 'class', 'upk-gratis-wrap');

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

		?>
		<div class="upk-gratis-grid">
			<div <?php $this->print_render_attribute_string('grid-wrap'); ?>>

				<?php 
				$i = 0;
				while ($wp_query->have_posts()) :
					$wp_query->the_post();

					$thumbnail_size = $settings['primary_thumbnail_size'];

					$i++;
					$active_item = '';
					if (_is_upk_pro_activated()) {
						$active_item = apply_filters('gratis_grid_active_item', $this, $i);
					}

				?>

					<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size, $active_item); ?>

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
