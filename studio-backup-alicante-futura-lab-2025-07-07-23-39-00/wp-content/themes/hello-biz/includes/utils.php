<?php

namespace HelloBiz\Includes;

use Elementor\App\App;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * class Utils
 **/
class Utils {

	private static ?bool $elementor_installed = null;

	private static ?bool $elementor_active = null;

	public static function elementor(): \Elementor\Plugin {
		return \Elementor\Plugin::$instance;
	}

	public static function has_pro(): bool {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	public static function is_elementor_active(): bool {
		if ( null === self::$elementor_active ) {
			self::$elementor_active = defined( 'ELEMENTOR_VERSION' );
		}

		return self::$elementor_active;
	}

	public static function is_elementor_installed(): bool {
		if ( null === self::$elementor_installed ) {
			self::$elementor_installed = file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' );
		}

		return self::$elementor_installed;
	}

	public static function is_hello_plus_active() {
		return defined( 'HELLO_PLUS_VERSION' );
	}

	public static function is_hello_plus_installed() {
		return file_exists( WP_PLUGIN_DIR . '/hello-plus/hello-plus.php' );
	}

	public static function is_hello_plus_setup_wizard_done() {
		if ( ! class_exists( 'HelloPlus\Modules\Admin\Classes\Menu\Pages\Setup_Wizard' ) ) {
			return false;
		}

		return \HelloPlus\Modules\Admin\Classes\Menu\Pages\Setup_Wizard::has_site_wizard_been_completed();
	}

	public static function get_hello_plus_activation_link() {
		$plugin = 'hello-plus/hello-plus.php';

		$url = 'plugins.php?action=activate&plugin=' . $plugin . '&plugin_status=all';

		return add_query_arg( '_wpnonce', wp_create_nonce( 'activate-plugin_' . $plugin ), $url );
	}

	public static function get_plugin_install_url( $plugin_slug ): string {
		$action = 'install-plugin';

		$url = add_query_arg(
			[
				'action' => $action,
				'plugin' => $plugin_slug,
				'referrer' => 'hello-biz',
			],
			admin_url( 'update.php' )
		);

		return add_query_arg( '_wpnonce', wp_create_nonce( $action . '_' . $plugin_slug ), $url );
	}

	public static function get_theme_builder_options(): array {
		$url = 'https://go.elementor.com/hello-biz-builder';
		$target = '_blank';

		if ( ! class_exists( 'Elementor\App\App' ) ) {
			return [
				'link' => $url,
				'target' => $target,
			];
		}

		if ( self::has_pro() ) {
			$url = admin_url( 'admin.php?page=' . App::PAGE_ID . '&ver=' . ELEMENTOR_VERSION ) . '#site-editor';
			$target = '_self';
		}

		if ( self::is_elementor_active() ) {
			$url = admin_url( 'admin.php?page=' . App::PAGE_ID . '&ver=' . ELEMENTOR_VERSION ) . '#site-editor/promotion';
			$target = '_self';
		}

		return [
			'link' => $url,
			'target' => $target,
		];
	}
}
