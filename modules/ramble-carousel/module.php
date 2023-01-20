<?php
namespace UltimatePostKit\Modules\RambleCarousel;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'ramble-carousel';
	}

	public function get_widgets() {

		$widgets = ['Ramble_Carousel'];
		
		return $widgets;
	}
}
