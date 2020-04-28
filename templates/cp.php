<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29.03.19
 * Time: 14:57
 */

namespace Stim\Sinatra;

defined( __NAMESPACE__ . '\SLUG' ) or die( 'Sinatra never sings alone!' );

$options = Admin::options();

if( $options['track_me'] )
    $_POST = $options['track'];

?>

<div id="revise-popup" class="mitsbox">
    Hi there!
</div>

<div class="wrap nosubsub">
    <!--p class="yandex-service"><?php printf( __( 'Service is provided by %s', SLUG ), '<a href="https://yandex.ru" target="_blank">'.__( 'Yandex', SLUG ) . '</a>'); ?></p-->
	<h1>Sinatra. <span class="header-sinatra"><?php printf( __( '%sSi%smple %sna%stive %stra%snslations', SLUG ), '<i class="heading-logo">', '</i>', '<i class="heading-logo">', '</i>', '<i class="heading-logo">', '</i>' ) ?></span></h1>
	<hr class="wp-header-end">
	<p><?php _e('Select and translate desired posts easily and automatically. Revise before saving. If needed - keep original text, but be sure you need double data.', SLUG ) ?></p>
	<form action=" " enctype="multipart/form-data" method="post" class="wp-sinatra-options">
		<h2><?php _e( 'Translation options', SLUG ) ?></h2>
		<p><?php _e( 'Use desired language codes and options below. All options are saved on the fly.', SLUG ) ?></p>
		<table class="form-table sinatra-options">
			<tbody>
			<tr>
                <th>
                    <label><?php _e( 'API provider', SLUG ) ?>:
                        <select name="api_provider" class="sinatra-select">
                            <option value="y" <?php echo( empty( $options['api_provider'] )
                                                          || 'y' === $options['api_provider'] ? 'selected' : '' ) ?>>
                                <?php _e( 'Yandex', SLUG ) ?>
                            </option>
                            <option value="g" <?php echo( ! empty( $options['api_provider'] )
                                                          && 'g' === $options['api_provider'] ? 'selected' : '' ) ?>>
                                <?php _e( 'Google', SLUG ) ?>
                            </option>
                        </select>
                    </label>
                </th>
				<td scope="row" align="right">
					<p><label><?php _e( 'Source language code', SLUG ) ?>
						<input type="text" name="source_lang" value="<?php echo $options['source_lang'] ?>" />
						</label></p>
					<p><label><?php _e( 'Destination language code', SLUG ) ?>
						<input type="text" name="dest_lang" value="<?php echo $options['dest_lang'] ?>" />
						</label></p>
				</td>
				<td scope="row">
					<p><label><input type="checkbox"
					              value="1"
							<?php echo ( $options['backup'] ? 'checked' : '' ) ?>
							      name="backup"
						/> <?php _e( 'Keep original text as meta data', SLUG ) ?>
						</label></p>
					<p><label title="<?php _e( 'Save current page and type on the fly and notify if changes not saved, log debug on everything', SLUG ) ?>">
							<input type="checkbox"
					               value="1"
								<?php echo ( $options['track_me'] ? 'checked' : '' ) ?>
								     name="track_me"
							/> <?php _e( 'Track me', SLUG ) ?>
                            <span class="last-tracked"></span>
						</label></p>
				</td>
			</tr>
			</tbody>
		</table>
	</form>
	<hr/>
	<div class="sinatra-search">
		<span class="search-icon"></span>
        <span class="search-reset"></span>
        <input type="text"
		       value="<?php echo ( empty( $_POST['search'] ) ? '' : $_POST['search'] ) ?>"
		       placeholder="<?php _e( 'Search' ) ?>"
               id="search" />
	</div>
	<h2><?php _e( 'To translate', SLUG ) ?>:
		<select id="translate_object" name="type" class="sinatra-select">
			<option value="posts" <?php echo ( empty( $_POST['type'] ) || 'posts' === $_POST['type'] ? 'selected' : '' ) ?> ><?php _e( 'posts' ) ?></option>
			<option value="terms" <?php echo ( ! empty( $_POST['type'] ) && 'terms' === $_POST['type'] ? 'selected' : '' ) ?> ><?php _e( 'terms' ) ?></option>
		</select>
	</h2>
	<form action=" " enctype="multipart/form-data" method="post" class="wp-sinatra-control">
		<div class="sinatra-content">
			<span class="spinner"></span>
		</div>
	</form>
</div>