<?php

/**
 * OxyCast
 * Accès à la DataSource d'un user.
 * Classe implémentant le singleton pour PDO
 * @author alfo
 */

class UserDS
{
	/**
	  * Instance de la classe PDO
	  */
	private $PDOInstance = null;
	 
	/**
	  * Instance de la classe UserDS
	  */
	private static $instance = null;
	
	/**
	  * Constructeur
	  */
	private function __construct($userDB)
	{
		$this->PDOInstance = @new PDO('mysql:dbname='.$userDB.';host=localhost', SQL_USERNAME, SQL_PASSWORD);
	}
	 
	/**
	  * Crée et retourne l'objet UserDS
	  * @return PDO2 $instance
	  */
	public static function getInstance($userDB = 0)
	{
		if(is_null(self::$instance))
		{
			self::$instance = new UserDS($userDB);
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