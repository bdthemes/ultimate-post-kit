<?php
namespace UltimatePostKit\Modules\NewsTicker\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

use UltimatePostKit\Traits\Global_Widget_Controls;
use UltimatePostKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class News Ticker
 */
class News_Ticker extends Group_Control_Query {

	use Global_Widget_Controls;

	/**
	 * @var \WP_Query
	 */
	private $_query = null;

	public function get_name() {
		return 'upk-news-ticker';
	}

	public function get_title() {
		return BDTUPK . esc_html__( 'News Ticker', 'ultimate-post-kit' );
	}

	public function get_icon() {
		return 'upk-widget-icon upk-icon-news-ticker';
	}

	public function get_categories() {
		return [ 'ultimate-post-kit' ];
	}

	public function get_keywords() {
		return [ 'news', 'ticker', 'report', 'message', 'information', 'blog' ];
	}

	public function get_style_depends() {
		if ( $this->upk_is_edit_mode() ) {
			return [ 'upk-all-styles' ];
		} else {
			return [ 'upk-news-ticker' ];
		}
	}

	public function get_script_depends() {
		if ( $this->upk_is_edit_mode() ) {
			return [ 'upk-all-scripts' ];
		} else {
			return [ 'news-ticker-js', 'upk-news-ticker' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/xiKwQActvwk';
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

		$this->add_control(
			'show_label',
			[
				'label'   => esc_html__( 'Label', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'news_label',
			[
				'label'       => esc_html__( 'Label', 'ultimate-post-kit' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'LATEST NEWS', 'ultimate-post-kit' ),
				'placeholder' => esc_html__( 'LATEST NEWS', 'ultimate-post-kit' ),
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_control(
			'news_content',
			[
				'label'   => esc_html__( 'News Content', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					'title'   => esc_html__( 'Title', 'ultimate-post-kit' ),
					'excerpt' => esc_html__( 'Excerpt', 'ultimate-post-kit' ),
				],
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'     => esc_html__( 'Date', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'news_content' => 'title'
				],
			]
		);

		$this->add_control(
			'date_reverse',
			[
				'label'     => esc_html__( 'Date Reverse', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_date' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'     => esc_html__( 'Time', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'news_content' => 'title'
				],
			]
		);

		$this->add_responsive_control(
            'news_ticker_height',
            [
				'label'   => __( 'Height', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 42,
				],
				'range' => [
					'px' => [
						'min' => 25,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
				],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => esc_html__( 'Navigation', 'ultimate-post-kit' ),
			]
		);

		$this->add_control(
			'show_navigation',
			[
				'label'   => esc_html__( 'Navigation', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'play_pause',
			[
				'label'   => esc_html__( 'Play/Pause Button', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'navigation_size',
			[
				'label'   => esc_html__( 'Navigation Size', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-navigation svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_navigation' => 'yes'
				]
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
			'posts_limit',
			[
				'label'   => esc_html__( 'Posts Limit', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
			]
		);
		
		$this->register_query_builder_controls();
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_style_animation',
			[
				'label' => esc_html__( 'Animation', 'ultimate-post-kit' ),
			]
		);

		$this->add_control(
			'slider_animations',
			[
				'label'     => esc_html__( 'Animations', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'scroll'  	  => esc_html__( 'Scroll', 'ultimate-post-kit' ),
					'slide-left'  => esc_html__( 'Slide Left', 'ultimate-post-kit' ),
					'slide-up'    => esc_html__( 'Slide Up', 'ultimate-post-kit' ),
					'slide-right' => esc_html__( 'Slide Right', 'ultimate-post-kit' ),
					'slide-down'  => esc_html__( 'Slide Down', 'ultimate-post-kit' ),
					'fade'        => esc_html__( 'Fade', 'ultimate-post-kit' ),
					'typography'  => esc_html__( 'Typography', 'ultimate-post-kit' ),
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);


		$this->add_control(
			'autoplay_interval',
			[
				'label'     => esc_html__( 'Autoplay Interval', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'   => esc_html__( 'Pause on Hover', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'speed',
			[
				'label'              => esc_html__( 'Animation Speed', 'ultimate-post-kit' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 500,
			]
		);

        $this->add_control(
            'scroll_speed',
            [
				'label'   => __( 'Scroll Speed', 'ultimate-post-kit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [
					'slider_animations' => 'scroll',
				],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_news_ticker',
			[
				'label'     => esc_html__( 'News Ticker', 'ultimate-post-kit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_label',
			[
				'label'     => esc_html__( 'Label', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
				'separator' => 'before',
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-label-inner' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$border_side = is_rtl() ? 'right' : 'left';

		$this->add_control(
			'label_background',
			[
				'label'     => esc_html__( 'Background', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-label'       => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-label:after' => 'border-' . $border_side . '-color: {{VALUE}};',
				],
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'label_typography',
				'label'     => esc_html__( 'Typography', 'ultimate-post-kit' ),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .upk-news-ticker .upk-news-ticker-label-inner',
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_control(
			'heading_content',
			[
				'label' => esc_html__( 'Content', 'ultimate-post-kit' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-content a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-content span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_background',
			[
				'label'     => esc_html__( 'Background', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker'     => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-content:before, {{WRAPPER}} .upk-news-ticker .upk-news-ticker-content:after'     => 'box-shadow: 0 0 12px 12px {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => esc_html__( 'Typography', 'ultimate-post-kit' ),
				'selector' => '{{WRAPPER}} .upk-news-ticker .upk-news-ticker-content',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => esc_html__( 'Navigation', 'ultimate-post-kit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_control(
			'navigation_background',
			[
				'label'     => esc_html__( 'Navigation Background', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-navigation' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_arrow_style' );

		$this->start_controls_tab(
			'tab_arrow_normal',
			[
				'label' => esc_html__( 'Normal', 'ultimate-post-kit' ),
			]
		);

		$this->add_control(
			'navigation_color',
			[
				'label'     => esc_html__( 'Navigation Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-navigation button span svg' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrow_hover',
			[
				'label' => esc_html__( 'Hover', 'ultimate-post-kit' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__( 'Color', 'ultimate-post-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .upk-news-ticker .upk-news-ticker-navigation button:hover span svg' => 'color: {{VALUE}};',
				],
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
	public function query_posts( $posts_per_page ) {
		
		$default = $this->getGroupControlQueryArgs();
		if ( $posts_per_page ) {
			$args['posts_per_page'] = $posts_per_page;
				$args['paged']  = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}
		$args         = array_merge( $default, $args );
		$this->_query = new WP_Query( $args );
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$this->query_posts( $settings['posts_limit'] );

		$wp_query = $this->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->render_header($settings);

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->render_loop_item($settings);
		}

		$this->render_footer();

		wp_reset_postdata();

	}

	protected function render_title() {
		$classes = ['upk-news-ticker-content-title'];
		?>

		<a href="<?php echo esc_url(get_permalink()); ?>">
			<?php $this->render_date(); ?>

			<?php $this->render_time(); ?>

			<?php the_title() ?>
		</a>
		<?php
	}


	protected function render_excerpt() {
		
		?>
		<a href="<?php echo esc_url(get_permalink()); ?>">
			<?php the_excerpt(); ?>
		</a>
		<?php
	}

	protected function render_header($settings) {

	    $this->add_render_attribute(
			[
				'slider-settings' => [
					'class' => [
						'upk-news-ticker',
					],
					'data-settings' => [
						wp_json_encode(array_filter([
							"effect"       => $settings["slider_animations"],
							"autoPlay"     => ($settings["autoplay"]) ? true : false,
							"interval"     => $settings["autoplay_interval"],
							"pauseOnHover" => ($settings["pause_on_hover"]) ? true : false,
							"scrollSpeed"  => (isset( $settings["scroll_speed"]["size"]) ?  $settings["scroll_speed"]["size"] : 1),
							"direction"    => (is_rtl()) ? 'rtl' : false
						]))
					],
				]
			]
		);
	    
		?>
		<div id="newsTicker1" <?php $this->print_render_attribute_string( 'slider-settings' ); ?>>
			<?php if ( 'yes' == $settings['show_label'] ) : ?>
		    	<div class="upk-news-ticker-label">
					<div class="upk-news-ticker-label-inner">
						<?php echo wp_kses( $settings['news_label'], ultimate_post_kit_allow_tags('title') ); ?>
	    			</div>
	    		</div>
		    <?php endif; ?>
		    <div class="upk-news-ticker-content">
		        <ul>
		<?php
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if ( ! $this->get_settings('show_date') ) {
			return;
		}

		$news_month = get_the_date('m');
		$news_day = get_the_date('d');
		
		?>

		<span class="upk-news-ticker-date bdt-margin-small-right" title="<?php esc_html_e( 'Published on:', 'ultimate-post-kit' ); ?> <?php echo get_the_date(); ?>">
			<?php if ('yes' == $settings['date_reverse']) : ?>
				<span class="upk-news-ticker-date-day"><?php echo esc_attr( $news_day ); ?></span>
				<span class="upk-news-ticker-date-sep">/</span>
				<span class="upk-news-ticker-date-month"><?php echo esc_attr( $news_month ); ?></span>
			<?php else: ?>
				<span class="upk-news-ticker-date-month"><?php echo esc_attr( $news_month ); ?></span>
				<span class="upk-news-ticker-date-sep">/</span>
				<span class="upk-news-ticker-date-day"><?php echo esc_attr( $news_day ); ?></span>
			<?php endif; ?>
			<span>:</span>
		</span>

		<?php
	}

	public function render_time() {

		if ( ! $this->get_settings('show_time') ) {
			return;
		}

		$news_hour = get_the_time();
		
		?>

		<span class="upk-news-ticker-time bdt-margin-small-right" title="<?php esc_html_e( 'Published on:', 'ultimate-post-kit' ); ?> <?php echo get_the_date(); ?> <?php echo get_the_time(); ?>">
			<span class="bdt-text-uppercase"><?php echo esc_attr( $news_hour ); ?></span>
			<span>:</span>
		</span>

		<?php
	}

	protected function render_footer() {
		$settings = $this->get_settings_for_display();
		?>


		        </ul>
		    </div>
		    <?php if ( $settings['show_navigation'] ) : ?>
		    <div class="upk-news-ticker-controls upk-news-ticker-navigation">

		        <button class="bdt-visible@m">
		        	<span class="upk-news-ticker-arrow upk-news-ticker-prev bdt-icon">
		        		<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="chevron-left">
		        			<polyline fill="none" stroke="#000" stroke-width="1.03" points="13 16 7 10 13 4"></polyline>
		        		</svg>
		        	</span>
		        </button>

		        <?php if ($settings['play_pause']) : ?>
		        <button class="bdt-visible@m">
		        	<span class="upk-news-ticker-action bdt-icon">
		        		<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="play" class="upk-news-ticker-play-pause">
		        			<polygon fill="none" stroke="#000" points="4.9,3 16.1,10 4.9,17 "></polygon>

		        			<rect x="6" y="2" width="1" height="16"/>
							<rect x="13" y="2" width="1" height="16"/>
		        		</svg>
		        	</span>
		        </button>
		    	<?php endif ?>
		        
		        <button>
		        	<span class="upk-news-ticker-arrow upk-news-ticker-next bdt-icon">
		        		<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="chevron-right">
		        			<polyline fill="none" stroke="#000" stroke-width="1.03" points="7 4 13 10 7 16"></polyline>
		        		</svg>
		        	</span>
		        </button>

		    </div>

			<?php endif; ?>
		</div>
		
		<?php
	}

	protected function render_loop_item($settings) {
		?>
		<li class="upk-news-ticker-item">
			

				<?php if( 'title' == $settings['news_content'] ) : ?>
					<?php $this->render_title(substr($this->get_name(), 4)); ?>
				<?php endif; ?>

				<?php if( 'excerpt' == $settings['news_content'] )  : ?>
					<?php $this->render_excerpt(); ?>
				<?php endif; ?>

			
		</li>
		<?php
	}
}
