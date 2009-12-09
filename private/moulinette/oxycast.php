<?php
require("conf.php");

// on regarde qu'est ce qui existe dans la table plannification pour voir si y a pas une playlist/podcast pour l'heure actuelle

$time = time();

$selectSearchAction = 'SELECT * FROM `planification` WHERE `jour` LIKE "'.date("N").'" AND `heure_debut` LIKE "%'.date("H:i").'%" AND `action` != "" LIMIT 1';
$resultSearchAction = mysql_query($selectSearchAction);

if (mysql_num_rows($resultSearchAction) == 1) 
{
	// on mate quelle est l'action
	$action = mysql_fetch_object($resultSearchAction);	

	// si c'est un podcast
	if (substr($action->action, 0, 7) == "podcast")
	{
		// on joue le podcast
		$playPodcast =  substr($action->action, 8);
		echo $directory.PATH.$playPodcast;

		// on met a jour l'heure de dernier passage et le nb de passage
		

	}
	
	// si c'est une playlist
	else if ($playPlaylist = substr($action->action, 0, 8) == "playlist")
	{
		// on cherche l'id de la playlist
		$selectSearchPlaylist = mysql_query('SELECT `id` FROM `playlist` WHERE `nom` = "'.substr($action->action, 9).'"');
		$SearchPlaylist = mysql_fetch_object($selectSearchPlaylist);

		// on cherche les id des musiques de la playlist
		$selectMusique = mysql_query('SELECT * FROM `musique_playlist` WHERE `id_playlist` = "'.$SearchPlaylist->id.'"'); 
		$musique = mysql_fetch_object($selectMusique);

 
		// on cherche les chansons qui sont dans la playlist et qui n'ont pas ete jouer depuis le debut de la playlist
		$selectSearchMusiqueFromPlaylist = 'SELECT * FROM `musique` WHERE `id` = "'.$musique->id_musique.'" AND `filename` != "" ORDER BY `dernier_passage` ASC LIMIT '.REPETE;
		$resultSearchMusiqueFromPlaylist = mysql_query($selectSearchMusiqueFromPlaylist);
		$searchMusiqueFromPlaylist = mysql_fetch_object($resultSearchMusiqueFromPlaylist);

		// on joue la musique
                echo $directory.$searchMusiqueFromPlaylist->path."/".$searchMusiqueFromPlaylist->filename;
		
		// on met a jour le dernier passage et le nb de passage
		$doUpdate = mysql_query('UPDATE `musique` SET dernier_passage = '.$time.', passage = passage + 1 WHERE id = '.$searchMusiqueFromPlaylist->id.' LIMIT 1');


		
		
	}
	
}

// sinon on joue de la zik aleatoire et on se tappe une barre laulaulaulaulaulaul
else
{
                $selectMusique = 'SELECT * FROM `musique` WHERE `filename` != "" ORDER BY RAND() DESC LIMIT '.REPETE;
                $resultSelectMusique = mysql_query($selectMusique);
                $musiqueToPlay = mysql_fetch_object($resultSelectMusique);

                $doUpdate = mysql_query('UPDATE `musique` SET dernier_passage = '.$time.', passage = passage + 1 WHERE id = '.$musiqueToPlay->id.' LIMIT 1');
                echo $directory.$musiqueToPlay->path."/".$musiqueToPlay->filename;

}
?>