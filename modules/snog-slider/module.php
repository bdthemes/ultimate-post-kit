<?php
namespace UltimatePostKit\Modules\SnogSlider;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'snog-slider';
	}

	public function get_widgets() {

		$widgets = [
			'Snog_Slider',
		];
		
		return $widgets;
	}
}
