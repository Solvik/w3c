<?php
require("conf.php");

// on regarde qu'est ce qui existe dans la table plannification pour voir si y a pas une playlist/podcast pour l'heure actuelle

function checkLastZik()
{
  $selectVerifZik = 'SELECT `titre` FROM `musique` WHERE `filename` != "" ORDER BY `dernier_passage` DESC LIMIT 1';
  $resultVerifZik = mysql_query($selectVerifZik);
  $VerifZik = mysql_fetch_object($resultVerifZik);

  return $VerifZik->titre;
}


$time = time();

$selectSearchAction = 'SELECT * FROM `planification` WHERE (`heure_debut` >= "'.date("G").'" AND `heure_debut` = `heure_fin` AND `minute_fin` >= "'.intval(date("i")).'") 
OR (`heure_debut` >= "'.date("G").'" AND `heure_debut` > `heure_fin` AND `heure_fin` >= "'.date("G").'");';
$resultSearchAction = mysql_query($selectSearchAction);

if (mysql_num_rows($resultSearchAction) == 1) 
{
  // on mate quelle est l'action
  $action = mysql_fetch_object($resultSearchAction);	

  // si c'est un podcast
  if ($action->type == "podcast")
    {
      // on joue le podcast
      $selectPodcast = 'SELECT path,filename FROM `musique` WHERE `id` = "'.$action->action.'" LIMIT 1';
      $resultPodcast = mysql_query($selectPodcast);
      $podcast = mysql_fetch_object($resultPodcast);

      echo $podcast->path.'/'.$podcast->filename;
      echo "podcast !\n";
      // on met a jour l'heure de dernier passage et le nb de passage
		

    }
	
  // si c'est une playlist
  else if ($playPlaylist = $action->type == "playlist")
    {
      // on cherche l'id de la playlist
      $selectSearchPlaylist = mysql_query('SELECT `id` FROM `playlist` WHERE `id` = "'.$action->action.'"');
      $SearchPlaylist = mysql_fetch_object($selectSearchPlaylist);

      // on cherche les id des musiques de la playlist
      $selectMusique = mysql_query('SELECT * FROM `musique_playlist` WHERE `id_playlist` = "'.$SearchPlaylist->id.'" ORDER BY RAND() LIMIT 15'); 
      $musique = mysql_fetch_object($selectMusique);
		 
      // on cherche les chansons qui sont dans la playlist et qui n'ont pas ete jouer depuis le debut de la playlist
      $selectSearchMusiqueFromPlaylist = 'SELECT id,path,filename FROM `musique` WHERE `id` = "'.$musique->id_musique.'" AND `filename` != "" ORDER BY `dernier_passage` ASC LIMIT '.REPETE;
      $resultSearchMusiqueFromPlaylist = mysql_query($selectSearchMusiqueFromPlaylist);
      $searchMusiqueFromPlaylist = mysql_fetch_object($resultSearchMusiqueFromPlaylist);

      if (checkLastZik() != $searchMusiqueFromPlaylist->titre)
	{
	  // on joue la musique
	  echo $searchMusiqueFromPlaylist->path."/".$searchMusiqueFromPlaylist->filename."\n";
	  // on met a jour le dernier passage et le nb de passage
	  $doUpdate = 'UPDATE `musique` SET dernier_passage = "'.time().'", passage = passage + 1 WHERE id = '.$searchMusiqueFromPlaylist->id.' LIMIT 1';
	  mysql_query($doUpdate);
	  echo "playlist !\n";
	}
      else
	exit();
    }
	
}

// sinon on joue de la zik aleatoire et on se tappe une barre laulaulaulaulaulaul
else
{
  $selectMusique = 'SELECT id,path,filename FROM `musique` WHERE `filename` != "" ORDER BY RAND() DESC LIMIT '.REPETE;
  $resultSelectMusique = mysql_query($selectMusique);
  $musiqueToPlay = mysql_fetch_object($resultSelectMusique);

  if (checkLastZik() != $searchMusiqueFromPlaylist->titre)
    {
      // on joue la musique
      echo $musiqueToPlay->path."/".$musiqueToPlay->filename."\n";
      // on update la date de dernier passage
      $doUpdate = 'UPDATE `musique` SET dernier_passage = '.time().', passage = passage + 1 WHERE id = '.$musiqueToPlay->id.' LIMIT 1';
      mysql_query($doUpdate);
    }
  else
    exit();
}
?>