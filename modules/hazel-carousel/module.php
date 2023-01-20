<?php
namespace UltimatePostKit\Modules\HazelCarousel;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'hazel-carousel';
	}

	public function get_widgets() {

		$widgets = ['Hazel_Carousel'];
		
		return $widgets;
	}
}
