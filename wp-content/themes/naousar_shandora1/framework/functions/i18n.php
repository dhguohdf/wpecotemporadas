<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * BON i18n
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Fuctions
 *
 *
*/ 

function bon_is_textdomain_loaded( $domain ) {
	global $bon;

	return ( isset( $bon->textdomain_loaded[ $domain ] ) && true === $bon->textdomain_loaded[ $domain ] ) ? true : false;
}

/**
 * Loads the framework's translation files.  The function first checks if the parent theme or child theme 
 * has the translation files housed in their '/languages' folder.  If not, it sets the translation file the the 
 * framework '/languages' folder.
 *
 * @since 1.0
 * @access private
 * @uses load_textdomain() Loads an MO file into the domain for the framework.
 * @param string $domain The name of the framework's textdomain.
 * @return true|false Whether the MO file was loaded.
 */
function bon_load_framework_textdomain( $domain ) {

	/* Get the WordPress installation's locale set by the user. */
	$locale = get_locale();

	/* Check if the mofile is located in parent/child theme /languages folder. */
	$mofile = locate_template( array( "languages/{$domain}-{$locale}.mo" ) );

	/* If no mofile was found in the parent/child theme, set it to the framework's mofile. */
	if ( empty( $mofile ) )
		$mofile = trailingslashit( BON_LANGUAGES ) . "{$domain}-{$locale}.mo";

	return load_textdomain( $domain, $mofile );
}


/**
 * Gets the parent theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * parent theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since 1.0
 * @access private
 * @uses get_template() Defines the theme textdomain based on the template directory.
 * @global object $bon The global bon object.
 * @return string $bon->textdomain The textdomain of the theme.
 */
function bon_get_parent_textdomain() {
	global $bon;

	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $bon->parent_textdomain ) )
		$bon->parent_textdomain = sanitize_key( apply_filters( bon_get_prefix() . 'parent_textdomain', get_template() ) );

	/* Return the expected textdomain of the parent theme. */
	return $bon->parent_textdomain;
}

/**
 * Gets the child theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * child theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since 1.0
 * @access private
 * @uses get_stylesheet() Defines the child theme textdomain based on the stylesheet directory.
 * @global object $bon The global Hybrid object.
 * @return string $bon->child_theme_textdomain The textdomain of the child theme.
 */
function bon_get_child_textdomain() {
	global $bon;

	/* If a child theme isn't active, return an empty string. */
	if ( !is_child_theme() )
		return '';

	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $bon->child_textdomain ) )
		$bon->child_textdomain = sanitize_key( apply_filters( bon_get_prefix() . 'child_textdomain', get_stylesheet() ) );

	/* Return the expected textdomain of the child theme. */
	return $bon->child_textdomain;
}

/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name 
 * of the mofile for translations.  This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 *
 * @since 1.0
 * @access private
 * @param string $mofile File name of the .mo file.
 * @param string $domain The textdomain currently being filtered.
 * @return $mofile
 */
function bon_load_textdomain_mofile( $mofile, $domain ) {

	/* If the $domain is for the parent or child theme, search for a $domain-$locale.mo file. */
	if ( $domain == bon_get_parent_textdomain() || $domain == bon_get_child_textdomain() ) {

		/* Check for a $domain-$locale.mo file in the parent and child theme root and /languages folder. */
		$locale = get_locale();
		$locate_mofile = locate_template( array( "languages/{$domain}-{$locale}.mo", "{$domain}-{$locale}.mo" ) );

		/* If a mofile was found based on the given format, set $mofile to that file name. */
		if ( !empty( $locate_mofile ) )
			$mofile = $locate_mofile;
	}

	/* Return the $mofile string. */
	return $mofile;
}

/**
 * Filters 'gettext' to change the translations used for the 'bon' textdomain.  This filter makes it possible 
 * for the theme's MO file to translate the framework's text strings.
 *
 * @since 1.0
 * @access private
 * @param string $translated The translated text.
 * @param string $text The original, untranslated text.
 * @param string $domain The textdomain for the text.
 * @return string $translated
 */
function bon_gettext( $translated, $text, $domain ) {

	/* Check if 'bon' is the current textdomain, there's no mofile for it, and the theme has a mofile. */
	if ( 'bon' == $domain && !bon_is_textdomain_loaded( 'bon' ) && bon_is_textdomain_loaded( bon_get_parent_textdomain() ) ) {

		/* Get the translations for the theme. */
		$translations = &get_translations_for_domain( bon_get_parent_textdomain() );

		/* Translate the text using the theme's translation. */
		$translated = $translations->translate( $text );
	}

	return $translated;
}

/**
 * Filters 'gettext' to change the translations used for the each of the extensions' textdomains.  This filter 
 * makes it possible for the theme's MO file to translate the framework's extensions.
 *
 * @since 1.0
 * @access private
 * @param string $translated The translated text.
 * @param string $text The original, untranslated text.
 * @param string $domain The textdomain for the text.
 * @return string $translated
 */
function bon_extensions_gettext( $translated, $text, $domain ) {

	/* Check if the current textdomain matches one of the framework extensions. */
	if ( in_array( $domain, array( 'breadcrumb-trail', 'custom-field-series', 'post-stylesheets', 'theme-layouts' ) ) ) {

		/* If the theme supports the extension, switch the translations. */
		if ( current_theme_supports( $domain ) ) {

			/* If the framework mofile is loaded, use its translations. */
			if ( bon_is_textdomain_loaded( 'bon' ) )
				$translations = &get_translations_for_domain( 'bon' );

			/* If the theme mofile is loaded, use its translations. */
			elseif ( bon_is_textdomain_loaded( bon_get_parent_textdomain() ) )
				$translations = &get_translations_for_domain( bon_get_parent_textdomain() );

			/* If translations were found, translate the text. */
			if ( !empty( $translations ) )
				$translated = $translations->translate( $text );
		}
	}

	return $translated;
}

?>