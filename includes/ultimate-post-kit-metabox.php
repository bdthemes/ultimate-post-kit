<?php
if (!class_exists('Ultimate_Post_Kit_Metabox')) {
    class Ultimate_Post_Kit_Metabox {
        public $enabled_video_features;
        public $enabled_audio_features;

        public function __construct() {
            $this->enabled_video_features = ultimate_post_kit_option('video_link', 'ultimate_post_kit_other_settings', 'off');
            $this->enabled_audio_features = ultimate_post_kit_option('audio_link', 'ultimate_post_kit_other_settings', 'off');

            if ($this->enabled_video_features == 'on' || $this->enabled_audio_features == 'on') {
                add_action('admin_init', [$this, 'upk_additional_features_fields']);
                add_action('save_post', [$this, 'upk_additional_features_save']);
            }
        }

        public function upk_additional_features_fields() {
            add_meta_box('upk_video_link_metabox', __('Ultimate Post Kit Additional'), [$this, 'upk_video_link_metabox_callback'], 'post', 'side', 'default');
        }
        public function upk_video_link_metabox_callback($post) {
            wp_nonce_field('upk_additional_features_nonce_action', 'upk_additional_features_nonce_field');

            $video_label     = esc_html__('Video Link', 'ultimate-post-kit');
            $video_link      = get_post_meta($post->ID, '_upk_video_link_meta_key', true);

            $audio_link_label     = esc_html__('Audio Link', 'ultimate-post-kit');
            $audio_link      = get_post_meta($post->ID, '_upk_audio_link_meta_key', true);

            $audio_label     = esc_html__('Audio Title', 'ultimate-post-kit');
            $audio_title      = get_post_meta($post->ID, '_upk_audio_title_meta_key', true);

            $artist_label     = esc_html__('Artist Name', 'ultimate-post-kit');
            $artist_name      = get_post_meta($post->ID, '_upk_artist_name_meta_key', true);


            $display_content = '<div class="upk-video-link-form-group">';
            if ($this->enabled_video_features == 'on') {
                $display_content .= '<label for="_upk_video_link_meta_key">' . $video_label . '</label>';
                $display_content .= '<input type="text" class="widefat" name="_upk_video_link_meta_key" id="_upk_video_link_meta_key" value="' . $video_link . '">';
            }
            if ($this->enabled_audio_features == 'on') {
                $display_content .= '<label for="_upk_audio_link_meta_key">' . $audio_link_label . '</label>';
                $display_content .= '<input type="text" class="widefat" name="_upk_audio_link_meta_key" id="_upk_audio_link_meta_key" value="' . $audio_link . '">';
                $display_content .= '<label for="_upk_audio_title_meta_key">' . $audio_label . '</label>';
                $display_content .= '<input type="text" class="widefat" name="_upk_audio_title_meta_key" id="_upk_audio_title_meta_key" value="' . $audio_title . '">';
                $display_content .= '<label for="_upk_artist_name_meta_key">' . $artist_label . '</label>';
                $display_content .= '<input type="text" class="widefat" name="_upk_artist_name_meta_key" id="_upk_artist_name_meta_key" value="' . $artist_name . '">';
            }
            $display_content .= '</div>';




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

        public function upk_additional_features_save($post_id) {
            if (!$this->is_secured_nonce('upk_additional_features_nonce_action', 'upk_additional_features_nonce_field', $post_id)) {
                return $post_id;
            }

            $video_link = isset($_POST['_upk_video_link_meta_key']) ? sanitize_text_field($_POST['_upk_video_link_meta_key']) : '';
            update_post_meta($post_id, '_upk_video_link_meta_key',  $video_link);

            $audio_link = isset($_POST['_upk_audio_link_meta_key']) ? sanitize_text_field($_POST['_upk_audio_link_meta_key']) : '';
            update_post_meta($post_id, '_upk_audio_link_meta_key', $audio_link);

            $audio_title = isset($_POST['_upk_audio_title_meta_key']) ? sanitize_text_field($_POST['_upk_audio_title_meta_key']) : '';
            update_post_meta($post_id, '_upk_audio_title_meta_key', $audio_title);

            $artist_name = isset($_POST['_upk_artist_name_meta_key']) ? sanitize_text_field($_POST['_upk_artist_name_meta_key']) : '';
            update_post_meta($post_id, '_upk_artist_name_meta_key', $artist_name);
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
