<?php

namespace UltimatePostKit\Includes\Controls\GroupQuery;

use Elementor\Controls_Manager;
use UltimatePostKit\Includes\Controls\SelectInput\Dynamic_Select;
use UltimatePostKit\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

abstract class Group_Control_Query extends Module_Base {

    protected function register_query_builder_controls() {

        $this->add_control(
            'posts_source',
            [
                'label'   => __('Source', 'ultimate-post-kit'),
                'type'    => Controls_Manager::SELECT,
                'options' => $this->getGroupControlQueryPostTypes(),
                'default' => 'post',
            ]
        );

        $this->add_control(
            'posts_selected_ids',
            [
                'label'       => __('Search & Select', 'ultimate-post-kit'),
                'type'        => Dynamic_Select::TYPE,
                'multiple'    => true,
                'label_block' => true,
                'query_args'  => [
                    'query' => 'posts',
                ],
                'condition'   => [
                    'posts_source' => 'manual_selection',
                ]
            ]
        );

        $this->start_controls_tabs(
            'tabs_posts_include_exclude',
            [
                'condition' => [
                    'posts_source!' => ['manual_selection', 'current_query'],
                ]
            ]
        );

        $this->start_controls_tab(
            'tab_posts_include',
            [
                'label'     => __('Include', 'ultimate-post-kit'),
                'condition' => [
                    'posts_source!' => ['manual_selection', 'current_query'],
                ]
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
                    'terms'   => __('Terms', 'ultimate-post-kit'),
                ],
                'condition'   => [
                    'posts_source!' => ['manual_selection', 'current_query'],
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
                    'posts_source!'    => ['manual_selection', 'current_query'],
                ]
            ]
        );

