<?php
namespace UltimatePostKit\Modules\SocialShare\Widgets;

use UltimatePostKit\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use UltimatePostKit\Modules\SocialShare\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Social_Share extends Module_Base {

	protected $_has_template_content = false;

	private static $medias_class = [
		'email'      => 'upk-icon-envelope',
		'vkontakte'  => 'upk-icon-vk',
	];

	private static function get_social_media_class( $media_name ) {
		if ( isset( self::$medias_class[ $media_name ] ) ) {
			return self::$medias_class[ $media_name ];
		}
		return 'upk-icon-' . $media_name;
	}


	public function get_name() {
		return 'upk-social-share';
	}

	public function get_title() {
		return BDTUPK . esc_html__( 'Social Share', 'ultimate-post-kit' );
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-social-share';
	}

	public function get_categories() {
		return [ 'ultimate-post-kit' ];
	}

	public function get_keywords() {
		return [ 'social', 'link', 'share' ];
	}

	public function get_style_depends() {
        if ($this->upk_is_edit_mode()) {
            return ['upk-all-styles'];
        } else {
            return [ 'upk-font', 'upk-social-share' ];
        }
    }
	
	public function get_script_depends() {
		if ( $this->upk_is_edit_mode() ) {
			return [ 'upk-all-scripts' ];
		} else {
			return [ 'goodshare' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/77S087dzK3Q';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_buttons_content',
			[
				'label' => esc_html__( 'Share Buttons', 'ultimate-post-kit' ),
			]
		);

		$repeater = new Repeater();

		$medias = Module::get_social_media();

		$medias_names = array_keys( $medias );

		$repeater->add_control(
			'button',
			[
				'label' => esc_html__( 'Social Media', 'ultimate-post-kit' ),
				'type' => Controls_Manager::SELECT,
				'options' => array_reduce( $medias_names, function( $options, $media_name ) use ( $medias ) {
					$options[ $media_name ] = $medias[ $media_name ]['title'];

					return $options;
				}, [] ),
				'default' => 'facebook',
			]
		);

		$repeater->add_control(
			'text',
			[
				'label' => esc_html__( 'Custom Label', 'ultimate-post-kit' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'share_buttons',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[ 'button' => 'facebook' ],
					[ 'button' => 'linkedin' ],
					[ 'button' => 'twitter' ],
					[ 'button' => 'pinterest' ],
				],
				'title_field' => '{{{ button }}}',
			]
		);

		$this->add_control(
			'view',
			[
				'label'       => esc_html__( 'View', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'options'     => [
					'icon-text' => 'Icon & Text',
					'icon'      => 'Icon',
					'text'      => 'Text',
				],
				'default'      => 'icon-text',
				'separator'    => 'before',
				'prefix_class' => 'upk-ss-btns-view-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'show_counter',
			[
				'label'     => esc_html__( 'Count', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'view!' => 'icon',
				],
			]
		);


		$this->add_control(
			'show_counter_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Note: Social share count only works with those platform: vkontakte, facebook, odnoklassniki, moimir, linkedin, tumblr, pinterest, buffer.', 'ultimate-post-kit' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
				'condition' => [
					'show_counter' => 'yes',
					'view!' => 'icon',
				],
			]
		);

		$this->add_control(
			'hr',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'layout_style',
			[
				'label'   => esc_html__( 'Display', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'inline' => esc_html__( 'Inline', 'ultimate-post-kit' ),
					'grid'   => esc_html__( 'Grid', 'ultimate-post-kit' ),
				],
				'prefix_class' => 'upk-layout-style--',
			]
		);
		
		$this->add_responsive_control(
			'columns',
			[
				'label'          => __( 'Columns', 'ultimate-post-kit' ),
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
					'{{WRAPPER}} .upk-social-share' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
				'condition' => [
					'layout_style' => 'grid'
				]
			]
		);
		
		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__( 'Gap', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-social-share' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => 'grid'
				]
			]
		);

		$this->add_responsive_control(
			'inline_column_gap',
			[
				'label'   => esc_html__( 'Columns Gap', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ss-btn' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .upk-ep-grid'             => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
				'condition' => [
					'layout_style' => 'inline'
				]
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__( 'Rows Gap', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ss-btn' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => 'inline'
				]
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'   => esc_html__( 'Alignment', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'ultimate-post-kit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'ultimate-post-kit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'ultimate-post-kit' ),
						'icon'  => 'eicon-text-align-right',
					],
					'space-between' => [
						'title' => esc_html__( 'Justify', 'ultimate-post-kit' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-social-share' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'layout_style' => 'inline'
				]
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type'    => Controls_Manager::DIVIDER,
				'condition' => [
					'layout_style' => 'inline'
				]
			]
		);

		$this->add_control(
			'layout_position',
			[
				'label'   => esc_html__( 'Position', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'      => esc_html__( 'Default', 'ultimate-post-kit' ),
					'top-left'     => esc_html__( 'Top Left', 'ultimate-post-kit' ),
					'top-right'    => esc_html__( 'Top Right', 'ultimate-post-kit' ),
					'center-left'  => esc_html__( 'Center Left', 'ultimate-post-kit' ),
					'center-right' => esc_html__( 'Center Right', 'ultimate-post-kit' ),
					'bottom-left'  => esc_html__( 'Bottom Left', 'ultimate-post-kit' ),
					'bottom-right' => esc_html__( 'Bottom Right', 'ultimate-post-kit' ),
				],
				'prefix_class' => 'upk-ss-position--',
				'condition' => [
					'layout_style' => 'inline'
				]
			]
		);

		$this->add_control(
			'social_share_offset',
			[
				'label' => __( 'Offset', 'ultimate-post-kit' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'layout_position!' => 'default',
					'layout_style' => 'inline'
				],
				'render_type' => 'ui',
			]
		);
		
		$this->start_popover();
		
		$this->add_responsive_control(
			'ss_horizontal_offset',
			[
				'label'       => esc_html__( 'Horizontal', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'   => -200,
						'max'   => 200,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'social_share_offset' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.upk-layout-style--inline .upk-social-share' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'ss_vertical_offset',
			[
				'label'       => esc_html__( 'Vertical', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'   => -200,
						'max'   => 200,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'social_share_offset' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.upk-layout-style--inline .upk-social-share' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_popover();

		$this->add_control(
			'share_url_type',
			[
				'label'   => esc_html__( 'Target URL', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'current_page' => esc_html__( 'Current Page', 'ultimate-post-kit' ),
					'custom'       => esc_html__( 'Custom', 'ultimate-post-kit' ),
				],
				'default'   => 'current_page',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'share_url',
			[
				'label'         => esc_html__( 'URL', 'ultimate-post-kit' ),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'placeholder'   => 'http://your-link.com',
				'condition'     => [
					'share_url_type' => 'custom',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_buttons_style',
			[
				'label' => esc_html__( 'Share Buttons', 'ultimate-post-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'flat'     => esc_html__( 'Flat', 'ultimate-post-kit' ),
					'framed'   => esc_html__( 'Framed', 'ultimate-post-kit' ),
					'gradient' => esc_html__( 'Gradient', 'ultimate-post-kit' ),
					'minimal'  => esc_html__( 'Minimal', 'ultimate-post-kit' ),
					'boxed'    => esc_html__( 'Boxed Icon', 'ultimate-post-kit' ),
				],
				'default'      => 'flat',
				'prefix_class' => 'upk-ss-btns-style-',
			]
		);

		$prefix_class = add_filter('social_share_prefix_class', '');

		$this->add_control(
			'icon_top',
			[
				'label'        => __( 'Icon Position Top', 'ultimate-post-kit' ) . BDTUPK_PC,
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => $prefix_class,
				'condition' => [
					'layout_position!' => ['center-left', 'center-right'],
					'view' => 'icon-text'
				],
				'classes' => BDTUPK_IS_PC
			]
		);

		$this->add_responsive_control(
			'ss_icon_padding',
			[
				'label'      => __('Icon Padding', 'ultimate-post-kit') . BDTUPK_PC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}}.upk-ss-icon-top--yes .upk-ss-btn .upk-ss-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'icon_top' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'ss_text_padding',
			[
				'label'      => __('Text Padding', 'ultimate-post-kit') . BDTUPK_PC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}}.upk-ss-icon-top--yes .upk-ss-btn .upk-social-share-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'icon_top' => 'yes'
				],
			]
		);

		$this->add_control(
			'divider_one',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'button_size',
			[
				'label' => esc_html__( 'Button Size', 'ultimate-post-kit' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0.5,
						'max'  => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ss-btn' => 'font-size: calc({{SIZE}}{{UNIT}} * 10);',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'ultimate-post-kit' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min'  => 0.5,
						'max'  => 4,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .upk-ss-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'view!' => 'text',
				],
			]
		);

		$this->add_responsive_control(
			'button_height',
			[
				'label' => esc_html__( 'Button Height', 'ultimate-post-kit' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min'  => 1,
						'max'  => 7,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .upk-ss-btn' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_size',
			[
				'label'      => esc_html__( 'Border Size', 'ultimate-post-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
					'em' => [
						'max'  => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-ss-btn' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => [ 'framed', 'boxed' ],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'ultimate-post-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .upk-ss-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label'      => esc_html__( 'Text Padding', 'ultimate-post-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}.upk-ss-btns-view-text .upk-social-share-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'view' => 'text',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'label'    => esc_html__( 'Typography', 'ultimate-post-kit' ),
				'selector' => '{{WRAPPER}} .upk-social-share-title, {{WRAPPER}} .upk-ss-counter',
				'exclude'  => [ 'line_height' ],
			]
		);

		$this->add_control(
			'color_source',
			[
				'label'       => esc_html__( 'Color', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'options'     => [
					'original' => 'Original Color',
					'custom'   => 'Custom Color',
				],
				'default'      => 'original',
				'prefix_class' => 'upk-ss-btns-color-',
				'separator'    => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'     => esc_html__( 'Normal', 'ultimate-post-kit' ),
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label'     => esc_html__( 'Primary Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.upk-ss-btns-style-flat .upk-ss-btn,
					 {{WRAPPER}}.upk-ss-btns-style-gradient .upk-ss-btn,
					 {{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-btn .upk-ss-icon,
					 {{WRAPPER}}.upk-ss-btns-style-minimal .upk-ss-btn .upk-ss-icon' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}.upk-ss-btns-style-framed .upk-ss-btn,
					 {{WRAPPER}}.upk-ss-btns-style-minimal .upk-ss-btn,
					 {{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-btn' => 'color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'secondary_color',
			[
				'label'     => esc_html__( 'Secondary Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.upk-ss-btns-style-flat .upk-ss-icon, 
					 {{WRAPPER}}.upk-ss-btns-style-flat .upk-social-share-text, 
					 {{WRAPPER}}.upk-ss-btns-style-gradient .upk-ss-icon,
					 {{WRAPPER}}.upk-ss-btns-style-gradient .upk-social-share-text,
					 {{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-icon,
					 {{WRAPPER}}.upk-ss-btns-style-minimal .upk-ss-icon' => 'color: {{VALUE}}',
					 '{{WRAPPER}}.upk-ss-btns-style-framed .upk-ss-btn' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-btn, {{WRAPPER}}.upk-ss-btns-style-framed .upk-ss-btn' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
					'style' => ['framed', 'boxed']
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'     => esc_html__( 'Hover', 'ultimate-post-kit' ),
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'primary_color_hover',
			[
				'label'     => esc_html__( 'Primary Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.upk-ss-btns-style-flat .upk-ss-btn:hover,
					 {{WRAPPER}}.upk-ss-btns-style-gradient .upk-ss-btn:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}.upk-ss-btns-style-framed .upk-ss-btn:hover,
					 {{WRAPPER}}.upk-ss-btns-style-minimal .upk-ss-btn:hover,
					 {{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-btn:hover' => 'color: {{VALUE}}; border-color: {{VALUE}}',
					'{{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-btn:hover .upk-ss-icon, 
					 {{WRAPPER}}.upk-ss-btns-style-minimal .upk-ss-btn:hover .upk-ss-icon' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'secondary_color_hover',
			[
				'label'     => esc_html__( 'Secondary Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.upk-ss-btns-style-flat .upk-ss-btn:hover .upk-ss-icon, 
					 {{WRAPPER}}.upk-ss-btns-style-flat .upk-ss-btn:hover .upk-social-share-text, 
					 {{WRAPPER}}.upk-ss-btns-style-gradient .upk-ss-btn:hover .upk-ss-icon,
					 {{WRAPPER}}.upk-ss-btns-style-gradient .upk-ss-btn:hover .upk-social-share-text,
					 {{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-btn:hover .upk-ss-icon,
					 {{WRAPPER}}.upk-ss-btns-style-minimal .upk-ss-btn:hover .upk-ss-icon' => 'color: {{VALUE}}',
					 '{{WRAPPER}}.upk-ss-btns-style-framed .upk-ss-btn:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'border_hover_color',
			[
				'label'     => esc_html__( 'Border Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.upk-ss-btns-style-boxed .upk-ss-btn:hover, {{WRAPPER}}.upk-ss-btns-style-framed .upk-ss-btn:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
					'style' => ['framed', 'boxed']
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function has_counter( $media_name ) {
		$settings = $this->get_active_settings();

		return 'icon' !== $settings['view'] && 'yes' === $settings['show_counter'] && ! empty( Module::get_social_media( $media_name )['has_counter'] );
	}
	
	public function render() {

		$settings  = $this->get_active_settings();

		if ( empty( $settings['share_buttons'] ) ) {
			return;
		}

		$show_text = 'text' === $settings['view'] || 'icon-text' === $settings['view'];
		?>
		<div class="upk-social-share upk-ep-grid">
			<?php
			foreach ( $settings['share_buttons'] as $button ) {
				$social_name = $button['button'];
				$has_counter = $this->has_counter( $social_name );

				if ( 'custom' === $settings['share_url_type'] ) {
					$this->add_render_attribute( 'social-attrs', 'data-url', esc_url( $settings['share_url']['url'] ), true );
				}

				$this->add_render_attribute(
					[
						'social-attrs' => [
							'class' => [
								'upk-ss-btn',
								'upk-ss-' . $social_name
							],
							'data-social' => $social_name,
						]
					], '', '', true
				);

				?>
				<div class="upk-social-share-item upk-ep-grid-item">
					<div <?php $this->print_render_attribute_string( 'social-attrs' ); ?>>
						<?php if ( 'icon' === $settings['view'] || 'icon-text' === $settings['view'] ) : ?>
							<span class="upk-ss-icon">
								<i class="<?php echo self::get_social_media_class( $social_name ); ?>"></i>
							</span>
						<?php endif; ?>
						<?php if ( $show_text || $has_counter ) : ?>
							<div class="upk-social-share-text upk-inline">
								<?php if ( 'icon-text' === $settings['view'] || 'text' === $settings['view'] ) : ?>
									<span class="upk-social-share-title">
										<?php echo $button['text'] ? esc_html($button['text']) : Module::get_social_media( $social_name )['title']; ?>
									</span>
								<?php endif; ?>
								<?php if ( $has_counter ) : ?>
									<span class="upk-social-share-counter" data-counter="<?php echo esc_attr($social_name); ?>"></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php
			}
			?>
		</div>

		
		<?php
	}

	
}
