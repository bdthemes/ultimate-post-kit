<?php
namespace UltimatePostKit\Modules\HaroldCarousel;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'harold-carousel';
	}

	public function get_widgets() {

		$widgets = [
			'Harold_Carousel',
		];
		
		return $widgets;
	}
}
