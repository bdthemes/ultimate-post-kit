<?php
namespace UltimatePostKit\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WPML_UPK_Pro_Post_Info
 * Handles translation of repeater items in the Post Info widget
 */
class WPML_UPK_Pro_Post_Info extends WPML_Module_With_Items {

    /**
     * @return string
     */
    public function get_items_field() {
        return 'icon_list';
    }

    /**
     * @return array
     */
    public function get_fields() {
        return array(
            'text_prefix',
            'custom_text',
            'custom_date_format',
            'custom_time_format',
            'string_no_comments',
            'string_one_comment',
            'string_comments',
            'custom_url' => ['url'],
        );
    }

    /**
     * @param string $field
     * @return string
     */
    protected function get_title( $field ) {
        switch ( $field ) {
            case 'text_prefix':
                return esc_html__( 'Text Prefix', 'ultimate-post-kit' );
            case 'custom_text':
                return esc_html__( 'Custom Text', 'ultimate-post-kit' );
            case 'custom_date_format':
                return esc_html__( 'Custom Date Format', 'ultimate-post-kit' );
            case 'custom_time_format':
                return esc_html__( 'Custom Time Format', 'ultimate-post-kit' );
            case 'string_no_comments':
                return esc_html__( 'No Comments String', 'ultimate-post-kit' );
            case 'string_one_comment':
                return esc_html__( 'One Comment String', 'ultimate-post-kit' );
            case 'string_comments':
                return esc_html__( 'Comments String', 'ultimate-post-kit' );
            case 'custom_url':
                return esc_html__( 'Custom URL', 'ultimate-post-kit' );
            default:
                return '';
        }
    }

    /**
     * @param string $field
     * @return string
     */
    protected function get_editor_type( $field ) {
        switch ( $field ) {
            case 'text_prefix':
            case 'custom_text':
            case 'custom_date_format':
            case 'custom_time_format':
            case 'string_no_comments':
            case 'string_one_comment':
            case 'string_comments':
                return 'LINE';
            case 'custom_url':
                return 'LINK';
            default:
                return 'LINE';
        }
    }
}
