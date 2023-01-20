<?php
if (!class_exists('Ultimate_Post_Kit_Metabox')) {
    class Ultimate_Post_Kit_Metabox {

        public function __construct() {
            $video_link = ultimate_post_kit_option('video_link', 'ultimate_post_kit_other_settings', 'off');
            if ($video_link == 'on') {
                add_action('admin_init', [$this, 'upk_video_link_metabox_fields']);
                add_action('save_post', [$this, 'upk_video_link_save_metabox']);
            }
        }

        public function upk_video_link_metabox_fields() {
            add_meta_box('upk_video_link_metabox', __('Ultimate Post Kit Additional'), [$this, 'upk_video_link_metabox_callback'], 'post', 'side', 'default');
        }
        public function upk_video_link_metabox_callback($post) {
            wp_nonce_field('upk_video_link_nonce_action', 'upk_video_link_nonce_field');
            $video_label     = esc_html__('Video Link', 'ultimate-post-kit');
            $video_link      = get_post_meta($post->ID, '_upk_video_link_meta_key', true);
            $display_content = '<div class="upk-video-link-form-group">
                <label for="_upk_video_link_meta_key">' . $video_label . '</label>
                <input type="text" class="widefat" name="_upk_video_link_meta_key" id="_upk_video_link_meta_key" value="' . $video_link . '">
                </div>';
            echo $this->get_control_output($display_content);
        }
        public function get_control_output($output) {
            $tags = [
                'div'   => ['class' => []],
                'label' => ['for' => []],
                'span'  => ['scope' => [], 'class' => []],
                'input' => ['type' => [], 'class' => [], 'id' => [], 'name' => [], 'value' => [], 'placeholder' => [], 'checked' => []],
            ];
            if (isset($output)) {
                echo wp_kses($output, $tags);
            }
        }

        public function upk_video_link_save_metabox($post_id) {
            if (!$this->is_secured_nonce('upk_video_link_nonce_action', 'upk_video_link_nonce_field', $post_id)) {
                return $post_id;
            }

            $video_link = isset($_POST['_upk_video_link_meta_key']) ? sanitize_text_field($_POST['_upk_video_link_meta_key']) : '';
            $video_link = sanitize_text_field($video_link);
            update_post_meta($post_id, '_upk_video_link_meta_key', $video_link);
        }


        protected function is_secured_nonce($action, $nonce_field, $post_id) {
            $nonce = isset($_POST[$nonce_field]) ? sanitize_text_field($_POST[$nonce_field]) : '';
            if ($nonce == '') {
                return false;
            } elseif (!wp_verify_nonce($nonce, $action)) {
                return false;
            } elseif (!current_user_can('edit_post', $post_id)) {
                return false;
            } elseif (wp_is_post_autosave($post_id)) {
                return false;
            } elseif (wp_is_post_revision($post_id)) {
                return false;
            } else {
                return true;
            }
        }
    }
    new Ultimate_Post_Kit_Metabox();
}
