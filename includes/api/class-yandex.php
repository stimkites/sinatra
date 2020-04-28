<?php

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\Yandex' ) ) return;

/**
 * Class Request
 *
 * Sends corresponding requests to Yandex API
 */
final class Yandex extends Api {

	/**
	 * Yandex API url
	 *
	 * @var string
	 */
	protected $url = "https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20190329T100221Z.e43e41db3fe2c0ff.a863e85f7187ce0783e8fa44987eefed186747fb";

	/**
	 * Initialization and sending request
     *
	 * @param \WP_Post $data
	 * @throws \Exception
	 */
	public function __construct( $data ) {
		if( empty( $data ) ) return null;
		$options = Admin::options();
		$body = $this->post_to_text( $data );
		$this->url .= '&lang=' . $options['source_lang'] . '-' . $options['dest_lang'] . '&format=text';
		$this->params = [
			'method' => 'POST',
			'timeout' => 120,
			'headers' => [
				'Content-Type'   => 'application/x-www-form-urlencoded',
				'Accept'         => '*/*',
				'Content-Length' => mb_strlen( $body )
			],
			'body' => $body
		];
		parent::__construct();
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
		$r = [];
		foreach ( self::parts as $part )
			$r[] = 'text=' . self::entities( $post->{ $part }, $part );
		return implode( '&', $r );
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
		foreach( self::parts as $i=>$part )
			$post->{ $part } = self::print_text( $json['text'][$i], $part );
		return $post;
	}

}