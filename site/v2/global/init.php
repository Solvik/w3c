<?php

// Inclusion du fichier de configuration (qui définit des constantes)
include 'global/config.php';

// Utilisation et démarrage des sessions
session_start();

// Désactivation des guillemets magiques
ini_set('magic_quotes_runtime', 0);
date_default_timezone_set('Europe/Paris'); // Pour PHP6
//set_magic_quotes_runtime(0);

if (1 == get_magic_quotes_gpc())
{
	function remove_magic_quotes_gpc(&$value) {
	
		$value = stripslashes($value);
	}
	array_walk_recursive($_GET, 'remove_magic_quotes_gpc');
	array_walk_recursive($_POST, 'remove_magic_quotes_gpc');
	array_walk_recursive($_COOKIE, 'remove_magic_quotes_gpc');
}

if(DEV_MODE) error_reporting(E_ALL);
else error_reporting(0);

// Inclusion de Pdo2, potentiellement utile partout
include LIB.'spdo.php';
include LIB.'UserDS.php';

//include CHEMIN_LIB.'image.php'; Inutile ATM
include LIB.'log.php';
	
// Initalisation du système de Log
$log = new Log();

function __autoload($name) {
    if (is_file(MODEL.$name.'.class.php')) require_once MODEL.$name.'.class.php';
}

// Vérifie si l'utilisateur est connecté
function is_online() {
	return !empty($_SESSION['login']);
}

// Vérifie si l'utilisateur est un administrateur
function is_admin() {
	return !empty($_SESSION['admin']);
}

// Retourne le hash d'un mot de passe
function generate_hash ($pass)
{
	return base64_encode(hash('ripemd160', pack('H*', sha1(trim('wdFivuW'.$pass.':O a big salt !')))));
}

// checkdnsrr() pour Windows
function win_checkdnsrr($host, $type='MX') {
    if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') { return; }
    if (empty($host)) { return; }
    $types=array('A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY');
    if (!in_array($type,$types)) {
        user_error("checkdnsrr() Type '$type' not supported", E_USER_WARNING);
        return;
    }
    @exec('nslookup -type='.$type.' '.escapeshellcmd($host), $output);
    foreach($output as $line){
        if (preg_match('/^'.$host.'/',$line)) { return true; }
    }
}

// Pour les autres systèmes
if (!function_exists('checkdnsrr')) {
    function checkdnsrr($host, $type='MX') {
        return win_checkdnsrr($host, $type);
    }
}

// Vérifie une addresse email
function validation_email($email)
{
	$exp = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";

	if(eregi($exp,$email)){
		if(checkdnsrr(array_pop(explode("@",$email)),"MX")){
			return true;
		} else {
			return false;
		}
	} else return false;
}

// Vérifie si l'IP est blacklistée
function is_blacklisted($ip) {
    $dnsbl_check=array("bl.spamcop.net",
                       "relays.osirusoft.com",
                       "list.dsbl.org",
                       "sbl.spamhaus.org");
    if ($ip) {
       $quads=explode(".",$ip);
        $rip=$quads[3].".".$quads[2].".".$quads[1].".".$quads[0];
        for ($i=0; $i<count($dnsbl_check); $i++) {
            if (checkdnsrr($rip.".".$dnsbl_check[$i],"A")) {
                $listed.=$dnsbl_check[$i]." ";
            }
         }
       if ($listed) { return $listed; } else { return FALSE; }
    }
}

// Formate une heure:minute
function time_format ($heure, $minute)
{
	$output = null;
	
	if($heure < 10) $output .= "0$heure:";
	else $output .= "$heure:";
	
	if($minute < 10) $output .= "0$minute";
	else $output .= "$minute";
	
	return $output;
}

// Retourne le jour en texte
function getJour ($jour)
{
	$jours = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	return $jours[$jour-1];
}

function nb_with_zero($nb)
{
  if ($nb < 10)
    {
      $toto = "0".$nb;
      return ($toto);
    }
  return ($nb);
}
