<?php
/**
 * Created by PhpStorm.
 * User: stim
 * Date: 4/1/19
 * Time: 11:10 AM
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );


/**
 * @global \WP_Post | \stdClass $translated
 */

?>

<td></td>
<td class="min-width"><?php echo strip_tags( $translated->post_title ) ?></td>
<td class="min-width"><?php echo mb_substr( strip_tags( $translated->post_excerpt ), 0, 150 ) ?></td>
<td><?php echo mb_substr( strip_tags( $translated->post_content ), 0, 250 ) ?></td>
<td class="min-width">
	<button class="button button-secondary sinatra-butt sinbutt sinatra-remove"      data-action="remove"   title="<?php _e( 'Remove translation', SLUG ) ?>"        value="<?php echo $translated->ID ?>"></button>
    <button class="button button-secondary sinatra-butt sinatra-revise"                                     title="<?php _e( 'Revise translated version', SLUG ) ?>" value="<?php echo $translated->ID ?>"></button>
    <button class="button button-secondary sinatra-butt sinbutt sinatra-save-single" data-action="save"     title="<?php _e( 'Save as original', SLUG ) ?>"          value="<?php echo $translated->ID ?>"></button>
</td>