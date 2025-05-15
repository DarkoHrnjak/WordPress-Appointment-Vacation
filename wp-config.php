<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'password' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'Z)@B*buBo/6V5i7JP37+K;v_+oP {%Nx=;E{ja:_|n@22eBT}o86N8r]&WHhay;`' );
define( 'SECURE_AUTH_KEY',  'dhotWg-irXG*F,Ia6,,$0(B,dB<Oufp}ff~n<+`qULg{@}$^MfRrD!X_IN!d+/rZ' );
define( 'LOGGED_IN_KEY',    '7miwk!BT!pI<,4] i`Kn~^$ L7UgI@U$T>6ez@lL nAXfd:k>wTsmhb)skB,$jGY' );
define( 'NONCE_KEY',        'uqm>gB!,NhRAU>Lu /A;F^?R(F6jpbRc:QmUd)SjMZhcv>Hm-9Qry? 5=V{?4n(n' );
define( 'AUTH_SALT',        'nY5|}6#~uVz@8aF! duCA(DV/ *{;&n:07DtM23AbkAB)OFXh}f!K]>V.$g_UlFZ' );
define( 'SECURE_AUTH_SALT', '#aAR4DO*?MoP9O$%YMMwC/?(^;/Lz`Tla75OE?V=F+dpBPN<1kA2J._r}bN0qkd0' );
define( 'LOGGED_IN_SALT',   'seswm#hQR3l5Ap7KpK)pYGII#s5-}IU`s7fQIN&NQEm^Gc%fZFDQ1jCEk]^o!M~3' );
define( 'NONCE_SALT',       'XWHR:5on@qyakz[|Cvj]N;X^RvQkf>E=u-1kc8kDhNEhxeNccF3M-_-`J@oI{chl' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
