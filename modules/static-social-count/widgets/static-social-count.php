<?php

namespace UltimatePostKit\Modules\StaticSocialCount\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Box_Shadow;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;

if (!defined('ABSPATH')) {
	exit;
}

class Static_Social_Count extends Group_Control_Query {
	public function get_name() {
		return 'upk-static-social-count';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Social Count (Static)', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-static-social-count';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-static-social-count'];
		}
	}
	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-static-social-count'];
		}
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news', 'social-count', 'count'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/MmbdYPee9qw';
	}

	public function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__('Layout', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'select_style',
			[
				'label'      => esc_html__('Select Style', 'ultimate-post-kit') . BDTUPK_NC,
				'type'       => Controls_Manager::SELECT,
				'default'    => '',
				'options'    => [
					''  => esc_html__('Style 1', 'ultimate-post-kit'),
					'upk-style-2' => esc_html__('Style 2', 'ultimate-post-kit'),
					'upk-style-3' => esc_html__('Style 3', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label' => __('Columns', 'ultimate-post-kit'),
				'type' => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '4',
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
					'{{WRAPPER}} .upk-static-social-count' => ' grid-template-columns: repeat({{VALUE}}, 1fr);'
				],
				// 'condition' => [
				// 	'select_layout' => 'grid'
				// ]
			]
		);

		$this->add_responsive_control(
			'social_icon_space_between',
			[
				'label'         => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_social_sites',
			[
				'label' => esc_html__('Social Count', 'ultimate-post-kit'),
			]
		);
		$repeater = new Repeater();
		$repeater->start_controls_tabs(
			'social_count_tabs'
		);
		$repeater->start_controls_tab(
			'social_count_tabs_content',
			[
				'label' => esc_html__('Content', 'ultimate-post-kit'),
			]
		);
		$repeater->add_control(
			'social_site_name',
			[
				'label'       => esc_html__('Label', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__('facebook', 'ultimate-post-kit'),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'social_site_link',
			[
				'label'             => esc_html__('Link', 'ultimate-post-kit'),
				'type'              => Controls_Manager::URL,
				'placeholder'       => esc_html__('https://facebook.com', 'ultimate-post-kit'),
				'show_external'     => true,
				'default'           => [
					'url'           => 'https://bdthemes.com',
					'is_external'   => true,
					'nofollow'      => true,
				],
			]
		);
		$repeater->add_control(
			'social_site_icon',
			[
				'label'         => esc_html__('Icon', 'ultimate-post-kit'),
				'type'          => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fab fa-wordpress',
					'library' => 'fa-brands',
				],
				'recommended' => [
					'fa-brands' => [
						'android',
						'apple',
						'behance',
						'bitbucket',
						'codepen',
						'delicious',
						'deviantart',
						'digg',
						'dribbble',
						'elementor',
						'facebook',
						'flickr',
						'foursquare',
						'free-code-camp',
						'github',
						'gitlab',
						'globe',
						'houzz',
						'instagram',
						'jsfiddle',
						'linkedin',
						'medium',
						'meetup',
						'mix',
						'mixcloud',
						'odnoklassniki',
						'pinterest',
						'product-hunt',
						'reddit',
						'shopping-cart',
						'skype',
						'slideshare',
						'snapchat',
						'soundcloud',
						'spotify',
						'stack-overflow',
						'steam',
						'telegram',
						'thumb-tack',
						'tripadvisor',
						'tumblr',
						'twitch',
						'twitter',
						'viber',
						'vimeo',
						'vk',
						'weibo',
						'weixin',
						'whatsapp',
						'wordpress',
						'xing',
						'yelp',
						'youtube',
						'500px',
					],
					'fa-solid' => [
						'envelope',
						'link',
						'rss',
					],
				],
				'skin' => 'inline',
				'label_block' => false
			]
		);
		$repeater->add_control(
			'social_counter',
			[
				'label'     => esc_html__('Number', 'ultimate-post-kit'),
				'type'      => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'social_site_meta',
			[
				'label'     => esc_html__('Meta', 'ultimate-post-kit'),
				'type'      => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'social_count_tabs_style',
			[
				'label' => esc_html__('Style', 'ultimate-post-kit'),
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'social_single_item_bg',
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector'  => '{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}}.upk-item',
			]
		);
		$repeater->add_control(
			'heading_icon_social_single',
			[
				'label'     => esc_html__('ICON NORMAL', 'ultiamte-post-kit-pro'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'social_single_color',
			[
				'label' => esc_html__('Color', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-icon span' => 'color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'social_single_bg_color',
			[
				'label' => esc_html__('Background', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-icon span' => 'background: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'social_single_border_color',
			[
				'label' => esc_html__('Border', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-icon span' => 'border-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'heading_icon_h_social_single',
			[
				'label'     => esc_html__('ICON HOVER', 'ultiamte-post-kit-pro'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'social_single_h_color',
			[
				'label' => esc_html__('Color', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-icon:hover span' => 'color:{{VALUE}}',
				],
			]
		);
		$repeater->add_control(
			'social_single_h_bg_color',
			[
				'label' => esc_html__('Background', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-icon:hover span' => 'background: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'social_single_h_border_color',
			[
				'label' => esc_html__('Border', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-icon:hover span' => 'border-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'heading_number_social_single',
			[
				'label'     => esc_html__('N U M B E R', 'ultiamte-post-kit-pro'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'social_single_count_color',
			[
				'label' => esc_html__('Color', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-count .counter-value' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'heading_meta_social_single',
			[
				'label'     => esc_html__('M E T A', 'ultiamte-post-kit-pro'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'social_single_meta_color',
			[
				'label' => esc_html__('Color', 'ultimate-post-kit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count {{CURRENT_ITEM}} .upk-meta' => 'color: {{VALUE}};',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$this->add_control(
			'social_counter_list',
			[
				'label'         => esc_html__('Social', 'ultimate-post-kit'),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'social_site_name'    => esc_html__('Facebook', 'ultimate-post-kit'),
						'social_site_icon' => [
							'value' => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
						'social_counter'    => '450',
						'social_site_meta'    => esc_html__('Likes', 'ultimate-post-kit'),
						'social_site_link' => [
							'url' => esc_html__('https://facebook.com/bdthemes', 'ultimate-post-kit')
						]
					],
					[
						'social_site_name'    => esc_html__('Twitter', 'ultimate-post-kit'),
						'social_site_icon' => [
							'value' => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
						'social_counter'    => '3000',
						'social_site_meta'    => esc_html__('Followers', 'ultimate-post-kit'),
						'social_site_link' => [
							'url' => esc_html__('https://twitter.com/bdthemescom', 'ultimate-post-kit')
						]
					],
					[
						'social_site_name'    => esc_html__('Youtube', 'ultimate-post-kit'),
						'social_site_icon' => [
							'value' => 'fab fa-youtube',
							'library' => 'fa-brands',
						],
						'social_counter'    => '2000000',
						'social_site_meta'    => esc_html__('Subscriber', 'ultimate-post-kit'),
						'social_site_link' => [
							'url' => esc_html__('https://youtube.com/bdthemes', 'ultimate-post-kit')
						]
					],
					[
						'social_site_name'    => esc_html__('Instagram', 'ultimate-post-kit'),
						'social_site_icon' => [
							'value' => 'fab fa-instagram',
							'library' => 'fa-brands',
						],
						'social_counter'    => '105000',
						'social_site_meta'    => esc_html__('Followers', 'ultimate-post-kit'),
						'social_site_link' => [
							'url' => esc_html__('https://instagram.com/bdthemes', 'ultimate-post-kit')
						]
					],
				],
				'title_field'   => '{{{ social_site_name }}}',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'static_social_items',
			[
				'label' => esc_html__('Items', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'static_social_item_tabs'
		);
		$this->start_controls_tab(
			'static_social_tab_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'social_static_background',
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-item',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'social_static_border',
				'label'     => esc_html__('Border', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-item',
			]
		);
		$this->add_responsive_control(
			'social_static_padding',
			[
				'label'                 => esc_html__('Padding', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-static-social-count .upk-item'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_static_border_radius',
			[
				'label'                 => esc_html__('Radius', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-static-social-count .upk-item'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'static_social_shadow',
				'label'     => esc_html__('Box Shadow', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-item',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'static_social_tab_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'static_social_hover_bg',
				'label'     => esc_html__('Background', 'ultiamte-post-kit-pro'),
				'types'     => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-item:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'section_social_count_style',
			[
				'label' => esc_html__('Icons', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'social_icons_tab'
		);
		$this->start_controls_tab(
			'social_icon_tab_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-icon span',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'social_icons_border',
				'label'     => esc_html__('Border', 'ultimate-post-kit'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-icon span',
			]
		);
		$this->add_responsive_control(
			'social_icon_padding',
			[
				'label'                 => esc_html__('Padding', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon span '    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'social_icon_radius',
			[
				'label'                 => esc_html__('Radius', 'ultimate-post-kit'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon span '    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'social_icon_size',
			[
				'label'         => esc_html__('Size', 'ultimate-post-kit'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon span i' => 'font-size: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'social_icon_spacing',
			[
				'label'         => esc_html__('Bottom Spacing', 'ultimate-post-kit'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'select_style' => ''
				]
			]
		);
		$this->add_responsive_control(
			'social_icon_spacing_left',
			[
				'label'         => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'select_style' => 'upk-style-2'
				]
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'social_icon_tab_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);
		$this->add_control(
			'icon_h_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon span:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_h_background',
				'label'     => esc_html__('Background', 'ultimate-post-kit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-icon span:hover',
			]
		);
		$this->add_control(
			'icon_h_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-icon span:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'social_icons_border_border!' => ''
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'social_counter',
			[
				'label' => esc_html__('Counter', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'counter_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-count .counter-value' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'counter_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-count .counter-value',
			]
		);
		$this->add_responsive_control(
			'social_counter_spacing',
			[
				'label'         => esc_html__('Bottom Spacing', 'ultimate-post-kit'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-count' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'social_meta',
			[
				'label' => esc_html__('Meta', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-static-social-count .upk-meta' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'meta_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-static-social-count .upk-meta',
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('upk-static-social-count', 'class', ['upk-static-social-count', $settings['select_style']], true);
		$social_links = $this->get_settings('social_counter_list'); ?>
		<div <?php $this->print_render_attribute_string('upk-static-social-count'); ?>>
			<?php foreach ($social_links as $link_key => $social_link) {
				$this->add_render_attribute($link_key, 'class', [
					'upk-item',
					'upk-social-icon',
					'upk-social-icon-' . strtolower($social_link['social_site_name']),
					'elementor-repeater-item-' . $social_link['_id'],
				]);
				if ($social_link['social_site_link']['is_external'] !== 'on') {
					$this->add_render_attribute($link_key, 'href', $social_link['social_site_link']['url']);
				} else {
					$this->add_render_attribute(
						$link_key,
						[
							'href' => [
								$social_link['social_site_link']['url']
							],
							'target' => '_blank'
						],
						null,
						true
					);
				}
			?>
				<a <?php $this->print_render_attribute_string($link_key); ?>>
					<div class="upk-icon">
						<span title="<?php echo esc_html($social_link['social_site_name']); ?>">
							<?php Icons_Manager::render_icon($social_link['social_site_icon'], ['aria-hidden' => 'true']); ?>
						</span>
					</div>
					<div class="upk-content">
						<div class="upk-count">
							<?php echo '<span class="counter-value">' . esc_html($social_link['social_counter']) . '</span>'; ?>
						</div>
						<div class="upk-meta">
							<?php printf('<span>%s</span>', esc_html($social_link['social_site_meta'])); ?>
						</div>
					</div>
				</a>
	<?php
			}
			echo '</div>';
		}
	}
