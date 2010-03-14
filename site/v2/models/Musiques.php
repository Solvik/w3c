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
  $toDelete = "streams/".$compte->login."-".$stream->id."/";
  $i = 0;
	
  foreach($pdo->query($query) as $musique)
    {
      $musiques[$i] = array('id'		=>		$musique['id'],
			    'artiste' =>		utf8_encode($musique['artiste']),
			    'titre' 	=>		utf8_encode($musique['titre']),
			    'chemin' 	=>		utf8_encode(str_replace($toDelete, "", $musique['path']).'/'.$musique['filename']),
			    'fade_in' =>		$musique['fade_in'],
			    'fade_out' =>		$musique['fade_out']);
		
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

/**
 * @desc Retourne le fade-in d'une musique
 * @param $musiqueId int l'ID de la musique
 * @param $compte Member L'instance du compte
 * @param $type string fade_in ou fade_out
 * @return float
 */

function getFader ($type, $musiqueId, Member $compte)
{
  @$pdo = UserDS::getFaddingInfos($compte->login."_".$compte-getStream()->$id);

  return $pdo->query('SELECT fade_in FROM musique WHERE id = '.intval($musiqueId))->fetchColumn();
}

/**
 * @desc Modifie la valeur du fadein/fadeout
 * @param $type fade in ou fade out
 * @param $value
 * @param $musiqueId int l'ID de la musique
 * @param $compte Member L'instance du compte
 * @return void
 */

function updateFader ($type, $value, $musiqueId, Member $compte)
{
  @$pdo = UserDS::getFaddingInfos($compte->login."_".$compte-getStream()->$id);

  $requete = $pdo->query('UPDATE `musique` SET "'.$type.'" = "'.$value.'" WHERE id = '.intval($musiqueId));
  $requete->execute();
}

