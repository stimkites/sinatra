<?php
/**
 * Created by PhpStorm.
 * User: Stim
 * Date: 07.04.19
 * Time: 17:49
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\Processor' ) ) return;

/**
 * Class Processor
 *
 * Processing POST requests
 *
 * @package Stim\Sinatra
 */
final class Processor {

	/**
	 * Perform preliminary actions
	 *
	 * @return string
	 */
	private static function preaction(){
		if( empty( $_POST['pre']['action'] ) ) return '';
		$pre_proc = __CLASS__ . '::' . $_POST['pre']['action'];
		if( ! is_callable( $pre_proc ) ) return 'ERROR: Preliminary action not exists!';
		$type = rtrim( $_POST['type'], 's' );
		if( empty( $_POST['pre']['ids'] ) ) return '';
		$errors = '';
		foreach( $_POST['pre']['ids'] as $id )
			$errors .= ( $errors ? "\r\n\r\n" : '' ) . $pre_proc( $id, $type );
		return $errors;
	}

	/**
	 * Set translation revised
	 *
	 * @param int $id
	 * @param string $type
	 *
	 * @return string
	 */
	public static function save_revised( $id, $type ){
		self::set_revised( $id, $type );
		return self::save( $id, $type );
	}

	/**
	 * Remove translation
	 *
	 * @param int $id
	 * @param string $type
	 */
	public static function remove( $id, $type ){
		$delete = 'delete_' . $type . '_meta';
		$delete( $id, _self::translated );
	}


	/**
	 * Set translation revised
	 *
	 * @param int $id
	 * @param string $type
	 */
	public static function set_revised( $id, $type ){
		$update = 'update_' . $type . '_meta';
		$post = new \stdClass();
		$post->ID = $id;
		foreach( $_POST['pre']['post'] as $key=>$value )
			$post->{ $key } = ( $key === 'post_content' ? Builder::merge_from_lines( $id, $value ) : $value );
		$update( $id, _self::translated,  $post );
	}


	/**
	 * Save options
	 *
	 * @return array
	 */
	public static function save_options(){
		if( empty( $_POST['options'] ) )
			return [ 'error' => 'No options to save!' ];
		Admin::options( $_POST['options'] );
		return [ 'error' => '' ];
	}


	/**
	 * Transform term to post
	 *
	 * @param \WP_term | \WP_Post $term
	 *
	 * @return mixed
	 */
	public static function term_to_post( $term ){
		if( ! isset( $term->term_id ) ) return $term;
		$term->post_title = $term->name;
		$term->post_excerpt=$term->description;
		$term->post_content = '';
		$term->ID = $term->term_id;
		return $term;
	}

	/**
	 * Format post to keep as an original (reduce data to keep)
	 *
	 * @param \WP_Post $post
	 * @return object
	 */
	private static function format_post( $post ){
		$obj = new \stdClass();
		$obj->ID = $post->ID;
		$obj->post_title = $post->post_title;
		$obj->post_excerpt = $post->post_excerpt;
		$obj->post_content = $post->post_content;
		return $obj;
	}


	/**
	 * Translate items
	 *
	 * @param int $id
	 * @param string $type
	 */
	public static function translate( $id, $type ){
		$get_item = 'get_' . $type;
		$update_meta = 'update_' . $type . '_meta';
		$item = self::term_to_post( $get_item( $id ) );
		$b_data = null;
		if ( Builder::is_active( $id ) )
			$item->post_content = Builder::get_data();
		$options = Admin::options();
		try {
			$response = (
				( empty( $options['api_provider'] ) || $options['api_provider'] === 'y' )
					? new Yandex( $item )
					: new Google( $item )
			);
		}catch( \Exception $e ){
			_self::log( $e );
			return;
		}
		if( ! empty( $response->error ) )
			_self::log( $response->error );
		else{
			$translated = $response->get_data();
			$translated->ID = $id;
			$translated->post_content = str_replace( '] [', '][', $translated->post_content );
		    $update_meta( $id, _self::translated, $translated );
		}
	}


