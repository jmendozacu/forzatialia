<?php
/**
 * forzaitalia functions and definitions
 *
 * @package forzaitalia
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'forzaitalia_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function forzaitalia_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on forzaitalia, use a find and replace
	 * to change 'forzaitalia' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'forzaitalia', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'forzaitalia' ),
	) );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'forzaitalia_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );
}
endif; // forzaitalia_setup
add_action( 'after_setup_theme', 'forzaitalia_setup' );

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function forzaitalia_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'forzaitalia' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'forzaitalia_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function forzaitalia_scripts() {
	wp_enqueue_style( 'forzaitalia-style', get_stylesheet_uri() );

	wp_enqueue_script( 'forzaitalia-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'forzaitalia-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'forzaitalia_scripts' );

function wptuts_scripts_with_jquery()  
{  
    // Register the script like this for a theme:  
	wp_register_script( 'custom-script1', get_template_directory_uri() . '/js/jquery-1.7.1.min.js', array( 'jquery' ) );  
    wp_register_script( 'custom-script2', get_template_directory_uri() . '/js/jquery.hoverIntent.min.js', array( 'jquery' ) );  
    wp_register_script( 'custom-script3', get_template_directory_uri() . '/js/script.js', array( 'jquery' ) );  
  
    // For either a plugin or a theme, you can then enqueue the script:  
    wp_enqueue_script( 'custom-script1' );  
	wp_enqueue_script( 'custom-script2' );  
	wp_enqueue_script( 'custom-script3' );  
}  
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_with_jquery' ); 
/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
/*-----------------------------------------------------------------------[ theme setup function ] */
//if ! wpflex_setup
if ( ! function_exists( 'wpflex_setup' ) ) :

//wpflex_setup
function wpflex_setup() {

    /*-----------------------------------[ register the custom nav menus ] */

    register_nav_menus( array(
        'primary' => 'Primary Menu'
    ));

}

endif;