<?php
namespace UltimatePostKit\Modules\SkideSlider;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'skide-slider';
	}

	public function get_widgets() {

		$widgets = [
			'Skide_Slider',
		];
		
		return $widgets;
	}
}
