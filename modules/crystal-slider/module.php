<?php
namespace UltimatePostKit\Modules\CrystalSlider;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'crystal-slider';
	}

	public function get_widgets() {

		$widgets = [
			'Crystal_Slider',
		];
		
		return $widgets;
	}
}
