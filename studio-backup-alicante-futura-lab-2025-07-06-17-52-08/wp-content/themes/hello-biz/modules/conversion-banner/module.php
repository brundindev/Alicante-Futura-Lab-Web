<?php

namespace HelloBiz\Modules\ConversionBanner;

use HelloBiz\Includes\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * class Module
 *
 * @package HelloPlus
 * @subpackage HelloPlusModules
 */
class Module extends Module_Base {
	/**
	 * @inheritDoc
	 */
	public static function get_name(): string {
		return 'conversion-banner';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_component_ids(): array {
		return [
			'Conversion_Banner',
			'Ajax_Handler',
		];
	}

	public static function is_active(): bool {
		return is_admin();
	}
}
