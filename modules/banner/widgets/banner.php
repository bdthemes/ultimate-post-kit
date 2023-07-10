<?php

namespace UltimatePostKit\Modules\Banner\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Stroke;
use UltimatePostKit\Utils;
use Elementor\Icons_Manager;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Banner extends Group_Control_Query
{

	use Global_Widget_Controls;

	private $_query = null;

	public function get_name()
	{
		return 'upk-banner';
	}

	public function get_title()
	{
		return BDTUPK . esc_html__('Banner', 'ultimate-post-kit');
	}

	public function get_icon()
	{
		return 'upk-widget-icon upk-icon-banner upk-new';
	}

	public function get_categories()
	{
		return ['ultimate-post-kit'];
	}

	public function get_keywords()
	{
		return ['post', 'grid', 'blog', 'recent', 'news', 'banner'];
	}

	public function get_style_depends()
	{
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-banner'];
		}
	}

	public function get_custom_help_url()
	{
		return 'https://youtu.be/ESZvXD-knVQ';
	}

	public function get_query()
	{
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
			'title_text',
			[
				'label'   => __('Title', 'ultimate-post-kit'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => __('Main Title', 'ultimate-post-kit'),
				'placeholder' => __('Enter your title', 'ultimate-post-kit'),
				'label_block' => true
			]
		);

		$this->add_control(
			'sub_title_text',
			[
				'label'   => __('Sub Title', 'ultimate-post-kit'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => __('Sub Title', 'ultimate-post-kit'),
				'placeholder' => __('Enter your sub title', 'ultimate-post-kit'),
				'label_block' => true,
				// 'separator' => 'before'
			]
		);

		$this->add_control(
			'description_text',
			[
				'label'       => esc_html__('Text', 'bdthemes-prime-slider'),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => esc_html__('It is a long established fact that a reader will be distracted by the readable', 'ultimate-post-kit', 'ultimate-post-kit'),
				'label_block' => true,
				'dynamic'     => ['active' => true],
				'separator' => 'before'
			]
        );

		$this->add_control(
			'badge_text',
			[
				'label'   => __('Primary Badge Text', 'ultimate-post-kit'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => __('40% off', 'ultimate-post-kit'),
				'placeholder' => __('Enter your sub title', 'ultimate-post-kit'),
				'label_block' => true,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'banner_size_text',
			[
				'label'   => __('Secondary Badge Text', 'ultimate-post-kit'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => __('banner 900px x 170px', 'ultimate-post-kit'),
				'placeholder' => __('Enter banner size text', 'ultimate-post-kit'),
				'label_block' => true,
			]
		);

		$this->add_responsive_control(
			'image',
			[
				'label'       => __('Image', 'ultimate-post-kit'),
				'type'        => Controls_Manager::MEDIA,
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-image: url("{{URL}}");',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'position', 
			[
				'label' => esc_html_x( 'Position', 'Background Control', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'responsive' => true,
				'options' => [
					'' => esc_html_x( 'Default', 'Background Control', 'elementor' ),
					'center center' => esc_html_x( 'Center Center', 'Background Control', 'elementor' ),
					'center left' => esc_html_x( 'Center Left', 'Background Control', 'elementor' ),
					'center right' => esc_html_x( 'Center Right', 'Background Control', 'elementor' ),
					'top center' => esc_html_x( 'Top Center', 'Background Control', 'elementor' ),
					'top left' => esc_html_x( 'Top Left', 'Background Control', 'elementor' ),
					'top right' => esc_html_x( 'Top Right', 'Background Control', 'elementor' ),
					'bottom center' => esc_html_x( 'Bottom Center', 'Background Control', 'elementor' ),
					'bottom left' => esc_html_x( 'Bottom Left', 'Background Control', 'elementor' ),
					'bottom right' => esc_html_x( 'Bottom Right', 'Background Control', 'elementor' ),
					'initial' => esc_html_x( 'Custom', 'Background Control', 'elementor' ),

				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-position: {{VALUE}};',
				],
				'condition' => [
					'image[url]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'xpos',
			[
				'label' => esc_html_x( 'X Position', 'Background Control', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'responsive' => true,
				'size_units' => [ 'px', 'em', '%', 'vw' ],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -800,
						'max' => 800,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-position: {{SIZE}}{{UNIT}} {{ypos.SIZE}}{{ypos.UNIT}}',
				],
				'condition' => [
					'position' => [ 'initial' ],
					'image[url]!' => '',
				],
				'required' => true,
			]
		);

		$this->add_responsive_control(
			'ypos',
			[
				'label' => esc_html_x( 'Y Position', 'Background Control', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'responsive' => true,
				'size_units' => [ 'px', 'em', '%', 'vh' ],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -800,
						'max' => 800,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'vh' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-position: {{xpos.SIZE}}{{xpos.UNIT}} {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'position' => [ 'initial' ],
					'image[url]!' => '',
				],
				'required' => true,
			]
		);

		$this->add_responsive_control(
			'attachment',
			[
				'label' => esc_html_x( 'Attachment', 'Background Control', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html_x( 'Default', 'Background Control', 'elementor' ),
					'scroll' => esc_html_x( 'Scroll', 'Background Control', 'elementor' ),
					'fixed' => esc_html_x( 'Fixed', 'Background Control', 'elementor' ),
				],
				'selectors' => [
					'(desktop+){{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-attachment: {{VALUE}};',
				],
				'condition' => [
					'image[url]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'attachment_alert',
			[
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control-field-description',
				'raw' => esc_html__( 'Note: Attachment Fixed works only on desktop.', 'elementor' ),
				'separator' => 'none',
				'condition' => [
					'image[url]!' => '',
					'attachment' => 'fixed',
				],
			]
		);

		$this->add_responsive_control(
			'repeat',
			[
				'label' => esc_html_x( 'Repeat', 'Background Control', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'responsive' => true,
				'options' => [
					'' => esc_html_x( 'Default', 'Background Control', 'elementor' ),
					'no-repeat' => esc_html_x( 'No-repeat', 'Background Control', 'elementor' ),
					'repeat' => esc_html_x( 'Repeat', 'Background Control', 'elementor' ),
					'repeat-x' => esc_html_x( 'Repeat-x', 'Background Control', 'elementor' ),
					'repeat-y' => esc_html_x( 'Repeat-y', 'Background Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-repeat: {{VALUE}};',
				],
				'condition' => [
					'image[url]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'size',
			[
				'label' => esc_html_x( 'Size', 'Background Control', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'responsive' => true,
				'default' => '',
				'options' => [
					'' => esc_html_x( 'Default', 'Background Control', 'elementor' ),
					'auto' => esc_html_x( 'Auto', 'Background Control', 'elementor' ),
					'cover' => esc_html_x( 'Cover', 'Background Control', 'elementor' ),
					'contain' => esc_html_x( 'Contain', 'Background Control', 'elementor' ),
					'initial' => esc_html_x( 'Custom', 'Background Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-size: {{VALUE}};',
				],
				'condition' => [
					'image[url]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'bg_width',
			[
				'label' => esc_html_x( 'Width', 'Background Control', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'responsive' => true,
				'size_units' => [ 'px', 'em', '%', 'vw' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'required' => true,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'background-size: {{SIZE}}{{UNIT}} auto',

				],
				'condition' => [
					'size' => [ 'initial' ],
					'image[url]!' => '',
				],
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_settings',
			[
				'label' => esc_html__('Additional Settings', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'layout_direction',
			[
				'label'      => __('Banner Style', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'style-1',
				'options'    => [
					'style-1'  => __('Style 1', 'ultimate-post-kit'),
					'style-2' => __('Style 2', 'ultimate-post-kit'),
					'style-3' => __('Style 3', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_responsive_control(
			'default_item_height',
			[
				'label'   => esc_html__('Item Height(px)', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'height: {{SIZE}}{{UNIT}};',
				],

			]
		);


		$this->add_responsive_control(
			'alignment',
			[
				'label'   => esc_html__('Alignment', 'ultimate-post-kit-pro'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'ultimate-post-kit-pro'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'ultimate-post-kit-pro'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'ultimate-post-kit-pro'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-content' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .upk-banner-style-2 .upk-buy-btn, {{WRAPPER}} .upk-banner-style-3 .upk-buy-btn' => 'justify-content: {{VALUE}}; text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => __('Show Title', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_size',
			[
				'label'   => __('Title HTML Tag', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => ultimate_post_kit_title_tags(),
				'condition'   => [
					'show_title' => 'yes',
					'title_text!' => ''
				],
			]
		);

		$this->add_control(
			'show_sub_title',
			[
				'label'        => __('Show Sub Title', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_text',
			[
				'label'        => __('Show Text', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				// 'separator' => 'before',
			]
		);

		$this->add_control(
			'readmore',
			[
				'label'   => esc_html__( 'Show Read More', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);


		$this->add_control(
			'show_badge',
			[
				'label'        => __('Show Primary Badge', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_badge_direction',
			[
				'label'      => __('Primary Badge Position', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'style-1',
				'options'    => [
					'style-1'  => __('Top Left', 'ultimate-post-kit'),
					'style-2' => __('Top Right', 'ultimate-post-kit'),
					'style-3' => __('Bottom Left', 'ultimate-post-kit'),
					'style-4' => __('Bottom Right', 'ultimate-post-kit'),
					'style-5' => __('Bottom Center', 'ultimate-post-kit'),
					'style-6' => __('Top Center', 'ultimate-post-kit'),
					'style-7' => __('Vertical Left', 'ultimate-post-kit'),
					'style-8' => __('Vertical Right', 'ultimate-post-kit'),
				],
				'condition' => [
					'show_badge' => 'yes'
				]
			]
		);


		$this->add_control(
			'show_banner_size',
			[
				'label'        => __('Show Secondary Badge', 'ultimate-post-kit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'separator' => 'before',
			]
		);

		

		$this->add_control(
			'size_badge_direction',
			[
				'label'      => __('Secondary Badge Position', 'ultimate-post-kit'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'style-4',
				'options'    => [
					'style-1'  => __('Top Left', 'ultimate-post-kit'),
					'style-2' => __('Top Right', 'ultimate-post-kit'),
					'style-3' => __('Bottom Left', 'ultimate-post-kit'),
					'style-4' => __('Bottom Right', 'ultimate-post-kit'),
					'style-5' => __('Bottom Center', 'ultimate-post-kit'),
					'style-6' => __('Top Center', 'ultimate-post-kit'),
					'style-7' => __('Vertical Left', 'ultimate-post-kit'),
					'style-8' => __('Vertical Right', 'ultimate-post-kit'),
				],
				'condition' => [
					'show_banner_size' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_readmore',
			[
				'label'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'condition' => [
					'readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Read More', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'readmore_link',
			[
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
				'default'     => [
					'url' => '#',
				],
				'label_block' => false,
			]
		);

		$this->add_control(
			'readmore_icon',
			[
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::CHOOSE,
				'default' => 'right',
                'toggle' => false,
                'options'   => [
                    'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon' => 'eicon-h-align-right',
					],
                ],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_indent',
			[
				'label'   => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
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
					'{{WRAPPER}} .upk-banner-readmore .upk-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upk-banner-readmore .upk-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
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

		$this->add_control(
			'overlay_type',
			[
				'label'   => esc_html__('Overlay', 'pixel-gallery'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
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
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-item::before',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(0, 0, 0, 0.609)',
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
					'{{WRAPPER}} .upk-banner-wrap .upk-item::before' => 'mix-blend-mode: {{VALUE}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-item',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-banner-wrap .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-item',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'show_title' => 'yes',
					'title_text!' => ''
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-banner-wrap .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'label' => __('Text Stroke', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-title',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label'     => esc_html__('Sub Title', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'show_sub_title' => 'yes',
					'sub_title_text!' => ''
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-sub-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_title_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-banner-wrap .upk-sub-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'sub_title_text_shadow',
				'label' => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-sub-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => esc_html__('Text', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => 'yes',
					'layout_direction!' => 'style-4',
					'description_text!' => ''
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_link_btn',
			[
				'label'     => esc_html__('Read More', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'readmore' => 'yes',
					'readmore_text!' => ''
				]
			]
		);


		$this->start_controls_tabs('tabs_link_btn_style');

		$this->start_controls_tab(
			'tab_link_btn_normal',
			[
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'link_btn_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'link_btn_background',
				'selector'  => '{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'link_btn_border',
				'selector'    => '{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a',
			]
		);

		$this->add_responsive_control(
			'link_btn_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'link_btn_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'link_btn_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'link_btn_shadow',
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_btn_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_btn_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'link_btn_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'link_btn_hover_background',
				'selector'  => '{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a:hover',
			]
		);

		$this->add_control(
			'link_btn_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'link_btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-buy-btn a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => esc_html__('Badge', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_badge',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_banner_size',
							'value'    => 'yes'
						],
					]
				],
			]
		);


		$this->start_controls_tabs('tabs_badge_style');

		$this->start_controls_tab(
			'tab_badge_normal',
			[
				'label' => esc_html__('Primary', 'ultimate-post-kit'),
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-discount' => 'color: {{VALUE}};',
				],
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'badge_background',
				'selector'  => '{{WRAPPER}} .upk-banner-wrap .upk-discount',
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'badge_border',
				'selector'    => '{{WRAPPER}} .upk-banner-wrap .upk-discount',
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-discount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-discount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'badge_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-discount' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-discount',
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-discount',
				'condition'	  => [
					'show_badge'	=> 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_badge_banner_size',
			[
				'label' => esc_html__('Secondary', 'ultimate-post-kit'),
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'banner_size_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text' => 'color: {{VALUE}};',
				],
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'banner_size_background',
				'selector'  => '{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text',
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'banner_size_border',
				'selector'    => '{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text',
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'banner_size_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'banner_size_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'banner_size_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'banner_size_shadow',
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text',
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'banner_size_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-banner-wrap .upk-banner-size-text',
				'condition'	  => [
					'show_banner_size'	=> 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	public function render_readmore() {
        $settings = $this->get_settings_for_display();

        if ( ! $settings['readmore'] ) {
			return;
		}

        $this->add_render_attribute(
            [
                'readmore-link' => [
                    'class' => [
                        'upk-banner-readmore',
                        $settings['readmore_hover_animation'] ? 'elementor-animation-' . $settings['readmore_hover_animation'] : '',
                    ],
                    'href'   => isset($settings['readmore_link']['url']) ? esc_url($settings['readmore_link']['url']) : '#',
                    'target' => $settings['readmore_link']['is_external'] ? '_blank' : '_self'
                ]
            ], '', '', true
        );

        ?>
        <?php if (( ! empty( $settings['readmore_link']['url'] )) && ( $settings['readmore'] )): ?>
            <div class="upk-buy-btn">
                <a <?php echo $this->get_render_attribute_string( 'readmore-link' ); ?>>
                    <?php echo esc_html($settings['readmore_text']); ?>
                    <?php if ($settings['readmore_icon']['value']) : ?>
                        <span class="upk-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">
                            <?php Icons_Manager::render_icon( $settings['readmore_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        <?php endif;
    }

	public function render_banner_content() {
		$settings  = $this->get_settings_for_display();

		?>

		<div class="upk-content">
			<div class="upk-content-inner">
				<?php if ($settings['show_title'] and $settings['title_text']) : ?>
					<<?php echo Utils::get_valid_html_tag($settings['title_size']); ?> class="upk-title">
						<?php echo wp_kses_post($settings['title_text'], ultimate_post_kit_title_tags('title_text')); ?>
					</<?php echo Utils::get_valid_html_tag($settings['title_size']); ?>>
				<?php endif; ?>

				<?php if ($settings['sub_title_text'] and 'yes' == $settings['show_sub_title']) : ?>
					<div class="upk-sub-title">
						<?php echo esc_html($settings['sub_title_text']); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ($settings['description_text'] and 'yes' == $settings['show_text'] and $settings['layout_direction'] !== 'style-4') : ?>
				<div class="upk-text">
					<?php echo wp_kses_post($settings['description_text'], ultimate_post_kit_title_tags('description_text')); ?>
				</div>
			<?php endif; ?>

			<?php $this->render_readmore(); ?>
		</div>

		<?php if ('yes' == $settings['show_badge'] and $settings['badge_text']) : ?>
			<div class="upk-discount">
				<?php echo esc_html($settings['badge_text']); ?>
			</div>
		<?php endif; ?>

		<?php if ('yes' == $settings['show_banner_size'] and $settings['banner_size_text']) : ?>
			<div class="upk-banner-size-text">
				<span>
					<?php echo esc_html($settings['banner_size_text']); ?>
				</span>
			</div>
		<?php endif; ?>

		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('upk-banner', 'class', ['upk-banner-wrap ', 'upk-sale-badge-' . $settings['sale_badge_direction'], 'upk-size-badge-' . $settings['size_badge_direction'], 'upk-banner-' . $settings['layout_direction'] . '']);

		?>
		<div <?php echo $this->get_render_attribute_string('upk-banner'); ?>>
			<div class="upk-item">
				<?php $this->render_banner_content(); ?>
			</div>
		</div>
		<?php
	}
}
