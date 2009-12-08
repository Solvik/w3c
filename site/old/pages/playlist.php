<?php
if (isset($_SESSION['login']))
{
  echo '<div class="boxtitle">Mes playlists</div>';
  echo '<div class="content_content">';

  $reqClient = "SELECT * FROM `clients` WHERE `login` = '".$_SESSION['login']."'";
  $requeteClient = mysql_query($reqClient);
  $clientInfos = mysql_fetch_object($requeteClient);
  
  $reqStream = "SELECT * FROM `streams` WHERE clientID='".$clientInfos->id."' AND `status` != 'commande'";
  $requeteStream = mysql_query($reqStream);
  $stream = mysql_fetch_object($requeteStream);
  
  $reqOffre = "SELECT * FROM `offers` WHERE id='".@$stream->offerId."'";
  $requeteOffre = mysql_query($reqOffre);
  $offre = mysql_fetch_object($requeteOffre);

  // on affiche que si y a un stream existant pour le client
  if($stream)
    {    
      // on selectionne les playlist dans la base de donnee du client (rappel : Login_Idstream)
      $selectPlaylist = 'SELECT * FROM `'.$clientInfos->login.'_'. $stream->id .'`.`playlist`';
      $resultSelectPlaylist = mysql_query($selectPlaylist);
      $playlist = mysql_fetch_object($resultSelectPlaylist);
      
      // si il cree une playlist
      if ($_GET['act'] == "add")
	{
	  if ($_POST['modifier'])
	    {
	      // on insert la base
	      $insertPlaylist = "INSERT INTO `".$clientInfos->login."_".$stream->id."`.`playlist` (`id`, `nom`) VALUES ('', '".htmlentities(trim($_POST['name']))."')";
	      $resultInsertPlaylist = mysql_query($insertPlaylist);
	      echo '<div align="center">Playlist ajout&eacute;e avec succ&egrave;s !</div>';
	    }
	  else 
	    {
	      // on affiche le formulaire
	      echo '<form action="" method="post">
<table border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td align="right">Nom de la playlist</td>
    <td coldspan="2"><input type=text" name="name" /></td>
  </tr>
    <td>&nbsp;</td>
    <td align="center"><input class="submit" type="submit" name="modifier" /></td>
  </tr>
</table>
</form>';
	    }
	}
      
      // si il supprime une playlist
      if ($_GET['act'] == "del")
	{
	  // on supprime la playlist
	  $deletePlaylist = 'DELETE FROM `'.$clientInfos->login.'_'. $stream->id .'`.`playlist` WHERE `id` = '.$playlist->id.'';
	  $resultDeletePlaylist = mysql_query($deletePlaylist);
	  
	  $deleteMusiqueFromPlaylist = 'DELETE FROM `'.$clientInfos->login.'_'. $stream->id .'`.`musique_playlist` WHERE `id_playlist` = "'.$playlist->id.'"';
	  $resultDeleteMusiqueFromPlaylist = mysql_query($deleteMusiqueFromPlaylist);
	  
	  echo '<div align="center">Playlist supprime&eacute;e avec succ&egrave;s !</div>';
	}
      
      // si il modif une playlist
      if ($_GET['act'] == "modif")
	{
	  $selectPlaylistInfos = 'SELECT * FROM `'.$clientInfos->login.'_'. $stream->id .'`.`playlist` WHERE `id` = '.$_GET['id'].'';
	  $resultSelectPlaylistInfos = mysql_query($selectPlaylistInfos);
	  $playlistInfos = mysql_fetch_object($resultSelectPlaylistInfos);
	  
	  if ($_POST['modifier'])
	    {
	      // on update la base
	      $modifPlaylist = 'UPDATE `'.$clientInfos->login.'_'. $stream->id .'`.`playlist` SET `nom` = "'.htmlentities(trim($_POST['name'])).'"';
	      $resultModifPlaylist = mysql_query($modifPlaylist);
	      echo '<div align="center">Playlist modifi&eacute;e avec succ&egrave;s !</div>';
	    }
	  else 
	    {
	      // on affiche le formulaire
	      echo '<form action="" method="post">
<table border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td align="right">Nom de la playlist</td>
    <td coldspan="2"><input type=text" name="name" value="'.$playlistInfos->nom .'"/></td>
  </tr>
    <td>&nbsp;</td>
    <td align="center"><input class="submit" type="submit" name="modifier" /></td>
  </tr>
</table>
</form>';
	      
	    }
	}	  
      // s'il veut manager sa playlist (ajout musique etc)
      if ($_GET['act'] == "manage")
	{
	  if ($_POST['modifier'])
	    {
              // on update musique_playlist
	      $id_playlist = $_GET['id'];
	      
	      if (isset($_POST['checked'])) 
	      {
		$ids_to_keep = ''; 
		foreach ($_POST['checked'] as $id_musique) 
		{
		  $ids_to_keep .= (int) $id_musique .',';
		}
		$selectCheckedMusicToDel = 'SELECT id_musique FROM `'.$clientInfos->login.'_'.$stream->id.'`.`musique_playlist` WHERE `id_musique` NOT IN('. substr($ids_to_keep,0,-1).') AND `id_playlist` = "'.$id_playlist.'"';
		$resultSelectCheckedMusicToDel = mysql_query($selectCheckedMusicToDel);
		$ids_to_del = ''; 
		while ($row = mysql_fetch_assoc($resultSelectCheckedMusicToDel))
		  {
		    $ids_to_del .= (int) $row['id_musique'] .',';
		  }
		$DeleteCheckedMusic = 'DELETE FROM`'.$clientInfos->login.'_'.$stream->id.'`.`musique_playlist` WHERE `id_musique` IN('.substr($ids_to_del,0,-1).') AND `id_playlist` = "'.$id_playlist.'"';
		$resultDeleteCheckedMusic = mysql_query($DeleteCheckedMusic);
		
		// on ajoute les musiques cochees
		foreach ($_POST['checked'] as $id_musique) 
		{
		  $selectCheckedMusic = 'SELECT * FROM `'.$clientInfos->login.'_'.$stream->id.'`.`musique_playlist` WHERE `id_musique` = "'.$id_musique.'" AND `id_playlist` = "'.$id_playlist.'"';
		  $resultSelectCheckedMusic = mysql_query($selectCheckedMusic);
		  if (mysql_num_rows ($resultSelectCheckedMusic) != 1) 
		  {
		    $insertCheckedMusic = 'INSERT INTO `'.$clientInfos->login.'_'.$stream->id.'`.`musique_playlist` (`id_musique`, `id_playlist`) VALUES ("'.$id_musique.'", "'.$id_playlist.'")';
		    $resultInsertCheckedMusic = mysql_query($insertCheckedMusic);
		  }
		}		
		echo '<div align="center">TOUS LES ELEMENTS DE VOTRE PLAYLIST ON ETE MIS A JOUR.<br /><a href="index.php?page=playlist">Retour &agrave; vos playlists.</a></div>';
	      }
	      else 
	      {
		echo '<div align="center">AUCUN ELEMENT SELECTIONNE, VOUS DEVEZ SELECTIONNER AU MOINS UN TITRE.<br /><a href="index.php?page=playlist&act=manage&id='.$id_playlist.'">Retour &agrave; l\'&eacute;dition de votre playlist.</a></div>';
	      }
	    }
	  // sinon on affiche les musiques 
	  else
	    {
	      echo '<form action="" method="post">';
	      echo "<table>\n";
	      echo '<tr><th><b>Artiste</b></th><th><b>Titre</b></th><th><b>R&eacute;pertoire</b></th><th><b>Appartient</b></th></tr>'."\n";

	      // on cherche toutes les musiques qui sont dans la bases
	      $selectSearchMusique = 'SELECT * FROM `'.$clientInfos->login.'_'.$stream->id.'`.`musique` WHERE `filename` != ""  ORDER BY `artiste` ASC';
	      $resultSearchMusique = mysql_query($selectSearchMusique);
	      
	      // on affiche toutes les musiques de la base
	      while ($SearchMusique = mysql_fetch_object($resultSearchMusique))
		{

		  // on selectionne les musiques qui 
		  $selectMusiqueFromPlaylist = 'SELECT * FROM '.$clientInfos->login.'_'.$stream->id.'.musique,'.$clientInfos->login.'_'.$stream->id.'.musique_playlist WHERE '.$clientInfos->login.'_'.$stream->id.'.musique.id='.$clientInfos->login.'_'.$stream->id.'.musique_playlist.id_musique AND '.$clientInfos->login.'_'.$stream->id.'.musique_playlist.id_musique='.$SearchMusique->id;
		  $resultMusiqueFromPlaylist = mysql_query($selectMusiqueFromPlaylist);
		  $MusiqueFromPlaylist = mysql_fetch_object($resultMusiqueFromPlaylist);
		  
		  // a delete
		  $toDelete = "/home/w3c/streams/".$clientInfos->login."-".$stream->id."/public/playlist";

		  echo '<tr><td>'.$SearchMusique->artiste.'</td><td>'.$SearchMusique->titre.'</td><td>'.str_replace($toDelete, "", $SearchMusique->path)."/".$SearchMusique->filename.'</td><td><input type="checkbox" name="checked[]" value="'.$SearchMusique->id.'"'; 
		  if($MusiqueFromPlaylist->id_musique == $SearchMusique->id) 
		    { 
		      echo 'checked="1"'; 
		    } 
		  echo '></td></tr>'; 
		}
	      echo '<tr><td align="center"colspan="4"><input class="submit" type="submit" name="modifier" value="Envoyer" /></td></tr>';	      
	      echo '</table>';
	      echo "</form>";
	    }
	  
	}

      else 
	{      
	  echo '<ul><li><a href="index.php?page=playlist&act=add">Ajouter une playlist</a></li></ul>';
	  echo '<hr>';
	  $req = "SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`playlist` ORDER BY `nom` ASC";
	  $requete = mysql_query($req);
	  
	  // s'il existe des playlist on les affiche
	  if(mysql_num_rows($requete) > 0)
	    {
	      echo '<p>Voici la liste de vos playlists :</p>';
	  echo "<table>\n";
	  echo '<tr><th width="70%"><b>Nom</b></th><th width="30%"><b>Action</b></th></tr>'."\n";
	  while($affichagePlaylist = mysql_fetch_assoc($requete))
	    {
	      echo "<tr>\n<td>".$affichagePlaylist['nom']."</td>";
	      echo '<td><a href="index.php?page=playlist&act=modif&id='.$affichagePlaylist['id'].'">Modifier</a>&nbsp;<a href="index.php?page=playlist&act=del&id='.$affichagePlaylist['id'].'">Supprimer</a>&nbsp;<a href="index.php?page=playlist&act=manage&id='.$affichagePlaylist['id'].'">G&eacute;rer</a></td>';
	    }
	  echo "</table>";
	  
	    }
	  // sinon
	  else
	    {
	      echo '<div align="center">Vous n\'avez pas de playlist ! Cliquez <a href="index.php?page=playlist&act=add"><b>ici</b></a> pour en ajouter une.</div>';
	    }
	}
?>
	  
</div>

<?php
    }
  else
    {
      echo '<div align="center">Vous ne poss&eacute;dez pas de stream.</div>';
    }
echo "</div>";
}
else
{

// si l'utilisateur est pas log, erreur
?>

<div class="boxtitle">Erreur</div>
   <div class="content_content">
   Vous n'etes pas identifi&eacute;.
   <div>


<?php
}
?>