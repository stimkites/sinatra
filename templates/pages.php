<?php

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

/**
 * Created by PhpStorm.
 * User: Stim
 * Date: 29.03.19
 * Time: 15:57
 * Template: pagination
 */

/**
 * @global $items
 * @global $pages
 * @global $page
 */

?>
<div class="alignleft actions bulkactions sinatra-bulk">
    <label for="bulk-action-selector-top"
           class="screen-reader-text">Select bulk action</label>
    <select name="action" class="sinatra-select">
        <option value="-1"><?php _e( 'Bulk Actions', SLUG ) ?></option>
        <option value="translate"><?php _e( 'Translate all', SLUG ) ?></option>
        <option value="restore"><?php _e( 'Restore all', SLUG ) ?></option>
        <option value="save"><?php _e( 'Save all', SLUG ) ?></option>
        <option value="remove"><?php _e( 'Remove translations', SLUG ) ?></option>
    </select>
    <input type="submit" class="button action" value="<?php _e( 'Apply' ) ?>" />
</div>
<div class="tablenav-pages <?php echo ( $pages ? '' : 'no-pages' ) ?>">
	<span class="displaying-num"><?php echo $items . ' ' . __( 'items' ) ?></span>
	<span class="pagination-links">
		<span class="<?php echo ( $page > 1 ? '' : 'inactive' ) ?> paging" value="<?php echo 1 ?>" aria-hidden="true">«</span>
		<span class="<?php echo ( $page > 1 ? '' : 'inactive' ) ?> paging" value="<?php echo ( $page > 1 ? $page - 1 : 1 ) ?>" aria-hidden="true">‹</span>
		<span class="paging-input">
			<label for="current-page-selector" class="screen-reader-text"><?php _e( 'Current Page' ) ?></label>
			<input class="current-page"
			       id="current-page-selector"
			       type="text"
			       name="paged"
			       value="<?php echo $page ?>"
			       size="1"
			       max="<?php echo $pages ?>"
			       aria-describedby="table-paging" />
			<span class="tablenav-paging-text"> <?php _e( 'of' ) ?> <span class="total-pages"><?php echo $pages ?></span></span>
		</span>
		<span class="<?php echo ( $page < $pages ? '' : 'inactive' ) ?> paging" value="<?php echo ( $page < $pages ? $page + 1 : $pages ) ?>" aria-hidden="true">›</span>
		<span class="<?php echo ( $page < $pages ? '' : 'inactive' ) ?> paging" value="<?php echo $pages ?>" aria-hidden="true">»</span>
	</span>
</div>
<br class="clear">
