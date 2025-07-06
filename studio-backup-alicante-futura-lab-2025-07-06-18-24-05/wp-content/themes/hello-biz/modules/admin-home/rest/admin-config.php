<?php

namespace HelloBiz\Modules\AdminHome\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\DocumentTypes\Page;
use HelloBiz\Includes\Utils;
use WP_REST_Server;

class Admin_Config extends Rest_Base {

	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/admin-settings',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_admin_config' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);
	}

	public function get_admin_config() {
		$config = $this->get_welcome_box_config( [] );

		$config = $this->get_site_parts( $config );

		$config = $this->get_resources( $config );

		$config = apply_filters( 'hello-plus-theme/rest/admin-config', $config );

		$config['config'] = [
			'showText'     => ! Utils::is_hello_plus_installed(),
			'nonceInstall' => wp_create_nonce( 'updates' ),
		];

		return rest_ensure_response( [ 'config' => $config ] );
	}

	public function get_resources( array $config ) {
		$config['resourcesData'] = [
			'community' => [
				[
					'title'  => __( 'Facebook', 'hello-biz' ),
					'link'   => 'https://www.facebook.com/groups/Elementors/',
					'icon'   => 'BrandFacebookIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'YouTube', 'hello-biz' ),
					'link'   => 'https://www.youtube.com/@Elementor',
					'icon'   => 'BrandYoutubeIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Discord', 'hello-biz' ),
					'link'   => 'https://discord.com/servers/elementor-official-community-1164474724626206720',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Rate Us', 'hello-biz' ),
					'link'   => 'https://wordpress.org/support/theme/hello-biz/reviews/#new-post',
					'icon'   => 'StarIcon',
					'target' => '_blank',
				],
			],
			'resources' => [
				[
					'title'  => __( 'Help Center', 'hello-biz' ),
					'link'   => 'https://go.elementor.com/hello-biz-help/',
					'icon'   => 'HelpIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Blog', 'hello-biz' ),
					'link'   => 'https://go.elementor.com/hello-biz-blog/',
					'icon'   => 'SpeakerphoneIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Platinum Support', 'hello-biz' ),
					'link'   => 'https://go.elementor.com/hello-biz-platinumcare',
					'icon'   => 'BrandElementorIcon',
					'target' => '_blank',
				],
			],
		];

		return $config;
	}

	public function get_site_parts( array $config ): array {
		$last_five_pages_query = new \WP_Query(
			[
				'posts_per_page'         => 5,
				'post_type'              => 'page',
				'post_status'            => 'publish',
				'orderby'                => 'post_date',
				'order'                  => 'DESC',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'lazy_load_term_meta'    => true,
				'update_post_meta_cache' => false,
			]
		);

		$site_pages = [];

		if ( $last_five_pages_query->have_posts() ) {
			$elementor_active    = Utils::is_elementor_active();
			$edit_with_elementor = $elementor_active ? '&action=elementor' : '';
			while ( $last_five_pages_query->have_posts() ) {
				$last_five_pages_query->the_post();
				$site_pages[] = [
					'title' => get_the_title(),
					'link'  => get_edit_post_link( get_the_ID(), 'admin' ) . $edit_with_elementor,
					'icon'  => 'PagesIcon',
				];
			}
		}

		$general = [
			[
				'title' => __( 'Add New Page', 'hello-biz' ),
				'link'  => self_admin_url( 'post-new.php?post_type=page' ),
				'icon'  => 'PageTypeIcon',
			],
			[
				'title' => __( 'Settings', 'hello-biz' ),
				'link'  => self_admin_url( 'admin.php?page=hello-plus-settings' ),
			],
		];

		$common_parts = [
			array_merge(
				[
					'id' => 'theme-builder',
					'title' => __( 'Theme Builder', 'hello-biz' ),
					'icon' => 'ThemeBuilderIcon',
				],
				Utils::get_theme_builder_options()
			),
		];

		$customizer_header_footer_url = self_admin_url( 'customize.php?autofocus[section]=hello-biz-options' );

		$header_part = [
			'id'           => 'header',
			'title'        => __( 'Header', 'hello-biz' ),
			'link'         => $customizer_header_footer_url,
			'icon'         => 'HeaderTemplateIcon',
			'showSublinks' => true,
			'sublinks'     => [],
		];
		$footer_part = [
			'id'           => 'footer',
			'title'        => __( 'Footer', 'hello-biz' ),
			'link'         => $customizer_header_footer_url,
			'icon'         => 'FooterTemplateIcon',
			'showSublinks' => true,
			'sublinks'     => [],
		];

		if ( Utils::is_elementor_active() ) {
			if ( Utils::has_pro() ) {
				$header_part = $this->update_pro_part( $header_part, 'header' );
				$footer_part = $this->update_pro_part( $footer_part, 'footer' );
			}
		}

		$site_parts = [
			'siteParts' => array_merge(
				[
					$header_part,
					$footer_part,
				],
				$common_parts
			),
			'sitePages' => $site_pages,
			'general'   => $general,
		];

		$config['siteParts'] = apply_filters( 'hello-plus-theme/template-parts', $site_parts );

		return $this->get_quicklinks( $config );
	}

	private function update_pro_part( array $part, string $location ): array {
		$theme_builder_module = \ElementorPro\Modules\ThemeBuilder\Module::instance();
		$conditions_manager   = $theme_builder_module->get_conditions_manager();

		$documents    = $conditions_manager->get_documents_for_location( $location );
		$add_new_link = \Elementor\Plugin::instance()->app->get_base_url() . '#/site-editor/templates/' . $location;
		if ( ! empty( $documents ) ) {
			$first_document_id    = array_key_first( $documents );
			$part['showSublinks'] = true;
			$part['sublinks']     = [
				[
					'title' => __( 'Edit', 'hello-biz' ),
					'link'  => get_edit_post_link( $first_document_id, 'admin' ) . '&action=elementor',
				],
				[
					'title' => __( 'Add New', 'hello-biz' ),
					'link'  => $add_new_link,
				],
			];
		} else {
			$part['link']         = $add_new_link;
			$part['showSublinks'] = false;
		}

		return $part;
	}

	public function get_open_homepage_with_tab( $action, $customizer_fallback_args = [] ): string {
		if ( Utils::is_elementor_active() && method_exists( Page::class, 'get_site_settings_url_config' ) ) {
			return Page::get_site_settings_url_config( $action )['url'];
		}

		return add_query_arg( $customizer_fallback_args, self_admin_url( 'customize.php' ) );
	}

	public function get_quicklinks( $config ): array {
		$config['quickLinks'] = [
			'site_name' => [
				'title' => __( 'Site Name', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( 'settings-site-identity', [ 'autofocus[section]' => 'title_tagline' ] ),
				'icon'  => 'TextIcon',
			],
			'site_logo' => [
				'title' => __( 'Site Logo', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( 'settings-site-identity', [ 'autofocus[section]' => 'title_tagline' ] ),
				'icon'  => 'PhotoIcon',
			],
			'site_favicon' => [
				'title' => __( 'Site Favicon', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( 'settings-site-identity', [ 'autofocus[section]' => 'title_tagline' ] ),
				'icon'  => 'AppsIcon',
			],
		];

		if ( Utils::is_elementor_active() ) {
			$config['quickLinks']['site_colors'] = [
				'title' => __( 'Site Colors', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( 'global-colors' ),
				'icon'  => 'BrushIcon',
			];

			$config['quickLinks']['site_fonts'] = [
				'title' => __( 'Site Fonts', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( 'global-typography' ),
				'icon'  => 'UnderlineIcon',
			];
		}

		return $config;
	}

	public function get_welcome_box_config( array $config ): array {
		$is_elementor_active  = Utils::is_elementor_active();
		$is_hello_plus_active = Utils::is_hello_plus_active();
		$cta_text             = __( 'Begin Setup', 'hello-biz' );

		if ( ! $is_hello_plus_active ) {
			$link = Utils::is_hello_plus_installed() ? Utils::get_hello_plus_activation_link() : 'install';
			$config['welcome'] = [
				'text'    => __( 'To get access to the full suite of features, including theme kits, header and footer templates, and more widgets, click “Begin setup” and start your web creator journey.', 'hello-biz' ),
				'image'   => [
					'src' => HELLO_BIZ_IMAGES_URL . 'banner-image.png',
					'alt' => $cta_text,
				],
				'buttons' => [
					[
						'title'   => $cta_text,
						'variant' => 'contained',
						'link'    => $link,
						'color'   => 'primary',
					],
				],
			];

			return $config;
		}

		if ( ! $is_elementor_active || ! Utils::is_hello_plus_setup_wizard_done() ) {
			$config['welcome'] = [
				'text'    => __( 'To get access to the full suite of features, including theme kits, header and footer templates, and more widgets, click “Begin setup” and start your web creator journey.', 'hello-biz' ),
				'image'   => HELLO_BIZ_IMAGES_URL . 'banner-image.png',
				'buttons' => [
					[
						'title'   => $cta_text,
						'variant' => 'contained',
						'link'    => self_admin_url( 'admin.php?page=hello-plus-setup-wizard' ),
						'color'   => 'primary',
					],
				],
			];

			return $config;
		}

		$config['welcome'] = [
			'text'    => __( 'Here you\'ll find links to some site settings that will help you set up and get running as soon as possible. With Hello Biz you\'ll find creating your website is a breeze.', 'hello-biz' ),
			'image'   => HELLO_BIZ_IMAGES_URL . 'banner-image.png',
			'buttons' => [
				[
					'title'   => __( 'Edit home page', 'hello-biz' ),
					'variant' => 'contained',
					'link'    => get_edit_post_link( get_option( 'page_on_front' ), 'admin' ) . '&action=elementor',
					'color'   => 'primary',
				],
				[
					'title'   => __( 'View site', 'hello-biz' ),
					'variant' => 'outlined',
					'link'    => get_site_url(),
					'color'   => 'secondary',
				],
			],
		];

		return $config;
	}
}
