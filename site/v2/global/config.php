<?php

// Identifiants pour la base de données. Nécessaires a PDO2.
define('SQL_DSN',      'mysql:dbname=oxycast;host=localhost');
define('SQL_USERNAME', 'oxycast');
define('SQL_PASSWORD', 'ene4UzAADHQ8Juxm');


// Chemins à utiliser pour accéder aux vues/modeles/librairies
$module = empty($module) ? !empty($_GET['module']) ? $_GET['module'] : 'index' : $module;
define('VIEW',     'views/'.$module.'/');
define('MODEL',  'models/');
define('LIB',     'libs/');

define('USERIP',		 getenv("REDIRECT_REMOTE_USER"));

define('DIR_LOG',        'log');
define('INFO_LOG_FILE',  'info.log');
define('ERR_LOG_FILE',   'err.log');

define('NON_CONNECTE',   'views/non_connecte.html');
define('DEV_MODE',   true);

// PayPal Configuration //
// Test Email : oxysel_1254055608_biz@gmail.com
// Prod Email : paypal@oxyradio.net ??? à confirmer
define('PAYPAL_RECEIVER_EMAIL',   'paypal@oxyradio.net');
define('PAYPAL_TEST_MODE',  	  false);

// Configuration du moteur de Blogs //
define('BLOG_BASE_URL',				'http://blog.oxycast.net/');
define('BLOG_SCRIPT_URL',			'../blog/minisite.class.php');

// Définition des User/Pass pour le script d'update distant                                                                                  
define('UPDATE_USER',                           'oxycast');
define('UPDATE_PASS',                           'fhrximo5');

