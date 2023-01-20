<?php
namespace UltimatePostKit\Modules\BuzzListCarousel;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'buzz-list-carousel';
	}

	public function get_widgets() {

		$widgets = [
			'Buzz_List_Carousel',
		];
		
		return $widgets;
	}
}