        $this->add_control(
            'posts_include_term_ids',
            [
                'label'       => __('Terms', 'ultimate-post-kit'),
                'description' => __('Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'ultimate-post-kit'),
                'type'        => Dynamic_Select::TYPE,
                'multiple'    => true,
                'label_block' => true,
                'placeholder' => __('Type and select terms', 'ultimate-post-kit'),
                'query_args'  => [
                    'query'        => 'terms',
                    'widget_props' => [
                        'post_type' => 'posts_source'
                    ]
                ],
                'condition'   => [
                    'posts_include_by' => 'terms',
                    'posts_source!'    => ['manual_selection', 'current_query'],
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_posts_exclude',
            [
                'label'     => __('Exclude', 'ultimate-post-kit'),
                'condition' => [
                    'posts_source!' => ['manual_selection', 'current_query'],
                ]
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
                    'current_post'     => __('Current Post', 'ultimate-post-kit'),
                    'manual_selection' => __('Manual Selection', 'ultimate-post-kit'),
                    'terms'            => __('Terms', 'ultimate-post-kit'),
                ],
                'condition'   => [
                    'posts_source!' => ['manual_selection', 'current_query'],
                ]
            ]
        );

        $this->add_control(
            'posts_exclude_ids',
            [
                'label'       => __('Search & Select', 'ultimate-post-kit'),
                'type'        => Dynamic_Select::TYPE,
                'multiple'    => true,
                'label_block' => true,
                'query_args'  => [
                    'query'        => 'posts',
                    'widget_props' => [
                        'post_type' => 'posts_source'
                    ]
                ],
                'condition'   => [
                    'posts_source!'    => ['manual_selection', 'current_query'],
                    'posts_exclude_by' => 'manual_selection',
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
                    'posts_source!'    => ['manual_selection', 'current_query'],
                ]
            ]
        );

        $this->add_control(
            'posts_exclude_term_ids',
            [
                'label'       => __('Terms', 'ultimate-post-kit'),
                'description' => __('Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'ultimate-post-kit'),
                'type'        => Dynamic_Select::TYPE,
                'multiple'    => true,
                'label_block' => true,
                'placeholder' => __('Type and select terms', 'ultimate-post-kit'),
                'query_args'  => [
                    'query'        => 'terms',
                    'widget_props' => [
                        'post_type' => 'posts_source'
                    ]
                ],
                'condition'   => [
                    'posts_exclude_by' => 'terms',
                    'posts_source!'    => ['manual_selection', 'current_query'],
                ]
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'posts_divider',
            [
                'type' => Controls_Manager::DIVIDER,
                'condition' => [
                    'posts_source!' => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'posts_offset',
            [
                'label'   => __('Offset', 'bdthemes-element-pack') . BDTUPK_NC,
                'type'    => Controls_Manager::NUMBER,
                'default' => 0,
                'condition' => [
                    'posts_source!' => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'posts_select_date',
            [
                'label'     => __('Date', 'ultimate-post-kit'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'anytime',
                'options'   => [
                    'anytime' => __('All', 'ultimate-post-kit'),
                    'today'   => __('Past Day', 'ultimate-post-kit'),
                    'week'    => __('Past Week', 'ultimate-post-kit'),
                    'month'   => __('Past Month', 'ultimate-post-kit'),
                    'quarter' => __('Past Quarter', 'ultimate-post-kit'),
                    'year'    => __('Past Year', 'ultimate-post-kit'),
                    'exact'   => __('Custom', 'ultimate-post-kit'),
                ],
                'condition' => [
                    'posts_source!' => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'posts_date_before',
            [
                'label'       => __('Before', 'ultimate-post-kit'),
                'type'        => Controls_Manager::DATE_TIME,
                'description' => __('Setting a ‘Before’ date will show all the posts published until the chosen date (inclusive).', 'ultimate-post-kit'),
                'condition'   => [
                    'posts_select_date' => 'exact',
                    'posts_source!'     => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'posts_date_after',
            [
                'label'       => __('After', 'ultimate-post-kit'),
                'type'        => Controls_Manager::DATE_TIME,
                'description' => __('Setting an ‘After’ date will show all the posts published since the chosen date (inclusive).', 'ultimate-post-kit'),
                'condition'   => [
                    'posts_select_date' => 'exact',
                    'posts_source!'     => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'posts_orderby',
            [
                'label'     => __('Order By', 'ultimate-post-kit'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'date',
                'options'   => [
                    'title'         => __('Title', 'ultimate-post-kit'),
                    'ID'            => __('ID', 'ultimate-post-kit'),
                    'date'          => __('Date', 'ultimate-post-kit'),
                    'author'        => __('Author', 'ultimate-post-kit'),
                    'comment_count' => __('Comment Count', 'ultimate-post-kit'),
                    'menu_order'    => __('Menu Order', 'ultimate-post-kit'),
                    'rand'          => __('Random', 'ultimate-post-kit'),
                ],
                'condition' => [
                    'posts_source!' => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'posts_order',
            [
                'label'     => __('Order', 'ultimate-post-kit'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'desc',
                'options'   => [
                    'asc'  => __('ASC', 'ultimate-post-kit'),
                    'desc' => __('DESC', 'ultimate-post-kit'),
                ],
                'condition' => [
                    'posts_source!' => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'posts_ignore_sticky_posts',
            [
                'label'        => __('Ignore Sticky Posts', 'ultimate-post-kit'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => [
                    'posts_source' => ['post', 'current_query'],
                ]
            ]
        );

        $this->add_control(
            'posts_only_with_featured_image',
            [
                'label'        => __('Only Featured Image Post', 'ultimate-post-kit'),
                'description'  => __('Enable to display posts only when featured image is present.', 'ultimate-post-kit'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'condition'    => [
                    'posts_source!' => 'current_query',
                ]
            ]
        );

        $this->add_control(
            'query_id',
            [
                'label'       => __('Query ID', 'ultimate-post-kit'),
                'description' => __('Give your Query a custom unique id to allow server side filtering', 'ultimate-post-kit'),
                'type'        => Controls_Manager::TEXT,
                'separator'   => 'before',
            ]
        );
    }

    private function setMetaQueryArgs() {

        $args = [];

        if ('current_query' === $this->getGroupControlQueryPostType()) {
            return [];
        }

        $args['order']   = $this->get_settings_for_display('posts_order');
        $args['orderby'] = $this->get_settings_for_display('posts_orderby');

        /**
         * Set Feature Images
         */

        if ($this->get_settings_for_display('posts_only_with_featured_image') === 'yes') {
            $args['meta_key'] = '_thumbnail_id';
        }

        /**
         * Set Date
         */

        $selected_date = $this->get_settings_for_display('posts_select_date');

        if (!empty($selected_date)) {
            $date_query = [];

            switch ($selected_date) {
                case 'today':
                    $date_query['after'] = '-1 day';
                    break;

                case 'week':
                    $date_query['after'] = '-1 week';
                    break;

                case 'month':
                    $date_query['after'] = '-1 month';
                    break;

                case 'quarter':
                    $date_query['after'] = '-3 month';
                    break;

                case 'year':
                    $date_query['after'] = '-1 year';
                    break;

                case 'exact':
                    $after_date = $this->get_settings_for_display('posts_date_after');

                    if (!empty($after_date)) {
                        $date_query['after'] = $after_date;
                    }

                    $before_date = $this->get_settings_for_display('posts_date_before');

                    if (!empty($before_date)) {
                        $date_query['before'] = $before_date;
                    }

                    $date_query['inclusive'] = true;
                    break;
            }

            if (!empty($date_query)) {
                $args['date_query'] = $date_query;
            }
        }

        return $args;
    }

    protected function getGroupControlQueryArgs() {

        $settings = $this->get_settings_for_display();
        $args     = $this->setMetaQueryArgs();

        $args['post_status']      = 'publish';
        $args['suppress_filters'] = false;
        $exclude_by               = $this->getGroupControlQueryParamBy('exclude');

        if (0 < $settings['posts_offset']) {
            $args['offset_to_fix'] = $settings['posts_offset'];
        }

        /**
         * Set Ignore Sticky
         */
        if (
            $this->getGroupControlQueryPostType() === 'post'
            && $this->get_settings_for_display('posts_ignore_sticky_posts') === 'yes'
        ) {
            $args['ignore_sticky_posts'] = true;

            if (in_array('current_post', $exclude_by)) {
                $args['post__not_in'] = [get_the_ID()];
            }
        }

        if ($this->getGroupControlQueryPostType() === 'manual_selection') {
            /**
             * Set Including Manually
             */
            $selected_ids      = $this->get_settings_for_display('posts_selected_ids');
            $selected_ids      = wp_parse_id_list($selected_ids);
            $args['post_type'] = 'any';
            if (!empty($selected_ids)) {
                $args['post__in'] = $selected_ids;
            }

            $args['ignore_sticky_posts'] = 1;
        } elseif ('current_query' === $this->getGroupControlQueryPostType()) {
            /**
             * Make Current Query
             */
            $args = $GLOBALS['wp_query']->query_vars;
            $args = apply_filters('ultimate_post_kit/query/get_query_args/current_query', $args);
        } elseif ('_ultimate_post_kit_pro_related_post_type' == $this->getGroupControlQueryPostType()) {
            /**
             * Set Related Query
             */
            $post_id           = get_queried_object_id();
            $related_post_id   = is_singular() && (0 !== $post_id) ? $post_id : null;
            $args['post_type'] = get_post_type($related_post_id);

            $exclude_by = $this->getGroupControlQueryParamBy('exclude');
            if (in_array('current_post', $exclude_by)) {
                $args['post__not_in'] = [get_the_ID()];
            }

            /**
             * Set Authors
             */
            $args = $this->getAuthorArgs($args, $settings, $related_post_id);

            /**
             * Set Taxonomy
             */
            $args = $this->getTermsArgs($args, $settings);

            $args['ignore_sticky_posts'] = 1;
            $args                        = apply_filters('ultimate_post_kit/query/get_query_args/related_query', $args);
        } else {

            /**
             * Set Post Type
             */
            $args['post_type'] = $this->getGroupControlQueryPostType();

            /**
             * Set Exclude Post
             */
            $exclude_by   = $this->getGroupControlQueryParamBy('exclude');
            $current_post = [];

            if (in_array('current_post', $exclude_by) && is_singular()) {
                $current_post = [get_the_ID()];
            }

            if (in_array('manual_selection', $exclude_by)) {
                $exclude_ids          = $settings['posts_exclude_ids'];
                $args['post__not_in'] = array_merge($current_post, wp_parse_id_list($exclude_ids));
            }

            /**
             * Set Authors
             */
            $args = $this->getAuthorArgs($args, $settings);

            /**
             * Set Taxonomy
             */
            $args = $this->getTermsArgs($args, $settings);
        }

        if ($this->get_settings_for_display('query_id')) {
            add_action('pre_get_posts', [$this, 'pre_get_posts_query_filter']);
        }

        // fixing custom offset
        ## https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
        add_action('pre_get_posts', [$this, 'fix_query_offset'], 1);
        add_filter('found_posts', [$this, 'prefix_adjust_offset_pagination'], 1, 2);

        return $args;
    }

    private function getAuthorArgs($args, $settings, $post = null) {

        $include_by = $this->getGroupControlQueryParamBy('include');
        $exclude_by = $this->getGroupControlQueryParamBy('exclude');
        $include_users = [];
        $exclude_users = [];

        if (in_array('authors', $include_by)) {
            $include_users = wp_parse_id_list($settings['posts_include_author_ids']);
        } elseif ($post) {
            $include_users = get_post_field('post_author', $post);
        }

        if (in_array('authors', $exclude_by)) {
            $exclude_users = wp_parse_id_list($settings['posts_exclude_author_ids']);
            $include_users = array_diff($include_users, $exclude_users);
        }

        if (!empty($include_users)) {
            $args['author__in'] = $include_users;
        }

        if (!empty($exclude_users)) {
            $args['author__not_in'] = $exclude_users;
        }

        return $args;
    }

    private function getTermsArgs($args, $settings) {

        $include_by     = $this->getGroupControlQueryParamBy('include');
        $exclude_by     = $this->getGroupControlQueryParamBy('exclude');
        $include_terms  = [];
        $terms_query    = [];

        if (in_array('terms', $include_by)) {
            $include_terms = wp_parse_id_list($settings['posts_include_term_ids']);
        }

        if (in_array('terms', $exclude_by)) {
            $exclude_terms = wp_parse_id_list($settings['posts_exclude_term_ids']);
            $include_terms = array_diff($include_terms, $exclude_terms);
        }

        if (!empty($include_terms)) {
            $tax_terms_map = $this->mapGroupControlQuery($include_terms);

            foreach ($tax_terms_map as $tax => $terms) {
                $terms_query[] = [
                    'taxonomy' => $tax,
                    'field'    => 'term_id',
                    'terms'    => $terms,
                    'operator' => 'IN',
                ];
            }
        }

        if (!empty($exclude_terms)) {
            $tax_terms_map = $this->mapGroupControlQuery($exclude_terms);

            foreach ($tax_terms_map as $tax => $terms) {
                $terms_query[] = [
                    'taxonomy' => $tax,
                    'field'    => 'term_id',
                    'terms'    => $terms,
                    'operator' => 'NOT IN',
                ];
            }
        }

        if (!empty($terms_query)) {
            $args['tax_query']             = $terms_query;
            $args['tax_query']['relation'] = 'AND';
        }

        return $args;
    }

    /**
     * @return mixed
     */
    private function getGroupControlQueryPostType() {
        return $this->get_settings_for_display('posts_source');
    }

    /**
     * Get Query Params by args
     *
     * @param string $by
     *
     * @return array|mixed
     */
    private function getGroupControlQueryParamBy($by = 'exclude') {
        $mapBy = [
            'exclude' => 'posts_exclude_by',
            'include' => 'posts_include_by',
        ];

        $setting = $this->get_settings_for_display($mapBy[$by]);

        return (!empty($setting) ? $setting : []);
    }

    /**
     * @param array $term_ids
     *
     * @return array
     */
    private function mapGroupControlQuery($term_ids = []) {
        $terms = get_terms(
            [
                'term_taxonomy_id' => $term_ids,
                'hide_empty'       => false,
            ]
        );

        $tax_terms_map = [];

        foreach ($terms as $term) {
            $taxonomy                   = $term->taxonomy;
            $tax_terms_map[$taxonomy][] = $term->term_id;
        }

        return $tax_terms_map;
    }


    /**
     * @return array|string[]|\WP_Post_Type[]
     */
    private function getGroupControlQueryPostTypes() {
        $post_types = get_post_types(['public' => true], 'objects');
        $post_types = array_column($post_types, 'label', 'name');

        $ignorePostTypes = [
            'elementor_library'    => '',
            'attachment'           => '',
            'bdt_template_manager' => '',
            'bdt-custom-template'  => '',
        ];

        $post_types = array_diff_key($post_types, $ignorePostTypes);

        $extra_types = [
            'manual_selection'   => __('Manual Selection', 'bdthemes-element-pack'),
            'current_query'      => __('Current Query', 'bdthemes-element-pack'),
            '_ultimate_post_kit_pro_related_post_type' => __('Related', 'ultimate-post-kit'),
        ];

        $post_types = array_merge($post_types, $extra_types);

        return $post_types;
    }

    /**
     * @param WP_Query $query fix the offset
     */

    function fix_query_offset(&$query) {

        if (isset($query->query_vars['offset_to_fix'])) {

            if ($query->is_paged) {
                $page_offset = $query->query_vars['offset_to_fix'] + (($query->query_vars['paged'] - 1) * $query->query_vars['posts_per_page']);
                $query->set('offset', $page_offset);
            } else {
                $query->set('offset', $query->query_vars['offset_to_fix']);
            }
        }
    }

    public function prefix_adjust_offset_pagination($found_posts, $query) {

        if (isset($query->query_vars['offset_to_fix'])) {
            $offset_to_fix = intval($query->query_vars['offset_to_fix']);

            if ($offset_to_fix) {
                $found_posts -= $offset_to_fix;
            }
        }

        return $found_posts;
    }

    public function pre_get_posts_query_filter($wp_query) {
        if ($this) {
            $query_id = $this->get_settings_for_display('query_id');
            do_action("ultimate_post_kit_pro/query/{$query_id}", $wp_query, $this);
        }
    }
}
