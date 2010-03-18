<?php
/**
 * OxyCast
 * @desc Gestion complète des membres
 * @author alfo
 * @version 1.0
 */

class Member
{
	protected $id;
	protected $login;
	protected $hash;
	protected $nom;
	protected $prenom;
	protected $mail;
	protected $adresse;
	protected $cp;
	protected $ville;
	protected $ip;
	protected $dateInscription;
	protected $dateUpdate;
	protected $isAdmin;
	protected $cle_conf;
	protected $recover;
	protected $blog;
	
	const NOUVEAU = 1;
	const EXISTANT = 2;
	
	/**
	 * @desc Constructeur de la classe Member.
	 * @param $type int Valeur indiquant la création ou l'utilisation d'un compte existant.
	 * @param $login,etc string Les infos du compte requises pour une création de compte.
	 * @return void
	 */
	 
	 //////// CLASSE NON FINIE, SUJETTE A BEAUCOUP DE MODIFICATIONS ! ////////////
	public function __construct ($type, $id, $login = null, $password = null, $nom = null, $prenom = null, $mail = null, $adresse = null, $cp = null, $ville = null, $clef = null)
	{
		if($type === self::EXISTANT)
		{
			$pdo = SPDO::getInstance();
			if(is_string($id)) {
				$infos = $pdo->query('SELECT * FROM clients WHERE login = \'' . trim($id) . '\'')
							  ->fetch(PDO::FETCH_OBJ);
			} else {
				$infos = $pdo->query('SELECT * FROM clients WHERE id = \'' . intval($id) . '\'')
							  ->fetch(PDO::FETCH_OBJ);
			}
			
			$this->id 				= 	$infos->id;
			$this->login 			= 	$infos->login;
			$this->hash 			= 	$infos->password;
			$this->nom 				= 	$infos->nom;
			$this->prenom 			= 	$infos->prenom;
			$this->mail 			= 	$infos->mail;
			$this->adresse 			= 	$infos->adresse;
			$this->cp 				= 	$infos->cp;
			$this->ville 			= 	$infos->ville;
			$this->ip 				= 	$infos->ip;
			$this->dateInscription 	= 	$infos->dateInscription;
			$this->dateUpdate 		= 	$infos->dateUpdate;
			$this->isAdmin		 	= 	$infos->admin;
			$this->cle_conf		 	= 	$infos->cle_conf;
			$this->recover		 	= 	$infos->recover;
			$this->blog			 	= 	$infos->blog;
		}
		elseif($type === self::NOUVEAU)
		{
			$pdo = SPDO::getInstance();
			
			$this->login 			= 	htmlspecialchars(trim($login));
			$this->hash			= 	generate_hash($password);
			$this->nom			= 	htmlspecialchars(utf8_decode($nom));
			$this->prenom	 		= 	htmlspecialchars(utf8_decode($prenom));
			$this->mail		 	= 	htmlspecialchars($mail);
			$this->adresse	 		= 	htmlspecialchars(utf8_decode($adresse));
			$this->cp		 	= 	htmlspecialchars($cp);
			$this->ville	 		= 	htmlspecialchars(utf8_decode($ville));
			$this->ip 			= 	USERIP;
			$this->dateInscription 		= 	time();
			$this->dateUpdate 		= 	time();
			$this->isAdmin		 	= 	0;
			$this->cle_conf		 	= 	$clef;
			$this->recover		 	= 	null;
			$this->blog			= 	null;
			
			$requete = $pdo->prepare("INSERT INTO clients SET
								id = '',
								login = :login,
								password = :password,
								nom = :nom,
								prenom = :prenom,
								mail = :mail,
								adresse = :adresse,
								cp = :cp,
								ville = :ville,
								ip = :ip,
								dateInscription = :dateInscription,
								dateUpdate = :dateUpdate,
								admin = :admin,
								cle_conf = :cle_conf");
			$requete->bindValue(':login', 			$this->login);
			$requete->bindValue(':password', 		$this->hash);
			$requete->bindValue(':nom', 			$this->nom);
			$requete->bindValue(':prenom', 			$this->prenom);
			$requete->bindValue(':mail', 			$this->mail);
			$requete->bindValue(':adresse',			$this->adresse);
			$requete->bindValue(':cp', 			$this->cp);
			$requete->bindValue(':ville', 			$this->ville);
			$requete->bindValue(':ip', 			$this->ip);
			$requete->bindValue(':dateInscription', 		$this->dateInscription);
			$requete->bindValue(':dateUpdate', 		$this->dateUpdate);
			$requete->bindValue(':admin', 			$this->isAdmin);
			$requete->bindValue(':cle_conf', 		$this->cle_conf);

			$requete->execute();

			$requete = $pdo->query('SELECT id FROM clients WHERE login = \'' . $this->login . '\'')->fetchColumn();
			$this->id = $requete;
		}
	}
	
	/**
     * @desc Méthode chargée de retourner la valeur de l'attribut en paramètre.
     * @param $attribut string Le nom de l'attribut.
     * @return string|int
     */
	public function __get ($attribut)
	{
		if(isset($this->{$attribut}))
			return $this->{$attribut};
		else return false;
	}
	
	/**
     * @desc Méthode chargée de changer la valeur de l'attribut en paramètre.
     * @param $attribut string Le nom de l'attribut.
     * @param $valeur int|string|bool La nouvelle valeur
     * @return void
     */
	public function __set ($attribut, $valeur)
	{
		if(isset($this->{$attribut}))
			$this->{$attribut} = $valeur;
	}
	
	/**
	 * @desc Retourne true si l'utilisateur possède un blog et false s'il n'en a pas.
	 * @return bool
	 */
	public function hasBlog ()
	{
		if($this->blog === '') return false;
		else return true;
	}
	
	/**
	 * @desc Retourne l'objet Stream du compte ou false s'il n'a pas de stream.
	 * @return bool
	 */
	public function getStream ()
	{
		return new Stream($this->id);
	}
	
	/**
	 * @desc Sauvegarde toutes les données relatives au compte
	 * @return void
	 */
	public function save ()
	{
		$pdo = SPDO::getInstance();
		
		$requete = $pdo->prepare("UPDATE clients SET
								login = :login,
								password = :password,
								nom = :nom,
								prenom = :prenom,
								mail = :mail,
								cp = :cp,
								ville = :ville,
								ip = :ip,
								dateInscription = :dateInscription,
								dateUpdate = :dateUpdate,
								admin = :admin,
								recover = :recover,
								cle_conf = :cle_conf,
								blog = :blog WHERE id = :id");
			$requete->bindValue(':login', 			$this->login);
			$requete->bindValue(':password', 		$this->hash);
			$requete->bindValue(':nom', 			$this->nom);
			$requete->bindValue(':prenom', 			$this->prenom);
			$requete->bindValue(':mail', 			$this->mail);
			$requete->bindValue(':cp', 				$this->cp);
			$requete->bindValue(':ville', 			$this->ville);
			$requete->bindValue(':ip', 				$this->ip);
			$requete->bindValue(':dateInscription', $this->dateInscription);
			$requete->bindValue(':dateUpdate', 		$this->dateUpdate);
			$requete->bindValue(':admin', 			$this->isAdmin);
			$requete->bindValue(':recover', 		$this->recover);
			$requete->bindValue(':cle_conf', 		$this->cle_conf);
			$requete->bindValue(':blog', 			$this->blog);
			$requete->bindValue(':id', 				$this->id);

			$requete->execute();
	}
	
	/**
	 * @desc Supprime le compte
	 * @return bool
	 */
	public function delete ()
	{
		$requete = $this->pdo->query('DELETE FROM clients WHERE id = \'' . $this->id . '\'');
		if ($requete)
			return true;
		return false;
	}
}
