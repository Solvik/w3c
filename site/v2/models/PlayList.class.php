<?php
/**
 * OxyCast
 * @desc Classe permettant de créer et de gérer une playlist
 * @author alfo
 * @version 1.0
 */

class PlayList
{
	protected $nom;
	protected $playlistId;
	protected $musiques = array();
	protected $compte;
	protected $stream;
	
	const NOUVEAU = -1;
	
	/**
	 * @desc Constructeur
	 * @param $playlistId int l'ID de la playlist
	 * @param $compte Member L'instance du compte
	 * @param $stream Stream L'instance du stream
	 * @param $playlistName string Le nom de la playlist à créer
	 * @return void
	 */
	public function __construct ($playlistId, Member $compte, Stream $stream, $playlistName = null)
	{
		@$pdo = UserDS::getInstance($compte->login."_".$stream->id);
		$this->compte = $compte;
		$this->stream = $stream;
		
		if($playlistId === self::NOUVEAU)
		{
			$requete = $pdo->prepare("INSERT INTO playlist SET nom = :nom");
			$requete->bindValue(':nom', $playlistName);
			$requete->execute();
			
			$this->nom = $playlistName;
			
			$requete = $pdo->query('SELECT id FROM playlist WHERE nom = \'' . $playlistName . '\'')->fetchColumn();
			$this->playlistId = $requete;
		
		} else {
			$requete = $pdo->query('SELECT nom FROM playlist WHERE id = \'' . $playlistId . '\'')->fetchColumn();
			$this->nom = $nom;
			
			$query = 'SELECT * FROM musique_playlist WHERE id_playlist = ' . intval($playlistId);
			$this->playlistId = intval($playlistId);
			
			$i = 0;
			foreach($pdo->query($query) as $musique)
			{
				$this->musiques[$i] = $musique['id_musique'];
				$i++;
			}
		}
	}
	
	/**
	 * @desc Retourne la liste de toutes les playlists
	 * @param $compte Member L'instance du compte
	 * @return array Liste des playlist
	 */
	public static function getPlaylists (Member $compte)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		
		$query = 'SELECT * FROM playlist';
		$list = array();
		$i = 0;
		
		foreach($pdo->query($query) as $playlist)
		{
			$list[$i]['id'] 	= $playlist['id'];
			$list[$i]['nom'] 	= $playlist['nom'];
			$i++;
		}
		return $list;
	}
	
	/**
	 * @desc Retourne le titre d'une playlist
	 * @type static
	 * @param $playlistId int l'ID de la playlist
	 * @param $compte Member l'Instance du compte
	 * @return string Nom de la playlist
	 */
	public static function getPlaylistName ($playlistId, Member $compte)
	{
		$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
		
		return $pdo->query('SELECT nom FROM playlist WHERE id = '.intval($playlistId))->fetchColumn();
	}
	
	/**
	 * @deprecated
	 * @desc Retourne true si la playlist passée en paramètre appartient à l'utilisateur passé en paramètre.
	 * @type static
	 * @param $playlist PlayList L'instance de la PlayList
	 * @param $comtpe Member L'instance du compte
	 * @return bool
	 */
	public static function belongs (PlayList $playlist, Member $compte)
	{
		$list = self::getPlaylists($compte);
		$id = $playlist->playlistId;
		
		foreach($list as $playlist)
		{
			if($id == $playlist['id']) return true;
		}
		return false;
	}
	
	/**
	 * @desc Retourne la liste des musiques
	 * @return array
	 */
	public function getMusiques ()
	{
		return $this->musiques;
	}
	
	/**
	 * @desc Ajoute la musique $musiqueId
	 * @param $musiqueId int l'ID de la musique à ajouter
	 * @return void
	 */
	public function addMusique ($musiqueId)
	{
		array_push($this->musiques, $musiqueId);
	}
	
	/**
	 * @desc Supprime la musique $musiqueId
	 * @param $musiqueId int l'ID de la musique à supprimer
	 * @return void
	 */
	public function delMusique ($musiqueId)
	{
		$i = 0;
		$musiques_temp = array();
		foreach($this->musiques as $musique)
		{
			if($musique != $musiqueId) $musiques_temp[$i] = $musique;
			$i++;
		}
		$this->musiques = $musiques_temp;
	}
	
	/**
	 * @desc Supprime la playlist
	 * @return void
	 */
	public function delete ()
	{
		@$pdo = UserDS::getInstance($this->compte->login."_".$this->stream->id);
		
		$pdo->query('DELETE FROM musique_playlist WHERE id_playlist = ' . intval($this->playlistId));
		$pdo->query('DELETE FROM playlist WHERE id = ' . intval($this->playlistId));
		$this->playlistId = null;
	}
	
	/**
     * Méthode chargée de retourner la valeur de l'attribut en paramètre.
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
     * Méthode chargée de changer la valeur de l'attribut en paramètre.
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
	 * @desc Sauvegarde toutes les données relatives à la playlist
	 * @return void
	 */
	public function save ()
	{
		@$pdo = UserDS::getInstance($this->compte->login."_".$this->stream->id);
		
		$requete = $pdo->prepare("UPDATE playlist SET nom = :nom WHERE id = :id");
		$requete->bindValue(':nom', $this->nom);
		$requete->bindValue(':id', $this->playlistId);
		$requete->execute();
		
		$pdo->query("DELETE FROM musique_playlist WHERE id_playlist = $this->playlistId");
		
		foreach($this->musiques as $musique)
		{
			$requete = $pdo->prepare("INSERT INTO musique_playlist SET
									id_musique = :id_musique,
									id_playlist = :id_playlist");
			$requete->bindValue(':id_musique', 		$musique);
			$requete->bindValue(':id_playlist', 	$this->playlistId);

			$requete->execute();
		}
	}
}
