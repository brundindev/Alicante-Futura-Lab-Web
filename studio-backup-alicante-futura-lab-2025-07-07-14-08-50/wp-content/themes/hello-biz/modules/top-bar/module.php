<?php

namespace HelloBiz\Modules\TopBar;

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
		return 'top-bar';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_component_ids(): array {
		return [
			'Admin_Top_Bar',
		];
	}

	public static function is_active(): bool {
		return is_admin();
	}
}
