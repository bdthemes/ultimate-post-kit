<?php
namespace UltimatePostKit\Modules\CarbonSlider;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'carbon-slider';
	}

	public function get_widgets() {

		$widgets = [
			'Carbon_Slider',
		];
		
		return $widgets;
	}
}
