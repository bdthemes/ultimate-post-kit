<?php
namespace UltimatePostKit\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WPML_UPK_Pro_Holux_Tabs
 * Handles translation of repeater items in the Holux Tabs widget
 */
class WPML_UPK_Pro_Holux_Tabs extends WPML_Module_With_Items {

    /**
     * @return string
     */
    public function get_items_field() {
        return 'filter_item_list';
    }

    /**
     * @return array
     */
    public function get_fields() {
        return array(
            'filter_label',
        );
    }

    /**
     * @param string $field
     * @return string
     */
    protected function get_title( $field ) {
        switch ( $field ) {
            case 'filter_label':
                return esc_html__( 'Label', 'ultimate-post-kit' );
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
            case 'filter_label':
                return 'LINE';
            default:
                return 'LINE';
        }
    }
}
