<?php
/**
 * Gestion des Logs suivant le niveau d'erreur
 * @author alfo
 * @version 1.0
 */

class Log {
	
	/**
	 * Contructeur chargé de vérifier la présence du dossier, sinon -> Creation
	 * @return void
	 */
	public function __construct ( ) {
		if (!is_dir(DIR_LOG)) mkdir(DIR_LOG, 0777);
	}
	
	public static function INFO($msg) {
		error_log(USERIP.' - '.date('d/m/Y - H:i:s'). " : " .$msg. "\n",3,DIR_LOG.'/INFO.log');
	}
	
	public static function CRIT($msg) {
		error_log(USERIP.' - '.date('d/m/Y - H:i:s'). " : " .$msg. "\n",3,DIR_LOG.'/CRIT.log');
	}
}