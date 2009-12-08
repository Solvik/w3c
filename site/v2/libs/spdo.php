<?php

/**
 * Classe implémentant le singleton pour PDO
 * @author alfo
 */

class SPDO
{
	/**
	  * Instance de la classe PDO
	  */
	private $PDOInstance = null;
	 
	/**
	  * Instance de la classe SPDO
	  */
	private static $instance = null;
	
	/**
	  * Nombre de requêtes executées
	  */
	//private static $QueryCount = 0;
	
	/**
	  * Constructeur
	  */
	private function __construct()
	{
		$this->PDOInstance = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
	}
	 
	/**
	  * Crée et retourne l'objet SPDO
	  * @return PDO2 $instance
	  */
	public static function getInstance()
	{
		if(is_null(self::$instance))
		{
			self::$instance = new SPDO();
		}
		return self::$instance;
	}
	
	/**
	 * Permet de catcher les erreurs PDOException
	 */
	public function __call ($nom, $args)
    {
		$pdo = $this->PDOInstance;
		$return = $this->PDOInstance->{$nom}($args[0]);
		if($return)
			return $return;
		else {
			$erreur = $this->PDOInstance->errorInfo();
			ob_start();
			debug_print_backtrace();
			$trace = ob_get_contents();
			ob_end_clean(); 
			Log::CRIT($erreur[2]."\n".$trace);
			trigger_error('<strong>Erreur PDO :</strong> '.$erreur[2].'<pre>'.
			$trace.'</pre>', E_USER_ERROR);
		}
    }

}