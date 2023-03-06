<?php

namespace UltimatePostKit\Modules\Author\Widgets;

use UltimatePostKit\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Author extends Module_Base {
	private $_query = null;

	public function get_name() {
		return 'upk-author';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Author', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-author';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'author', 'list'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-author'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/rW8rTtw62ko';
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
			'layout_style',
			[
				'label'   => esc_html__('Style', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid_1',
				'options' => [
					'grid_1' => esc_html__('Grid', 'ultimate-post-kit'),
					'list' => esc_html__('List', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label'          => __('Columns', 'ultimate-post-kit'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors'      => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper.upk-author-layout-grid_1' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
				'condition'      => [
					'layout_style' => ['grid_1']
				]
			]
		);

		$this->add_responsive_control(
			'list_columns',
			[
				'label'          => __('Columns', 'ultimate-post-kit'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
				],
				'selectors'      => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper.upk-author-layout-list' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
				'condition'      => [
					'layout_style' => 'list'
				]
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'     => __('Alignment', 'ultimate-post-kit'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_alignment',
			[
				'label'     => __('Meta Alignment', 'ultimate-post-kit'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'  => [
						'title' => __('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'show_author_avatar',
			[
				'label'   => esc_html__('Show Avatar', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'author_avatar_size',
			[
				'label'     => __('Avatar Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SELECT,
				'condition' => [
					'show_author_avatar' => 'yes'
				],
				'options'   => [
					'25'  => '25 x 25',
					'35'  => '35 x 35',
					'45'  => '45 x 45',
					'60'  => '60 x 60',
					'80'  => '80 x 80',
					'100' => '100 x 100',
					'150' => '150 x 150',
					'200' => '200 x 200',
					'250' => '250 x 250',
				],
				'default'   => '250',
			]
		);

		$this->add_control(
			'show_author_name',
			[
				'label'   => esc_html__('Show Name', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_role',
			[
				'label'   => esc_html__('Show Role', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_description',
			[
				'label'   => esc_html__('Show Description', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_post_count',
			[
				'label'   => esc_html__('Show Post Count', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_link',
			[
				'label'   => esc_html__('Show Social Link', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'social_links',
			[
				'label'       => __('Social Links', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => false,
				'default'     => ['email', 'url'],
				'options'     => ultimate_post_kit_user_contact_methods([], true),
				'condition'   => [
					'show_author_link' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_grid_query',
			[
				'label' => esc_html__('Query', 'ultimate-post-kit'),
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
					'size' => 8,
				],
			]
		);

		$this->add_control(
			'role',
			[
				'label'   => esc_html__('Role', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => false,
				'options' => [
					'subscriber'    => esc_html__('Subscriber', 'ultimate-post-kit'),
					'contributor'   => esc_html__('Contributor', 'ultimate-post-kit'),
					'author'        => esc_html__('Author', 'ultimate-post-kit'),
					'editor'        => esc_html__('Editor', 'ultimate-post-kit'),
					'administrator' => esc_html__('Administrator', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'exclude',
			[
				'label'       => esc_html__('Exclude', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('User ID: 1,2', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__('Order By', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'display_name',
				'options' => [
					'display_name'   => esc_html__('Nicename', 'ultimate-post-kit'),
					'post_count' => esc_html__('Post Count', 'ultimate-post-kit'),
					'registered' => esc_html__('Registered', 'ultimate-post-kit'),
					'rand'       => esc_html__('Random', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__('Order', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => esc_html__('ASC', 'ultimate-post-kit'),
					'desc' => esc_html__('DESC', 'ultimate-post-kit'),
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'upk_section_style',
			[
				'label' => esc_html__('Item', 'ultimate-post-kit'),
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'glassmorphism_effect',
			[
				'label' => esc_html__('Glassmorphism', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'ultimate-post-kit'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),

			]
		);

		$this->add_control(
			'glassmorphism_blur_level',
			[
				'label'       => __('Blur Level', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'glassmorphism_effect' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'item_border',
				'label'          => __('Border', 'elementor'),
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
						'default' => '#e9edf4',
					],
				],
				'selector'       => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item',
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
				'name'     => 'item_hover_background',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item:hover',
			]
		);

		$this->add_control(
			'item_border_hover_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'item_border_border!' => '',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_hover',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => esc_html__('Avatar', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_mask_popover',
			[
				'label'        => esc_html__('Image Mask', 'ultimate-post-kit'),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'ui',
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'image_mask_shape',
			[
				'label'     => esc_html__('Masking Shape', 'ultimate-post-kit'),
				'title'     => esc_html__('Masking Shape', 'ultimate-post-kit'),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'default',
				'options'   => [
					'default' => [
						'title' => esc_html__('Default Shapes', 'ultimate-post-kit'),
						'icon'  => 'eicon-star',
					],
					'custom'  => [
						'title' => esc_html__('Custom Shape', 'ultimate-post-kit'),
						'icon'  => 'eicon-image-bold',
					],
				],
				'toggle'    => false,
				'condition' => [
					'image_mask_popover' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_mask_shape_default',
			[
				'label'          => _x('Default', 'Mask Image', 'ultimate-post-kit'),
				'label_block'    => true,
				'show_label'     => false,
				'type'           => Controls_Manager::SELECT,
				'default'        => 0,
				'options'        => ultimate_post_kit_mask_shapes(),
				'selectors'      => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => '-webkit-mask-image: url({{VALUE}}); mask-image: url({{VALUE}});',
				],
				'condition'      => [
					'image_mask_popover' => 'yes',
					'image_mask_shape'   => 'default',
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'image_mask_shape_custom',
			[
				'label'      => _x('Custom Shape', 'Mask Image', 'ultimate-post-kit'),
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false,
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}});',
				],
				'condition'  => [
					'image_mask_popover' => 'yes',
					'image_mask_shape'   => 'custom',
				],
			]
		);

		$this->add_control(
			'image_mask_shape_position',
			[
				'label'                => esc_html__('Position', 'ultimate-post-kit'),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center-center',
				'options'              => [
					'center-center' => esc_html__('Center Center', 'ultimate-post-kit'),
					'center-left'   => esc_html__('Center Left', 'ultimate-post-kit'),
					'center-right'  => esc_html__('Center Right', 'ultimate-post-kit'),
					'top-center'    => esc_html__('Top Center', 'ultimate-post-kit'),
					'top-left'      => esc_html__('Top Left', 'ultimate-post-kit'),
					'top-right'     => esc_html__('Top Right', 'ultimate-post-kit'),
					'bottom-center' => esc_html__('Bottom Center', 'ultimate-post-kit'),
					'bottom-left'   => esc_html__('Bottom Left', 'ultimate-post-kit'),
					'bottom-right'  => esc_html__('Bottom Right', 'ultimate-post-kit'),
				],
				'selectors_dictionary' => [
					'center-center' => 'center center',
					'center-left'   => 'center left',
					'center-right'  => 'center right',
					'top-center'    => 'top center',
					'top-left'      => 'top left',
					'top-right'     => 'top right',
					'bottom-center' => 'bottom center',
					'bottom-left'   => 'bottom left',
					'bottom-right'  => 'bottom right',
				],
				'selectors'            => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => '-webkit-mask-position: {{VALUE}}; mask-position: {{VALUE}};',
				],
				'condition'            => [
					'image_mask_popover' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_mask_shape_size',
			[
				'label'     => esc_html__('Size', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'contain',
				'options'   => [
					'auto'    => esc_html__('Auto', 'ultimate-post-kit'),
					'cover'   => esc_html__('Cover', 'ultimate-post-kit'),
					'contain' => esc_html__('Contain', 'ultimate-post-kit'),
					'initial' => esc_html__('Custom', 'ultimate-post-kit'),
				],
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => '-webkit-mask-size: {{VALUE}}; mask-size: {{VALUE}};',
				],
				'condition' => [
					'image_mask_popover' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_mask_shape_custom_size',
			[
				'label'      => _x('Custom Size', 'Mask Image', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SLIDER,
				'responsive' => true,
				'size_units' => ['px', 'em', '%', 'vw'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 100,
					'unit' => '%',
				],
				'required'   => true,
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => '-webkit-mask-size: {{SIZE}}{{UNIT}}; mask-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'image_mask_popover'    => 'yes',
					'image_mask_shape_size' => 'initial',
				],
			]
		);

		$this->add_control(
			'image_mask_shape_repeat',
			[
				'label'                => esc_html__('Repeat', 'ultimate-post-kit'),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'no-repeat',
				'options'              => [
					'repeat'          => esc_html__('Repeat', 'ultimate-post-kit'),
					'repeat-x'        => esc_html__('Repeat-x', 'ultimate-post-kit'),
					'repeat-y'        => esc_html__('Repeat-y', 'ultimate-post-kit'),
					'space'           => esc_html__('Space', 'ultimate-post-kit'),
					'round'           => esc_html__('Round', 'ultimate-post-kit'),
					'no-repeat'       => esc_html__('No-repeat', 'ultimate-post-kit'),
					'repeat-space'    => esc_html__('Repeat Space', 'ultimate-post-kit'),
					'round-space'     => esc_html__('Round Space', 'ultimate-post-kit'),
					'no-repeat-round' => esc_html__('No-repeat Round', 'ultimate-post-kit'),
				],
				'selectors_dictionary' => [
					'repeat'          => 'repeat',
					'repeat-x'        => 'repeat-x',
					'repeat-y'        => 'repeat-y',
					'space'           => 'space',
					'round'           => 'round',
					'no-repeat'       => 'no-repeat',
					'repeat-space'    => 'repeat space',
					'round-space'     => 'round space',
					'no-repeat-round' => 'no-repeat round',
				],
				'selectors'            => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => '-webkit-mask-repeat: {{VALUE}}; mask-repeat: {{VALUE}};',
				],
				'condition'            => [
					'image_mask_popover' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'avatar_border',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'avatar_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'layout_style' => ['grid_1']
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'avatar_box_shadow',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img',
			]
		);

		$this->add_responsive_control(
			'avatar_size',
			[
				'label'     => esc_html__('Size(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => ['list']
				]
			]
		);

		$this->add_responsive_control(
			'avatar_spacing',
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => 'list'
				]
			]
		);

		$this->start_controls_tabs('tabs_avatar_style');

		$this->start_controls_tab(
			'tab_avatar_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-image a img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_avatar_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'hover_css_filters',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item:hover .upk-author-image a img',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Name', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_author_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-name a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-name a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-name a',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'name_text_shadow',
				'label'    => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-name a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_role',
			[
				'label'     => esc_html__('Role', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_author_role' => 'yes',
				]
			]
		);

		$this->add_control(
			'role_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-role' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'role_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-role',
			]
		);

		$this->add_responsive_control(
			'role_spacing',
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-role' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label'     => esc_html__('Description', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_author_description' => 'yes',
				]
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-description',
			]
		);

		$this->add_responsive_control(
			'description_spacing',
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-description' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_post_count',
			[
				'label'     => esc_html__('Post Count', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_count' => 'yes',
				]
			]
		);

		$this->add_control(
			'post_count_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'post_count_background',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'post_count_border',
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
					// 'color'  => [
					// 	'default' => '#8D99AE',
					// ],
				],
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count',
			]
		);

		$this->add_responsive_control(
			'post_count_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_count_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'post_count_shadow',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_count_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count',
			]
		);

		$this->add_responsive_control(
			'post_count_spacing',
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-post-count' => 'top: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_social_link',
			[
				'label'     => esc_html__('Social Link', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_author_link' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'social_link_bottom_spacing',
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_social_link_style');

		$this->start_controls_tab(
			'tab_social_link_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'social_link_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'social_link_background',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'social_link_border',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a',
			]
		);

		$this->add_responsive_control(
			'social_link_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_link_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_link_spacing',
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
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'social_link_shadow',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'social_link_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_social_link_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'social_link_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'social_link_hover_background',
				'selector' => '{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a:hover',
			]
		);

		$this->add_control(
			'social_link_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'social_link_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_link_border_radius_hover',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-author .upk-author-wrapper .upk-author-item .upk-author-content .upk-author-link a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$users = get_users([
			'orderby'  => $settings['orderby'],
			'order'    => $settings['order'],
			'role__in' => (!empty($settings['role'])) ? $settings['role'] : null,
			'number'   => $settings['item_limit']['size'],
			'exclude'  => explode(',', esc_attr($settings["exclude"])),
		]);

		$social_links = $settings['social_links'];

?>
		<div class="upk-author">
			<div class="upk-author-wrapper upk-author-layout-<?php echo esc_html($settings['layout_style']) ?>">
				<?php
				foreach ($users as $author) {
				?>
					<div class="upk-author-item">
						<?php if ($settings['show_author_avatar']) : ?>
							<div class="upk-author-image">
								<a href="<?php echo get_bloginfo('url') . "/?author=" . $author->ID; ?>">
									<?php echo get_avatar($author->ID, $settings['author_avatar_size']); ?>
								</a>
							</div>
						<?php endif; ?>

						<div class="upk-author-content">
							<?php if ($settings['show_author_name']) : ?>
								<div class="upk-author-name">
									<a href="<?php echo get_bloginfo('url') . "/?author=" . $author->ID; ?>">
										<?php echo get_the_author_meta('display_name', $author->ID); ?>
									</a>
								</div>
							<?php endif; ?>

							<?php if ($settings['show_author_role']) : ?>
								<div class="upk-author-role">
									<?php echo ucwords(get_user_role($author->ID)); ?>
								</div>
							<?php endif; ?>

							<?php if ($settings['show_author_description'] and get_the_author_meta('description', $author->ID)) : ?>
								<div class="upk-author-description">
									<?php echo get_the_author_meta('description', $author->ID); ?>
								</div>
							<?php endif; ?>

							<?php if ($settings['show_author_link'] and !empty($social_links)) : ?>

								<div class="upk-author-link">


									<?php foreach ($social_links as $link) : ?>

										<?php if (get_the_author_meta($link, $author->ID)) : ?>
											<?php

											$final_url = get_the_author_meta($link, $author->ID);
											$alt_title = esc_html('Click here to go ' . ucwords($link), 'ultimate-post-kit');

											if ($link == 'email') {
												$final_url = 'mailto:' . get_the_author_meta($link, $author->ID);
												$alt_title = esc_html('Click here to ' . ucwords($link), 'ultimate-post-kit');
											}

											?>

											<a href="<?php echo esc_url($final_url); ?>" title="<?php echo esc_html($alt_title); ?>">
												<i class="upk-icon-<?php echo esc_attr($link); ?>" aria-hidden="true"></i>
											</a>
										<?php endif; ?>

									<?php endforeach; ?>


								</div>
							<?php endif; ?>

						</div>

						<?php if ($settings['show_post_count']) : ?>
							<div class="upk-author-post-count">
								<?php

								$count = count_user_posts($author->ID);

								$total_count = sprintf(_n('Post: %s', 'Posts: %s', $count, 'ultimate-post-kit'), $count);

								echo esc_attr($total_count);

								?>

							</div>
						<?php endif; ?>

					</div>
				<?php } ?>
			</div>
		</div>
<?php
	}
}
