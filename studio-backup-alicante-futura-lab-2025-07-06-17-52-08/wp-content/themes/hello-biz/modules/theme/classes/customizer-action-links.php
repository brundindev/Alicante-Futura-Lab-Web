<?php
namespace HelloBiz\Modules\Theme\Classes;

use HelloBiz\Includes\Utils;
use ParagonIE\Sodium\Core\Util;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Customizer_Action_Links extends \WP_Customize_Control {

	// Whitelist content parameter
	public $content = '';

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper.
	 *
	 * @return void
	 */
	public function render_content() {
		$this->print_customizer_action_links();

		if ( isset( $this->description ) ) {
			echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
		}
	}

	private function is_header_footer_experiment_active(): bool {
		if ( ! Utils::is_elementor_active() ) {
			return false;
		}

		return (bool) ( \Elementor\Plugin::$instance->experiments->is_feature_active( 'hello-theme-header-footer' ) );
	}

	/**
	 * Print customizer action links.
	 *
	 * @return void
	 */
	private function print_customizer_action_links() {
		$action_link_data = [];

		if ( ! Utils::is_elementor_installed() ) {
			$action_link_type = 'install-elementor';
		} elseif ( ! Utils::is_elementor_active() ) {
			$action_link_type = 'activate-elementor';
		} elseif ( ! $this->is_header_footer_experiment_active() ) {
			$action_link_type = 'activate-header-footer-experiment';
		} else {
			$action_link_type = 'style-header-footer';
		}

		switch ( $action_link_type ) {
			case 'install-elementor':
				$action_link_data = [
					'image' => HELLO_BIZ_IMAGES_URL . 'elementor.svg',
					'alt' => esc_attr__( 'Elementor', 'hello-biz' ),
					'title' => esc_html__( 'Install Elementor', 'hello-biz' ),
					'message' => esc_html__( 'Create cross-site header & footer using Elementor.', 'hello-biz' ),
					'button' => esc_html__( 'Install Elementor', 'hello-biz' ),
					'link' => wp_nonce_url(
						add_query_arg(
							[
								'action' => 'install-plugin',
								'plugin' => 'elementor',
							],
							self_admin_url( 'update.php' )
						),
						'install-plugin_elementor'
					),
				];
				break;
			case 'activate-elementor':
				$action_link_data = [
					'image' => HELLO_BIZ_IMAGES_URL . 'elementor.svg',
					'alt' => esc_attr__( 'Elementor', 'hello-biz' ),
					'title' => esc_html__( 'Activate Elementor', 'hello-biz' ),
					'message' => esc_html__( 'Create cross-site header & footer using Elementor.', 'hello-biz' ),
					'button' => esc_html__( 'Activate Elementor', 'hello-biz' ),
					'link' => wp_nonce_url( 'plugins.php?action=activate&plugin=elementor/elementor.php', 'activate-plugin_elementor/elementor.php' ),
				];
				break;
			case 'activate-header-footer-experiment':
				$action_link_data = [
					'image' => HELLO_BIZ_IMAGES_URL . 'elementor.svg',
					'alt' => esc_attr__( 'Elementor', 'hello-biz' ),
					'title' => esc_html__( 'Style cross-site header & footer', 'hello-biz' ),
					'message' => esc_html__( 'Click “Begin setup” to customize your cross-site header & footer.', 'hello-biz' ),
					'button' => esc_html__( 'Begin Setup', 'hello-biz' ),
					'link' => '#',
					'underButton' => esc_html__( 'By clicking “Begin setup” I agree to install and activate the Hello+ plugin.', 'hello-biz' ),
				];
				break;
			default:
				return;
		}

		$action_link_data = apply_filters( 'hello-plus-theme/customizer/action-links', $action_link_data );

		$customizer_content = $this->get_customizer_action_links_html( $action_link_data );

		echo wp_kses_post( $customizer_content );
	}

	/**
	 * Get the customizer action links HTML.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	private function get_customizer_action_links_html( $data ) {
		if (
			empty( $data )
			|| ! isset( $data['image'] )
			|| ! isset( $data['alt'] )
			|| ! isset( $data['title'] )
			|| ! isset( $data['message'] )
			|| ! isset( $data['link'] )
			|| ! isset( $data['button'] )
		) {
			return;
		}

		return sprintf(
			'<div class="ehp-action-links">
				<img src="%1$s" alt="%2$s">
				<p class="ehp-action-links-title">%3$s</p>
				<p class="ehp-action-links-message">%4$s</p>
				<a class="button button-primary" id="ehp-begin-setup" target="_blank" href="%5$s">%6$s</a>
				<p class="ehp-action-links-under-button">%7$s</p>
			</div>',
			$data['image'],
			$data['alt'],
			$data['title'],
			$data['message'],
			$data['link'],
			$data['button'],
			$data['underButton'] ?? '',
		);
	}
}
