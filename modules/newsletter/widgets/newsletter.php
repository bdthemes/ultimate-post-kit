<?php

namespace UltimatePostKit\Modules\Newsletter\Widgets;

use UltimatePostKit\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Newsletter extends Module_Base {

	public function get_name() {
		return 'upk-newsletter';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Newsletter', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-newsletter';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['newsletter', 'email', 'marketing', 'newsletter'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-newsletter'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-newsletter'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/8ZgQVoSPEyw';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'show_before_icon',
			[
				'label' => esc_html__('Before Icon', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'mailchimp_before_icon',
			[
				'label'       => __('Choose Icon', 'ultimate-post-kit'),
				'type'        => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-envelope-open',
					'library' => 'fa-regular',
				],
				'condition'   => [
					'show_before_icon' => 'yes'
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type'         => Controls_Manager::DIVIDER,
				'condition'   => [
					'show_before_icon' => 'yes'
				]
			]
		);

		$this->add_control(
			'before_text',
			[
				'label'       => esc_html__('Before Text', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('Before Text', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'after_text',
			[
				'label'       => esc_html__('After Text', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('After Text', 'ultimate-post-kit'),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => __('Alignment', 'ultimate-post-kit'),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'    => [
						'title' => __('Left', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'before_text',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'after_text',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-before-text, {{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-after-text' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hr_2',
			[
				'type'         => Controls_Manager::DIVIDER,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'before_text',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'after_text',
							'operator' => '!=',
							'value'    => '',
						],
					],
				]
			]
		);

		$this->add_control(
			'email_field_placeholder',
			[
				'label'       => esc_html__('Email Field Placeholder', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'default'     => esc_html__('Email *', 'ultimate-post-kit'),
				'placeholder' => esc_html__('Email *', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'show_fname',
			[
				'label' => esc_html__('Show Name', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'fname_field_placeholder',
			[
				'label'       => esc_html__('Name Field Placeholder', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'default'     => esc_html__('Name ', 'ultimate-post-kit'),
				'placeholder' => esc_html__('Name ', 'ultimate-post-kit'),
				'condition'	=> [
					'show_fname' => 'yes',
				]
			]
		);

		$this->add_control(
			'fullwidth_input',
			[
				'label' => esc_html__('Fullwidth Fields', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'upk-field-full--',
			]
		);

		$this->add_control(
			'fullwidth_button',
			[
				'label'     => esc_html__('Fullwidth Button', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn' => 'width: 100%;',
				],
				'condition' => [
					'fullwidth_input' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button',
			[
				'label' => esc_html__('Signup Button', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__('Button Text', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('SIGNUP', 'ultimate-post-kit'),
				'default'     => esc_html__('SIGNUP', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'mailchimp_button_icon',
			[
				'label'       => __('Icon', 'ultimate-post-kit'),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => __('Icon Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => __('Left', 'ultimate-post-kit'),
					'right'  => __('Right', 'ultimate-post-kit'),
				],
				'condition' => [
					'mailchimp_button_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_indent',
			[
				'label' => __('Icon Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 8,
				],
				'condition' => [
					'mailchimp_button_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-btn .upk-flex-align-right'  => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-newsletter-btn .upk-flex-align-left'   => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_before_icon',
			[
				'label'     => __('Before Icon', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_before_icon' => 'yes',
					'mailchimp_before_icon[value]!'     => '',
				],
			]
		);

		$this->start_controls_tabs('tabs_before_icon_style');

		$this->start_controls_tab(
			'tab_before_icon_normal',
			[
				'label' => __('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'before_icon_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-before-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-newsletter-before-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'before_icon_background',
				'selector'  => '{{WRAPPER}} .upk-newsletter-before-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'before_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-newsletter-before-icon',
			]
		);

		$this->add_responsive_control(
			'before_icon_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-before-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_icon_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-before-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_icon_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-before-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'before_icon_shadow',
				'selector' => '{{WRAPPER}} .upk-newsletter-before-icon',
			]
		);

		$this->add_responsive_control(
			'before_icon_size',
			[
				'label' => __('Size', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-before-icon'   => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_before_icon_hover',
			[
				'label' => __('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'before_icon_hover_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-before-icon:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-newsletter-before-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'before_icon_hover_background',
				'selector'  => '{{WRAPPER}} .upk-newsletter-before-icon:hover',
			]
		);

		$this->add_control(
			'before_icon_hover_border_color',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'before_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-before-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__('Field', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_input_style');

		$this->start_controls_tab(
			'tab_input_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper input[type*="email"]::placeholder, {{WRAPPER}} .upk-newsletter-wrapper input[type*="text"]::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_color',
			[
				'label'     => esc_html__('Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_background',
				'selector'  => '{{WRAPPER}} .upk-newsletter-wrapper .upk-input',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'input_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-newsletter-wrapper .upk-input',
			]
		);

		$this->add_responsive_control(
			'input_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-input-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'input_shadow',
				'selector' => '{{WRAPPER}} .upk-newsletter-wrapper .upk-input',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'placeholder_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-newsletter-wrapper .upk-input',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_input_focus',
			[
				'label' => esc_html__('Focus', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'input_focus_color',
			[
				'label'     => esc_html__('Text Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper input:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_focus_background',
				'selector'  => '{{WRAPPER}} .upk-newsletter-wrapper input:focus',
			]
		);

		$this->add_control(
			'input_focus_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'input_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper input:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__('Sign Up Button', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'selector'  => '{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn',
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'selector'  => '{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __('Hover Animation', 'ultimate-post-kit'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label'     => __('Signup Button Icon', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'mailchimp_button_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs('tabs_signup_btn_icon_style');

		$this->start_controls_tab(
			'tab_signup_btn_icon_normal',
			[
				'label' => __('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'signup_btn_icon_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'signup_btn_icon_background',
				'selector'  => '{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'signup_btn_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon',
			]
		);

		$this->add_responsive_control(
			'signup_btn_icon_radius',
			[
				'label'      => __('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'signup_btn_icon_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'signup_btn_icon_margin',
			[
				'label'      => __('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'signup_btn_icon_shadow',
				'selector' => '{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon',
			]
		);

		$this->add_responsive_control(
			'signup_btn_icon_size',
			[
				'label' => __('Size', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-btn .upk-newsletter-btn-icon'   => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_signup_btn_icon_hover',
			[
				'label' => __('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'signup_btn_icon_hover_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-btn:hover .upk-newsletter-btn-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-newsletter-btn:hover .upk-newsletter-btn-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'signup_btn_icon_hover_background',
				'selector'  => '{{WRAPPER}} .upk-newsletter-btn:hover .upk-newsletter-btn-icon',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => __('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'signup_btn_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-btn:hover .upk-newsletter-btn-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_before_text',
			[
				'label'     => __('Before Text', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'before_text!' => '',
				],
			]
		);

		$this->add_control(
			'before_text_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-before-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_text_spacing',
			[
				'label' => __('Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-before-text'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'before_text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-before-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_after_text',
			[
				'label'     => __('After Text', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'after_text!' => '',
				],
			]
		);

		$this->add_control(
			'after_text_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-after-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'after_text_spacing',
			[
				'label' => __('Spacing', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-after-text'   => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'after_text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-newsletter-wrapper .upk-newsletter-after-text',
			]
		);

		$this->end_controls_section();
	}

	public function render_text($settings) {

		$this->add_render_attribute('content-wrapper', 'class', 'upk-newsletter-btn-content-wrapper');

		if ('left' == $settings['icon_align'] or 'right' == $settings['icon_align']) {
			$this->add_render_attribute('content-wrapper', 'class', 'upk-flex upk-flex-middle upk-flex-center');
		}

		$this->add_render_attribute('icon-align', 'class', 'elementor-align-icon-' . $settings['icon_align']);
		$this->add_render_attribute('icon-align', 'class', 'upk-newsletter-btn-icon');

		$this->add_render_attribute('text', 'class', ['upk-newsletter-btn-text']);
		$this->add_inline_editing_attributes('text', 'none');

?>
		<div <?php $this->print_render_attribute_string('content-wrapper'); ?>>
			<?php if (!empty($settings['mailchimp_button_icon']['value'])) : ?>
				<div class="upk-newsletter-btn-icon upk-flex-align-<?php echo esc_attr($settings['icon_align']); ?>">

					<?php Icons_Manager::render_icon($settings['mailchimp_button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>

				</div>
			<?php endif; ?>
			<div <?php $this->print_render_attribute_string('text'); ?>><?php echo wp_kses($settings['button_text'], ultimate_post_kit_allow_tags('title')); ?></div>
		</div>
	<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('input-wrapper', 'class', 'upk-newsletter-input-wrapper');
	?>
		<div class="upk-newsletter-wrapper">

			<?php if (!empty($settings['before_text'])) : ?>
				<div class="upk-newsletter-before-text"><?php echo esc_attr($settings['before_text']); ?></div>
			<?php endif; ?>

			<form action="<?php echo esc_url(site_url()) ?>/wp-admin/admin-ajax.php" class="upk-newsletter">

				<?php if ($settings['show_before_icon'] and !empty($settings['mailchimp_before_icon']['value'])) : ?>
					<div class="upk-newsletter-before-icon">

						<?php Icons_Manager::render_icon($settings['mailchimp_before_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>

					</div>
				<?php endif; ?>

				<?php if ($settings['show_fname'] == 'yes') : ?>
					<div <?php $this->print_render_attribute_string('input-wrapper'); ?>>
						<input type="text" name="fname" placeholder="<?php echo esc_attr($settings['fname_field_placeholder']); ?>" class="upk-input" />
					</div>
				<?php endif; ?>

				<div <?php $this->print_render_attribute_string('input-wrapper'); ?>>
					<input type="email" name="email" placeholder="<?php echo esc_attr($settings['email_field_placeholder']); ?>" required class="upk-input" />
					<input type="hidden" name="action" value="ultimate_post_kit_mailchimp_subscribe" />
					<!-- we need action parameter to receive ajax request in WordPress -->
				</div>
				<?php

				$this->add_render_attribute('signup_button', 'class', ['upk-newsletter-btn']);

				if ($settings['hover_animation']) {
					$this->add_render_attribute('signup_button', 'class', 'elementor-animation-' . $settings['hover_animation']);
				}

				?>
				<div class="upk-newsletter-signup-wrapper">
					<button <?php $this->print_render_attribute_string('signup_button'); ?>>
						<?php $this->render_text($settings); ?>
					</button>
				</div>
			</form>

			<!-- after text -->
			<?php if (!empty($settings['after_text'])) : ?>
				<div class="upk-newsletter-after-text"><?php echo esc_attr($settings['after_text']); ?></div>
			<?php endif; ?>

		</div><!-- end newsletter-signup -->


<?php
	}
}
