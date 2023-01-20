<?php
namespace UltimatePostKit\Modules\Animations;

use Elementor\Controls_Manager;
use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'animations';
	}

	public function __construct() {
		parent::__construct();
		$this->add_actions();
		
	}

	public function register_section($element) {
		
		

		$element->start_controls_section(
			'section_upk_in_animation_controls',
			[
				'tab'   => Controls_Manager::TAB_CONTENT,
				'label' => esc_html__('Entrance Animation', 'ultimate-post-kit') . BDTUPK_NC,
			]
		);

		$element->end_controls_section();
	}


	public function register_controls( $widget, $args ) {
			
		$widget->add_control(
			'upk_in_animation_show',
			[
				'label'              => esc_html__( 'Entrance Animation', 'ultimate-post-kit' ),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'upk_in_animation_perspective',
			[
				'label'       => esc_html__('Perspective', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-perspective: {{SIZE}}px;'
				],
				'condition' => [
					'upk_in_animation_show' => 'yes',
				],
				'separator' => 'before',
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);

		$widget->add_control(
			'upk_in_animation_delay',
			[
				'label' => esc_html__('Delay(ms)', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'step' => 10,
						'max' => 1000,
					],
				],
				'condition' 	=> [
					'upk_in_animation_show' => 'yes',
				],
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);
		
		$widget->add_control(
			'upk_in_animation_transition_duration',
			[
				'label' => esc_html__('Transition Duration(ms)', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'step' => 10,
						'max' => 2000,
					],
				],
				'condition' 	=> [
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-transition-duration: {{SIZE}}ms;'
				],
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);

		$widget->add_control(
			'upk_in_animation_transform_origin',
			[
				'label'     => esc_html__('Transform Origin', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'center top',
				'condition' 	=> [
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-transform-origin: {{VALUE}};'
				],
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);

		$widget->add_control(
			'upk_in_animation_transform_heading',
			[
				'label' 	=> __( 'TRANSFORM', 'bdthemes-element-pack' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'upk_in_animation_show' => 'yes',
				],
			]
		);

		$widget->add_control(
			'upk_in_animation_translate_toggle',
			[
				'label' 		=> __( 'Translate', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'upk_in_animation_show' => 'yes',
				],
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'upk_in_animation_translate_x',
			[
				'label'      => esc_html__( 'Translate X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => '%',
				],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'upk_in_animation_translate_toggle' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-translate-x: {{SIZE}}{{UNIT}};'
				],
				'render_type' => 'template',
			]
		);

		$widget->add_responsive_control(
			'upk_in_animation_translate_y',
			[
				'label'      => esc_html__( 'Translate Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-translate-y: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'upk_in_animation_translate_toggle' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'render_type' => 'template',
			]
		);


		$widget->end_popover();

		$widget->add_control(
			'upk_in_animation_rotate_toggle',
			[
				'label' 		=> __( 'Rotate', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'upk_in_animation_show' => 'yes',
				],
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);

		$widget->start_popover();


		$widget->add_responsive_control(
			'upk_in_animation_rotate_x',
			[
				'label'      => esc_html__( 'Rotate X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => -80,
				],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'upk_in_animation_rotate_toggle' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-rotate-x: {{SIZE||0}}deg;'
				],
				'render_type' => 'template',
			]
		);

		$widget->add_responsive_control(
			'upk_in_animation_rotate_y',
			[
				'label'      => esc_html__( 'Rotate Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'upk_in_animation_rotate_toggle' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-rotate-y: {{SIZE||0}}deg;'
				],
				'render_type' => 'template',
			]
		);


		$widget->add_responsive_control(
			'upk_in_animation_rotate_z',
			[
				'label'   => __( 'Rotate Z', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-rotate-z: {{SIZE||0}}deg;'
				],
				'condition' => [
					'upk_in_animation_rotate_toggle' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'render_type' => 'template',
			]
		);

		$widget->end_popover();


		$widget->add_control(
			'upk_in_animation_scale',
			[
				'label' 		=> __( 'Scale', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'upk_in_animation_show' => 'yes',
				],
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'upk_in_animation_scale_x',
			[
				'label'      => esc_html__( 'Scale X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1
					],
				],
				'condition' => [
					'upk_in_animation_scale' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-scale-x: {{SIZE}};'
				],
				'render_type' => 'template',
			]
		);

		$widget->add_responsive_control(
			'upk_in_animation_scale_y',
			[
				'label'      => esc_html__( 'Scale Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1
					],
				],
				'condition' => [
					'upk_in_animation_scale' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-scale-y: {{SIZE}};'
				],
				'render_type' => 'template',
			]
		);

		$widget->end_popover();

		$widget->add_control(
			'upk_in_animation_skew',
			[
				'label' 		=> __( 'Skew', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'upk_in_animation_show' => 'yes',
				],
				'render_type' => 'template',
				'classes' => BDTUPK_IS_PC
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'upk_in_animation_skew_x',
			[
				'label'      => esc_html__( 'Skew X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'upk_in_animation_skew' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-skew-x: {{SIZE}}deg;'
				],
				'render_type' => 'template',
			]
		);

		$widget->add_responsive_control(
			'upk_in_animation_skew_y',
			[
				'label'      => esc_html__( 'Skew Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'upk_in_animation_skew' => 'yes',
					'upk_in_animation_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--upk-skew-y: {{SIZE}}deg;'
				],
				'render_type' => 'template',
			]
		);

		$widget->end_popover();

	}

	public function in_animation_before_render( $widget ) {
		$settings = $widget->get_settings_for_display();
		
		
		if ( isset($settings['upk_in_animation_show']) and $settings['upk_in_animation_show'] == 'yes' ) {
			wp_enqueue_script( 'upk-animations' );
		}
	}

	protected function add_actions() {
		
		$widgets = [
			'upk-alex-grid',
			'upk-alice-grid',
			'upk-alter-grid',
			'upk-amox-grid',
			'upk-buzz-list',
			'upk-classic-list',
			'upk-elite-grid',
			'upk-fanel-list',
			'upk-featured-list',
			'upk-harold-list',
			'upk-hazel-grid',
			'upk-kalon-grid',
			'upk-maple-grid',
			// 'upk-pixina-grid',
			'upk-ramble-grid',
			'upk-recent-comments',
			'upk-scott-list',
			'upk-tiny-list',
			'upk-welsh-list',
			'upk-wixer-grid',
		];
		
		foreach ( $widgets as $widget) {
			add_action(
				'elementor/element/' .$widget. '/upk_section_style/before_section_start', [
				$this,
				'register_section'
			] );
			
			add_action(
				'elementor/element/' .$widget. '/section_upk_in_animation_controls/before_section_end', [
				$this,
				'register_controls'
			], 10, 2 );
			
			add_action( 'elementor/frontend/widget/before_render', [
				$this,
				'in_animation_before_render'
			], 10, 1 );
		}
	}
}
