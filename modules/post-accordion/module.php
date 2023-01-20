<?php
namespace UltimatePostKit\Modules\PostAccordion;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'post-accordion';
	}

	public function get_widgets() {

		$widgets = [
			'Post_Accordion',
		];
		
		return $widgets;
	}
}
