<?php
/**
 * OxyCast
 * @desc Gestion d'un Creneau en particulier
 * @author alfo
 * @version 1.0
 */

class Creneau
{
	protected $id;
	protected $id_anim;
	protected $jour;
	protected $heure_debut;
	protected $heure_fin;
	protected $minute_debut;
	protected $minute_fin;
	protected $compte;
	
	/**
	 * @desc Constrcteur
	 * @param $creneauId int l'ID du Creneau
	 * @param $compte Member L'instance du compte
	 * @return void
	 */
	public function __construct ($creneauId, $compte)
	{
		$pdo 				= UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$this->id 			= intval($creneauId);
		$this->compte		= $compte;
		$infos 				= $pdo->query('SELECT * FROM animateurs_creneaux WHERE id ='.intval($creneauId))
								  ->fetch(PDO::FETCH_OBJ);
		
		$this->id_anim			= $infos->id_anim;
		$this->jour			= $infos->jour;
		$this->heure_debut	= $infos->heure_debut;
		$this->heure_fin	= $infos->heure_fin;
		$this->minute_debut	= $infos->minute_debut;
		$this->minute_fin	= $infos->minute_fin;
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
	 * @desc Méthode chargée de sauvegarder toutes les données relatives au Creneau
	 * @return void
	 */
	public function save ()
	{
		$pdo = UserDS::getInstance($this->compte->login."_".$this->compte->getStream()->id);
		
		$requete = $pdo->prepare("UPDATE animateurs_creneaux SET
								heure_debut = :hdebut,
								heure_fin = :hfin,
								minute_debut = :mdebut,
								minute_fin = :mfin,
								action = :action
								WHERE id = :id");
		$requete->bindValue(':hdebut', 		$this->heure_debut);
		$requete->bindValue(':hfin', 		$this->heure_fin);
		$requete->bindValue(':mdebut', 		$this->minute_debut);
		$requete->bindValue(':mfin', 		$this->minute_fin);
		$requete->bindValue(':id', 			$this->id);
		$requete->execute();
	}
}
