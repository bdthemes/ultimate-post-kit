<?php

namespace UltimatePostKit\Modules\NoxeSlider\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Plugin;
use UltimatePostKit\Utils;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Noxe_Slider extends Group_Control_Query {

	use Global_Widget_Controls;
	use Global_Widget_Functions;

	private $_query = null;

	public function get_name() {
		return 'upk-noxe-slider';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Noxe Slider', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-noxe-slider';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'carousel', 'blog', 'recent', 'news', 'slider', 'noxe'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-font', 'upk-noxe-slider'];
		}
	}

	public function get_script_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-scripts'];
		} else {
			return ['upk-noxe-slider'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/CyhG4NK8_lo';
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
			'item_height',
			[
				'label' => esc_html__('Image Height', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1080,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'primary_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'full',
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type'    => Controls_Manager::DIVIDER,
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
			'show_date',
			[
				'label'   => esc_html__('Show Date', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'   => esc_html__('Show Text', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'       => esc_html__('Text Limit', 'ultimate-post-kit'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'ultimate-post-kit'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 15,
				'condition'   => [
					'show_excerpt' => 'yes'
				],
			]
		);

		$this->add_control(
			'strip_shortcode',
			[
				'label'     => esc_html__('Strip Shortcode', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_excerpt' => 'yes',
				],
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
				'label'   => esc_html__('Show Comments', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'label'       => __('Separator', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '|',
				'label_block' => false,
			]
		);

		//Global Reading Time Controls
		$this->register_reading_time_controls();

		$this->end_controls_section();

		// Query Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'item_limit',
			[
				'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'default' => [
					'size' => 3,
				],
			]
		);

		$this->register_query_builder_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => __('Slider Settings', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => __('Autoplay', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', 'ultimate-post-kit'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[
				'label' => esc_html__('Pause on Hover', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'grab_cursor',
			[
				'label'   => __('Grab Cursor', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => __('Loop', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);


		$this->add_control(
			'speed',
			[
				'label'   => __('Animation Speed (ms)', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'observer',
			[
				'label'       => __('Observer', 'ultimate-post-kit'),
				'description' => __('When you use carousel in any hidden place (in tabs, accordion etc) keep it yes.', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SWITCHER,
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_background',
				'selector'  => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-item',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-navigation' => 'bottom: {{BOTTOM}}{{UNIT}}; right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

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
					'{{WRAPPER}} .upk-noxe-slider .upk-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-noxe-slider .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'label'    => __('Text Shadow', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-noxe-slider .upk-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'      => 'title_text_stroke',
				'label'     => __('Text Stroke', 'ultimate-post-kit') . BDTUPK_NC,
				'selector'  => '{{WRAPPER}} .upk-noxe-slider .upk-title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
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
					'{{WRAPPER}} .upk-noxe-slider .upk-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => esc_html__('Text', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-desc' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'text_margin',
			[
				'label' 	 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-noxe-slider .upk-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'text_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-noxe-slider .upk-desc',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_date',
			[
				'label'     => esc_html__('Date', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-date-wrap' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'date_background',
				'selector'  => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-date-wrap',
			]
		);

		$this->add_responsive_control(
			'date_padding',
			[
				'label' 	 => __('Padding', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-date-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'date_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-date-wrap',
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
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-meta, {{WRAPPER}} .upk-noxe-slider .upk-noxe-meta .upk-author-name-wrap .upk-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-meta .upk-author-name-wrap .upk-author-name:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-meta',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__('Category', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_category' => 'yes'
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
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'selector'  => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing',
			[
				'label'   => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_category_hover',
			[
				'label' => esc_html__('Hover', 'ultimate-post-kit'),
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'selector'  => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a:hover',
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
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Css
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => __('Navigation', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrows_heading',
			[
				'label'     => __('A R R O W S', 'ultimate-post-kit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-navigation .upk-link--arrowed g' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-noxe-navigation .upk-link--arrowed:hover g' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_spacing',
			[
				'label'   => esc_html__('Space Between', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-noxe-slider .upk-navigation-next' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'arrows_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-noxe-slider .upk-noxe-navigation .upk-link--arrowed',
			]
		);

		$this->end_controls_section();
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

		<img class="upk-noxe-img swiper-lazy" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">

	<?php
	}

	public function render_excerpt($excerpt_length) {

		if (!$this->get_settings('show_excerpt')) {
			return;
		}
		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');
	?>
		<div class="upk-desc" data-swiper-parallax-y="-100">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo ultimate_post_kit_custom_excerpt($excerpt_length, $strip_shortcode);
			}
			?>
		</div>

	<?php
	}

	public function render_category() {

		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<div class="upk-noxe-category" data-swiper-parallax-opacity="0.5">
			<?php echo upk_get_category($this->get_settings('posts_source')); ?>
		</div>
	<?php
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}
	?>
		<div class="upk-author-name-wrap">
			<span class="upk-by"><?php echo esc_html_x('by', 'Frontend', 'ultimate-post-kit') ?></span>
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

		<div class="upk-noxe-comments">
			<?php echo get_comments_number($id) ?>
			<?php echo esc_html__('Comments', 'ultimate-post-kit') ?>
		</div>

	<?php
	}

	public function render_header() {
		$id              = 'upk-noxe-slider-' . $this->get_id();
		$settings        = $this->get_settings_for_display();

		$this->add_render_attribute('noxe-slider', 'id', $id);
		$this->add_render_attribute('noxe-slider', 'class', ['upk-noxe-slider']);

		$this->add_render_attribute(
			[
				'noxe-slider' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
							"loop"           => ($settings["loop"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"effect"         => 'fade',
							"lazy"           => true,
							"parallax"       => true,
							"grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"  => 1,
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"navigation" => [
								"nextEl" => "#" . $id . " .upk-navigation-next",
								"prevEl" => "#" . $id . " .upk-navigation-prev",
							],
							// "pagination" => [
							// 	"el"             => "#" . $id . " .swiper-pagination",
							// 	"clickable"      => "true",
							// ],
							"lazy" => [
								"loadPrevNext"  => "true",
							],
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);

	?>
		<div <?php $this->print_render_attribute_string('noxe-slider'); ?>>
			<div class="upk-noxe-slider-wrapper">
				<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
					<div class="swiper-wrapper">
					<?php
				}

				public function render_footer() {
					$settings = $this->get_settings_for_display();

					?>
					</div>
					<!-- HTML EDIT BY ASIK START -->

					<div class="upk-noxe-navigation" data-swiper-parallax-opacity="0.5">
						<div class="upk-navigation-prev">
							<a class="upk-link--arrowed" href="#">
								<svg class="upk-arrow-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 32 32">
									<g fill="none" stroke="#ff215a" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
										<circle class="upk-arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
										<path class="upk-arrow-icon--arrow" d="M16.14 9.93L22.21 16l-6.07 6.07M8.23 16h13.98"></path>
									</g>
								</svg>
							</a>
						</div>

						<div class="upk-navigation-next">
							<a class="upk-link--arrowed" href="#">
								<svg class="upk-arrow-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 32 32">
									<g fill="none" stroke="#ff215a" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
										<circle class="upk-arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
										<path class="upk-arrow-icon--arrow" d="M16.14 9.93L22.21 16l-6.07 6.07M8.23 16h13.98"></path>
									</g>
								</svg>
							</a>
						</div>
					</div>

					<!-- HTML EDIT BY ASIK END -->
				</div>

			</div>
		</div>

	<?php
				}

				public function render_post_grid_item($post_id, $image_size, $excerpt_length) {
					$settings = $this->get_settings_for_display();

					$this->add_render_attribute('slider-item', 'class', 'upk-noxe-item swiper-slide', true);

	?>

		<div <?php $this->print_render_attribute_string('slider-item'); ?>>
			<div class="upk-noxe-item-box-wrap">
				<div class="upk-noxe-image-wrap">
					<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>
					<?php $this->render_category(); ?>
					<?php if ($settings['show_date']) : ?>
						<div class="upk-noxe-date-wrap">
							<span class="upk-noxe-month" data-swiper-parallax-x="-40"><?php echo get_the_date('m/d'); ?></span>
							<span class="upk-noxe-year" data-swiper-parallax-x="-30"><?php echo get_the_date('Y'); ?></span>
						</div>
					<?php endif; ?>
				</div>
				<div class="upk-noxe-content">
					<div class="upk-noxe-inner-content">
						<?php if ($settings['show_title']) : ?>
							<div data-swiper-parallax-y="-150">
								<?php $this->render_title(substr($this->get_name(), 4)); ?>
							</div>
						<?php endif; ?>

						<?php if ($settings['show_author'] or $settings['show_comments'] or $settings['show_reading_time']) : ?>
							<div class="upk-noxe-meta" data-swiper-parallax-y="-80">
								<?php $this->render_author(); ?>

								<?php if ($settings['show_comments'] == 'yes') : ?>
									<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php $this->render_comments($post_id); ?>
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

					<?php $this->render_excerpt($excerpt_length); ?>
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

						$this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']);
					}

					$this->render_footer();

					wp_reset_postdata();
				}
			}
