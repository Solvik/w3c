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
			$this->password			= $infos->password;
			$this->compte			= $compte;
				
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
			return 1;
		} else
			return 0;
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

  /**
   * OxyCast
   * @desc Gestion des creneaux
   * @author alfo
   * @version 1.0
   */

class Creneaux
{
  protected $compte;
  protected $id_anim;
  protected $jour;
  protected $heure_debut;
  protected $heure_fin;
  protected $jingle;

  const NOUVEAU = -1;

  public function __construct (Member $compte, $animateurId, $id_anim = null, $jour = null, $heure_debut = null, $heure_fin = null, $jingle = null)
  {
    @$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
    $this->compte = $compte;

    if ($animateurId == self::NOUVEAU)
      {
	$requete = $pdo->prepare("INSERT INTO animateurs_creneaux SET jour = :jour, heure_debut = :heure_debut, heure_fin = :heure_fin");
	$requete->bindValue(':id_anim', $id_anim);
	$requete->bindValue(':jour', $jour);
	$requete->bindValue(':heure_debut', $heure_debut);
	$requete->bindValue(':heure_fin', $heure_fin);
	$requete->execute();

	$this->id_anim			= $id_anim;
	$this->jour			= $jour;
	$this->heure_debut		= $heure_debut;
	$this->heure_fin		= $heure_fin;


// 	$requete = $pdo->query('SELECT id FROM animateurs WHERE name = \''. $name .'\'')->fetchColumn();
// 	$this->id			= $requete;
      }
    else
      {		
	$infos = $pdo->query('SELECT * FROM `animateurs` WHERE id = '.intval($animateurId))->fetch(PDO::FETCH_OBJ);
		
	if($infos === false) return false;
		
	$this->id			= $infos->id;
	$this->name			= $infos->name;
	$this->password			= $infos->password;
	$this->compte			= $compte;
		
	return true;
      }
  }
  public static function getCreneaux(Member $compte)
  {
    @$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
    $query = 'SELECT * FROM `animateurs_creneaux`';
    $anim = array();

    $i = 0;
    foreach ($pdo->query($query) as $result)
      {
	$anim[$i] = array('id'		=> $result['id'],
			  'id_anim'	=> $result['id_anim'],
			  'jour'	=> $result['jour'],
			  'heure_debut'	=> $result['heure_debut'],
			  'heure_fin'	=> $result['heure_fin']);
	$i++;
      }
    $anim[$i] = NULL;
    return ($anim);    
  }

  public static function deleteCreneaux(Member $compte, $id)
  {
    @$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
    $query = 'SELECT `id` FROM `animateurs_creneaux` WHERE id = "'.$id.'"';
    $res = $pdo->query($query);

    if ($res->fetchColumn() > 0)
      {
	$pdo->query('DELETE FROM `animateurs_creneaux` WHERE `id`= '.$id.'');
	return (1);
      }
    else
      return (0);
  }

  public static function getAnim(Member $compte, $id)
  {
    @$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
    $query = 'SELECT `name` FROM `animateurs` WHERE id = "'.$id.'"';
    $res = $pdo->query($query);

    return ($res);
  }

  public static function getDate($id)
  {
    $jour = array(1 => "Lundi", 2 => "Mardi", 3 => "Mercredi", 4 => "Jeudi", 5 => "Vendredi", 6 => "Samedi", 7 => "Dimanche");

    return ($jour[$id]);
  }


  public function save ()
  {
    @$pdo = UserDS::getInstance($this->compte->login."_".$this->compte->getStream()->id);
		
    $requete = $pdo->prepare("UPDATE animateurs_creneaux SET
						jour = :jour,
						heure_debut = :heure_debut,
						heure_fin = :heure_fin
					WHERE id = :id");

    $requete->bindValue(':jour', 		$this->jour);
    $requete->bindValue(':heure_debut',		$this->heure_debut);
    $requete->bindValue(':heure_fin',		$this->heure_fin);
    $requete->bindValue(':id',			$this->id);
    $requete->execute();
  }
}

?>