<?php
/**
 * WordPress file sytstem API.
 *
 * @link       https://www.cookieyes.com/
 * @since      3.0.0
 * @package    CookieYes\Lite\Includes
 */

namespace CookieYes\Lite\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Handles admin notices for the plugin.
 */
class Notice {

	/**
	 * Instance of the current class
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Return the current instance of the class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Holds all dismissed notices
	 *
	 * @access public
	 * @since 3.0.0
	 * @var array $notices Array of dismissed notices.
	 */
	public $notices;

	/**
	 * Holds all promo notices
	 *
	 * @access public
	 * @since 3.0.0
	 * @var array $promo_notices Array of promo notices.
	 */
	public $promo_notices;

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 3.0.0
	 */
	public function __construct() {

		// Populate $notices.
		$this->notices = $this->get_dismissed();
		foreach ( $this->notices as $notice => $timeout ) {
			if ( $timeout && $timeout < time() ) {
				$this->undismiss( $notice );
			}
		}

		// Populate $promo_notices.
		$this->promo_notices = $this->get_dismissed_promo();
		foreach ( $this->promo_notices as $notice => $timeout ) {
			if ( $timeout && $timeout < time() ) {
				$this->undismiss_promo( $notice );
			}
		}
	}

	/**
	 * Checks if a given notice has been dismissed or not
	 *
	 * @since 6.0.0
	 * @param string $notice Programmatic Notice Name.
	 * @return boolean  Notice Dismissed
	 */
	public function is_dismissed( $notice ) {
		if ( ! isset( $this->notices[ $notice ] ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Marks the given notice as dismissed
	 *
	 * @since 3.0.0
	 * @param string  $notice Programmatic Notice Name.
	 * @param integer $expiry Notice expiry.
	 * @return void
	 */
	public function dismiss( $notice, $expiry = 0 ) {
		$dismissed = $this->get_dismissed();
		if ( 0 !== $expiry ) {
			$dismissed[ $notice ] = time() + $expiry;
		} else {
			$dismissed[ $notice ] = false;
		}
		update_option( 'cky_admin_notices', $dismissed );
	}


	/**
	 * Marks a notice as not dismissed
	 *
	 * @access public
	 * @since 6.0.0
	 *
	 * @param string $notice Programmatic Notice Name.
	 * @return void
	 */
	public function undismiss( $notice ) {
		unset( $this->notices[ $notice ] );
		update_option( 'cky_admin_notices', $this->notices );
	}

	/**
	 * Add notice
	 *
	 * @param string $notice Notice ID.
	 * @param array  $options Notice options.
	 * @return void
	 */
	public function add( $notice, $options = array() ) {
		$options = wp_parse_args(
			$options,
			array(
				'dismissible' => true,
				'type'        => 'default',
				'expiration'  => 0, // Default 0 (no expiration).
				'message'     => '',
			)
		);
		if ( isset( $this->notices[ $notice ] ) ) {
			unset( $this->notices[ $notice ] );
		} else {
			$this->notices[ $notice ] = $options;
		}
	}

	/**
	 * Add promo notice
	 *
	 * @param string $notice Notice ID.
	 * @param array  $options Notice options.
	 * @return void
	 */
	public function add_promo( $notice, $options = array() ) {
		// Check if the notice is dismissed
		if ( $this->is_promo_dismissed( $notice ) ) {
			return; // Don't add dismissed notices
		}
		
		$options = wp_parse_args(
			$options,
			array(
				'dismissible' => true,
				'type'        => 'promo',
				'expiration'  => 0, // Default 0 (no expiration).
				'message'     => '',
			)
		);
		if ( isset( $this->promo_notices[ $notice ] ) ) {
			unset( $this->promo_notices[ $notice ] );
		} else {
			$this->promo_notices[ $notice ] = $options;
		}
		// Update the database
		update_option( 'cky_promo_notices', $this->promo_notices );
	}

	/**
	 * Get all the notices.
	 *
	 * @return array
	 */
	public function get() {
		return $this->notices;
	}

	/**
	 * Get all the promo notices.
	 *
	 * @return array
	 */
	public function get_promo() {
		return $this->promo_notices;
	}

	/**
	 * Get dismissed notices
	 *
	 * @return array
	 */
	public function get_dismissed() {
		return get_option( 'cky_admin_notices', array() );
	}

	/**
	 * Get dismissed promo notices
	 *
	 * @return array
	 */
	public function get_dismissed_promo() {
		return get_option( 'cky_promo_notices', array() );
	}

	/**
	 * Marks the given promo notice as dismissed
	 *
	 * @since 3.0.0
	 * @param string  $notice Programmatic Notice Name.
	 * @param integer $expiry Notice expiry.
	 * @return void
	 */
	public function dismiss_promo( $notice, $expiry = 0 ) {
		$dismissed = $this->get_dismissed_promo();
		if ( 0 !== $expiry ) {
			$dismissed[ $notice ] = time() + $expiry;
		} else {
			$dismissed[ $notice ] = false;
		}
		update_option( 'cky_promo_notices', $dismissed );
	}

	/**
	 * Marks a promo notice as not dismissed
	 *
	 * @access public
	 * @since 6.0.0
	 *
	 * @param string $notice Programmatic Notice Name.
	 * @return void
	 */
	public function undismiss_promo( $notice ) {
		$dismissed = $this->get_dismissed_promo();
		unset( $dismissed[ $notice ] );
		update_option( 'cky_promo_notices', $dismissed );
	}

	/**
	 * Checks if a given promo notice has been dismissed or not
	 *
	 * @since 6.0.0
	 * @param string $notice Programmatic Notice Name.
	 * @return boolean  Notice Dismissed
	 */
	public function is_promo_dismissed( $notice ) {
		$dismissed = $this->get_dismissed_promo();
		if ( ! isset( $dismissed[ $notice ] ) ) {
			return false;
		}
		return true;
	}
}
