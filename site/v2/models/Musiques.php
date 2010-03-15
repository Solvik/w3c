<?php
  /**
   * OxyCast
   * @desc Gestion simpliste de la musique d'un compte.
   * @author alfo
   * @version 1.0
   */

  /**
   * @desc Retourne la liste complète des musiques du compte
   * @param $compte Member L'instance du compte
   * @param $stream Stream L'instance du Stream
   * @return array
   */
function getMusicList(Member $compte, Stream $stream, $orderBy = 'id' , $sens = 'ASC')
{
  @$pdo = UserDS::getInstance($compte->login."_".$stream->id);

  $query = 'SELECT * FROM `musique` ORDER BY '.$orderBy.' '.$sens;
  $musiques = array();
  $toDelete = "/home/oxycast/streams/".$compte->login."-".$stream->id."/";
  $i = 0;
	
  foreach($pdo->query($query) as $musique)
    {
      $musiques[$i] = array('id'	=>		$musique['id'],
			    'artiste'	=>		utf8_encode($musique['artiste']),
			    'titre' 	=>		utf8_encode($musique['titre']),
			    'chemin' 	=>		utf8_encode(str_replace($toDelete, "", $musique['path']).'/'.$musique['filename']),
			    'duree'	=>		$musique['duree'],
			    'fade_in'	=>		$musique['fade_in'],
			    'fade_out'	=>		$musique['fade_out']);
		
      $i++;
    }
  return $musiques;
}

/**
 * @desc Retourne les infos d'une musique
 * @param $musiqueId int l'ID de la musique
 * @param $compte Member L'instance du compte
 * @param $stream Stream L'instance du Stream
 * @return array
 */

function getMusicInfos ($musiqueId, Member $compte, Stream $stream)
{
  @$pdo = UserDS::getInstance($compte->login."_".$stream->id);
	
  $query = 'SELECT * FROM `musique` WHERE id = '.intval($musiqueId);
  $infos = $pdo->query($query)->fetch(PDO::FETCH_BOTH);

  return $infos;
}

/**
 * @desc Retourne la liste des podcast d'un compte
 * @param $compte Member L'instance du compte
 * @return array
 */

function getPodcasts (Member $compte)
{
  @$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
  $podcasts = array();
  $query = 'SELECT * FROM `musique` WHERE `path` LIKE \'%/podcast\' ORDER BY `filename` ASC';
  $i = 0;
	
  foreach($pdo->query($query) as $podcast)
    {
      $podcasts[$i] = array('id'		=>		$podcast['id'],
			    'titre' 	=>		utf8_encode($podcast['titre']),
			    'filename' => utf8_encode($podcast['filename']));
      $i++;
    }
  return $podcasts;
}

/**
 * @desc Retourne le titre d'un podcast
 * @param $musiqueId int l'ID de la musique
 * @param $compte Member L'instance du compte
 * @return string
 */

function getPodcastName ($musiqueId, Member $compte)
{
  @$pdo = UserDS::getInstance($compte->login."_".$compte->getStream()->id);
	
  return $pdo->query('SELECT titre FROM musique WHERE id = '.intval($musiqueId))->fetchColumn();
}