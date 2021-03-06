<?php
/**
 * Controls layout structures.
 *
 * @category   Genesis
 * @package    Structure
 * @subpackage Layout
 * @author     StudioPress
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://www.studiopress.com/themes/genesis
 */

add_filter( 'content_width', 'genesis_content_width', 10, 3 );
/**
 * Filters the content width based on the user selected layout.
 *
 * @since 1.6.0
 *
 * @uses genesis_site_layout() Gets the site layout for current context
 *
 * @param integer $default Default width
 * @param integer $small Small width
 * @param integer $large Large width
 * @return integer Content width
 */
function genesis_content_width( $default, $small, $large ) {

	switch ( genesis_site_layout( 0 ) ) {
		case 'full-width-content':
			$width = $large;
			break;
		case 'content-sidebar-sidebar':
		case 'sidebar-content-sidebar':
		case 'sidebar-sidebar-content':
			$width = $small;
			break;
		default:
			$width = $default;
	}

	return $width;

}

add_filter( 'body_class', 'genesis_custom_body_class', 15 );
/**
 * Adds custom field body class(es) to the body classes.
 *
 * It accepts values from a per-post / page custom field, and only outputs when
 * viewing a singular page.
 *
 * @since 1.4.0
 *
 * @uses genesis_get_custom_field() Get custom field value
 *
 * @param array $classes Existing classes
 * @return array Amended classes
 */
function genesis_custom_body_class( array $classes ) {

	$new_class = is_singular() ? genesis_get_custom_field( '_genesis_custom_body_class' ) : null;

	if ( $new_class )
		$classes[] = esc_attr( $new_class );

	return $classes;

}

add_filter( 'body_class', 'genesis_header_body_classes' );
/**
 * Adds header-* classes to the body class.
 *
 * We can use pseudo-variables in our CSS file, which helps us achieve multiple
 * header layouts with minimal code.
 *
 * @since 0.2.2
 *
 * @uses genesis_get_option() Get theme setting value
 *
 * @param array $classes Existing classes
 * @return array Amended classes
 */
function genesis_header_body_classes( array $classes ) {

	if ( current_theme_supports( 'custom-header' ) ) {
		if ( get_theme_support( 'custom-header', 'default-text-color' ) != get_header_textcolor() || get_theme_support( 'custom-header', 'default-image' ) != get_header_image() )
			$classes[] = 'custom-header';
	}

	if ( 'image' == genesis_get_option( 'blog_title' ) || ( get_header_image() && ! display_header_text() ) )
		$classes[] = 'header-image';

	if ( ! is_active_sidebar( 'header-right' ) && ! has_action( 'genesis_header_right' ) )
		$classes[] = 'header-full-width';

	return $classes;

}

add_filter( 'body_class', 'genesis_layout_body_classes' );
/**
 * Adds site layout classes to the body classes.
 *
 * We can use pseudo-variables in our CSS file, which helps us achieve multiple
 * site layouts with minimal code.
 *
 * @since 0.2.2
 *
 * @uses genesis_site_layout() Returns the site layout for different contexts
 *
 * @param array $classes Existing classes
 * @return array Amended classes
 */
function genesis_layout_body_classes( array $classes ) {

	$site_layout = genesis_site_layout();

	if ( $site_layout )
		$classes[] = $site_layout;

	return $classes;

}

add_filter( 'body_class', 'genesis_style_selector_body_classes' );
/**
 * Adds style selector classes to the body classes.
 *
 * Enables style selector support in child themes, which helps us achieve
 * multiple site styles with minimal code.
 *
 * @since 1.8.0
 *
 * @uses genesis_get_option() Get theme setting value
 *
 * @param array $classes Existing classes
 * @return array Amended classes
 */
function genesis_style_selector_body_classes( array $classes ) {

	$current = genesis_get_option( 'style_selection' );

	if ( $current )
		$classes[] = esc_attr( sanitize_html_class( $current ) );

	return $classes;

}

add_filter( 'body_class', 'genesis_cpt_archive_body_class', 15 );
/**
 * Adds a custom class to the custom post type archive body classes.
 *
 * It accepts a value from the archive settings page.
 *
 * @since 2.0.0
 *
 * @uses genesis_has_post_type_archive_support()
 * @uses genesis_get_cpt_option() Get CPT Archive setting.
 *
 * @param array $classes Existing classes.
 *
 * @return array Amended classes.
 */
function genesis_cpt_archive_body_class( array $classes ) {

	if ( ! is_post_type_archive() || ! genesis_has_post_type_archive_support() )
		return $classes;

	$new_class = genesis_get_cpt_option( 'body_class' );

	if ( $new_class )
		$classes[] = esc_attr( sanitize_html_class( $new_class ) );

	return $classes;

}

add_action( 'genesis_after_content', 'genesis_get_sidebar' );
/**
 * Outputs the sidebar.php file if layout allows for it.
 *
 * @since 0.2.0
 *
 * @uses genesis_site_layout() Returns the site layout for different contexts
 */
function genesis_get_sidebar() {

	$site_layout = genesis_site_layout();

	/** Don't load sidebar on pages that don't need it */
	if ( $site_layout == 'full-width-content' )
		return;

	get_sidebar();

}

add_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt' );
/**
 * Outputs the sidebar_alt.php file if layout allows for it.
 *
 * @since 0.2.0
 *
 * @uses genesis_site_layout() Returns the site layout for different contexts
 */
function genesis_get_sidebar_alt() {

	$site_layout = genesis_site_layout();

	/** Don't load sidebar-alt on pages that don't need it */
	if ( in_array( $site_layout, array( 'content-sidebar', 'sidebar-content', 'full-width-content' ) ) )
		return;

	get_sidebar( 'alt' );

}
