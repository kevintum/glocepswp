<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'R@]0XU9:;b AcT:hj:(klfQUJR0kxU-$v!lJt,VqZ/ [^<D<T_dYqGz#1lFrS]:M' );
define( 'SECURE_AUTH_KEY',   '?QdP-k8_$vjG-nOR_$QQy<?;*/``]M/uAk950B:,jQvwo8K9-qDP /)xGW,o4cP?' );
define( 'LOGGED_IN_KEY',     'DyZVcUIy~B$l- o!ULRYcBP7UK-WLzP3M(GWz+$cB btQnE3J2h]P:k!?Mkok!yl' );
define( 'NONCE_KEY',         '19a u#b$r53Y^C .;8L#E0itgL:8]2Zb@xN!:r7r&^2xZ2H7 Kat8i{Pll?wi<NJ' );
define( 'AUTH_SALT',         'kbh~A W^qr:_&9ve;TPD#H:MZUrm(yv`-s5>E)y8DKnn_<@[ih-83m47YWq8^q!2' );
define( 'SECURE_AUTH_SALT',  'UL|:V[Qx#rlMhiU8B[=6{8fty0:}yB6quidI))hGBBiJl#ptfIa|tvnIAY5)qKO(' );
define( 'LOGGED_IN_SALT',    '6#zuSq6vDK|IN*K%01MK`_=*.R2+cRc~+McS8:J_aGvbAwm?eUkTB~TcE)^E#XmQ' );
define( 'NONCE_SALT',        '6~$qy<&wN+A$Ze.[B|(gT]w&lE~=+0EPy<K;NH(pr3~0.2T3Lb-$OWNi@,Y/#5Sw' );
define( 'WP_CACHE_KEY_SALT', 'rC[~(0JOjZu(Kd4!mAo7Oy>80I$/OCWaW:pYSgW[O.jq,mQq1Z2<jbEh%Ys;;ozX' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
