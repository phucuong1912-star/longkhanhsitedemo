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
define( 'DB_NAME', 'longkhanhsites' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '1234' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

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
define( 'AUTH_KEY', 'cbh*4}J6ENWTT:7y[tj%nkm#/K7XMR2yL/4;6:g6gtk}l>Sw.99H**@e71PK)^:U' );
define( 'SECURE_AUTH_KEY', '8tWxX^Y)tw&:<P4s5_gZAhpli5^@&>B.@/!BP3(QAah;aH/?cRHDB{GS:Jz9I%Jd' );
define( 'LOGGED_IN_KEY', '_NO%fd7+r_6z:M]2bQKXHJF{{J4)g%L-%:nF>AMx?6,D*&:Z[RgP#K/{uXU.,%jD' );
define( 'NONCE_KEY', 'g6E5(v-2&>%qm@hW,y:qG-kP.e<Y=zv:{mb7,pCDnCpDoa5|UG-if%#Sq5j_,F7}' );
define( 'AUTH_SALT', ':T!L<j5*M)D_QuF9R||VV5QO2fif<o126%kk>=jzm3zW}[G;(@rR:}|*W>/T#Icy' );
define( 'SECURE_AUTH_SALT', '3AJW|i>*<v)hWYiI*k_}(B#nZkRhcFSw1ev(|/hQsHodvM:Gw([0i8JM17BR%O4/' );
define( 'LOGGED_IN_SALT', '<R.snDAw]^?oJwj[OsL{o/ecDbu&FZ~?4E<9|&jFIO+1g}W:?i?4JQnh|Ql1~WwQ' );
define( 'NONCE_SALT', 'xb75S@0WTlY[U>(YPkx.8=1fC7<ZL{Y.2(jDr&o5p+#R@2VtxdZ8|f4Dtp9~)ga;' );

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
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

// Keep the WordPress core version selected for this local site.
define( 'WP_AUTO_UPDATE_CORE', false );


define( 'WP_HOME', 'http://localhost:180/longkhanhsite' );
define( 'WP_SITEURL', 'http://localhost:180/longkhanhsite' );
/* Add any custom values between this line and the "stop editing" line. */


define( 'DUPLICATOR_AUTH_KEY', '8kUF!43J<7R{!VlY~8#Wg8|cp4`2Z,CjhSdIMJ~;fIoiI^PB3abEr7 pB=u+cP9i' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
