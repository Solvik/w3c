<?php
/**
 * OxyCast
 * @desc Gestion des offres
 * @author alfo
 * @version 1.0
 */

class Offre
{
	protected $id;
	protected $bitrate;
	protected $slots;
	protected $prix;
	protected $hidden;
	
	/**
	 * @desc Constructeur
	 * @param $offerId int l'ID de l'offre
	 * @return void
	 */
	public function __construct ($offerId)
	{
		$pdo = SPDO::getInstance();
		$infos = $pdo->query('SELECT * FROM offers WHERE id = \'' . intval($offerId) . '\'')
					 ->fetch(PDO::FETCH_OBJ);
		
		$this->id		= $infos->id;
		$this->bitrate	= $infos->bitrate;
		$this->slots	= $infos->slots;
		$this->prix		= $infos->prixEuros;
		$this->hidden	= $infos->hidden;
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
	 * @desc Retourne le prix d'une offre
	 * @param $offerId int l'ID de l'offre
	 * @return float
	 */
	public function getPrice ($offerId)
	{
		return SPDO::getInstance()->query('SELECT prixEuros FROM offers WHERE id = '.intval($offerId))
								  ->fetchColumn();
	}
}
