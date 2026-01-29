<?php
namespace UltimatePostKit\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WPML_UPK_Static_Social_Count
 * Handles translation of repeater items in the Static Social Count widget
 */
class WPML_UPK_Static_Social_Count extends WPML_Module_With_Items {

    /**
     * @return string
     */
    public function get_items_field() {
        return 'social_counter_list';
    }

    /**
     * @return array
     */
    public function get_fields() {
        return array(
            'social_site_name',
            'social_site_meta',
            'social_site_link' => ['url'],
        );
    }

    /**
     * @param string $field
     * @return string
     */
    protected function get_title( $field ) {
        switch ( $field ) {
            case 'social_site_name':
                return esc_html__( 'Label', 'ultimate-post-kit' );
            case 'social_site_link':
                return esc_html__( 'Link', 'ultimate-post-kit' );
            case 'social_site_meta':
                return esc_html__( 'Meta', 'ultimate-post-kit' );
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
            case 'social_site_name':
            case 'social_site_meta':
                return 'LINE';
            case 'social_site_link':
                return 'LINK';
            default:
                return 'LINE';
        }
    }
}
