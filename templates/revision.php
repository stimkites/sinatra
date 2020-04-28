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
 * @global $post
 * @global $translated
 */

?>
<table class="wp-list-table widefat sinatra sinatra-revision">
	<thead>
	<tr>
        <td></td>
		<th scope="col" id="post_title"
		    class="manage-column column-original column-primary">
			<?php _e( 'Original' ) ?>
		</th>
		<th scope="col" id="post_excerpt"
		    class="manage-column column-translated">
			<?php _e( 'Translated', SLUG ) ?>
		</th>
	</tr>
	</thead>
	<tbody id="revised-content">
        <tr>
            <th scope="row"><?php _e( 'Title', SLUG ) ?></th>
            <td class="original">
                <div id="matcher_origin" class="matcher"></div>
                <pre>
                    <?php echo htmlspecialchars( $post->post_title ) ?>
                </pre>
            </td>
            <td class="revision">
                <div id="matcher_translate" class="matcher"></div>
                <pre class="editable" contenteditable="true" name="post_title">
                    <?php echo htmlspecialchars( $translated->post_title ) ?>
                </pre>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'Excerpt', SLUG ) ?></th>
            <td class="original">
                <pre>
                    <?php echo htmlspecialchars( $post->post_excerpt ) ?>
                </pre>
            </td>
            <td class="revision">
                <pre class="editable" contenteditable="true" name="post_excerpt">
                    <?php echo htmlspecialchars( $translated->post_excerpt ) ?>
                </pre>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'Content', SLUG ) ?></th>
            <td class="original">
                <div class="original-lines">
                    <?php echo Builder::split_into_lines( $post->ID, htmlspecialchars( $post->post_content ) ) ?>
                </div>
            </td>
            <td class="revision">
                <div class="editable translated-lines" contenteditable="true" name="post_content">
                    <?php echo Builder::split_into_lines( $post->ID, htmlspecialchars( $translated->post_content ) ) ?>
                </div>
            </td>
        </tr>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="3">
            <button class="button button-primary" value="<?php echo $post->ID ?>" id="revision-save"><?php _e( 'Save as original', SLUG ) ?></button>
            <button class="button button-secondary" value="<?php echo $post->ID ?>" id="revision-set"><?php _e( 'Set revised', SLUG ) ?></button>
		</td>
	</tr>
	</tfoot>
</table>
