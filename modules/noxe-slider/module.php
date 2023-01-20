<?php
namespace UltimatePostKit\Modules\NoxeSlider;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'noxe-slider';
	}

	public function get_widgets() {

		$widgets = [
			'Noxe_Slider',
		];
		
		return $widgets;
	}
}
