<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'forzaita_forzaitalia_blog');

/** MySQL database username */
define('DB_USER', 'forzaita_forzaib');

/** MySQL database password */
define('DB_PASSWORD', 'e!]fV@R(M0_n');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'i@!fay{w!-qpK56r#=lFoTm+S(@FQM`3!Q/H{OkVdYOUcx_!S[3E&dPidN6bJ-l5');
define('SECURE_AUTH_KEY',  'E-9Lf(%-*fk>|_rMo@ E-+/+V)tPW%9Y}|W>lN*rCBu<jV25*M7dMH?kAcM&k2z&');
define('LOGGED_IN_KEY',    '0]H$5L5>K`,<ox0r-<OW>Ju|Z5f#`kE2RH`q,#pV{w*L9>+-rv5h^JOiJ2RH*#+i');
define('NONCE_KEY',        'G&C!1D4j{cE0,bWHE!g(^#Wyj4v2kLx0M~fi][J:f]b88(<GN2k{<E3-E7b|W$ig');
define('AUTH_SALT',        'cTj=+cE7*#Lk[snR]0@w-ZxB_53^Y9bx@j|yQ5hq8Ou=7Y:9d0TZ5U_@1SpzjI|K');
define('SECURE_AUTH_SALT', ')0+*i/4FZ->faQhh6r;RI)W?q%o5@G3o#$!<R]balOIU,J8>n=2649{(dgi_7WO=');
define('LOGGED_IN_SALT',   '?n.&iiiWW6rs1zHO%41lk fogzvOD$~EviMV~Rx0|M!n{~lXw2,*}<!}bU6SAV`[');
define('NONCE_SALT',       'aGs4K1[6G?nV/ZGMyt<+Q^AHG-<:+a.._&ZGgR,>F-|X<r|IIDo8Mr0|q_DO@+|z');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
