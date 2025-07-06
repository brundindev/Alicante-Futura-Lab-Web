<?php

namespace HelloBiz\Modules\TopBar\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Admin_Top_Bar {

	private function render_admin_top_bar() {
		?>
		<div id="ehp-admin-top-bar-root" style="height: 50px">
		</div>
		<?php
	}

	private function is_top_bar_active() {
		$current_screen = get_current_screen();

		return strpos( $current_screen->id ?? '', 'hello-biz' ) !== false;
	}

	private function enqueue_scripts() {
		$handle = 'hello-biz-admin-top-bar';
		$asset_path = HELLO_BIZ_SCRIPTS_PATH . 'hello-biz-admin-top-bar.asset.php';
		$asset_url = HELLO_BIZ_SCRIPTS_URL;

		if ( ! file_exists( $asset_path ) ) {
			return;
		}

		$asset = require $asset_path;

		wp_enqueue_script(
			$handle,
			$asset_url . 'hello-biz-admin-top-bar.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( $handle, 'hello-biz' );
	}

	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'current_screen', function () {
			if ( ! $this->is_top_bar_active() ) {
				return;
			}

			add_action( 'in_admin_header', function () {
				$this->render_admin_top_bar();
			} );

			add_action( 'admin_enqueue_scripts', function () {
				$this->enqueue_scripts();
			} );
		} );
	}
}
