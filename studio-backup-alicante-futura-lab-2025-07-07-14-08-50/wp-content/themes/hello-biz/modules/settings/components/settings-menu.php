<?php

namespace HelloBiz\Modules\Settings\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Menu {

	const SETTINGS_PAGE_SLUG = 'hello-plus-settings';

	public function register_settings_page( $parent_slug ): void {
		add_submenu_page(
			$parent_slug,
			__( 'Settings', 'hello-biz' ),
			__( 'Settings', 'hello-biz' ),
			'manage_options',
			self::SETTINGS_PAGE_SLUG,
			[ $this, 'render_settings_page' ]
		);
	}

	public function render_settings_page(): void {
		echo '<div id="ehp-admin-settings" data-themestyleurl="' . esc_attr( HELLO_BIZ_STYLE_URL ) . '"></div>';
	}

	public function __construct() {
		add_action( 'hello-plus-theme/admin-menu', [ $this, 'register_settings_page' ], 10, 1 );
	}
}
