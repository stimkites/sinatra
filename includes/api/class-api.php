<?php

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\Api' ) ) return;

/**
 * API prototype
 *
 * Parent API interface
 */
abstract class Api extends \WP_HTTP_Response {

	/**
	 * Parts to translate
	 */
	const parts = [ 'post_title', 'post_excerpt', 'post_content' ];

	/**
	 * Send request url
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * Send request params
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 * Last error
	 *
	 * @var string
	 */
	public $error = '';

	/**
	 * Last response code
	 *
	 * @var int
	 */
	public $code = 0;

	/**
	 * Patterns to filter out with corresponding separators
	 */
	const patterns = [
		'attr' => [
			'/((?<=alt=")([^"]+)|(?<=placeholder=")([^"]+)|(?<=title=")([^"]+)|(?<=value=")([^"]+))/im',
			'0000101'
		],
		'tags' => [
			'/<[^>]*>/im',
			'0001254'
		],
		'ctrl' => [
			'/[\t\r\n]+/im',
			'00000103'
		]
	];

	/**
	 * Stored tags from the text
	 *
	 * @var array
	 */
	protected static $tags = [];

	/**
	 * Stored caret controls
	 *
	 * @var array
	 */
	protected static $ctrl = [];

	/**
	 * Api constructor.
	 *
	 * @throws \Exception
	 */
	public function __construct(){
		_self::log( 'API init:' );
		_self::log( $this->url );
		_self::log( $this->params );
		$response = $this->send( $this->url, $this->params );
		if( ! isset( $response['code'] ) ) return null;
		if( $response['code'] != 200 )  {
			$this->error = $response['body'];
			$this->code = $response['code'];
		}
		parent::__construct( $response['body'], $response['code'], $response['headers'] );
	}

	/**
	 * Return decoded data
	 *
	 * @return \WP_Post
	 */
	public function get_data(){
		return $this->text_to_post( json_decode( $this->data, true ) );
	}


	/**
	 * Send request
	 *
	 * @param $url
	 * @param $params
	 * @return \WP_Error | null | array
	 * @throws \Exception
	 */
	protected function send( $url, $params ){
		$curl = curl_init( $url );
		curl_setopt( $curl, CURLOPT_POST,           'POST' === $params['method']    );
		curl_setopt( $curl, CURLOPT_TIMEOUT,        $params['timeout']              );
		curl_setopt( $curl, CURLOPT_POSTFIELDS,     $params['body']                 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER,     $params['headers']              );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true                            );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true                            );
		curl_setopt( $curl, CURLOPT_VERBOSE,        false                           );
		curl_setopt( $curl, CURLOPT_HEADER,         true                            );
		try {
			$response = curl_exec( $curl );
		}catch( \Exception $e ){
			return _self::log( 'ERROR EXCEPTION: ' . $e->getMessage() );
		}
		$info           = curl_getinfo( $curl );
		$header_size    = curl_getinfo( $curl, CURLINFO_HEADER_SIZE);
		$headers        = explode( "\r\n", substr( $response, 0, $header_size ) );
		$body           = substr( $response, $header_size );
		$code           = $info['http_code'];
		return _self::log( [
			'code'      => $code,
			'body'      => $body,
			'headers'   => $headers
		] );
	}


	/**
	 * Transform post to translate into text
	 *
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	protected function post_to_text( $post = null ){
		if( ! $post ) return null;
		return '';
	}

	/**
	 * Transform response text into post
	 *
	 * @param array $json
	 *
	 * @return \stdClass | \WP_Post
	 */
	protected function text_to_post( $json = null ){
		if( ! $json ) return null;
		$post = new \stdClass();
		return $post;
	}

	/**
	 * Proper string encoding
	 *
	 * @param string $str
	 * @param string $part
	 * 
	 * @return string
	 */
	protected static function entities( $str, $part ){
		return self::fetch_text(
			htmlspecialchars_decode(
				preg_replace(
					'/(&)([0-9]*;)/',
					'$1#$2',
					html_entity_decode(
						$str,
						ENT_QUOTES,
						"UTF-8"
					)
				)
			),
			$part
		);
	}

	/**
	 * Get all tags attributes
	 *
	 * @param string $text
	 * @param string $part
	 *
	 * @return string
	 */
	protected static function fetch_text( $text, $part ){
		if( preg_match_all( self::patterns['attr'][0], $text, $attrs ) )
			$text = preg_replace(
				self::patterns['attr'][0],
				'%s',
				$text )
			        . self::patterns['attr'][1]
			        . implode( self::patterns['attr'][1], $attrs[0] );
		return self::purify( $text, $part );
	}

	/**
	 * Fill in all caret controls, tags and tag's attributes
	 *
	 * @param string $text
	 * @param string $part
	 *
	 * @return string
	 */
	protected static function print_text( $text, $part ){
		$attrs = explode( self::patterns['attr'][1], $text );
		$text = $attrs[0];
		if( ! empty( self::$ctrl[ $part ] ) )
			$text = vsprintf( str_replace( self::patterns['ctrl'][1], '%s', $text ), self::check_ctrls( self::$ctrl[ $part ] ) );
		if( ! empty( self::$tags[ $part ] ) )
			$text = vsprintf( str_replace( self::patterns['tags'][1], '%s', $text ), self::$tags[ $part ] );
		array_shift( $attrs );
		if( ! empty( $attrs ) )
			return vsprintf( $text, $attrs );
		return $text;
	}

	/**
	 * Purify text - remove tags and caret controls
	 *
	 * @param string $text
	 * @param string $part
	 *
	 * @return string
	 */
	protected static function purify( $text, $part ){
		if( preg_match_all( self::patterns['ctrl'][0], $text, $ctrl ) ) {
			self::$ctrl[ $part ] = $ctrl[0];
			$text = preg_replace( self::patterns['ctrl'][0], ' ' . self::patterns['ctrl'][1] . ' ', $text );
		}
		if( preg_match_all( self::patterns['tags'][0], $text, $tags ) ){
			self::$tags[ $part ] = $tags[0];
			$text = preg_replace( self::patterns['tags'][0], ' ' . self::patterns['tags'][1] . ' ', $text );
		}
		return $text;
	}

	/**
	 * Make caret pos controls sprintf-able
	 *
	 * @param array $ctrls
	 *
	 * @return array
	 */
	protected static function check_ctrls( $ctrls ){
		foreach( $ctrls as &$ctrl )
			if( "\n" === $ctrl )
				$ctrl = "\r\n";
		return $ctrls;
	}

}