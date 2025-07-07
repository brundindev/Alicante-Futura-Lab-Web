<?php

namespace HelloBiz\Modules\AdminHome\Rest;

use HelloBiz\Includes\Utils;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Promotions extends Rest_Base {

	public function get_promotions() {
		$action_links_data = [];
		if (
			! defined( 'ELEMENTOR_IMAGE_OPTIMIZER_VERSION' ) &&
			! defined( 'IMAGE_OPTIMIZATION_VERSION' )
		) {
			$action_links_data[] = [
				'type' => 'go-image-optimizer',
				'image' => HELLO_BIZ_IMAGES_URL . 'image-optimizer.svg',
				'url' => Utils::get_plugin_install_url( 'image-optimization' ),
				'alt' => __( 'Elementor Image Optimizer', 'hello-biz' ),
				'title' => '',
				'messages' => [
					__( 'Optimize Images.', 'hello-biz' ),
					__( 'Reduce Size.', 'hello-biz' ),
					__( 'Improve Speed.', 'hello-biz' ),
					__( 'Try Image Optimizer for free', 'hello-biz' ),
				],
				'button' => __( 'Install', 'hello-biz' ),
				'width' => 72,
				'height' => 'auto',
				'target' => '_self',
				'backgroundImage' => HELLO_BIZ_IMAGES_URL . 'image-optimization-bg.svg',
			];
		}

		if ( ! defined( 'SEND_VERSION' ) ) {
			$action_links_data[] = [
				'type' => 'go-send',
				'image' => HELLO_BIZ_IMAGES_URL . 'send-logo.gif',
				'backgroundColor' => '#EFEFFF',
				'url' => 'https://go.elementor.com/Hello_Biz_send',
				'alt' => __( 'Send', 'hello-biz' ),
				'title' => '',
				'messages' => [
					__( 'Connect any website to automated Email & SMS workflows in a click with Send.', 'hello-biz' ),
				],
				'button' => __( 'Install', 'hello-biz' ),
				'buttonBgColor' => '#524CFF',
				'width' => 72,
				'height' => 'auto',
			];
		} elseif (
			! defined( 'ELEMENTOR_AI_VERSION' ) &&
			Utils::is_elementor_installed()
		) {
			$action_links_data[] = [
				'type' => 'go-ai',
				'image' => HELLO_BIZ_IMAGES_URL . 'ai.png',
				'url' => 'https://go.elementor.com/biz-home-wp-elementor-ai/',
				'alt' => __( 'Elementor AI', 'hello-biz' ),
				'title' => __( 'Elementor AI', 'hello-biz' ),
				'messages' => [
					__( 'Boost creativity with Elementor AI. Craft & enhance copy, create custom CSS & Code, and generate images to elevate your website.', 'hello-biz' ),
				],
				'button' => __( 'Let\'s Go', 'hello-biz' ),
			];
		}

		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) && Utils::is_elementor_installed() ) {
			$action_links_data[] = [
				'type' => 'go-pro',
				'image' => HELLO_BIZ_IMAGES_URL . 'go-pro.svg',
				'url' => 'https://go.elementor.com/biz-home-wp-elementor-plugin-pricing/',
				'alt' => __( 'Elementor Pro', 'hello-biz' ),
				'title' => __( 'Bring your vision to life', 'hello-biz' ),
				'messages' => [
					__( 'Get complete design flexibility for your website with Elementor Pro’s advanced tools and premium features.', 'hello-biz' ),
				],
				'button' => __( 'Upgrade Now', 'hello-biz' ),
				'upgrade' => true,
				'features' => [
					__( 'Popup Builder', 'hello-biz' ),
					__( 'Custom Code & CSS', 'hello-biz' ),
					__( 'E-commerce Features', 'hello-biz' ),
					__( 'Collaborative Notes', 'hello-biz' ),
					__( 'Form Submission', 'hello-biz' ),
					__( 'Form Integrations', 'hello-biz' ),
					__( 'Customs Attribute', 'hello-biz' ),
					__( 'Role Manager', 'hello-biz' ),
				],
			];
		}

		return rest_ensure_response( [ 'links' => $action_links_data ] );
	}

	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/promotions',
			[
				'methods' => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_promotions' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);
	}
}
