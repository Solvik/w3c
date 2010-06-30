<?php
  /**
   * OxyCast
   * @desc Gestion des Creneaux
   * @author alfo
   * @version 1.0
   */

class Creneaux
{
	
	/**
	 * @desc Retourne un la liste des Creneaux pour un jour $jour
	 * @param $jour int Le jour
	 * @param $compte Member Le compte
	 * @return array
	 */
	public static function getCreneaux (Member $compte, $jour)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		
		$query = 'SELECT id FROM animateurs_creneaux WHERE jour = '.intval($jour);
		$list = array();
		$i = 0;
		
		foreach($pdo->query($query) as $Creneau)
		{
			$list[$i]	= new Creneau($Creneau['id'], $compte);
			$i++;
		}
		return $list;
	}
	
	/**
	 * @desc Supprime le Creneau $creneauId
	 * @param $creneauId l'ID du Creneau
	 * @param $compte Member L'instance du compte
	 * @return void
	 */
	public static function delCreneau (Member $compte, $creneauId)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$pdo->query('DELETE FROM animateurs_creneaux WHERE id = '.intval($creneauId));
	}
	
	/**
	 * @desc Supprime tous les Creneaux d'un animateur
	 * @param $animId l'ID de l'Animateur
	 * @param $compte Member L'instance du compte
	 * @return void
	 */
	public static function delAnimCreneaux (Member $compte, $animId)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$pdo->query('DELETE FROM animateurs_creneaux WHERE id_anim = '.intval($animId));
		return true;
	}
	
	/**
	 * @desc Ajoute un Creneau
	 * @param $compte Member L'instance du compte
	 * @param $id_anim int l'ID de l'animateur
	 * @param $jour int Le jour : 1 à 7
	 * @param $hdebut int
	 * @param $hfin int
	 * @return void
	 */
	public static function addCreneau (Member $compte, $id_anim, $jour, $hdebut, $hfin)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$requete = $pdo->prepare("INSERT INTO animateurs_creneaux SET
								id = '',
								id_anim = :id_anim,
								jour = :jour,
								heure_debut = :hdebut,
								heure_fin = :hfin,
								action = :action");
		$requete->bindValue(':id_anim', 	$id_anim);
		$requete->bindValue(':jour', 		$jour);
		$requete->bindValue(':hdebut', 		$hdebut);
		$requete->bindValue(':hfin', 		$hfin);
		$requete->execute();
	}
}
