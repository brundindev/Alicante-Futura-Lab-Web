<?php
namespace HelloBiz\Modules\Theme\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\WPNotificationsPackage\V120\Notifications as Notifications_SDK;

class Notificator {
	private ?Notifications_SDK $notificator = null;

	public function get_notifications_by_conditions( $force_request = false ) {
		return $this->notificator->get_notifications_by_conditions( $force_request );
	}

	public function __construct() {
		if ( ! class_exists( 'Elementor\WPNotificationsPackage\V120\Notifications' ) ) {
			require_once HELLO_BIZ_PATH . '/vendor/autoload.php';
		}

		$this->notificator = new Notifications_SDK( [
			'app_name' => 'hello-biz',
			'app_version' => HELLO_BIZ_ELEMENTOR_VERSION,
			'short_app_name' => 'ehbiz',
			'app_data' => [
				'theme_name' => EHP_THEME_SLUG,
			],
		] );
	}
}
