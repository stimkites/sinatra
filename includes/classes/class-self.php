<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29.03.19
 * Time: 13:13
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\_self' ) ) return;

/**
 * Class _self
 *
 * Plugin loader
 *
 * @package Stim\Sinatra
 */
final class _self {

	/**
	 * Meta slug for keeping translated post body
	 */
	const translated = '_sinatra_translated';

	/**
	 * Meta slug for storing original body
	 */
	const original = '_sinatra_original';

	/**
	 * Load and initialize all plugin parts
	 */
	public static function load(){
		Ajax::init();
		Admin::init();
	}

	/**
	 * Write log
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	public static function log( $data ){
		$options = Admin::options();
		if( ! $options['track_me'] ) return $data;
		if( function_exists( 'wc_get_logger' ) ){
			$logger = wc_get_logger();
			$logger->log( 'debug', print_r( $data, 1 ), [ 'source' => 'sinatra' ] );
		}else
			error_log( print_r( $data, 1 ) );
		return $data;
	}

}