<?php

namespace UltimatePostKit\Modules\AmoxGrid\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Traits\Global_Widget_Functions;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Amox_Grid extends Group_Control_Query
{

    use Global_Widget_Controls;
    use Global_Widget_Functions;

    private $_query = null;

    public function get_name()
    {
        return 'upk-amox-grid';
    }

    public function get_title()
    {
        return BDTUPK . esc_html__('Amox Grid', 'ultimate-post-kit');
    }

    public function get_icon()
    {
        return 'upk-widget-icon upk-icon-amox-grid';
    }

    public function get_categories()
    {
        return ['ultimate-post-kit'];
    }

    public function get_keywords()
    {
        return ['post', 'grid', 'blog', 'recent', 'news', 'amox'];
    }

    public function get_style_depends()
    {
        if ($this->upk_is_edit_mode()) {
            return ['upk-all-styles'];
        } else {
            return ['upk-font', 'upk-amox-grid'];
        }
    }

    public function get_custom_help_url()
    {
        return 'https://youtu.be/BeJ77OLErAk';
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

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'ultimate-post-kit'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
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
                    '{{WRAPPER}} .upk-amox-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Row Gap', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'primary_thumbnail',
                'exclude' => ['custom'],
                'default' => 'medium',
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label' => __('Alignment', 'ultimate-post-kit'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'ultimate-post-kit'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'ultimate-post-kit'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'ultimate-post-kit'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-item .upk-content' => 'text-align: {{VALUE}};',
                ],
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
                'label' => esc_html__('Item Limit', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 21,
                    ],
                ],
                'default' => [
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
                'label' => esc_html__('Category', 'ultimate-post-kit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        //Global Date Controls
        $this->register_date_controls();

        //Global Reading Time Controls
        $this->register_reading_time_controls();

        $this->add_control(
            'meta_separator',
            [
                'label' => __('Separator', 'ultimate-post-kit') . BDTUPK_NC,
                'type' => Controls_Manager::TEXT,
                'default' => '.',
                'label_block' => false,
            ]
        );

        $this->add_control(
            'show_comments',
            [
                'label' => esc_html__('Show Comments', 'ultimate-post-kit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Pagination', 'ultimate-post-kit'),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'global_link',
            [
                'label' => __('Item Wrapper Link', 'ultimate-post-kit'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'upk-global-link-',
                'description' => __('Be aware! When Item Wrapper Link activated then title link and read more link will not work', 'ultimate-post-kit'),
            ]
        );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'upk_section_style',
            [
                'label' => esc_html__('Items', 'ultimate-post-kit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Content Padding', 'ultimate-post-kit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-item',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'ultimate-post-kit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __('Padding', 'ultimate-post-kit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-item',
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
                'name' => 'content_hover_background',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-item:hover',
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_hover_box_shadow',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Title', 'ultimate-post-kit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_style',
            [
                'label' => esc_html__('Style', 'ultimate-post-kit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'underline',
                'options' => [
                    'underline' => esc_html__('Underline', 'ultimate-post-kit'),
                    'middle-underline' => esc_html__('Middle Underline', 'ultimate-post-kit'),
                    'overline' => esc_html__('Overline', 'ultimate-post-kit'),
                    'middle-overline' => esc_html__('Middle Overline', 'ultimate-post-kit'),
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => esc_html__('Spacing', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'ultimate-post-kit'),
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'label' => __('Text Shadow', 'ultimate-post-kit'),
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-title',
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label' => esc_html__('Normal', 'ultimate-post-kit'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_hover',
            [
                'label' => esc_html__('Hover', 'ultimate-post-kit'),
            ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_meta',
            [
                'label' => esc_html__('Meta', 'ultimate-post-kit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_date',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_comments',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_space_between',
            [
                'label' => esc_html__('Space Between', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'label' => esc_html__('Typography', 'ultimate-post-kit'),
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-meta',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_category',
            [
                'label' => esc_html__('Category', 'ultimate-post-kit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_category' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_bottom_spacing',
            [
                'label' => esc_html__('Spacing', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-category a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'category_background',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'category_border',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-category a',
            ]
        );

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label' => esc_html__('Border Radius', 'ultimate-post-kit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label' => esc_html__('Padding', 'ultimate-post-kit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_spacing',
            [
                'label' => esc_html__('Space Between', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-category' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'category_shadow',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => esc_html__('Typography', 'ultimate-post-kit'),
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-category a',
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
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'category_hover_background',
                'selector' => '{{WRAPPER}} .upk-amox-grid .upk-category a:hover',
            ]
        );

        $this->add_control(
            'category_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'category_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .upk-amox-grid .upk-category a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        if (_is_upk_pro_activated()) {
            $this->start_controls_tab(
                'tab_category_active',
                [
                    'label' => esc_html__('Active', 'ultimate-post-kit'),
                ]
            );

            $this->add_control(
                'category_active_normal_heading',
                [
                    'label' => esc_html__('NORMAL', 'ultimate-post-kit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->end_controls_tab();
        }

        $this->end_controls_tabs();

        $this->end_controls_section();

        //Pagination
        $this->start_controls_section(
            'section_style_pagination',
            [
                'label' => esc_html__('Pagination', 'ultimate-post-kit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_pagination_style');

        $this->start_controls_tab(
            'tab_pagination_normal',
            [
                'label' => esc_html__('Normal', 'ultimate-post-kit'),
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li a, {{WRAPPER}} ul.upk-pagination li span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pagination_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} ul.upk-pagination li a',
                'separator' => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border',
                'label' => esc_html__('Border', 'ultimate-post-kit'),
                'selector' => '{{WRAPPER}} ul.upk-pagination li a',
            ]
        );

        $this->add_responsive_control(
            'pagination_offset',
            [
                'label' => esc_html__('Offset', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .upk-pagination' => 'margin-top: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_space',
            [
                'label' => esc_html__('Spacing', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .upk-pagination' => 'margin-left: {{SIZE}}px;',
                    '{{WRAPPER}} .upk-pagination > *' => 'padding-left: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_padding',
            [
                'label' => esc_html__('Padding', 'ultimate-post-kit'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li a' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_radius',
            [
                'label' => esc_html__('Radius', 'ultimate-post-kit'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li a' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_arrow_size',
            [
                'label' => esc_html__('Arrow Size', 'ultimate-post-kit'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'label' => esc_html__('Typography', 'ultimate-post-kit'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} ul.upk-pagination li a, {{WRAPPER}} ul.upk-pagination li span',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_pagination_hover',
            [
                'label' => esc_html__('Hover', 'ultimate-post-kit'),
            ]
        );

        $this->add_control(
            'pagination_hover_color',
            [
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pagination_hover_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} ul.upk-pagination li a:hover',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_pagination_active',
            [
                'label' => esc_html__('Active', 'ultimate-post-kit'),
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label' => esc_html__('Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li.upk-active a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_border_color',
            [
                'label' => esc_html__('Border Color', 'ultimate-post-kit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.upk-pagination li.upk-active a' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pagination_active_background',
                'selector' => '{{WRAPPER}} ul.upk-pagination li.upk-active a',
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
            $args['paged'] = max(1, get_query_var('paged'), get_query_var('page'));
        }
        $args = array_merge($default, $args);
        $this->_query = new WP_Query($args);
    }

    public function render_comments($id = 0)
    {

        if (!$this->get_settings('show_comments')) {
            return;
        }
        ?>

		<div class="upk-comments upk-flex upk-flex-middle">
			<i class="eicon-comments"></i>
			<span><?php echo get_comments_number($id) ?></span>
		</div>

	<?php
}

    public function render_post_grid_item($post_id, $image_size, $active_item)
    {
        $settings = $this->get_settings_for_display();

        if ('yes' == $settings['global_link']) {

            $this->add_render_attribute('grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
        }

        $this->add_render_attribute('grid-item', 'class', 'upk-item ' . $active_item, true);

        ?>
		<div <?php $this->print_render_attribute_string('grid-item');?>>
			<div class="upk-img-wrap">
				<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size);?>
			</div>
			<div class="upk-content">
				<?php $this->render_category();?>
				<?php $this->render_title(substr($this->get_name(), 4));?>

				<?php if ($settings['show_comments'] or $settings['show_date'] or $settings['show_reading_time']): ?>
				<div class="upk-meta upk-flex-inline upk-flex-middle">
					<?php $this->render_date();?>
					<?php if (_is_upk_pro_activated()):
            if ('yes' === $settings['show_reading_time']): ?>
													<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
														<?php echo ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']);?>
													</div>
												<?php endif;?>
					<?php endif;?>
					<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
						<?php $this->render_comments($post_id);?>
					</div>
				</div>
				<?php endif;?>
			</div>
		</div>


	<?php
}

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $this->query_posts($settings['item_limit']['size']);
        $wp_query = $this->get_query();

        if (!$wp_query->found_posts) {
            return;
        }

        $this->add_render_attribute('grid-wrap', 'class', 'upk-amox-grid');

        if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
            $this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
            if (isset($settings['upk_in_animation_delay']['size'])) {
                $this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
            }
        }

        ?>
		<div <?php $this->print_render_attribute_string('grid-wrap');?>>
			<?php
$i = 0;
        while ($wp_query->have_posts()):
            $wp_query->the_post();
            $thumbnail_size = $settings['primary_thumbnail_size'];

            $i++;
            $active_item = '';
            if (_is_upk_pro_activated()) {
                $active_item = apply_filters('amox_grid_active_item', $this, $i);
            }

            ?>
            <?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size, $active_item);?>
        <?php endwhile;?>
		</div>

		<?php

        if ($settings['show_pagination']) {?>
			<div class="ep-pagination">
				<?php ultimate_post_kit_post_pagination($wp_query, $this->get_id());?>
			</div>
<?php
}
        wp_reset_postdata();
    }
}
