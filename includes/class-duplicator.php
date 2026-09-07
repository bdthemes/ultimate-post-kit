<?php

namespace UltimatePostKit\Includes;

use Elementor\Core\Files\CSS\Post as Post_CSS;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Duplicator Class
 */

if (!class_exists(__NAMESPACE__ . '\\BdThemes_Duplicator')) :
    class BdThemes_Duplicator {

        public function __construct() {
            add_action('admin_action_ultimate_post_kit_duplicate_as_draft', [$this, 'bdt_duplicate_as_draft']);
            add_filter('post_row_actions', [$this, 'bdt_duplicate_post_link'], 10, 2);
            add_filter('page_row_actions', [$this, 'bdt_duplicate_post_link'], 10, 2);
        }

        public function bdt_duplicate_as_draft() {

            if (!current_user_can('edit_posts')) {
                wp_die('You don\'t have permission to duplicate it; please go back!');
            }

            if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'ultimate_post_kit_duplicate_as_draft' == $_REQUEST['action']))) {
                wp_die('No post to duplicate has been supplied!');
            }

            /**
             * get the original post id
             *
             * This has to be read before the nonce check because the nonce action is bound
             * to the post being duplicated (see bdt_duplicate_post_link()). The value is
             * cast to an integer and used only to build that action string; nothing is read
             * or written with it until the nonce and capability checks below have passed.
             */
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- only used to construct the nonce action, which is verified on the next statement.
            $post_id = isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post'] ?? 0);

            /**
             * Nonce verification.
             *
             * The nonce is bound to the post being duplicated, so one nonce cannot be
             * replayed against every other post (and post type) on the site.
             */
            if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['duplicate_nonce'])), 'upk_duplicate_post_' . $post_id)) {
                return;
            }

            /**
             * and all the original post data then
             */
            $post = get_post($post_id);

            if (!$post) {
                wp_die(esc_html('Failed. Not Found Post: ' . $post_id));
            }

            /**
             * Authorise against THIS post, not against a generic role capability.
             *
             * edit_others_posts is only the capability for the built-in 'post' type; it
             * grants nothing over a post type registered with its own capability set. Use
             * the meta capability so WordPress maps it through the target post type, and
             * check create_posts separately because duplicating creates a new object.
             */
            if (!current_user_can('edit_post', $post_id)) {
                wp_die('You don\'t have permission to duplicate it; please go back!');
            }

            $post_type_object = get_post_type_object($post->post_type);

            if (!$post_type_object || !current_user_can($post_type_object->cap->create_posts)) {
                wp_die('You don\'t have permission to duplicate it; please go back!');
            }

            $this->duplicate_edit_post($post_id);
        }

        /**
         * duplicate edit post
         */
        public function duplicate_edit_post($post_id) {
            global $wpdb;
            /**
             * and all the original post data then
             */
            $bdt_post = get_post($post_id);
            /**
             * if you don't want current user to be the new post author,
             * then change next couple of lines to this: $new_post_author = $post->post_author;
             */
            $bdt_current_user    = wp_get_current_user();
            $bdt_new_post_author = $bdt_current_user->ID;

            /**
             * if post data exists, create the post duplicate
             */
            if (isset($bdt_post) && $bdt_post != null) {
                /**
                 * new post data array
                 */
                $bdt_args = [
                    'post_status'    => 'draft',
                    /* translators: %1$s post title */
                    'post_title'     => sprintf(__('%1$s - [Duplicated]', 'ultimate-post-kit'), $bdt_post->post_title),
                    'post_type'      => $bdt_post->post_type,
                    'post_name'      => $bdt_post->post_name,
                    'post_content'   => $bdt_post->post_content,
                    'post_excerpt'   => $bdt_post->post_excerpt,
                    'post_author'    => $bdt_new_post_author,
                    'post_parent'    => $bdt_post->post_parent,
                    'post_password'  => $bdt_post->post_password,
                    'comment_status' => $bdt_post->comment_status,
                    'ping_status'    => $bdt_post->ping_status,
                    'menu_order'     => $bdt_post->menu_order,
                    'to_ping'        => $bdt_post->to_ping,
                ];

                /**
                 * insert the post by wp_insert_post() function
                 */
                $bdt_new_post_id = wp_insert_post($bdt_args);

                /**
                 * get all current post terms ad set them to the new post draft
                 */
                $bdt_taxonomies = get_object_taxonomies($bdt_post->post_type);

                /**
                 * returns array of taxonomy names for post type, ex array("category", "post_tag");
                 */

                foreach ($bdt_taxonomies as $bdt_taxonomy) {
                    $bdt_post_terms = wp_get_object_terms($post_id, $bdt_taxonomy, ['fields' => 'slugs']);
                    wp_set_object_terms($bdt_new_post_id, $bdt_post_terms, $bdt_taxonomy, false);
                }

                /**
                 * duplicate all post meta just in two SQL queries
                 */
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-off admin duplicate action, caching not applicable.
                $bdt_post_meta_infos = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d", $post_id));

                if (is_array($bdt_post_meta_infos)) {
                    $bdt_sql_query_sel = [];
                    $bdt_sql_values    = [];

                    foreach ($bdt_post_meta_infos as $bdt_meta_info) {
                        $bdt_sql_query_sel[] = '( %d, %s, %s )';
                        $bdt_sql_values[]    = $bdt_new_post_id;
                        $bdt_sql_values[]    = $bdt_meta_info->meta_key;
                        $bdt_sql_values[]    = wp_slash($bdt_meta_info->meta_value);
                    }

                    if (!empty($bdt_sql_query_sel)) {
                        $bdt_sql_query = "INSERT INTO {$wpdb->postmeta} ( post_id, meta_key, meta_value ) VALUES " . implode(', ', $bdt_sql_query_sel);
                        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Query is built only from static "%d, %s, %s" placeholders and the trusted {$wpdb->postmeta} table name; all values are bound via $wpdb->prepare().
                        $wpdb->query($wpdb->prepare($bdt_sql_query, $bdt_sql_values));
                    }

                    /**
                     * fix template type issues
                     */
                    $source_type = get_post_meta($post_id, '_elementor_template_type', true);
                    delete_post_meta($bdt_new_post_id, '_elementor_template_type');
                    update_post_meta($bdt_new_post_id, '_elementor_template_type', $source_type);
                }

                $css = Post_CSS::create($bdt_new_post_id);
                $css->update();

                /**
                 * finally, redirect to the edit post screen for the new draft
                 */

                $bdt_all_post_types = get_post_types([], 'names');

                foreach ($bdt_all_post_types as $bdt_key => $bdt_value) {
                    $bdt_names[] = $bdt_key;
                }

                $current_post_type = get_post_type($post_id);

                if (is_array($bdt_names) && in_array($current_post_type, $bdt_names)) {
                    wp_safe_redirect(admin_url('edit.php?post_type=' . $current_post_type));
                    exit;
                }

                exit;
            } else {
                wp_die(esc_html('Failed. Not Found Post: ' . $post_id));
            }
        }


        public function bdt_duplicate_post_link($actions, $post) {

            if (current_user_can('edit_post', $post->ID)) {
                if ($post->post_type == 'post') {
                    $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=ultimate_post_kit_duplicate_as_draft&post=' . $post->ID, 'upk_duplicate_post_' . $post->ID, 'duplicate_nonce') . '" title="Duplicate this post" rel="permalink">' . esc_html_x("Duplicate Post", "Admin String", "ultimate-post-kit") . '</a>';
                } elseif ($post->post_type == 'page') {
                    $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=ultimate_post_kit_duplicate_as_draft&post=' . $post->ID, 'upk_duplicate_post_' . $post->ID, 'duplicate_nonce') . '" title="Duplicate this page" rel="permalink">' . esc_html_x("Duplicate Page", "Admin String", "ultimate-post-kit") . '</a>';
                } elseif ($post->post_type == 'elementor_library') {
                    $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=ultimate_post_kit_duplicate_as_draft&post=' . $post->ID, 'upk_duplicate_post_' . $post->ID, 'duplicate_nonce') . '" title="Duplicate this template" rel="permalink">' . esc_html_x("Duplicate Template", "Admin String", "ultimate-post-kit") . '</a>';
                }
            }
            return $actions;
        }
    }
endif;

/**
 * Instantiate the namespaced class.
 *
 * The guard above and this statement must resolve to the same class. An
 * unqualified class_exists() string is always resolved against the global
 * namespace, so a sibling plugin declaring a global \BdThemes_Duplicator
 * (Live Copy Paste does) used to satisfy the old guard and skip the
 * declaration, while this line still asked for
 * UltimatePostKit\Includes\BdThemes_Duplicator -- a fatal error.
 */
new BdThemes_Duplicator();