	/**
	 * Save single translation as original
	 *
	 * @param int $id
	 * @param string $type
	 *
	 * @return string
	 */
	public static function save( $id, $type ){
		$get = 'get_' . $type . '_meta';
		$update = 'update_' . $type . '_meta';
		$delete = 'delete_' . $type . '_meta';
		$item = $get( $id, _self::translated, 1 );
		if( ! $item ) return 'Error: Translation not found!';
		$error = '';
		$options = Admin::options();
		if( ! empty( $options['backup'] ) ) {
			if( 'term' === $type )
				$post = self::term_to_post( get_term( $id ) );
			else
				$post = self::format_post( get_post( $id ) );
			if( Builder::is_active( $id ) )
				Builder::backup();
			else
				$update( $id, _self::original, $post );
		}
		switch( $type ){
			case 'post':
				if( Builder::is_active( $id ) )
				    $error = Builder::set_data();
				else
					$error = ( wp_update_post( [
						'ID'            => $id,
						'post_title'    => $item->post_title,
						'post_excerpt'  => $item->post_excerpt,
						'post_content'  => $item->post_content
					] ) ? '' : 'Error: Could not update WordPress post!' );
			break;
			case 'term':
				$term = get_term( $id );
				$error = ( wp_update_term( $id, $term->taxonomy, [
					'name'          => $item->post_title,
					'description'   => $item->post_excerpt
				] ) ? '' : 'ERROR: Could not update WordPress term!' );
			break;
		}
		if( ! $error )
			$delete( $id, _self::translated );
		return $error;
	}

	
	/**
	 * Restore original translation
	 *
	 * @param int $id
	 * @param string $type
	 *
	 * @return string
	 */
	public static function restore( $id, $type ){
		$get = 'get_' . $type . '_meta';
		$delete = 'delete_' . $type . '_meta';
		$item = $get( $id, _self::original, 1 );
		if( ! $item ) return 'ERROR: Could not restore, original item is empty!';
		$r = true;
		if( 'post' === $type )
			$r = ( Builder::is_active( $id )
			       && Builder::restore()
			       || wp_update_post( [
					'ID'            => $id,
					'post_title'    => $item->post_title,
					'post_excerpt'  => $item->post_excerpt,
					'post_content'  => $item->post_content
				] ) );
		elseif( 'term' === $type ) {
			$term = get_term( $id );
			if( wp_update_term( $id, $term->taxonomy, [
				'name'        => $item->post_title,
				'description' => $item->post_excerpt
			] ) )
				$delete( $id, _self::original );
		}
		if( $r )
			$delete( $id, _self::original );
		return ( $r ? '' : 'ERROR: Could not restore original!' );
	}


	/**
	 * Fetch items from DB
	 *
	 * @return array
	 */
	public static function fetch(){
		$error = self::preaction();
		if( ! empty( $error ) )
			return [ 'error' => $error ];
		$options = Admin::options();
		if( $options['track_me'] ) {
			if( 15 == $_POST['tracked'] ) {
				$options['track'] = $_POST;
				Admin::options( $options );
			} elseif( ! empty( $options['track'] ) )
				$_POST = $options['track'];
		}
		if( $_POST['type'] === 'posts' ) {
			remove_all_filters( 'pre_get_posts' );
			$query = new \WP_Query( [
				'paged'             => $_POST['page'],
				'posts_per_page'    => 30,
				'post_type'         => [ 'page', 'post', 'product', 'product_variation' ],
				'post_status'       => [ 'publish', 'private', 'draft' ],
				'suppress_filters'  => 1,
				's'                 => $_POST['search'],
				'orderby'           => 'ID',
				'order'             => 'desc'
			] );
			$pages = $query->max_num_pages;
			$page  = $_POST['page'];
			$items = $query->found_posts;
			foreach( $query->posts as $post ) {
				if ( Builder::is_active( $post->ID ) ) {
					$post->post_excerpt = __( 'Wetail Page Builder is active', SLUG );
					$post->post_content = Builder::get_data();
				}
				$posts[] = $post;
			}
		}else{
			$args = [
				'hide_empty'       => 1,
				'number'           => 30,
				'suppress_filters' => 1,
				'search'           => $_POST['search'],
				'orderby'          => 'term_id',
				'order'            => 'desc'
			];
			$query = new \WP_Term_Query( $args );
			$args[ 'offset' ] = $_POST['page'];
			$all = new \WP_Term_Query( $args );
			$ttl = count( $all->terms );
			$pages = ceil( $ttl / 30 );
			$page  = $_POST['page'];
			$items = $ttl;
			$posts = array_map( __CLASS__ . '::term_to_post', $query->terms );
		}
		$type = rtrim( $_POST['type'], 's' );
		ob_start();
		include ROOT_TPL_PATH . "/list.php";
		return [ 'result' => ob_get_clean() ];
	}

	/**
	 * Revise translation
	 *
	 * @return array
	 */
	public static function revise(){
		$type = rtrim( $_POST['type'], 's' );
		$get = 'get_' . $type;
		$get_meta = 'get_' . $type . '_meta';
		$post = self::term_to_post( $get( $_POST['id'] ) );
		if( Builder::is_active( $_POST['id'] ) )
            $post->post_content = Builder::get_data();
        $translated = $get_meta( $_POST['id'], _self::translated, 1 );
		ob_start();
		include ROOT_TPL_PATH . "/revision.php";
		return [ 'result' => ob_get_clean() ];
	}

}