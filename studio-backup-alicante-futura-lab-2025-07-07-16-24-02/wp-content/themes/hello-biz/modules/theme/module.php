<?php

namespace HelloBiz\Modules\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HelloBiz\Includes\Module_Base;
use HelloBiz\Modules\Settings\Components\Settings_Controller;

/**
 * Theme module
 *
 * @package HelloBiz
 * @subpackage HelloBizModules
 */
class Module extends Module_Base {
	const HELLO_BIZ_THEME_VERSION_OPTION = 'hello_biz_theme_version';

	/**
	 * @inheritDoc
	 */
	public static function get_name(): string {
		return 'theme';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_component_ids(): array {
		return [
			'Customizer',
			'Theme_Support',
			'Notificator',
		];
	}


	/**
	 * Check whether to display the theme's default header & footer.
	 *
	 * @return bool
	 */
	public static function display_header_footer(): bool {
		return ! Settings_Controller::should_hide_header_footer();
	}

	public function display_header_footer_filter( bool $display ): bool {
		$show = self::display_header_footer();
		return $show ? $display : false;
	}

	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	public function scripts_styles() {
		if ( ! Settings_Controller::should_hide_hello_theme() ) {
			wp_enqueue_style(
				'hello-biz',
				HELLO_BIZ_STYLE_URL . 'theme.css',
				[],
				HELLO_BIZ_ELEMENTOR_VERSION
			);
		}

		if ( self::display_header_footer() ) {
			wp_enqueue_style(
				'hello-biz-header-footer',
				HELLO_BIZ_STYLE_URL . 'header-footer.css',
				[],
				HELLO_BIZ_ELEMENTOR_VERSION
			);
		}
	}

	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	public function content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello-plus-theme/content_width', 800 );
	}

	/**
	 * @inheritDoc
	 */
	protected function register_hooks(): void {
		parent::register_hooks();
		add_action( 'after_setup_theme', [ $this, 'content_width' ], 0 );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts_styles' ] );
		add_filter( 'hello-plus-theme/display-default-footer', [ $this, 'display_header_footer_filter' ] );
		add_filter( 'hello-plus-theme/display-default-header', [ $this, 'display_header_footer_filter' ] );
	}
}
