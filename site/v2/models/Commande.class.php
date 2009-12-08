<?php
/**
 * OxyCast
 * @desc Gestion des commandes
 * @author alfo
 * @version 1.0
 */

class Commande
{
	// Définition des paramètres de la classe
	protected $transacId;
	protected $clientId;
	protected $payment_type;
	protected $status;
	protected $amount;
	protected $offerId;
	protected $email;

	/**
	 * @desc Constructeur
	 * @param $transacId int l'ID de la transaction
	 * @return bool
	 */
	public function __construct ($transacId)
	{
		if($transacId == 0) return true;
		
		$pdo = SPDO::getInstance();
		
		$infos = $pdo->query('SELECT * FROM commandes WHERE transacId = \'' . intval($transacId) . '\'')
					 ->fetch(PDO::FETCH_OBJ);
		
		$this->transacId 		= 	$infos->transacId;
		$this->clientId 		= 	$infos->clientId;
		$this->payment_type		= 	$infos->payment_type;
		$this->status 			= 	$infos->status;
		$this->amount 			= 	$infos->amount;
		$this->offerId 			= 	$infos->offerId;
		$this->email 			= 	$infos->email;

		return true;
	}
	
	/**
	 * @desc Permet de créer une commande
	 * @param $clientID int l'ID du compte
	 * @param $payment_type string Le type de paiement : paypal ou cheque
	 * @param $status string Le statut de la commande : waiting, done ou cancelled
	 * @param $amout float Le montant de la transaction
	 * @param $offerId int l'ID de l'offre
	 * @param $email string L'email du client
	 * @return void
	 */
	public function create ($clientId, $payment_type, $status, $amount, $offerId, $email)
	{
		$pdo = SPDO::getInstance();
		
		$id = $pdo->query('SELECT transacId FROM commandes ORDER BY transacId DESC')->fetchColumn();
		
		$transacId = (int) $id + 1;
		$this->transacId 		= 	$transacId;
		$this->clientId 		= 	$clientId;
		$this->payment_type		= 	$payment_type;
		$this->status 			= 	$status;
		$this->amount 			= 	$amount;
		$this->offerId 			= 	$offerId;
		$this->email 			= 	$email;
		
		$requete = $pdo->prepare("INSERT INTO commandes SET
								transacId = :transacId,
								clientId = :clientId,
								payment_type = :payment_type,
								status = :status,
								amount = :amount,
								offerId = :offerId,
								email = :email");
		$requete->bindValue(':transacId', 		$this->transacId);
		$requete->bindValue(':clientId', 		$this->clientId);
		$requete->bindValue(':payment_type', 	$this->payment_type);
		$requete->bindValue(':status', 			$this->status);
		$requete->bindValue(':amount', 			$this->amount);
		$requete->bindValue(':offerId', 		$this->offerId);
		$requete->bindValue(':email', 			$this->email);

		$requete->execute();
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
	 * @desc Méthode chargée de sauvegarder toutes les données relatives à la commande
	 * @return void
	 */
	public function save ()
	{
		$pdo = SPDO::getInstance();
		
		$requete = $pdo->prepare("UPDATE commandes SET
								clientId = :clientId,
								payment_type = :payment_type,
								status = :status,
								amount = :amount,
								offerId = :offerId,
								email = :email
								WHERE transacId = :transacId");
		$requete->bindValue(':transacId', 		$this->transacId);
		$requete->bindValue(':clientId', 		$this->clientId);
		$requete->bindValue(':payment_type', 	$this->payment_type);
		$requete->bindValue(':status', 			$this->status);
		$requete->bindValue(':amount', 			$this->amount);
		$requete->bindValue(':offerId', 		$this->offerId);
		$requete->bindValue(':email', 			$this->email);

		$requete->execute();
	}
}
