<?php
	
	namespace UltimatePostKit\Modules\ExoticList\Widgets;
	
	use Elementor\Controls_Manager;
	use Elementor\Group_Control_Border;
	use Elementor\Group_Control_Box_Shadow;
	use Elementor\Group_Control_Typography;
	use Elementor\Group_Control_Text_Shadow;
	use Elementor\Group_Control_Image_Size;
	use Elementor\Group_Control_Background;
	
	use UltimatePostKit\Traits\Global_Widget_Controls;
	use UltimatePostKit\Traits\Global_Widget_Functions;
	use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
	use WP_Query;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	
	class Exotic_List extends Group_Control_Query {
		
		use Global_Widget_Controls;
		use Global_Widget_Functions;

		private $_query = null;
		
		public function get_name() {
			return 'upk-exotic-list';
		}
		
		public function get_title() {
			return BDTUPK . esc_html__( 'Exotic List', 'ultimate-post-kit' );
		}
		
		public function get_icon() {
			return 'upk-widget-icon upk-icon-exotic-list upk-new';
		}
		
		public function get_categories() {
			return [ 'ultimate-post-kit' ];
		}
		
		public function get_keywords() {
			return [ 'post', 'grid', 'blog', 'recent', 'news', 'exotic', 'list' ];
		}
		
		public function get_style_depends() {
			if ( $this->upk_is_edit_mode() ) {
				return [ 'upk-all-styles' ];
			} else {
				return [ 'upk-font', 'upk-exotic-list' ];
			}
		}

		public function get_query() {
			return $this->_query;
		}
		
		protected function register_controls() {
			$this->start_controls_section(
				'section_content_layout',
				[
					'label' => esc_html__( 'Layout', 'ultimate-post-kit' ),
				]
			);
			

			$this->add_responsive_control(
				'row_gap',
				[
					'label'     => esc_html__( 'Row Gap', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'image_direction_style',
				[
					'label'   => esc_html__('Image Direction', 'ultimate-post-kit'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'right',
					'options' => [
						'right' => esc_html__('Right', 'ultimate-post-kit'),
						'left' => esc_html__('Left', 'ultimate-post-kit'),
					],
				]
			);

			$this->add_responsive_control(
				'content_alignment',
				[
					'label'   => __('Alignment', 'ultimate-post-kit'),
					'type'    => Controls_Manager::CHOOSE,
					'options' => [
						'left'   => [
							'title' => __('Left', 'ultimate-post-kit'),
							'icon'  => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __('Center', 'ultimate-post-kit'),
							'icon'  => 'eicon-h-align-center',
						],
						'right'  => [
							'title' => __('Right', 'ultimate-post-kit'),
							'icon'  => 'eicon-h-align-right',
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .upk-exotic-list .upk-content-wrap' => 'text-align: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name'    => 'primary_thumbnail',
					'exclude' => [ 'custom' ],
					'default' => 'medium',
				]
			);
			
			$this->end_controls_section();
			
			// Query Settings
			$this->start_controls_section(
				'section_post_query_builder',
				[
					'label' => __( 'Query', 'ultimate-post-kit' ) . BDTUPK_NC,
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'item_limit',
				[
					'label'   => esc_html__( 'Item Limit', 'ultimate-post-kit' ),
					'type'    => Controls_Manager::SLIDER,
					'range'   => [
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
				'section_content_additional',
				[
					'label' => esc_html__( 'Additional', 'ultimate-post-kit' ),
				]
			);
			
			$this->add_control(
				'show_image',
				[
					'label'   => esc_html__( 'Show Image', 'ultimate-post-kit' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);
			
			//Global Title Controls
			$this->register_title_controls();

			$this->add_control(
				'show_category',
				[
					'label'   => esc_html__( 'Show Category', 'ultimate-post-kit' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);
			
			$this->add_control(
				'show_author',
				[
					'label'   => esc_html__( 'Show Author', 'ultimate-post-kit' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'meta_separator',
				[
					'label'       => __('Separator', 'ultimate-post-kit'),
					'type'        => Controls_Manager::TEXT,
					'default'     => '//',
					'label_block' => false,
				]
			);
			
			//Global Date Controls
			$this->register_date_controls();

			//Global Reading Time Controls
			$this->register_reading_time_controls();
			
			$this->add_control(
				'show_pagination',
				[
					'label' => esc_html__( 'Show Pagination', 'ultimate-post-kit' ),
					'type'  => Controls_Manager::SWITCHER,
					'separator' => 'before'
				]
			);
			
			$this->add_control(
				'global_link',
				[
					'label'        => __( 'Item Wrapper Link', 'ultimate-post-kit' ),
					'type'         => Controls_Manager::SWITCHER,
					'prefix_class' => 'upk-global-link-',
					'description'  => __( 'Be aware! When Item Wrapper Link activated then title link and read more link will not work', 'ultimate-post-kit' ),
				]
			);
			
			$this->end_controls_section();
			
			$this->start_controls_section(
				'section_style_image',
				[
					'label'     => esc_html__( 'Image', 'ultimate-post-kit' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_image' => 'yes'
					]
				]
			);

			$this->add_responsive_control(
				'content_gap',
				[
					'label'     => esc_html__( 'Spacing', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-content-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'item_image_border',
					'selector' => '{{WRAPPER}} .upk-exotic-list .upk-image-wrap .upk-img',
				]
			);
			
			$this->add_responsive_control(
				'item_image_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'ultimate-post-kit' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .upk-exotic-list .upk-image-wrap .upk-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'item_image_height',
				[
					'label'     => esc_html__( 'Height', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 100,
							'max' => 500,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-image-wrap' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
					],
				]
			);
			
			$this->end_controls_section();
			
			$this->start_controls_section(
				'section_style_title',
				[
					'label'     => esc_html__( 'Title', 'ultimate-post-kit' ),
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
					'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-title a' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_control(
				'title_hover_color',
				[
					'label'     => esc_html__( 'Hover Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-title a:hover' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'title_spacing',
				[
					'label'     => esc_html__( 'Spacing', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'label'    => esc_html__( 'Typography', 'ultimate-post-kit' ),
					'selector' => '{{WRAPPER}} .upk-exotic-list .upk-title',
				]
			);
			
			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name'     => 'title_text_shadow',
					'label'    => __( 'Text Shadow', 'ultimate-post-kit' ),
					'selector' => '{{WRAPPER}} .upk-exotic-list .upk-title a',
				]
			);
			
			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_date',
				[
					'label'     => esc_html__( 'Date', 'ultimate-post-kit' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'conditions' => [
						'relation' => 'or',
						'terms'    => [
							[
								'name'  => 'show_date',
								'value' => 'yes'
							],
						]
					],
				]
			);
			
			$this->add_control(
				'date_color',
				[
					'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-date-wrap' => 'color: {{VALUE}};',
					],
				]
			);


			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'date_background',
					'selector' => '{{WRAPPER}}  .upk-exotic-list .upk-date-wrap',
				]
			);
			
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'           => 'date_border',
					'label'          => __( 'Border', 'ultimate-post-kit' ),
					'selector'       => '{{WRAPPER}}  .upk-exotic-list .upk-date-wrap',
				]
			);
			
			$this->add_responsive_control(
				'date_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'ultimate-post-kit' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}}  .upk-exotic-list .upk-date-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'date_padding',
				[
					'label'      => esc_html__( 'Padding', 'ultimate-post-kit' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}}  .upk-exotic-list .upk-date-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			
			$this->add_responsive_control(
				'date_spacing',
				[
					'label'     => esc_html__( 'Spacing', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min'  => 0,
							'max'  => 50,
							'step' => 2,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-content-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'date_typography',
					'label'    => esc_html__( 'Typography', 'ultimate-post-kit' ),
					'selector' => '{{WRAPPER}} .upk-exotic-list .upk-date-wrap',
				]
			);
			
			$this->end_controls_section();

			
			$this->start_controls_section(
				'section_style_author',
				[
					'label'     => esc_html__( 'Author', 'ultimate-post-kit' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'conditions' => [
						'relation' => 'or',
						'terms'    => [
							[
								'name'  => 'show_author',
								'value' => 'yes'
							],
						]
					],
				]
			);
			
			$this->add_control(
				'author_color',
				[
					'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-author a' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_control(
				'author_hover_color',
				[
					'label'     => esc_html__( 'Hover Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .upk-exotic-list .upk-author a:hover' => 'color: {{VALUE}};',
					],
				]
			);

			// $this->add_responsive_control(
			// 	'author_spacing',
			// 	[
			// 		'label'     => esc_html__( 'Spacing', 'ultimate-post-kit' ),
			// 		'type'      => Controls_Manager::SLIDER,
			// 		'range'     => [
			// 			'px' => [
			// 				'min'  => 0,
			// 				'max'  => 50,
			// 				'step' => 2,
			// 			],
			// 		],
			// 		'selectors' => [
			// 			'{{WRAPPER}} .upk-exotic-list .upk-meta' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
			// 		],
			// 	]
			// );
			$this->add_responsive_control(
				'author_spacing',
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
						'{{WRAPPER}} .upk-exotic-list .upk-meta > div:before' => 'margin: 0 {{SIZE}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'author_typography',
					'label'    => esc_html__( 'Typography', 'ultimate-post-kit' ),
					'selector' => '{{WRAPPER}} .upk-exotic-list .upk-author',
				]
			);
			
			$this->end_controls_section();
			
			$this->start_controls_section(
				'section_style_category',
				[
					'label'     => esc_html__( 'Category', 'ultimate-post-kit' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_category' => 'yes',
					],
				]
			);
			
			$this->start_controls_tabs( 'tabs_category_style' );
			
			$this->start_controls_tab(
				'tab_category_normal',
				[
					'label' => esc_html__( 'Normal', 'ultimate-post-kit' ),
				]
			);
			
			$this->add_control(
				'category_color',
				[
					'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}  .upk-exotic-list .upk-category a' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'category_background',
					'selector' => '{{WRAPPER}}  .upk-exotic-list .upk-category a',
				]
			);
			
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'           => 'category_border',
					'label'          => __( 'Border', 'ultimate-post-kit' ),
					'selector'       => '{{WRAPPER}}  .upk-exotic-list .upk-category a',
				]
			);
			
			$this->add_responsive_control(
				'category_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'ultimate-post-kit' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}}  .upk-exotic-list .upk-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'category_padding',
				[
					'label'      => esc_html__( 'Padding', 'ultimate-post-kit' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}}  .upk-exotic-list .upk-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'category_spacing',
				[
					'label'     => esc_html__( 'Space Between', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min'  => 0,
							'max'  => 50,
							'step' => 2,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .upk-exotic-list .upk-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
					],
				]
			);
			
			
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'category_shadow',
					'selector' => '{{WRAPPER}}  .upk-exotic-list .upk-category a',
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'category_typography',
					'label'    => esc_html__( 'Typography', 'ultimate-post-kit' ),
					'selector' => '{{WRAPPER}}  .upk-exotic-list .upk-category a',
				]
			);
			
			$this->end_controls_tab();
			
			$this->start_controls_tab(
				'tab_category_hover',
				[
					'label' => esc_html__( 'Hover', 'ultimate-post-kit' ),
				]
			);
			
			$this->add_control(
				'category_hover_color',
				[
					'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}  .upk-exotic-list .upk-category a:hover' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'category_hover_background',
					'selector' => '{{WRAPPER}}  .upk-exotic-list .upk-category a:hover',
				]
			);
			
			$this->add_control(
				'category_hover_border_color',
				[
					'label'     => esc_html__( 'Border Color', 'ultimate-post-kit' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'category_border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}}  .upk-exotic-list .upk-category a:hover' => 'border-color: {{VALUE}};',
					],
				]
			);
			
			$this->end_controls_tab();
			
			$this->end_controls_tabs();
			
			$this->end_controls_section();
			
			//Global Pagination Controls
			$this->register_pagination_controls();
		}
		
		/**
		 * Main query render for this widget
		 * @param $posts_per_page number item query limit
		 */
		public function query_posts( $posts_per_page ) {
			
			$default = $this->getGroupControlQueryArgs();
			if ( $posts_per_page ) {
				$args['posts_per_page'] = $posts_per_page;
				$args['paged']  = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
			}
			$args         = array_merge( $default, $args );
			$this->_query = new WP_Query( $args );
		}
		
		public function render_author() {
			
			if ( ! $this->get_settings( 'show_author' ) ) {
				return;
			}
			
			?>
            <div class="upk-author">
                <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ) ?>">
                    <?php echo get_the_author() ?>
                </a>
            </div>
			
			<?php
		}
		
		public function render_date() {
			$settings = $this->get_settings_for_display();
			
			if ( ! $this->get_settings( 'show_date' ) ) {
				return;
			}
			
			?>
				<div class="upk-date-wrap">
					<span>
						<?php if ($settings['human_diff_time'] == 'yes') {
							echo ultimate_post_kit_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
						} else {
							echo get_the_date();
						} ?>
					</span>
				</div>
				<?php if ($settings['show_time']) : ?>
				<div class="upk-post-time">
					<i class="upk-icon-clock" aria-hidden="true"></i>
					<?php echo get_the_time(); ?>
				</div>
				<?php endif; ?>
			
			<?php
		}
		
		public function render_post_grid_item( $post_id, $image_size ) {
			$settings = $this->get_settings_for_display();
			
			if ( 'yes' == $settings['global_link'] ) {
				
				$this->add_render_attribute( 'list-item', 'onclick', "window.open('" . esc_url( get_permalink() ) . "', '_self')", true );
			}
			$this->add_render_attribute( 'list-item', 'class', 'upk-item', true );
			
			?>
            <div <?php $this->print_render_attribute_string( 'list-item' ); ?>>

                    <div class="upk-content-wrap">
				    	<?php $this->render_date(); ?>
						<div class="upk-inner-content">
							<?php $this->render_title(substr($this->get_name(), 4)); ?>
							<div class="upk-meta">
								<?php $this->render_category(); ?>

								<?php if ($settings['show_author']) : ?>
									<div data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
									<?php $this->render_author(); ?>
									</div>
								<?php endif; ?>
								<?php if (_is_upk_pro_activated()) :
									if ('yes' === $settings['show_reading_time']) : ?>
										<div class="upk-reading-time" data-separator="<?php echo esc_html($settings['meta_separator']); ?>">
											<?php ultimate_post_kit_reading_time(get_the_content(), $settings['avg_reading_speed']); ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
		              </div>
                    </div>
					<?php if ( 'yes' == $settings['show_image'] ) : ?>
                        <div class="upk-image-wrap">
							<?php $this->render_image( get_post_thumbnail_id( $post_id ), $image_size ); ?>
                        </div>
					<?php endif; ?>
            </div>
			
			<?php
		}
		
		protected function render() {
			$settings = $this->get_settings_for_display();
			
			$this->query_posts( $settings['item_limit']['size'] );
			$wp_query = $this->get_query();
			
			if ( ! $wp_query->found_posts ) {
				return;
			}
			$this->add_render_attribute( 'list-wrap', 'class', 'upk-exotic-list upk-image-direction-' . $settings['image_direction_style']);
		
			if (isset($settings['upk_in_animation_show']) && ($settings['upk_in_animation_show'] == 'yes')) {
				$this->add_render_attribute( 'list-wrap', 'class', 'upk-in-animation' );
				if (isset($settings['upk_in_animation_delay']['size'])) {
					$this->add_render_attribute( 'list-wrap', 'data-in-animation-delay', $settings['upk_in_animation_delay']['size'] );
				}
			}

			?>
			<div <?php $this->print_render_attribute_string('list-wrap'); ?>>
					
					<?php while ( $wp_query->have_posts() ) :
						$wp_query->the_post();
						
						$thumbnail_size = $settings['primary_thumbnail_size'];
						
						?>
						
						<?php $this->render_post_grid_item( get_the_ID(), $thumbnail_size ); ?>
					
					<?php endwhile; ?>
            </div>
			
			<?php
			
			if ( $settings['show_pagination'] ) { ?>
                <div class="ep-pagination">
					<?php ultimate_post_kit_post_pagination( $wp_query, $this->get_id() ); ?>
                </div>
				<?php
			}
			wp_reset_postdata();
		}
	}
