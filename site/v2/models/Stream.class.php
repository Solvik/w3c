<?php
/**
 * OxyCast
 * @desc Classe permettant de gérer le Stream d'un utilisateur
 * @author alfo
 * @version 1.0
 */

class Stream
{
	protected $id;
	protected $offerId;
	protected $offre;
	protected $clientId;
	protected $status;
	protected $nom;
	protected $description;
	protected $genre;
	protected $mountpoint;
	protected $url;
	protected $password;
	protected $port;
	protected $dateDebut;
	protected $dateFin;
	protected $ip_serveur;
	protected $format_live;
	protected $format_output;
	protected $nb_jingles;
	protected $start_before;
	protected $skip_blank_sec;
	protected $skip_blank_db;
	protected $skip_blank_mail;
	
	/**
	 * @desc Constructeur
	 * @param $clientId int l'ID du compte
	 * @return bool
	 */
	public function __construct ($clientId)
	{
		if($clientId == 0) return true;
		
		$pdo = SPDO::getInstance();
		
		$infos = $pdo->query('SELECT * FROM streams WHERE clientId = ' . intval($clientId))
					 ->fetch(PDO::FETCH_OBJ);
		
		if($infos === false) return false;
		
		$this->id				= $infos->id;
		$this->offerId			= $infos->offerId;
		if(class_exists('Offre')) $this->offre			= @new Offre($infos->offerId);
		$this->clientId			= $infos->clientId;
		$this->status			= $infos->status;
		$this->nom			= $infos->nom;
		$this->description		= $infos->description;
		$this->genre			= $infos->genre;
		$this->mountpoint		= $infos->mountpoint;
		$this->url			= $infos->url;
		$this->password			= $infos->password;
		$this->port			= $infos->port;
		$this->dateDebut		= $infos->dateDebut;
		$this->dateFin			= $infos->dateFin;
		$this->ip_serveur		= $infos->ip_serveur;
		$this->format_live		= $infos->format_live;
		$this->format_output		= $infos->format_output;
		$this->nb_jingles		= $infos->nb_jingles;
		$this->start_before		= $infos->start_before;
		$this->skip_blank_sec		= $infos->skip_blank_sec;
		$this->skip_blank_db		= $infos->skip_blank_db;
		$this->skip_blank_mail		= $infos->skip_blank_mail;
		
		return true;
	}
	
	/**
	 * @desc Retourne true si le stream est vailde, false s'il ne l'es pas
	 * @return bool
	 */
	public function hasStream ()
	{
		if($this->id == null) return false;
		else return true;
	}
	
