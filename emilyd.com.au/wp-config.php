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
define('DB_NAME', 'forzaita_emily');

/** MySQL database username */
define('DB_USER', 'forzaita_demily');

/** MySQL database password */
define('DB_PASSWORD', 't*d*5*.2LWZ]');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
define('FS_METHOD', 'direct');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'DpLW0NgyNDQ  H5zo<Y369z<thh=i:)/`id`Um9||eNQ)l-~^rR5dX,KwhvZWYol');
define('SECURE_AUTH_KEY',  'm;BqQiq&y6dgEwPwiv{iM*$nZ&dxk[`k6xTqW:+Q>[RIJE.AJ~8F* |(vtK3QR%[');
define('LOGGED_IN_KEY',    'eqpk|628u,~Wg->y0#amvjC5NUfb8?nfP8z}Ou|7!v5{1g@2Vc+gN$/!*-hRO&K{');
define('NONCE_KEY',        '@W~ArP5K=zamf/-F4?2Ms!`8.U835/@3f7@}1)CDNX?FMy_WokLf$VS}HFe{i@aR');
define('AUTH_SALT',        'ry&g~-myIAqSm;8SSz:%I@N!E/pZV!SysXwLgEX{SS]t.]@rgZ-+o]~NZdYia:Ih');
define('SECURE_AUTH_SALT', '^T9J+<aVM#+U--yoJGdmUx1}>Edy@ k[^AOr$.l8o{Nsh3l;T]rw%iM5HpCU-3Lp');
define('LOGGED_IN_SALT',   '<w*&;Q#oz|tYY!??I: 3oh4-tniBj[QKQR0Wn^8lzXw]L:k-*_<<#^y(nY`@,|!e');
define('NONCE_SALT',       'O4b(),<^W1/5[t`85)Ad<T++t~q6[{K[&PB!YdJH*2Dif,KR%PEj4g-C6=ViQ?--');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'fp_';

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
