<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29.03.19
 * Time: 13:20
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\Admin' ) ) return;

/**
 * Class Admin
 *
 * Settings, options and UI loader
 *
 * @package Stim\Sinatra
 */
final class Admin {

	/**
	 * Initialize admin parts
	 */
	public static function init(){
		add_action( 'admin_menu',                   __CLASS__ . '::add_admin_menu' );
		add_action( 'admin_enqueue_scripts',        __CLASS__ . '::add_be_scripts' );
		add_filter( 'plugin_action_links_' . INDEX, __CLASS__ . '::setting_link' );
		load_plugin_textdomain( SLUG, false, ROOT_PATH . '/languages' );
	}

	/**
	 * Link to translate control panel
	 *
	 * @param $l
	 * @return array
	 */
	public static function setting_link( $l ) {
		return array_merge( [
			'<a href="' . admin_url( 'tools.php?page=' . SLUG ) . '">' . __( 'Translations', SLUG ) . '</a>'
		], $l );
	}

	/**
	 * Enqueue admin scripts
	 */
	public static function add_be_scripts(){
		if( ! isset( $_REQUEST['page'] ) || false === strpos( $_REQUEST['page'], SLUG ) ) return;
		if( ! wp_script_is( 'jquery' ) )
			wp_enqueue_script( 'jquery' );
		if( ! wp_script_is( 'jquery-blockui' ) )
			wp_enqueue_script( 'jquery-blockui', ASSETS_URL . '/js/jquery-blockui.min.js', [ 'jquery' ], '0.0.1', false );
		if( ! wp_script_is( 'mitsbox' ) )
			wp_enqueue_script( 'mitsbox', ASSETS_URL . '/js/mitsbox.js', [ 'jquery' ], '0.0.3', false );
		wp_enqueue_style( SLUG, ASSETS_URL . '/css/styles.css', null, time() );
		wp_register_script( SLUG , ASSETS_URL . '/js/default.js', [ 'jquery', 'jquery-ui-core' ] );
		wp_enqueue_script( SLUG , ASSETS_URL . '/js/default.js', [ 'jquery', 'jquery-ui-core' ], time(), false );
		wp_localize_script( SLUG, 'sinatra', [
			'nonce'        => wp_create_nonce( SLUG ),
			'action'       => Ajax::handle,
			'options'      => self::options(),
			'warn'         => __( 'Warning! Changes not saved. Proceed?', SLUG )
		] );
	}

	/**
	 * Adding admin menus
	 */
	public static function add_admin_menu(){
		add_management_page(
			'Sinatra',
			'Sinatra',
			'manage_options',
			SLUG,
			__CLASS__ . '::render'
		);
	}

	/**
	 * Render control panel
	 */
	public static function render(){
		include ROOT_TPL_PATH . '/cp.php';
	}

	/**
	 * Options stuff
	 */
	const defaults = [
		'source_lang'   => 'sv',
		'dest_lang'     => 'ru',
		'backup'        => '1',
		'track_me'      => '1'
	];

	private static $options = [];

	/**
	 * Get or save plugin options or defaults
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public static function options( $options = [] ){
		if( ! empty( $options ) ){
			self::$options = $options;
			return update_option( SLUG, $options );
		}
		if( empty( self::$options ) ) {
			$options = get_option( SLUG );
			if( $options )
				self::$options = $options;
			else
				self::$options = $options = self::defaults;
		} else
			$options = self::$options;
		return $options;
	}

}