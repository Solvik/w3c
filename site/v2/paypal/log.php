<?php
/**
 * Gestion des Logs suivant le niveau d'erreur
 * @author alfo
 * @version 1.0
 */

class Log {
	
	/**
	 * Contructeur charg� de v�rifier la pr�sence du dossier, sinon -> Creation
	 * @return void
	 */
	public function __construct ( ) {
	}
	
	public static function PAYPAL($msg) {
		error_log(date('d/m/Y - H:i:s'). " : " .$msg. "\n",3,'paypal.log');
	}
}