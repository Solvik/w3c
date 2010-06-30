<?php 
  /**
   * Musique
   * @desc Gère les animateurs
   * @author solvik
   * @version 1.0
   */


class Animateur
{
  /** Variables **/
  protected $id;
  protected $name;
  protected $password;
  protected $email;
  protected $compte;
	
  const NOUVEAU = -1;

	/**
	 * @desc Contructeur
	 * @return bool
	 */
	public function __construct (Member $compte, $animateurId, $name = null, $password = null)
	{
		@$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$this->compte = $compte;

		if ($animateurId == self::NOUVEAU)
		{
			$requete = $pdo->prepare("INSERT INTO animateurs SET name = :name, password = :password");
			$requete->bindValue(':name', $name);
			$requete->bindValue(':password', $password);
			$requete->execute();

			$this->name		= $name;
			$this->password	= $password;

			$requete = $pdo->query('SELECT id FROM animateurs WHERE name = \''. $name .'\'')->fetchColumn();
			$this->id = $requete;
		}
		else
		{		
			$infos = $pdo->query('SELECT * FROM `animateurs` WHERE id = '.intval($animateurId))->fetch(PDO::FETCH_OBJ);
				
			if($infos === false) return false;
				
			$this->id			= $infos->id;
			$this->name			= $infos->name;
			$this->password		= $infos->password;
			$this->email		= $infos->email;
			$this->compte		= $compte;
				
			return true;
		}
	}

	public static function getAnimateurs(Member $compte)
	{
		@$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$query = 'SELECT * FROM `animateurs`';
		$anim = array();

		foreach ($pdo->query($query) as $result)
		{
			array_push($anim, array('id'		=> $result['id'],
									'name'		=> utf8_encode($result['name']),
									'password'	=> $result['password'])
					  );
		}
		return $anim;
	}

	public static function deleteAnim(Member $compte, $id)
	{
		@$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		$query = 'SELECT `id` FROM `animateurs` WHERE id = "'.$id.'"';
		$res = $pdo->query($query);

		if ($res->fetchColumn() > 0)
		{
			$pdo->query('DELETE FROM `animateurs` WHERE `id` = '.$id.'');
			$pdo->query('DELETE FROM `animateurs_creneaux` WHERE `id_anim`= '.$id.'');
			return true;
		} else
			return false;
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
    @$pdo = UserDS::getInstance($this->compte->login."_".$this->compte->getStream()->id);
		
    $requete = $pdo->prepare("UPDATE animateurs SET
						name = :name,
						password = :password
					WHERE id = :id");

    $requete->bindValue(':name', 		$this->name);
    $requete->bindValue(':password',		$this->password);
    $requete->bindValue(':id',			$this->id);
    $requete->execute();
  }
}
?>