	/**
	 * @desc Permet de créer un stream
	 * @params ...
	 * @return void
	 */
	public function create ($clientId, $offerId, $status, $nom, $description, $genre, $mountpoint, $url, $password)
	{
		$pdo = SPDO::getInstance();
		
		$id = $pdo->query('SELECT id FROM streams ORDER BY id DESC')->fetchColumn();
		
		$this->id 			= (int) $id + 1;
		$this->offerId			= (int) $offerId;
		$this->offre			= @new Offre($offerId);
		$this->clientId			= (int) $clientId;
		$this->status			= $status;
		$this->nom			= htmlspecialchars($nom);
		$this->description		= htmlspecialchars($description);
		$this->genre			= htmlspecialchars($genre);
		$this->mountpoint		= htmlspecialchars($mountpoint);
		$this->url			= htmlspecialchars($url);
		$this->password			= htmlspecialchars($password);
		$this->port			= 9000 + $this->id;
		$this->ip_serveur		= '88.191.114.8';
		$this->format_live		= 'mp3';
		$this->format_output		= 'mp3';
		$this->nb_jingles		= 10;
		$this->start_before		= 3;
		
		
		
		$requete = $pdo->prepare("INSERT INTO streams SET
								id = :id,
								offerId = :offerId,
								clientId = :clientId,
								status = :status,
								nom = :nom,
								description = :description,
								genre = :genre,
								mountpoint = :mountpoint,
								url = :url,
								password = :password,
								port = :port,
								ip_serveur = :ip_serveur,
								format_live = :format_live,
								format_output = :format_output,
								nb_jingles = :nb_jingles,
								start_before = :start_before");
		$requete->bindValue(':id', 				$this->id);
		$requete->bindValue(':offerId', 		$this->offerId);
		$requete->bindValue(':clientId', 		$this->clientId);
		$requete->bindValue(':status', 			$this->status);
		$requete->bindValue(':nom', 			$this->nom);
		$requete->bindValue(':description', 	$this->description);
		$requete->bindValue(':genre', 			$this->genre);
		$requete->bindValue(':mountpoint', 		$this->mountpoint);
		$requete->bindValue(':url',				$this->url);
		$requete->bindValue(':password',		$this->password);
		$requete->bindValue(':port', 			$this->port);
		$requete->bindValue(':ip_serveur', 		$this->ip_serveur);
		$requete->bindValue(':format_live', 	$this->format_live);
		$requete->bindValue(':format_output', 	$this->format_output);
		$requete->bindValue(':nb_jingles', 	$this->nb_jingles);
		$requete->bindValue(':start_before', 	$this->start_before);
				
		$requete->execute();
	}
	
	/**
	 * @desc Retourne le nombre de stream portant ce nom [et tout sauf $uservalue]
	 * @param $string string Le nom du stream
	 * @param $uservalue string La valeur actuelle de l'utilisateur
	 * @return int
	 */
	public static function exist ($nom, $valeur, $uservalue = null)
	{
		return (int) SPDO::getInstance()->query('SELECT COUNT(*) AS count FROM streams WHERE '.addslashes(trim($nom)).' = \''.addslashes(trim($valeur)).'\' AND '.$nom.' != \''.addslashes(trim($uservalue)).'\'')
										->fetchColumn();
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
	 * @desc Sauvegarde toutes les données relatives au stream
	 * @return void
	 */
	public function save ()
	{
		$pdo = SPDO::getInstance();
		
		$requete = $pdo->prepare("UPDATE streams SET
								offerId = :offerId,
								clientId = :clientId,
								status = :status,
								nom = :nom,
								description = :description,
								genre = :genre,
								mountpoint = :mountpoint,
								url = :url,
								password = :password,
								port = :port,
								dateDebut = :dateDebut,
								dateFin = :dateFin,
								ip_serveur = :ip_serveur,
								format_live = :format_live,
								format_output = :format_output,
								nb_jingles = :nb_jingles,
								start_before = :start_before,
								skip_blank_sec = :skip_blank_sec,
								skip_blank_db = :skip_blank_db,
								skip_blank_mail = :skip_blank_mail 
								WHERE id = :id");
			$requete->bindValue(':id', 			$this->id);
			$requete->bindValue(':offerId', 		$this->offerId);
			$requete->bindValue(':clientId', 		$this->clientId);
			$requete->bindValue(':status', 			$this->status);
			$requete->bindValue(':nom', 			$this->nom);
			$requete->bindValue(':description',		$this->description);
			$requete->bindValue(':genre', 			$this->genre);
			$requete->bindValue(':mountpoint', 		$this->mountpoint);
			$requete->bindValue(':url',			$this->url);
			$requete->bindValue(':password',		$this->password);
			$requete->bindValue(':port', 			$this->port);
			$requete->bindValue(':dateDebut', 		$this->dateDebut);
			$requete->bindValue(':dateFin', 		$this->dateFin);
			$requete->bindValue(':ip_serveur', 		$this->ip_serveur);
			$requete->bindValue(':format_live',		$this->format_live);
			$requete->bindValue(':format_output',		$this->format_output);
			$requete->bindValue(':nb_jingles',		$this->nb_jingles);
			$requete->bindValue(':start_before',		$this->start_before);
			$requete->bindValue(':skip_blank_sec',		$this->skip_blank_sec);
			$requete->bindValue(':skip_blank_db',		$this->skip_blank_db);
			$requete->bindValue(':skip_blank_mail',		$this->skip_blank_mail);

			$requete->execute();
	}
}
