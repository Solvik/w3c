<?php
/**
 * OxyCast
 * @desc Classe permettant la gestion complète de TOUS les comptes
 * @author alfo
 * @version 1.0
 */

class Members
{
	/**
	 * @desc Retourne le nomrbe total de comptes.
	 * @type static
	 * @return int
	 */
	public static function getCount ()
	{
		return (int) SPDO::getInstance()->query('SELECT COUNT(*) AS count FROM clients')
										->fetchColumn();
	}
	
	/**
	 * @desc Retourne le nombre total de comptes en fonction de la condition $cond
	 * @type static
	 * @param $cond string La condition sans le WHERE
	 * @return int
	 */
	public static function getCountByCond ($cond)
	{
		return (int) SPDO::getInstance()->query('SELECT COUNT(*) AS count FROM clients WHERE '.$cond)
										->fetchColumn();
	}
}
