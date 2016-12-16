<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_HOME', 'http://'.$_SERVER['HOST_NAME'].'/');
define('WP_SITEURL', 'http://'.$_SERVER['HOST_NAME'].'/');
define('DB_NAME', 'text_relipa');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '1111');

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

define('AUTH_KEY',         'At{s0l!4=x 7JC+XY?RUN~|j9:/7c7qilsrMmWC]BN#klF7= ^vz*x#%6:A(|r@=');
define('SECURE_AUTH_KEY',  '4%1YPF!o}/Lq6sG[gTWzg6hb>E;h+^NPJ^KSOCADu~G$3=XByScVx+F;h0{fG@])');
define('LOGGED_IN_KEY',    'yM1kLO%<J~IzL=J/T<m+3R6V/sgMy+x(u5?_*cKr-72z24](*G005RuB-phFl+R-');
define('NONCE_KEY',        'B^{o=_}#B~|7X{R BKOOrqf^lh0<1r-E|],x6KgkrV);23[>^NMJv3T!_|1(u{$C');
define('AUTH_SALT',        '{qi?}[GHC*1:q(+7=`%$XR-OU(g]$Y~&L9B%c?f=nd2)yB^1f;qZi.jP_9(`|54S');
define('SECURE_AUTH_SALT', 'hD&7<nr!)*l~3JL!| :VVA9tX~@r]Ar&$Stqm6f_6PZ-._v=>O`juTw}2PB#M:4c');
define('LOGGED_IN_SALT',   '&oB||+B{R_~+bU/^4u6::)(cci|#TP8y@jxwH,>2*@eBBm*:fQ^kx|PlWb7/K^>Y');
define('NONCE_SALT',       'XcIdenyT+Me@J=v+4Li#xWf,i|IpY}a;;d4=5:+<K-O(E]-W#Z%O]9>%W]ywVKL<');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
