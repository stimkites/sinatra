<?php
/**
 * Created by PhpStorm.
 * User: Stim
 * Date: 29.03.19
 * Time: 15:57
 * Template: list of items to translate
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

/**
 * @global $posts
 * @global $type
 */

?>
<div class="tablenav top">
	<?php include "pages.php" ?>
</div>
<table class="wp-list-table widefat fixed striped sinatra">
	<thead>
	<tr>
		<td id="cb" class="manage-column column-cb check-column">
			<label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All' ) ?></label>
			<input id="cb-select-all-1" type="checkbox" />
		</td>
		<th scope="col" id="post_title"
		    class="manage-column column-title column-primary min-width">
			<?php _e( 'Title' ) ?>
		</th>
		<th scope="col" id="post_excerpt"
		    class="manage-column column-excerpt min-width">
			<?php _e( 'Excerpt', SLUG ) ?>
		</th>
		<th scope="col" id="post_content"
		    class="manage-column column-content">
			<?php _e( 'Content', SLUG ) ?>
		</th>
		<th scope="col" id="actions"
		    class="manage-column column-actions min-width"></th>
	</tr>
	</thead>
	<tbody id="the-list">
	<?php if ( empty( $posts ) ) : ?>
	<tr class="no-items"><td class="colspanchange" colspan="5">No items found.</td></tr>
	<?php else : ?>
		<?php
		    $get_meta = 'get_' . $type . '_meta';
            foreach ($posts as $post) { ?>
			<tr class="row-original" id="item-<?php echo $post->ID ?>">
				<td><input type="checkbox" name="posts[]" class="item-id" value="<?php echo $post->ID ?>" /></td>
                <td class="min-width"><?php echo ( isset( $post->term_id ) ? $post->post_title : '<a href="post.php?post='.$post->ID.'&action=edit" target="_blank">'.$post->post_title.'</a>' ) ?></td>
				<td class="min-width"><?php echo mb_substr( strip_tags( $post->post_excerpt ), 0, 150 ) ?></td>
				<td><?php echo mb_substr( strip_tags( $post->post_content ), 0, 250 ) ?></td>
				<td class="min-width">
                    <?php
                    if( $get_meta( $post->ID, _self::original, 1 ) ) :
                    ?>
                    <button class="button button-secondary sinatra-butt sinbutt sinatra-restore" data-action="restore" title="<?php _e( 'Restore original', SLUG ) ?>" value="<?php echo $post->ID ?>"></button>
                    <?php else : ?>
                    <button class="button button-secondary sinatra-butt sinbutt sinatra-translate" data-action="translate" title="<?php _e( 'Translate', SLUG ) ?>" value="<?php echo $post->ID ?>"></button>
                    <?php endif; ?>
                </td>
			</tr>
            <tr id="item-<?php echo $post->ID ?>-TR0">
		<?php
		    if( ! empty(  $translated = $get_meta( $post->ID, _self::translated, true ) ) )
		        include ROOT_TPL_PATH . '/translated.php';
		    ?>
            </tr>
		<?php } ?>
	<?php endif; ?>
	</tbody>
	<tfoot>
	<tr>
		<td id="cb" class="manage-column column-cb check-column">
			<label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All' ) ?></label>
			<input id="cb-select-all-1" type="checkbox" />
		</td>
		<th scope="col" id="post_title"
		    class="manage-column column-title column-primary">
			<?php _e( 'Title' ) ?>
		</th>
		<th scope="col" id="post_excerpt"
		    class="manage-column column-excerpt">
			<?php _e( 'Excerpt', SLUG ) ?>
		</th>
		<th scope="col" id="post_content"
		    class="manage-column column-content">
			<?php _e( 'Content', SLUG ) ?>
		</th>
		<th scope="col" id="actions"
		    class="manage-column column-actions"></th>
	</tr>
	</tfoot>
</table>
<div class="tablenav bottom">
	<?php include "pages.php" ?>
</div>
