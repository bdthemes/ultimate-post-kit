<?php

namespace UltimatePostKit\Modules\RecentComments\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use UltimatePostKit\Includes\Controls\SelectInput\Dynamic_Select;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Comment_Query;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Recent_Comments extends Group_Control_Query {

	use Global_Widget_Controls;

	private $_query = null;

	public function get_name() {
		return 'upk-recent-comments';
	}

	public function get_title() {
		return BDTUPK . esc_html__('Recent Comments', 'ultimate-post-kit');
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-recent-comments';
	}

	public function get_categories() {
		return ['ultimate-post-kit'];
	}

	public function get_keywords() {
		return ['post', 'featured', 'blog', 'recent', 'news', 'classic', 'list'];
	}

	public function get_style_depends() {
		if ($this->upk_is_edit_mode()) {
			return ['upk-all-styles'];
		} else {
			return ['upk-recent-comments'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/_RFwr9Lx7Gs';
	}


	protected function register_controls() {
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
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Row Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'columns!' => '1'
				]
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'       => __('Alignment', 'ultimate-post-kit'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
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
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .upk-recent-comments .upk-item' => 'text-align: {{VALUE}};',
				],
			]
		);

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
			'post_type',
			[
				'label'   => __('Post Type', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => $this->ultimate_post_kit_get_post_types(),
			]
		);

		$this->start_controls_tabs(
			'tabs_posts_include_exclude'
		);

		$this->start_controls_tab(
			'tab_posts_include',
			[
				'label'     => __('Include', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'posts_include_by',
			[
				'label'       => __('Include By', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => [
					'authors' => __('Authors', 'ultimate-post-kit'),
				]
			]
		);

		$this->add_control(
			'posts_include_author_ids',
			[
				'label'       => __('Authors', 'ultimate-post-kit'),
				'type'        => Dynamic_Select::TYPE,
				'multiple'    => true,
				'label_block' => true,
				'query_args'  => [
					'query' => 'authors',
				],
				'condition'   => [
					'posts_include_by' => 'authors',
				]
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_posts_exclude',
			[
				'label'     => __('Exclude', 'ultimate-post-kit'),
			]
		);

		$this->add_control(
			'posts_exclude_by',
			[
				'label'       => __('Exclude By', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => [
					'authors'          => __('Authors', 'ultimate-post-kit'),

				]
			]
		);


		$this->add_control(
			'posts_exclude_author_ids',
			[
				'label'       => __('Authors', 'ultimate-post-kit'),
				'type'        => Dynamic_Select::TYPE,
				'multiple'    => true,
				'label_block' => true,
				'query_args'  => [
					'query' => 'authors',
				],
				'condition'   => [
					'posts_exclude_by' => 'authors',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'number',
			[
				'label'       => __('Limit', 'ultimate-post-kit'),
				'description' => __('The maximum number of comments to return.', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '4',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'offset',
			[
				'label'        => __('Offset', 'ultimate-post-kit'),
				'description' => __(' The number of comments to pass over in the query.', 'ultimate-post-kit'),
				'type'         => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'parent',
			[
				'label'       => __('Only Parent', 'ultimate-post-kit'),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'status',
			[
				'label'   => __('Status', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'approve',
				'options' => [
					'approve' => __('Approve', 'ultimate-post-kit'),
					'hold'    => __('Hold', 'ultimate-post-kit'),
					'all'     => __('All', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __('Order By', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'comment_date',
				'options' => [
					'comment_author'   => __('Author', 'ultimate-post-kit'),
					'comment_approved' => __('Approved', 'ultimate-post-kit'),
					'comment_date'     => __('Date', 'ultimate-post-kit'),
					'comment_content'  => __('Content', 'ultimate-post-kit'),
					'none'             => __('Random', 'ultimate-post-kit'),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __('Order', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => __('ASC', 'ultimate-post-kit'),
					'desc' => __('DESC', 'ultimate-post-kit'),
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
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'ultimate-post-kit'),
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
			'excerpt_limit',
			[
				'label'       => esc_html__('Text Limit', 'ultimate-post-kit'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'condition'   => [
					'show_excerpt' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'     => esc_html__('Show Author', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'author_middle_text',
			[
				'label'   => __( 'Author Middle Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => ' @ ',
				'placeholder' => __( 'Enter your text', 'bdthemes-element-pack' ),
				'label_block' => false,
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'     => esc_html__('Show Date', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'upk_section_style',
			[
				'label'     => esc_html__('Items', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .upk-recent-comments .upk-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .upk-recent-comments .upk-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-recent-comments .upk-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .upk-recent-comments .upk-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .upk-recent-comments .upk-item',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => esc_html__('Text', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes'
				]
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments .upk-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'text_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-recent-comments .upk-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'     => esc_html__('Meta', 'ultimate-post-kit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_author',
							'value' => 'yes'
						],
						[
							'name'  => 'show_date',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
			[
				'label'     => esc_html__('Spacing', 'ultimate-post-kit'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_comments_meta_style');

		$this->start_controls_tab(
			'tab_comments_avatar',
			[
				'label' => esc_html__('Avatar', 'ultimate-post-kit'),
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_control(
			'avatar_size',
			[
				'label'   => __('Size', 'ultimate-post-kit'),
				'type'    => Controls_Manager::SELECT,
				'default' => '48',
				'options' => [
					'16'  => '16 X 16',
					'24'  => '24 X 24',
					'48'  => '48 X 48',
					'64'  => '64 X 64',
					'80'  => '80 X 80',
					'90'  => '90 X 90',
					'100' => '100 X 100',
					'120' => '120 X 120',
				],
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-avatar img' => 'min-width: {{VALUE}}px;',
				],
				'condition' => [
					'show_author' => 'yes'
				],
				'render_type' => 'template'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'avatar_border',
				'selector' => '{{WRAPPER}} .upk-recent-comments .upk-meta .upk-avatar img',
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'avatar_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'avatar_padding',
			[
				'label'      => __('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-avatar img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'avatar_margin',
			[
				'label' 	 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-avatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'avatar_box_shadow',
				'selector' => '{{WRAPPER}} .upk-recent-comments .upk-meta .upk-avatar img',
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_comments_name',
			[
				'label' => esc_html__('Author Text', 'ultimate-post-kit'),
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_control(
			'author_name_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-author-name' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_control(
			'author_name_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-author-name:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'author_name_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-recent-comments .upk-meta .upk-author-name',
				'condition' => [
					'show_author' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_comments_date',
			[
				'label' => esc_html__('Date', 'ultimate-post-kit'),
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'date_color',
			[
				'label'     => esc_html__('Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-date' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'date_margin',
			[
				'label' 	 => __('Margin', 'ultimate-post-kit'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upk-recent-comments .upk-meta .upk-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'date_typography',
				'label'     => esc_html__('Typography', 'ultimate-post-kit'),
				'selector'  => '{{WRAPPER}} .upk-recent-comments .upk-meta .upk-date',
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function ultimate_post_kit_get_post_types($args = []) {

		$post_type_args = [
			'show_in_nav_menus' => true,
		];

		if (!empty($args['post_type'])) {
			$post_type_args['name'] = $args['post_type'];
		}

		$_post_types = get_post_types($post_type_args, 'objects');

		$post_types = ['0' => esc_html__('All', 'ultimate-post-kit')];

		foreach ($_post_types as $post_type => $object) {
			$post_types[$post_type] = $object->label;
		}

		return $post_types;
	}

	public function get_html_output($output) {
		$tags = [
			'a'    => ['href'  => [], 'target'      => [], 'class' => []],
			'img'  => ['src'   => [], 'alt' => [], 'srcset' => [], 'height' => [], 'width' => [], 'loading' => []],
		];

		if (isset($output)) {
			echo wp_kses($output, $tags);
		}
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		global $post_id;
		$query_args = [
			'status'    => $settings['status'],
			'order'     => $settings['order'],
			'orderby'   => $settings['orderby'],
			'post_id'   => $post_id,
			'number'    => $settings['number'],
			'offset'    => $settings['offset'],
			'post_type' => $settings['post_type'],
		];

		if ($settings['parent'] == 'yes') {
			$query_args['parent'] = 0;
		}
		/**
		 * Set Authors
		 */
		$include_users = [];
		$exclude_users = [];
		if (!empty($settings['posts_include_author_ids'])) {
			if (in_array('authors', $settings['posts_include_by'])) {
				$include_users = wp_parse_id_list($settings['posts_include_author_ids']);
			}
		}
		if (!empty($settings['posts_exclude_author_ids'])) {
			if (in_array('authors', $settings['posts_exclude_by'])) {
				$exclude_users = wp_parse_id_list($settings['posts_exclude_author_ids']);
				$include_users = array_diff($include_users, $exclude_users);
			}
		}
		if (!empty($include_users)) {
			$query_args['author__in'] = $include_users;
		}

		if (!empty($exclude_users)) {
			$query_args['author__not_in'] = $exclude_users;;
		}

		$this->add_render_attribute('grid-wrap', 'class', 'upk-recent-comments');

		if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
			$this->add_render_attribute('grid-wrap', 'class', 'upk-in-animation');
			if (isset($settings['upk_in_animation_delay']['size'])) {
				$this->add_render_attribute('grid-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size']);
			}
		}

		$comments_query = new WP_Comment_Query;
		$comments = $comments_query->query($query_args);
		if ($comments) { ?>
			<div <?php $this->print_render_attribute_string('grid-wrap'); ?>>
				<?php
				foreach ($comments as $comment) {
					$title      = get_the_title($comment->comment_post_ID);
					$comment_id = $comment->comment_ID;
					$content    = wp_trim_words($comment->comment_content, $settings['excerpt_limit']);
					$avatar     = get_avatar($comment->comment_author_email, $size = $settings['avatar_size']);
					$author     = $comment->comment_author;
					$date 		= get_comment_date('F j, Y \a\t g:i a', $comment_id);

					$this->add_render_attribute(
						'upk-author',
						[
							'class'  => 'upk-author-name',
							'href'   => esc_url(get_permalink($comment->comment_post_ID)) . '#comment-' . $comment_id,
							'target' => '_blank'
						],
						null,
						true
					);

				?>
					<div class="upk-item">

						<?php if ($settings['show_author'] or $settings['show_date']) : ?>
							<div class="upk-meta upk-flex-inline upk-flex-middle">

								<?php if ($settings['show_author']) : ?>
									<div class="upk-avatar">
										<?php $this->get_html_output($avatar); ?>
									</div>
								<?php endif; ?>

								<div class="upk-author-info">
									<?php if ($settings['show_author']) : ?>
										<a <?php $this->print_render_attribute_string('upk-author'); ?>>
											<?php echo esc_html($author); ?>
											<?php if ($settings['show_title']) : ?>
												<?php echo esc_html($settings['author_middle_text']); ?>
												<?php echo esc_html($title) ?>
											<?php endif; ?>
										</a>
									<?php endif; ?>

									<?php if ($settings['show_date']) : ?>
										<div class="upk-date"><?php echo esc_html($date); ?></div>
									<?php endif; ?>
								</div>

							</div>
						<?php endif; ?>

						<?php if ($settings['show_excerpt']) : ?>
							<div class="upk-text"><?php echo wp_kses_post($content); ?></div>
						<?php endif; ?>

					</div>
	<?php
				}
				echo '</div>';
				wp_reset_postdata();
			} else {
				echo esc_html('No Comments Found.', 'ultimate-post-kit');
			}
		}
	}
