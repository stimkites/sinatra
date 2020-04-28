<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29.03.19
 * Time: 13:20
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\Ajax' ) ) return;

/**
 * Class Ajax
 *
 * Ajax requests handler
 *
 * @package Stim\Sinatra
 */
final class Ajax {

	/**
	 * Unique ajax handler
	 */
	const handle = "sinatra_sings_loudly";

	/**
	 * Initialize ajax handling
	 */
	public static function init(){
		add_action( 'wp_ajax_' . self::handle, __CLASS__ . '::handler' );
	}

	/**
	 * Send a response
	 *
	 * @param $data
	 */
	private static function response( $data ){
		die( json_encode( $data ) );
	}

	/**
	 * Handle all incoming AJAX requests
	 */
	public static function handler() {
		if( ! wp_verify_nonce( $_POST['nonce'], SLUG ) )
			self::response( [ 'error' => __( 'Please, refresh the page...', SLUG ) ] );
		$proc = __NAMESPACE__ . '\Processor::' . $_POST['do'];
		if( ! is_callable( $proc ) )
			self::response( [ 'error' => 'Unrecognized action!' ] );
		self::response( $proc() );
	}





}