<?php

namespace UltimatePostKit\Modules\TagCloud\Widgets;

use UltimatePostKit\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Tag_Cloud extends Module_Base {
	private $_query = null;

	public function get_name() {
		return 'upk-tag-cloud';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Tag Cloud', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-tag-cloud';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'list', 'blog', 'recent', 'news', 'category', 'tag', 'cloud', 'tags'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-tag-cloud'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/DLl_bqh_E2M';
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
				'default' => 'inline',
				'options' => [
					'inline' => esc_html__('Inline', 'ultimate-post-kit'),
					'grid'   => esc_html__('Grid', 'ultimate-post-kit'),
				],
				'prefix_class' => 'upk-layout-style--',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'ultimate-post-kit'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
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
					'{{WRAPPER}} .upk-tag-cloud' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
				'condition' => [
					'layout_style' => 'grid'
				]
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Row Gap', 'ultimate-post-kit') . BDTUPK_NC,
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => 'grid'
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
					'{{WRAPPER}} .upk-tag-cloud' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => 'grid'
				]
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'     => esc_html__('Item Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => 'inline'
				]
			]
		);

		$this->add_responsive_control(
			'item_height',
			[
				'label'     => esc_html__('Item Height(px)', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_style' => 'grid'
				]
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
					'flex-end'  => [
						'title' => __('Right', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-right',
					],
					'space-between'  => [
						'title' => __('justify', 'ultimate-post-kit'),
						'icon'  => 'eicon-text-align-justify',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'layout_style' => 'grid'
				]
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'     => esc_html__('Show Count', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_text',
			[
				'label'   => esc_html__('Show Description as Tooltip', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'tooltip_position',
			[
				'label'   => esc_html__('Tooltip Position', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'top'          => esc_html__('Top', 'ultimate-post-kit'),
					'top-left'     => esc_html__('Top Left', 'ultimate-post-kit'),
					'top-right'    => esc_html__('Top Right', 'ultimate-post-kit'),
					'bottom'       => esc_html__('Bottom', 'ultimate-post-kit'),
					'bottom-left'  => esc_html__('Bottom Left', 'ultimate-post-kit'),
					'bottom-right' => esc_html__('Bottom Right', 'ultimate-post-kit'),
					'left'         => esc_html__('Left', 'ultimate-post-kit'),
					'right'        => esc_html__('Right', 'ultimate-post-kit'),
				],
				'condition' => [
					'show_text' => 'yes'
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
				'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'   => esc_html__('Taxonomy', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_tag',
				'options' => ultimate_post_kit_get_taxonomies(),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__('Order By', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'       => esc_html__('Name', 'ultimate-post-kit'),
					'post_date'  => esc_html__('Date', 'ultimate-post-kit'),
					'post_title' => esc_html__('Title', 'ultimate-post-kit'),
					'menu_order' => esc_html__('Menu Order', 'ultimate-post-kit'),
					'rand'       => esc_html__('Random', 'ultimate-post-kit'),
				],
			]
		);



		$this->add_control(
			'order',
			[
				'label'   => esc_html__('Order', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => [
					'asc'  => esc_html__('ASC', 'ultimate-post-kit'),
					'desc' => esc_html__('DESC', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'exclude',
			[
				'label'       => esc_html__('Exclude', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Tag ID: 12,3,1', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'parent',
			[
				'label'       => esc_html__('Parent', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Tag ID: 12', 'ultimate-post-kit'),
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
				'label' => esc_html__('Normal', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'single_background',
			[
				'label'   => esc_html__('Single Background', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item',
				'condition' => [
					'single_background' => 'yes'
				]
			]
		);

		$this->add_control(
			'multiple_background',
			[
				'label'       => esc_html__('Multiple Background', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => '#000000, #f5f5f5, #999999',
				'condition' => [
					'single_background' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-tag-cloud .upk-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item',
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
				'name'     => 'itam_background_hover',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item:hover',
			]
		);

		$this->add_control(
			'item_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'item_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_hover',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item:hover',
			]
		);

		// $this->add_control(
		// 	'hover_animation',
		// 	[
		// 		'label' => esc_html__( 'Hover Animation', 'ultimate-post-kit' ),
		// 		'type' => Controls_Manager::HOVER_ANIMATION,
		// 		'separator' => 'before'
		// 	]
		// );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category_name',
			[
				'label' => esc_html__('Name', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'category_name_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item .upk-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_name_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item:hover .upk-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_name_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item .upk-name',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_count',
			[
				'label' => esc_html__('Count', 'ultimate-post-kit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'count_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item:hover .upk-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'count_background',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'count_border',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count',
			]
		);

		$this->add_responsive_control(
			'count_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'count_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'count_box_shadow',
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'label'    => esc_html__('Typography', 'ultimate-post-kit'),
				'selector' => '{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count',
			]
		);

		$this->add_responsive_control(
			'count_spacing',
			[
				'label'     => __('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-tag-cloud .upk-item .upk-count' => 'margin-left: {{SIZE}}px;'
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$categories = get_categories([
			'taxonomy'   => $settings["taxonomy"],
			'orderby'    => $settings["orderby"],
			'order'      => $settings["order"],
			'hide_empty' => 0,
			'exclude'    => explode(',', esc_attr($settings["exclude"])),
			'parent'     => $settings["parent"],
		]);


		if (!empty($categories)) :

?>
			<div class="upk-tag-cloud">
				<?php
				$multiple_bg = explode(',', rtrim($settings['multiple_background'], ','));
				$total_category = count($categories);

				// re-creating array for the multiple colors
				$jCount = count($multiple_bg);
				$j = 0;
				for ($i = 0; $i < $total_category; $i++) {
					if ($j == $jCount) {
						$j = 0;
					}
					$multiple_bg_create[$i] = $multiple_bg[$j];
					$j++;
				}


				foreach ($categories as $index => $cat) :
					$output = '';

					$this->add_render_attribute('category-item', 'class', 'upk-item', true);

					$this->add_render_attribute('category-item', 'href', get_category_link($cat->cat_ID), true);

					$bg_color = strToHex($cat->cat_name);

					if (!empty($settings['multiple_background'])) {
						$bg_color =  $multiple_bg_create[$index];
						if (!preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $multiple_bg_create[$index])) {
							$bg_color = strToHex($cat->cat_name);
						}
					}

					if ($settings['single_background'] == '') {
						$this->add_render_attribute('category-item', 'style', "background-color: $bg_color", true);
					}

					if (!empty($cat->category_description) and $settings['show_text'] == 'yes') {
						$this->add_render_attribute('category-item', 'aria-label', $cat->category_description, true);
						$this->add_render_attribute('category-item', 'role', 'tooltip', true);
						$this->add_render_attribute('category-item', 'data-microtip-position', $settings['tooltip_position'], true);
					}

				?>

					<a <?php $this->print_render_attribute_string('category-item'); ?>>
						<span class="upk-name"><?php echo esc_html($cat->cat_name); ?></span>

						<?php if ($settings['show_count'] == 'yes') : ?>
							<span class="upk-count"><?php echo esc_html($cat->category_count); ?></span>
						<?php endif; ?>
					</a>
				<?php

					if (!empty($settings['item_limit']['size'])) {
						if ($index == ($settings['item_limit']['size'] - 1)) break;
					}

				endforeach;
				?>
			</div>
<?php
		else :

			echo '<div class="upk-alert">' . esc_html__('Category Not Found!', 'ultimate-post-kit') . '</div>';

		endif;
	}
}
