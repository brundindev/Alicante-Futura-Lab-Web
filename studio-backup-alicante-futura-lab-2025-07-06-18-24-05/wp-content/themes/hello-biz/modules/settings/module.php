<?php

namespace HelloBiz\Modules\Settings;

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
		return 'settings';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_component_ids(): array {
		return [
			'Settings_Controller',
			'Settings_Menu',
		];
	}

	public static function is_active(): bool {
		return is_admin();
	}
}
