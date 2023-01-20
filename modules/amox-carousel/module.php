<?php
namespace UltimatePostKit\Modules\AmoxCarousel;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'amox-carousel';
	}

	public function get_widgets() {

		$widgets = [
			'Amox_Carousel',
		];
		
		return $widgets;
	}
}
