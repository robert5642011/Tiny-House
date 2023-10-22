<?php
define( 'WP_CACHE', true );



/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'paradise_tinyhouseperu_tyn');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'paradise_android');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', '=)f72v*Ti_*3');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'Cu0p2cSF&du94eXaB[G`6c}Bi>[LyWqp6~`a%,KlN$AwXNz7tT;{wiUw^vA1eLMb');
define('SECURE_AUTH_KEY', '!wwX~ K#fSW&.KE*M-ES!G.>Drd5Z&>!}N~i+cfu$mH*sd!BO1uop)bT]_poB! e');
define('LOGGED_IN_KEY', 'K!o6sF~oq:X{gc-bNQ&;f!Mb:2g/s Ddwn/ mln>6W7oJWL6yR:mGi#!)&;~rB$q');
define('NONCE_KEY', 'J 2{L!LT+V5b>K&P*eX18#Xadb)WO10LIW`sa_vA{98p.M.@e=Q!fLvf>|O^-JkS');
define('AUTH_SALT', '#Oiw(yHbY3U 82k-SsFMeC.LDydSld:wL:=K/u^Draf>v]J,f7otj1>G1+W{9MpZ');
define('SECURE_AUTH_SALT', '-q-5E(lFNLCsy?|x>:Hs#mGqPT;JbJD3WC#H_d3kKq*ugW7HnA@;DZv&0q;D|6?L');
define('LOGGED_IN_SALT', 'GDGHy+:{]#}i`<QuMs)qR;0`G1*YMPfs0SG>TZZ`-^WWYTLZo1ZM:k4HCRI+DQ{.');
define('NONCE_SALT', '<egOG[DL -dxxFMaf}6r%}{v_Zr,FN=,6JhniC~sYuDJ*|_TS[AZf%b&D46?b*a:');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'cor_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

