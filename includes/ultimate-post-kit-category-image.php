<?php
if (!class_exists('Ultimate_Post_Kit_Category_Image')) {
    class Ultimate_Post_Kit_Category_Image {
        public function __construct() {
            add_action('category_add_form_fields', [$this, 'add_new_category_image'], 10, 2);
            add_action('created_category', [$this, 'save_category_image'], 10, 2);
            add_action('category_edit_form_fields', [$this, 'edit_category_image'], 10, 2);
            add_action('edited_category', [$this, 'updated_category_image'], 10, 2);
            add_action('admin_enqueue_scripts', [$this, 'load_media_files']);
            add_action('admin_footer', [$this, 'load_category_media_scripts']);
        }

        public function add_new_category_image() { ?>
            <div class="form-field term-group-wrap">
                <label for="upk-category-image-id"><?php _e('Image', 'ultimate-post-kit'); ?></label>
                <input type="hidden" id="upk-category-image-id" name="upk-category-image-id" class="upk-hidden-media-url" value="">
                <div id="upk-category-image-wrapper">
                </div>
                <p>
                    <input type="button" class="button button-secondary upk-category-image-add" id="upk-category-image-add" name="upk-category-image-add" value="<?php _e('Add Image', 'ultimate-post-kit'); ?>" />
                    <input type="button" class="button button-secondary upk-category-image-remove" id="upk-category-image-remove" name="upk-category-image-remove" value="<?php _e('Remove Image', 'ultimate-post-kit'); ?>" />
                </p>
            </div>
        <?php
        }
        public function save_category_image($term_id) {
            if (isset($_POST['upk-category-image-id']) && '' !== $_POST['upk-category-image-id']) {
                $image = sanitize_key($_POST['upk-category-image-id']);
                add_term_meta($term_id, 'upk-category-image-id', $image, true);
            }
        }
        public function edit_category_image($term) { ?>
            <tr class="form-field term-group-wrap">
                <th scope="row">
                    <label for="upk-category-image-id"><?php _e('Image', 'ultimate-post-kit'); ?></label>
                </th>
                <td>
                    <?php $image_id = get_term_meta($term->term_id, 'upk-category-image-id', true); ?>
                    <input type="hidden" id="upk-category-image-id" name="upk-category-image-id" value="<?php echo esc_attr($image_id); ?>">
                    <div id="upk-category-image-wrapper">
                        <?php if ($image_id) { ?>
                            <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                        <?php } ?>
                    </div>
                    <p>
                        <input type="button" class="button button-secondary upk-category-image-add" id="upk-category-image-add" name="upk-category-image-add" value="<?php _e('Add Image', 'ultimate-post-kit'); ?>" />
                        <input type="button" class="button button-secondary upk-category-image-remove" id="upk-category-image-remove" name="upk-category-image-remove" value="<?php _e('Remove Image', 'ultimate-post-kit'); ?>" />
                    </p>
                </td>
            </tr>
        <?php
        }

        public function updated_category_image($term_id) {
            if (isset($_POST['upk-category-image-id']) && '' !== $_POST['upk-category-image-id']) {
                $image = sanitize_key($_POST['upk-category-image-id']);
                update_term_meta($term_id, 'upk-category-image-id', $image);
            } else {
                update_term_meta($term_id, 'upk-category-image-id', '');
            }
        }

        public function load_media_files() {
            wp_enqueue_media();
        }

        public function load_category_media_scripts() { ?>
            <script>
                (function($) {
                    $(document).ready(function() {
                        $(".upk-category-image-add.button").on("click", function() {
                            let UPK = wp.media({
                                multiple: false
                            });
                            UPK.on('select', function() {
                                let attachment = UPK.state().get('selection').first().toJSON();
                                $("#upk-category-image-id").val(attachment.id);
                                $("#upk-category-image-url").val(attachment.url);
                                $("#upk-category-image-wrapper").html(`<img width="150" height="150" src='${attachment.url}' />`);
                            });
                            UPK.open();
                            return false;
                        });
                        $('.upk-category-image-remove.button').on("click", function() {
                            $('#upk-category-image-id').val('');
                            $('#upk-category-image-wrapper').html('<img width=150; height=150; class="upk-media-hidden-image" src="" style="margin:0; max-height:100px; padding:0;float:none;" />');
                        })
                    });
                })(jQuery);
            </script>
<?php }
    }

    new Ultimate_Post_Kit_Category_Image();
}
