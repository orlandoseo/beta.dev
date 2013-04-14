<?php
/**
 * Outputs the Genesis-specific in-post option boxes.
 *
 * It also handles saving the user input from those boxes, when a post or page
 * gets published or updated.
 *
 * @category   Genesis
 * @package    Admin
 * @subpackage Inpost-metaboxes
 * @author     StudioPress
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://www.studiopress.com/themes/genesis
 */

add_action( 'admin_menu', 'genesis_add_inpost_seo_box' );
/**
 * Register a new meta box to the post / page edit screen, so that the user can
 * set SEO options on a per-post or per-page basis.
 *
 * @category Genesis
 * @package Admin
 * @subpackage Inpost-Metaboxes
 *
 * @since 0.1.3
 *
 * @see genesis_inpost_seo_box() Generates the content in the meta box
 */
function genesis_add_inpost_seo_box() {

	foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
		if ( post_type_supports( $type, 'genesis-seo' ) )
			add_meta_box( 'genesis_inpost_seo_box', __( 'Theme SEO Settings', 'genesis' ), 'genesis_inpost_seo_box', $type, 'normal', 'high' );
	}

}

/**
 * Callback for in-post SEO meta box.
 *
 * Echoes out HTML.
 *
 * @category Genesis
 * @package Admin
 * @subpackage Inpost-Metaboxes
 *
 * @since 0.1.3
 */
function genesis_inpost_seo_box() {

	wp_nonce_field( 'genesis_inpost_seo_save', 'genesis_inpost_seo_nonce' );
	?>

	<p><label for="genesis_title"><b><?php _e( 'Custom Document Title', 'genesis' ); ?></b> <abbr title="&lt;title&gt; Tag">[?]</abbr> <span class="hide-if-no-js"><?php printf( __( 'Characters Used: %s', 'genesis' ), '<span id="genesis_title_chars">'. strlen( genesis_get_custom_field( '_genesis_title' ) ) .'</span>' ); ?></span></label></p>
	<p><input class="large-text" type="text" name="genesis_seo[_genesis_title]" id="genesis_title" value="<?php echo esc_attr( genesis_get_custom_field( '_genesis_title' ) ); ?>" /></p>

	<p><label for="genesis_description"><b><?php _e( 'Custom Post/Page Meta Description', 'genesis' ); ?></b> <abbr title="&lt;meta name=&quot;description&quot; /&gt;">[?]</abbr> <span class="hide-if-no-js"><?php printf( __( 'Characters Used: %s', 'genesis' ), '<span id="genesis_description_chars">'. strlen( genesis_get_custom_field( '_genesis_description' ) ) .'</span>' ); ?></span></label></p>
	<p><textarea class="large-text" name="genesis_seo[_genesis_description]" id="genesis_description" rows="4" cols="4"><?php echo esc_textarea( genesis_get_custom_field( '_genesis_description' ) ); ?></textarea></p>

	<p><label for="genesis_keywords"><b><?php _e( 'Custom Post/Page Meta Keywords, comma separated', 'genesis' ); ?></b> <abbr title="&lt;meta name=&quot;keywords&quot; /&gt;">[?]</abbr></label></p>
	<p><input class="large-text" type="text" name="genesis_seo[_genesis_keywords]" id="genesis_keywords" value="<?php echo esc_attr( genesis_get_custom_field( '_genesis_keywords' ) ); ?>" /></p>

	<p><label for="genesis_canonical"><b><?php _e( 'Custom Canonical URI', 'genesis' ); ?></b> <a href="http://www.mattcutts.com/blog/canonical-link-tag/" target="_blank" title="&lt;link rel=&quot;canonical&quot; /&gt;">[?]</a></label></p>
	<p><input class="large-text" type="text" name="genesis_seo[_genesis_canonical_uri]" id="genesis_canonical" value="<?php echo esc_url( genesis_get_custom_field( '_genesis_canonical_uri' ) ); ?>" /></p>

	<p><label for="genesis_redirect"><b><?php _e( 'Custom Redirect URI', 'genesis' ); ?></b> <a href="http://www.google.com/support/webmasters/bin/answer.py?hl=en&amp;answer=93633" target="_blank" title="301 Redirect">[?]</a></label></p>
	<p><input class="large-text" type="text" name="genesis_seo[redirect]" id="genesis_redirect" value="<?php echo esc_url( genesis_get_custom_field( 'redirect' ) ); ?>" /></p>

	<br />

	<p><b><?php _e( 'Robots Meta Settings', 'genesis' ); ?></b></p>

	<p>
		<input type="checkbox" name="genesis_seo[_genesis_noindex]" id="genesis_noindex" value="1" <?php checked( genesis_get_custom_field( '_genesis_noindex' ) ); ?> />
		<label for="genesis_noindex"><?php printf( __( 'Apply %s to this post/page', 'genesis' ), '<code>noindex</code>' ); ?> <a href="http://yoast.com/articles/robots-meta-tags/" target="_blank">[?]</a></label><br />

		<input type="checkbox" name="genesis_seo[_genesis_nofollow]" id="genesis_nofollow" value="1" <?php checked( genesis_get_custom_field( '_genesis_nofollow' ) ); ?> />
		<label for="genesis_nofollow"><?php printf( __( 'Apply %s to this post/page', 'genesis' ), '<code>nofollow</code>' ); ?> <a href="http://yoast.com/articles/robots-meta-tags/" target="_blank">[?]</a></label><br />

		<input type="checkbox" name="genesis_seo[_genesis_noarchive]" id="genesis_noarchive" value="1" <?php checked( genesis_get_custom_field( '_genesis_noarchive' ) ); ?> />
		<label for="genesis_nofollow"><?php printf( __( 'Apply %s to this post/page', 'genesis' ), '<code>noarchive</code>' ); ?> <a href="http://yoast.com/articles/robots-meta-tags/" target="_blank">[?]</a></label>
	</p>

	<br />

	<p><label for="genesis_scripts"><b><?php _e( 'Custom Tracking/Conversion Code', 'genesis' ); ?></b></label></p>
	<p><textarea class="large-text" rows="4" cols="4" name="genesis_seo[_genesis_scripts]" id="genesis_scripts"><?php echo esc_textarea( genesis_get_custom_field( '_genesis_scripts' ) ); ?></textarea></p>
	<?php

}

