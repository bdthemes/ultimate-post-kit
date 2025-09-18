<?php

if ( ! defined( 'BDTUPK_TITLE' ) ) {
    $white_label_title = get_option( 'upk_white_label_title' );
	define( 'BDTUPK_TITLE', $white_label_title );
}

if ( ! defined( 'BDTUPK_LO' ) ) {
    $hide_license = get_option( 'upk_white_label_hide_license', false );
    if ( $hide_license ) {
        define( 'BDTUPK_LO', true );
    }
}

if ( ! defined( 'BDTUPK_HIDE' ) ) {
    $hide_ep = get_option( 'upk_white_label_bdtupk_hide', false );
    if ( $hide_ep ) {
        define( 'BDTUPK_HIDE', true );
    }
}