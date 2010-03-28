<?php
  /**
   * OxyCast
   * @desc Gestion de la programmation des évenements
   * @author alfo
   * @version 1.0
   */

class Programmation
{
	
	/**
	 * @desc Retourne un la liste des events pour un jour $jour
	 * @param $jour int Le jour
	 * @param $compte Member Le compte
	 * @return array
	 */
	public static function getEvents ($jour, Member $compte)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		
		$query = 'SELECT id FROM planification WHERE jour = '.intval($jour);
		$list = array();
		$i = 0;
		
		foreach($pdo->query($query) as $event)
		{
			$list[$i]	= new Event($event['id'], $compte);
			$i++;
		}
		return $list;
	}
	
	/**
	 * @desc Supprime l'evenement $eventId
	 * @param $eventId l'ID de l'évenement
	 * @param $compte Member L'instance du compte
	 * @return void
	 */
	public static function delEvent ($eventId, Member $compte)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$pdo->query('DELETE FROM planification WHERE id = '.intval($eventId));
	}
	
	/**
	 * @desc Ajoute un évenement
	 * @param $compte Member L'instance du compte
	 * @param $type string Le type d'évenement : playlist ou podcast
	 * @param $jour int Le jour : 1 à 7
	 * @param $hdebut int
	 * @param $hfin int
	 * @param $mdebut int
	 * @param $mfin int
	 * @param $action int L'action à éxecuter : ID de la playlist ou ID de la musique dans le cas d'un podcast
	 * @return void
	 */
	public static function addEvent (Member $compte, $type, $jour, $hdebut, $hfin, $mdebut, $mfin, $action)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$requete = $pdo->prepare("INSERT INTO planification SET
								id = '',
								type = :type,
								jour = :jour,
								heure_debut = :hdebut,
								heure_fin = :hfin,
								minute_debut = :mdebut,
								minute_fin = :mfin,
								action = :action");
		$requete->bindValue(':type', 		$type);
		$requete->bindValue(':jour', 		$jour);
		$requete->bindValue(':hdebut', 		$hdebut);
		$requete->bindValue(':hfin', 		$hfin);
		$requete->bindValue(':mdebut', 		$mdebut);
		$requete->bindValue(':mfin', 		$mfin);
		$requete->bindValue(':action', 		$action);
		$requete->execute();
	}
}