add_action( 'save_post', 'genesis_inpost_seo_save', 1, 2 );
/**
 * Save the SEO settings when we save a post or page.
 *
 * Some values get sanitized, the rest are pulled from identically named subkeys
 * in the $_POST['genesis_seo'] array.
 *
 * @since 0.1.3
 *
 * @uses genesis_save_custom_field() Perform checks and saves post meta / custom
 *                                   field data to a post or page.
 *
 * @param integer  $post_id Post ID.
 * @param stdClass $post    Post object.
 *
 * @return mixed Returns post id if permissions incorrect, null if doing autosave,
 *               ajax or future post, false if update or delete failed, and true
 *               on success.
 */
function genesis_inpost_seo_save( $post_id, $post ) {

	if ( ! isset( $_POST['genesis_seo'] ) )
		return;

	/** Merge user submitted options with fallback defaults */
	$data = wp_parse_args( $_POST['genesis_seo'], array(
		'_genesis_title'         => '',
		'_genesis_description'   => '',
		'_genesis_keywords'      => '',
		'_genesis_canonical_uri' => '',
		'redirect'               => '',
		'_genesis_noindex'       => 0,
		'_genesis_nofollow'      => 0,
		'_genesis_noarchive'     => 0,
		'_genesis_scripts'       => '',
	) );

	/** Sanitize the title, description, and tags */
	foreach ( (array) $data as $key => $value )
		if ( in_array( $key, array( '_genesis_title', '_genesis_description', '_genesis_keywords' ) ) )
			$data[ $key ] = esc_html( strip_tags( $value ) );

	/** Save custom field data */
	genesis_save_custom_fields( $data, 'genesis_inpost_seo_save', 'genesis_inpost_seo_nonce', $post, $post_id );

}

