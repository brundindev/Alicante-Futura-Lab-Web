<?php

namespace HelloBiz\Modules\ConversionBanner\Components;

use HelloBiz\Includes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ajax_Handler {

	public function __construct() {
		add_action( 'wp_ajax_hello_biz_install_hp', [ $this, 'install_hello_plus' ] );
	}

	public function install_hello_plus() {
		wp_ajax_install_plugin();
	}
}
