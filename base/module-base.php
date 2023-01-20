<?php

namespace UltimatePostKit\Base;

use Elementor\Widget_Base;
use UltimatePostKit\Ultimate_Post_Kit_Loader;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

abstract class Module_Base extends Widget_Base {

    protected function upk_is_edit_mode(){

        if(Ultimate_Post_Kit_Loader::elementor()->preview->is_preview_mode() || Ultimate_Post_Kit_Loader::elementor()->editor->is_edit_mode() ){
            return true;
        }

        return false;
    }
}