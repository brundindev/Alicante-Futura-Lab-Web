<?php
namespace HelloBiz\Modules\Theme\Components;

use HelloBiz\Modules\Theme\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Theme_Support {

	/**
	 * @return void
	 */
	public function setup() {
		if ( is_admin() ) {
			$this->maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello-plus-theme/register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-biz' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-biz' ) ] );
		}

		if ( apply_filters( 'hello-plus-theme/post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello-plus-theme/add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( HELLO_BIZ_STYLE_PATH . 'editor-styles.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );
			add_theme_support( 'editor-styles' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello-plus-theme/add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}

			add_theme_support( 'starter-content', $this->get_starter_content() );
		}
	}

	public function get_starter_content(): array {
		$content = [
			'options' => [
				'page_on_front' => '{{home}}',
				'show_on_front' => 'page',
			],
			'posts' => [
				'home' => require HELLO_BIZ_PATH . '/modules/theme/starter-content/home.php',
			],
		];

		return apply_filters( 'hello-plus-theme/starter-content', $content );
	}

	protected function maybe_update_theme_version_in_db() {
		// The theme version saved in the database.
		$theme_db_version = get_option( Module::HELLO_BIZ_THEME_VERSION_OPTION );

		// If the 'hello_plus_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
		if ( ! $theme_db_version || version_compare( $theme_db_version, HELLO_BIZ_ELEMENTOR_VERSION, '<' ) ) {
			update_option( Module::HELLO_BIZ_THEME_VERSION_OPTION, HELLO_BIZ_ELEMENTOR_VERSION );
		}
	}

	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'setup' ] );
	}
}
