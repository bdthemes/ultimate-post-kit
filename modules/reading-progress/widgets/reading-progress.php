<?php
namespace UltimatePostKit\Modules\ReadingProgress\Widgets;

use UltimatePostKit\Base\Module_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class News Ticker
 */
class Reading_Progress extends Module_Base {

	public function get_name() {
		return 'upk-reading-progress';
	}

	public function get_title() {
		return BDTUPK . esc_html__( 'Reading Progress Bar', 'ultimate-post-kit' );
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-reading-progress';
	}

	public function get_categories() {
		return [ 'ultimate-post-kit' ];
	}

	public function get_keywords() {
		return [ 'bar', 'scroll', 'animate', 'line', 'scrolline', 'pop', 'reading progress' ];
	}

	public function get_script_depends() {
		if ( $this->upk_is_edit_mode() ) {
			return [ 'upk-all-scripts' ];
		} else {
			return [ 'scrolline', 'upk-reading-progress' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/9N_2WDXUjo0';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_style_reading_progress',
			[
				'label'     => esc_html__( 'Reading Progress', 'ultimate-post-kit' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'direction',
			[
				'label'   => esc_html__( 'Direction', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'ultimate-post-kit' ),
					'vertical'   => esc_html__( 'Vertical', 'ultimate-post-kit' ),
				],
				'render_type' => 'template'
			]
		);
		
		$this->add_control(
			'horizontal_position',
			[
				'label'   => esc_html__( 'Position', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'top'    => esc_html__( 'Top', 'ultimate-post-kit' ),
					'bottom' => esc_html__( 'Bottom', 'ultimate-post-kit' ),
				],
				'condition' => [
					'direction' => 'horizontal'
				],
				'render_type' => 'template'
			]
		);
		
		$this->add_control(
			'vertical_position',
			[
				'label'   => esc_html__( 'Position', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => esc_html__( 'Left', 'ultimate-post-kit' ),
					'right'  => esc_html__( 'Right', 'ultimate-post-kit' ),
				],
				'condition' => [
					'direction' => 'vertical'
				],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'back_color',
			[
				'label'     => esc_html__('Back Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'front_color',
			[
				'label'     => esc_html__('Front Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'weight',
			[
				'label'   => esc_html__( 'Weight', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'opacity',
			[
				'label'   => esc_html__( 'Opacity', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
			]
		);

		$this->add_control(
			'zindex',
			[
				'label'   => esc_html__( 'Z-index', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::TEXT,
				'label_block' => false
			]
		);

		$this->add_control(
			'reverse',
			[
				'label'   => esc_html__( 'Reverse', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'render_type' => 'template'
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$id       = 'upk-reading-progress-' . $this->get_id();
		$settings = $this->get_settings_for_display();

		$position = $settings['direction'] == 'horizontal' ? $settings['horizontal_position'] : $settings['vertical_position'];

	    $this->add_render_attribute(
			[
				'reading-progress-settings' => [
					'id'			=> $id,
					'class'			=> 'upk-reading-progress',
					'data-settings' => [
						wp_json_encode(array_filter([
							"direction"  => $settings['direction'],
							"position"   => $position,
							"backColor"  => $settings['back_color'],
							"frontColor" => $settings['front_color'],
							"weight"     => $settings['weight']['size'],
							"opacity"    => $settings['opacity']['size'],
							"zindex"     => $settings['zindex'],
							"reverse"    => ($settings["reverse"] == 'yes') ? true: false,
						]))
					],
				]
			]
		);

	    echo '<div '.$this->get_render_attribute_string( 'reading-progress-settings' ).'></div>';

	}
}
