<?php
namespace UltimatePostKit\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WPML_UPK_Pro_Social_Link
 * Handles translation of repeater items in the Social Link widget
 */
class WPML_UPK_Pro_Social_Link extends WPML_Module_With_Items {

    /**
     * @return string
     */
    public function get_items_field() {
        return 'share_buttons';
    }

    /**
     * @return array
     */
    public function get_fields() {
        return array(
            'text',
            'social_link',
        );
    }

    /**
     * @param string $field
     * @return string
     */
    protected function get_title( $field ) {
        switch ( $field ) {
            case 'text':
                return esc_html__( 'Custom Label', 'ultimate-post-kit' );
            case 'social_link':
                return esc_html__( 'Link', 'ultimate-post-kit' );
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
            case 'text':
            case 'social_link':
                return 'LINE';
            default:
                return 'LINE';
        }
    }
}