add_action( 'admin_menu', 'genesis_add_inpost_layout_box' );
/**
 * Register a new meta box to the post / page edit screen, so that the user can
 * set layout options on a per-post or per-page basis.
 *
 * @category Genesis
 * @package Admin
 * @subpackage Inpost-Metaboxes
 *
 * @since 0.2.2
 *
 * @see genesis_inpost_layout_box() Generates the content in the boxes
 *
 * @return null Returns null if Genesis layouts are not supported
 */
function genesis_add_inpost_layout_box() {

	if ( ! current_theme_supports( 'genesis-inpost-layouts' ) )
		return;

	foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
		if ( post_type_supports( $type, 'genesis-layouts' ) )
			add_meta_box( 'genesis_inpost_layout_box', __( 'Layout Settings', 'genesis' ), 'genesis_inpost_layout_box', $type, 'normal', 'high' );
	}

}

/**
 * Callback for in-post layout meta box.
 *
 * Echoes out HTML.
 *
 * @category Genesis
 * @package Admin
 * @subpackage Inpost-Metaboxes
 *
 * @since 0.2.2
 */
function genesis_inpost_layout_box() {

	wp_nonce_field( 'genesis_inpost_layout_save', 'genesis_inpost_layout_nonce' );

	$layout = genesis_get_custom_field( '_genesis_layout' );

	?>
	<div class="genesis-layout-selector">
		<p><input type="radio" name="genesis_layout[_genesis_layout]" class="default-layout" id="default-layout" value="" <?php checked( $layout, '' ); ?> /> <label class="default" for="default-layout"><?php printf( __( 'Default Layout set in <a href="%s">Theme Settings</a>', 'genesis' ), menu_page_url( 'genesis', 0 ) ); ?></label></p>

		<p><?php genesis_layout_selector( array( 'name' => 'genesis_layout[_genesis_layout]', 'selected' => $layout, 'type' => 'site' ) ); ?></p>
	</div>

	<br class="clear" />

	<p><label for="genesis_custom_body_class"><b><?php _e( 'Custom Body Class', 'genesis' ); ?></b></label></p>
	<p><input class="large-text" type="text" name="genesis_layout[_genesis_custom_body_class]" id="genesis_custom_body_class" value="<?php echo esc_attr( sanitize_html_class( genesis_get_custom_field( '_genesis_custom_body_class' ) ) ); ?>" /></p>

	<p><label for="genesis_custom_post_class"><b><?php _e( 'Custom Post Class', 'genesis' ); ?></b></label></p>
	<p><input class="large-text" type="text" name="genesis_layout[_genesis_custom_post_class]" id="genesis_custom_post_class" value="<?php echo esc_attr( sanitize_html_class( genesis_get_custom_field( '_genesis_custom_post_class' ) ) ); ?>" /></p>
	<?php

}

add_action( 'save_post', 'genesis_inpost_layout_save', 1, 2 );
/**
 * Saves the layout options when we save a post / page.
 *
 * Since there's no sanitizing of data, the values are pulled from identically
 * named keys in $_POST.
 *
 * @since 0.2.2
 *
 * @uses genesis_save_custom_field() Perform checks and saves post meta / custom
 *                                   field data to a post or page.
 *
 * @param integer  $post_id Post ID.
 * @param stdClass $post    Post object.
 *
 * @return mixed Returns post id if permissions incorrect, null if doing autosave,
 *               ajax or future post, false if update or delete failed, and true
 *               on success.
 *
 * @todo Sanitize classes
 */
function genesis_inpost_layout_save( $post_id, $post ) {

	if ( ! isset( $_POST['genesis_layout'] ) )
		return;

	$data = wp_parse_args( $_POST['genesis_layout'], array(
		'_genesis_layout'            => '',
		'_genesis_custom_body_class' => '',
		'_genesis_post_class'        => '',
	) );

	genesis_save_custom_fields( $data, 'genesis_inpost_layout_save', 'genesis_inpost_layout_nonce', $post, $post_id );

}

