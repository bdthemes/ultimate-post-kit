<?php
namespace UltimatePostKit\Modules\EliteCarousel;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'elite-carousel';
	}

	public function get_widgets() {

		$widgets = ['Elite_Carousel'];
		
		return $widgets;
	}
}
