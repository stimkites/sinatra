<?php

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\Google' ) ) return;

/**
 * Class Request
 *
 * Sends corresponding requests to Yandex API
 */
final class Google extends Api {

	/**
	 * url to send request to
	 *
	 * @var string
	 */
	protected $url = "https://script.google.com/macros/s/AKfycby82lrj0KnSt0vX4Tl0VQIo9MX7uqXf2Nxo_3lxoeBWL8-ocrA/exec";

	/**
	 * Initialization and sending request
     *
	 * @param \WP_Post $data
	 * @throws \Exception
	 */
	public function __construct( $data ) {
		if( empty( $data ) ) return null;
		$options = Admin::options();
		$this->params = [
			'method' => 'POST',
			'timeout' => 0,
			'headers' => [
				'Content-Type'   => 'application/json',
				'Accept'         => 'application/json'
			],
			'body'  => json_encode( [
				'source' => $options['source_lang'],
				'target' => $options['dest_lang'],
				'format' => 'text',
				'q'      => $this->post_to_text( $data ),
			] )
		];
		return parent::__construct();
	}

	/**
	 * Transform post to translate into text
	 *
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	protected function post_to_text( $post = null ){
		if( ! $post ) return null;
		$r = [];
		foreach( self::parts as $part )
			$r[ $part ] = self::entities( $post->{ $part }, $part );
		return $r;
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
		if( ! empty( $json ) )
			foreach ( $json as $key=>$value )
				$post->{ $key }   = self::print_text( $value, $key );
		return $post;
	}


}