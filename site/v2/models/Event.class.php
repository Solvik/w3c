<?php
/**
 * OxyCast
 * @desc Gestion d'un évenement en particulier
 * @author alfo
 * @version 1.0
 */

class Event
{
	protected $id;
	protected $type;
	protected $jour;
	protected $heure_debut;
	protected $heure_fin;
	protected $minute_debut;
	protected $minute_fin;
	protected $action;
	protected $compte;
	protected $heure_d;
	protected $heure_f;
	
	/**
	 * @desc Constrcteur
	 * @param $eventId int l'ID de l'évenement
	 * @param $compte Member L'instance du compte
	 * @return void
	 */
	public function __construct ($eventId, $compte)
	{
		$pdo 				= UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$this->id 			= intval($eventId);
		$this->compte		= $compte;
		$infos 				= $pdo->query('SELECT * FROM planification WHERE id ='.intval($eventId))
								  ->fetch(PDO::FETCH_OBJ);
		
		$this->type			= $infos->type;
		$this->jour			= $infos->jour;
		$this->heure_debut	= $infos->heure_debut;
		$this->heure_fin	= $infos->heure_fin;
		$this->minute_debut	= $infos->minute_debut;
		$this->minute_fin	= $infos->minute_fin;
		$this->action		= $infos->action;
		$this->heure_d		= $infos->heure_d;
		$this->heure_f		= $infos->heure_f;
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
	 * @desc Méthode chargée de sauvegarder toutes les données relatives à l'Event
	 * @return void
	 */
	public function save ()
	{
		$pdo = UserDS::getInstance($this->compte->login."_".$this->compte->getStream()->id);
		
		$requete = $pdo->prepare("UPDATE planification SET
								heure_debut = :hdebut,
								heure_fin = :hfin,
								minute_debut = :mdebut,
								minute_fin = :mfin,
								action = :action,
								heure_d = :heure_d,
								heure_f = :heure_f
								WHERE id = :id");
		$requete->bindValue(':hdebut', 		$this->heure_debut);
		$requete->bindValue(':hfin', 		$this->heure_fin);
		$requete->bindValue(':mdebut', 		$this->minute_debut);
		$requete->bindValue(':mfin', 		$this->minute_fin);
		$requete->bindValue(':action', 		$this->action);
		$requete->bindValue(':id', 			$this->id);
		$requete->bindValue(':heure_d',		$this->heure_d);
		$requete->bindValue(':heure_d',		$this->heure_f);
		$requete->execute();
	}
}
