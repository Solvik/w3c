<?php 
/**
 * Musique
 * @desc Gère les musiques
 * @author alfo
 * @version 1.0
 */

class Musique
{
	/** Variables **/
	protected $id;
	protected $titre;
	protected $artiste;
	protected $filename;
	protected $path;
	protected $chemin;
	protected $dernier_passage;
	protected $passage;
	protected $duree;
	protected $fade_in;
	protected $fade_out;
	
	/**
	 * @desc Contructeur
	 * @return bool
	 */
	public function __construct (Member $compte, $musiqueId)
	{
		if($musiqueId == 0) return false;
		
		@$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		
		$infos = $pdo->query('SELECT * FROM `musique` WHERE id = '.intval($musiqueId))->fetch(PDO::FETCH_OBJ);
		
		if($infos === false) return false;
		
		$this->id			= $infos->id;
		$this->titre			= $infos->titre;
		$this->artiste			= $infos->artiste;
		$this->filename			= $infos->filename;
		$this->path			= $infos->path;
		$this->chemin			= generateChemin($compte, $this->path, $this->filename);
		$this->dernier_passage		= $infos->dernier_passage;
		$this->passage			= $infos->passage;
		$this->duree			= $infos->duree;
		$this->fade_in			= $infos->fade_in;
		$this->fade_out			= $infos->fade_out;
		
		return true;
	}
	
	/**
	 * Génère un chemin du fichier relatif à l'utilisateur
	 * @param Member L'instance de l'utilisateur
	 * @param String Le chemin absolu
	 * @param String Le nom du fichier
	 * @return String
	 */
	public static function generateChemin (Member $compte, $path, $filename)
	{
		$toDelete = "/home/oxycast/streams/".$compte->login."-".$compte->getStream()->id."/";
		return str_replace($toDelete, "", $path).'/'.$filename)
		
	}
	
	/**
	 * @desc Retourne la liste de toutes les musiques où chaque élement pointe sur une instance de Musique
	 * @return array|Musique
	 */
	 public static function getMusics(Member $compte, $orderBy = 'id' , $sens = 'ASC')
	 {
	 	@$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);

		$query = 'SELECT id FROM `musique` ORDER BY '.$orderBy.' '.$sens;
		$musiques = array();
		$i = 0;
	
		foreach($pdo->query($query) as $musique)
		{
			$musiques[$i] = new Musique($musique['id']);
      			$i++;
    		}
  		return $musiques;
	 }
	
	/**
	 * @desc Retourne la liste de tous les podcasts où chaque élement pointe sur une instance de Musique
	 * @return array|Musique
	 */
	public static function getPodcasts(Member $compte)
	{
		@$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$podcasts = array();
		$query = 'SELECT id FROM `musique` WHERE `path` LIKE \'%/podcast\' ORDER BY `filename` ASC';
		$i = 0;
	
		foreach($pdo->query($query) as $podcast)
		{
			$podcasts[$i] = new Musique($podcast['id']);
      			$i++;
    		}
		return $podcasts;
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
	 * @desc Sauvegarde toutes les données relatives à la musique
	 * @return void
	 */
	public function save ()
	{
		@$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		
		$requete = $pdo->prepare("UPDATE musique SET
						id = :id,
						titre = :titre,
						artiste = :artiste,
						filename = :filename,
						path = path,
						dernier_passage = :dernier_passage,
						passage = :passage,
						duree = :duree,
						fade_in = :fade_in,
						fade_out = :fade_out
					WHERE id = :id");
			$requete->bindValue(':id', 		$this->id);
			$requete->bindValue(':titre', 		$this->titre);
			$requete->bindValue(':artiste', 	$this->artiste);
			$requete->bindValue(':filename', 	$this->filename);
			$requete->bindValue(':path', 		$this->path);
			$requete->bindValue(':dernier_passage',	$this->dernier_passage);
			$requete->bindValue(':passage', 	$this->passage);
			$requete->bindValue(':duree', 		$this->duree);
			$requete->bindValue(':fade_in',		$this->fade_in);
			$requete->bindValue(':fade_out',	$this->fade_out);

			$requete->execute();
	}
} ?>
