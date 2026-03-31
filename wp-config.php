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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'MyN3wStrongP@ss!' );

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
define( 'AUTH_KEY',         '.8([72|xHph*9B*ebnq6w;3&asnP>aa$GlW8Zoo26Uy<:^ILi?z{1u0a5)dN6@^W' );
define( 'SECURE_AUTH_KEY',  'Q^=l(RP9f^kwYW,.AxJ*`~N%Jf$%tfm4wE@0V~[g7!k:|oLHFt[=P>M+CV.B{T$C' );
define( 'LOGGED_IN_KEY',    'G`Bmn7}b.oa{aHHrSX>:aP&FC(PHhr{ZDTL{`b]yLWX`4*)Kp=l2tpov7@z02>j$' );
define( 'NONCE_KEY',        'ipBK[*80P&3!~&5Uhk|JV2A/)(97!d{?d~#>:vAOUu?fZ8|:y-=`Tr(@(/s1<;%7' );
define( 'AUTH_SALT',        'i*d=4aDMx.5sTV9;z-4=>LmEh_{oM.yv>c]fQKW@vMt6wK-LnA&Q2t/x@q&cTQZ;' );
define( 'SECURE_AUTH_SALT', 'e7a,Dg{]N%@L`]ZaNd!_?v.}{<TUE44~|3NsM_37H%N~je],aRL`xPZ:JpSnga&R' );
define( 'LOGGED_IN_SALT',   'B8]0Tgn_WE3kX;9qPLkWznEq;%:}=5V`?{]OnOtc_Q[|tkLG1Pe,X${qpk2scONC' );
define( 'NONCE_SALT',       'a8K-4j&xkHL.i0eKh#N5VlMKlP]>gYjh>K`>Xu8P($;O_-Dbz0a7vOu*}rTVh .+' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
ini_set('display_errors','On');
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );



/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
