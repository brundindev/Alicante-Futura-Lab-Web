<?php

namespace HelloBiz\Modules\Settings\Components;

use HelloPlus\Modules\Admin\Classes\Menu\Pages\Kits_Library;
use HelloPlus\Modules\Admin\Classes\Menu\Pages\Settings;
use HelloPlus\Modules\Admin\Classes\Menu\Pages\Setup_Wizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Controller {

	const SETTINGS_META_KEY = 'ehp_theme_settings';
	const SETTINGS_FILTER_NAME = 'hello-plus-theme/settings';
	const SETTINGS_DEFAULT_FILTER_NAME = 'hello-plus-theme/settings/default';
	const SKIP_LINK = 'skip_link';
	const HEADER_FOOTER = 'header_footer';
	const PAGE_TITLE = 'page_title';
	const HELLO_THEME = 'hello_theme';

	public static function get_default_setting(): array {
		static $default_setting = null;
		if ( is_null( $default_setting ) ) {
			$default_setting = [
				self::SKIP_LINK => apply_filters( self::SETTINGS_DEFAULT_FILTER_NAME . '/' . self::SKIP_LINK, false ),
				self::HEADER_FOOTER => apply_filters( self::SETTINGS_DEFAULT_FILTER_NAME . '/' . self::HEADER_FOOTER, false ),
				self::PAGE_TITLE => apply_filters( self::SETTINGS_DEFAULT_FILTER_NAME . '/' . self::PAGE_TITLE, true ),
				self::HELLO_THEME => apply_filters( self::SETTINGS_DEFAULT_FILTER_NAME . '/' . self::HELLO_THEME, false ),
			];
		}

		return $default_setting;
	}

	public static function get_settings(): array {
		$option = get_option( self::SETTINGS_META_KEY, self::get_default_setting() );
		return apply_filters( self::SETTINGS_FILTER_NAME, $option );
	}

	protected static function get_option( string $option_name, $default_value = false ) {
		$option = self::get_settings()[ $option_name ] ?? $default_value;
		return apply_filters( self::SETTINGS_FILTER_NAME . '/' . $option_name, $option );
	}

	public static function should_skip_links() {
		return self::get_option( self::SKIP_LINK );
	}

	public static function should_hide_header_footer() {
		return self::get_option( self::HEADER_FOOTER );
	}

	public static function should_hide_page_title() {
		return self::get_option( self::PAGE_TITLE );
	}

	public static function should_hide_hello_theme() {
		return self::get_option( self::HELLO_THEME );
	}

	public function maybe_initialize_settings() {
		$settings = get_option( self::SETTINGS_META_KEY );

		if ( ! $settings ) {
			add_option(
				self::SETTINGS_META_KEY,
				self::get_default_setting()
			);
		}
	}

	public function enqueue_hello_plus_settings_scripts() {
		$screen = get_current_screen();

		if ( 'hello-biz_page_hello-plus-settings' !== $screen->id ) {
			return;
		}

		$handle = 'hello-biz-settings';
		$asset_path = HELLO_BIZ_SCRIPTS_PATH . $handle . '.asset.php';
		$asset_url = HELLO_BIZ_SCRIPTS_URL;

		if ( ! file_exists( $asset_path ) ) {
			throw new \Exception( 'You need to run `npm run build` for the "hello-biz" first.' );
		}

		$script_asset = require $asset_path;

		$script_asset['dependencies'][] = 'wp-util';

		wp_enqueue_script(
			$handle,
			$asset_url . "$handle.js",
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations( $handle, 'hello-biz' );
	}

	public function __construct() {
		add_action( 'hello-plus/init', [ $this, 'maybe_initialize_settings' ], 9 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_hello_plus_settings_scripts' ] );
	}
}
