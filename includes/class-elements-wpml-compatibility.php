<?php

namespace UltimatePostKit\Includes;

/**
 * UltimatePostKit_WPML class
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Ultimate_Post_Kit_WPML {

    /**
     * A reference to an instance of this class.
     * @since 3.1.0
     * @var   object
     */
    private static $instance = null;

    /**
     * Constructor for the class
     */
    public function init() {

        // WPML existence check - register nodes when WPML core or String Translation is present
        if (defined('WPML_ST_VERSION') || defined('WPML_VERSION') || defined('ICL_SITEPRESS_VERSION') || function_exists('icl_register_string')) {
            add_filter('wpml_elementor_widgets_to_translate', array($this, 'add_translatable_nodes'));
        }
    }

    /**
     * Load wpml required repeater class files.
     * @return void
     */
    public function load_wpml_modules() {

        require_once( BDTUPK_PATH . 'includes/compatiblity/wpml/wpml-module-with-items.php' );

        require_once( BDTUPK_PATH . 'includes/compatiblity/wpml/class-wpml-upk-static-social-count.php' );
        require_once( BDTUPK_PATH . 'includes/compatiblity/wpml/class-wpml-upk-social-share.php' );

        require_once( BDTUPK_PATH . 'includes/compatiblity/wpml/class-wpml-upk-pro-social-link.php' );
        require_once( BDTUPK_PATH . 'includes/compatiblity/wpml/class-wpml-upk-pro-holux-tabs.php' );
        require_once( BDTUPK_PATH . 'includes/compatiblity/wpml/class-wpml-upk-pro-post-info.php' );
    }

    /**
     * Add ultimate post kit translation nodes
     * @param array $nodes_to_translate
     * @return array
     */
    public function add_translatable_nodes($nodes_to_translate) {

        $this->load_wpml_modules();

        $nodes_to_translate['upk-banner'] = [
			'conditions'        => [
				'widgetType' => 'upk-banner',
			],
			'fields'            => [
                [
                    'field'       => 'title_text',
                    'type'        => esc_html__( 'Title Text', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
                [
                    'field'       => 'sub_title_text',
                    'type'        => esc_html__( 'Sub Title Text', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
                [
                    'field'       => 'description_text',
                    'type'        => esc_html__( 'Description Text', 'ultimate-post-kit' ),
                    'editor_type' => 'VISUAL',
                ],
                [
                    'field'       => 'badge_text',
                    'type'        => esc_html__( 'Badge Text', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
                [
                    'field'       => 'banner_size_text',
                    'type'        => esc_html__( 'Banner Size Text', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
                [
                    'field'       => 'readmore_text',
                    'type'        => esc_html__( 'Read More Text', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
                'readmore_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Read More Link', 'ultimate-post-kit' ),
					'editor_type' => 'LINK',
				],
            ]
		];

        $nodes_to_translate['upk-forbes-tabs'] = [
            'conditions'        => [
                'widgetType' => 'upk-forbes-tabs',
            ],
            'fields'            => [
                [
                    'field'       => 'header_title_text',
                    'type'        => esc_html__( 'Header Label', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
            ]
        ];

        $nodes_to_translate['upk-remote-arrows'] = [
            'conditions'        => [
                'widgetType' => 'upk-remote-arrows',
            ],
            'fields'            => [
                [
                    'field'       => 'next_text',
                    'type'        => esc_html__( 'Next Text', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
                [
                    'field'       => 'prev_text',
                    'type'        => esc_html__( 'Prev Text', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
            ]
        ];

        $nodes_to_translate['upk-news-ticker'] = [
            'conditions'        => [
                'widgetType' => 'upk-news-ticker',
            ],
            'fields'            => [
                [
                    'field'       => 'news_label',
                    'type'        => esc_html__( 'Label', 'ultimate-post-kit' ),
                    'editor_type' => 'LINE',
                ],
            ]
        ];

        $nodes_to_translate['upk-static-social-count'] = [
			'conditions'        => [
				'widgetType' => 'upk-static-social-count',
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_UPK_Static_Social_Count',
			'fields'            => []
		];

        $nodes_to_translate['upk-social-share'] = [
			'conditions'        => [
				'widgetType' => 'upk-social-share',
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_UPK_Social_Share',
			'fields'            => []
		];

        $nodes_to_translate['upk-social-link'] = [
			'conditions'        => [
				'widgetType' => 'upk-social-link',
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_UPK_Pro_Social_Link',
			'fields'            => []
		];

        $nodes_to_translate['upk-holux-tabs'] = [
			'conditions'        => [
				'widgetType' => 'upk-holux-tabs',
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_UPK_Pro_Holux_Tabs',
			'fields'            => []
		];

        $nodes_to_translate['upk-post-info'] = [
			'conditions'        => [
				'widgetType' => 'upk-post-info',
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_UPK_Pro_Post_Info',
			'fields'            => []
		];

        return $nodes_to_translate;
    }

    /**
     * Returns the instance.
     * @since  3.1.0
     * @return object
     */
    public static function get_instance() {

        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
