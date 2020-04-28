<?php
/**
 * Created by PhpStorm.
 * User: stim
 * Date: 4/23/19
 * Time: 11:14 AM
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

if( class_exists( __NAMESPACE__ . '\Builder' ) ) return;

/**
 * Class Builder
 *
 * Compatibility to Wetail Page Builder data on posts
 *
 * @package Stim\Sinatra
 */
final class Builder {

	/**
	 * Original Builder slug
	 */
	const slug       = '_fl_builder_data';
	const draft      = '_fl_builder_draft';

	/**
	 * Fields to translate
	 */
	const fields    = [
		'settings'          => [
			'title', 'heading', 'text', 'heading_title',
			'heading_title2',   'heading_sub_title',
			'heading_title3',   'label'
		],
		'settings->data'    => [
			'title', 'dateFormatted', 'uploadedToTitle', 'text'
		]
	];

	/**
	 * Original data
	 *
	 * @var null
	 */
	private static $data = null;

    /**
     * Post id we refer to
     *
     * @var int
     */
	private static $post_id = 0;


    /**
	 * Check if page builder is active on the post
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public static function is_active( $post_id ){
		return (
            ( null !== self::$data && self::$post_id === $post_id )
            ||
            ( ( self::$data = get_post_meta( $post_id, self::slug, true  ) ) && ( self::$post_id = $post_id ) )
        );
	}

    /**
     * Backup data
     *
     * @return bool|int
     */
	public static function backup(){
	    $post = get_post( self::$post_id );
	    self::$data['original_post_title'] = $post->post_title;
	    return update_post_meta(
	        self::$post_id,
            _self::original,
            self::$data
        );
    }

    /**
     * Restore data
     *
     * @return bool|int
     */
    public static function restore(){
        $data = get_post_meta( self::$post_id, _self::original, true );
        if( ! wp_update_post( [
            'ID'            => self::$post_id,
            'post_title'    => $data['original_post_title']
        ] ) ) return false;
        return (
        	update_post_meta( self::$post_id, self::slug, $data ) &&
	        update_post_meta( self::$post_id, self::draft, $data )
        );
    }

	/**
	 * Set builder translated data
     *
     * @return string
	 */
	public static function set_data(){
        if( ! ( $post = get_post_meta( self::$post_id, _self::translated, true ) ) )
        	return 'ERROR: Builder translated data not found on post [' . self::$post_id . ']!';
        $content = str_replace( '] [', '][', $post->post_content );
        $values = explode( '][', $content );
        $data = self::get_data( true );
        $l = count( $data );
        $cv = count( $values );
        if( $cv !== $l )
        	return sprintf( __( 'ERROR: Cannot save because translated data length (%s) differs from original one (%s). Please, revise translation.', SLUG ), $cv, $l );
		foreach ( $data as $index=>$object )
			self::$data[ $object[0] ]->{ $object[1] }->{ $object[2] } = self::field( $values[ $index ] );
		return ( (
		    update_post_meta( self::$post_id, self::slug, self::$data ) &&
		    update_post_meta( self::$post_id, self::draft, self::$data ) &&
            wp_update_post( [ 'ID' => self::$post_id, 'post_title' => $post->post_title ] )
        ) ? '' : 'ERROR: WordPress database entries could not be updated!' );
	}

	/**
	 * Fetch builder data for external usage
     *
     * @param bool $as_structure
	 *
	 * @return string | array
	 */
	public static function get_data( $as_structure = false ){
		$rez = [];
		if( ! self::$data ) return 'WRONG DATA!';
		foreach( self::$data as $object_id=>$object )
			foreach( self::fields as $field=>$field_fields )
				if( isset( $object->{ $field } ) )
					foreach ( $field_fields as $object_field )
						if ( isset( $object->{ $field }->{ $object_field } ) )
                            ( $as_structure
                                    ? $rez[] = [ $object_id,  $field, $object_field ]
                                    : $rez[] = self::text( $object->{ $field }->{ $object_field } )
                            );
		return ( $as_structure ? $rez : implode( '', $rez ) );
	}

    /**
     * Transform into text value
     *
     * @param string $field
     *
     * @return string
     */
	private static function text( $field = '' ){
	    return '[' . $field . ']';
    }

    /**
     * Transform into field value
     *
     * @param string $text
     * @return string
     */
    private static function field( $text = '[]' ){
	    $text = trim( $text );
	    return ltrim( rtrim( $text, ']' ), '[' );
    }


	/**
	 * Split text into revision lines
	 *
	 * @param int $id
	 * @param string $text
	 *
	 * @return string
	 */
    public static function split_into_lines( $id, $text ){
    	if( ! self::is_active( $id) ) return $text;
    	$text = trim( $text );
    	$lines = explode( '][', $text );
    	return '<pre>' . implode( "]</pre><pre>[", $lines ) . '</pre>';
    }


	/**
	 * Merge text from revision lines
	 *
	 * @param int $id
	 * @param string $text
	 *
	 * @return mixed|string
	 */
    public static function merge_from_lines( $id, $text ){
	    if( ! self::is_active( $id) ) return $text;
	    $text = trim( $text );
	    return ltrim( rtrim( str_replace( "]</pre><pre>[", '][', $text ), '</pre>' ), '<pre>' );
    }

}