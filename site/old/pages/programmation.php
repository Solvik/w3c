<?php
if (isset($_SESSION['login']))
{
  $reqClient = "SELECT * FROM `clients` WHERE `login` = '".$_SESSION['login']."'";
  $requeteClient = mysql_query($reqClient);
  $clientInfos = mysql_fetch_object($requeteClient);

  $reqStream = "SELECT * FROM `streams` WHERE clientID='".$clientInfos->id."'";
  $requeteStream = mysql_query($reqStream);
  $stream = mysql_fetch_object($requeteStream);

  if ($stream)
  {

   $jours = array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche');

  if(isset($_POST['ajout'])) {
    $type = $_POST['type'];
    $h_debut = $_POST['hdebut'];
    $h_fin = $_POST['hfin'];
    $jour = $_GET['jour'];
    $action = $_POST['action'];
      //on verifie d'abord si y'a pas un truc planifie a cette heure
      $req_select_planification = "SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`planification` WHERE `jour` = '".$jour."' AND `heure_debut` >= '".$h_debut."' AND `heure_fin` <= '".$h_fin."'"; 
      $res_select_planification = mysql_query($req_select_planification);
      if (mysql_num_rows($res_select_planification) != 0) {
	$retour = '<div align="center"><b>Une planification existe d&eacute;j&agrave; pour ce jour / cette heure</b></div>';;
      }
      else {
	$req_insert_planification = "INSERT INTO `".$clientInfos->login."_".$stream->id."`.`planification` (`type`, `jour`,`heure_debut`,`heure_fin`, `action`) VALUES ('".$type."', '".$jour."', '".$h_debut."', '".$h_fin."', '".$action."')";
	$res_insert_planification = mysql_query($req_insert_planification);
	$retour = '<div align="center"><b>Planification ajout&eacute;e</b></div>';
      }
  }
  if(isset($_POST['modif'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];
    $h_debut = $_POST['hdebut'];
    $h_fin = $_POST['hfin'];
    $jour = $_GET['jour'];
    $action = $_POST['action'];
      //on verifie d'abord si y'a pas un truc planifie a cette heure
      $req_select_planification = "SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`planification` WHERE `jour` = '".$jour."' AND `heure_debut` >= '".$h_debut."' AND `heure_fin` <= '".$h_fin."' AND `id` != '".$id."'"; 
      $res_select_planification = mysql_query($req_select_planification);
      if (mysql_num_rows($res_select_planification) != 0) {
	$retour = '<div align="center"><b>une planification existe deja pour cette heure</b></div>';
      }
      else {
	$req_insert_planification = "UPDATE `".$clientInfos->login."_".$stream->id."`.`planification` SET `type` = '".$type."', `jour` = '".$jour."', `heure_debut` = '".$h_debut."', `heure_fin` = '".$h_fin."', `action` = '".$action."' WHERE `id` = '".$id."'";
	echo $req_insert_planification;
	$res_insert_planification = mysql_query($req_insert_planification);
	$retour = '<div align="center"><b>Planification modifi&eacute;e</b></div>';
      }
  }

?>

<div class="boxtitle">Ma Programmation</div>
<div class="content_content">

<br /><br/>

<?php
   if(!isset($_GET['jour'])) {
?>
<table border="1" align="center" cellpadding="5" cellspacing="0">
<tr>
  <td colspan="7" align="center"><h2>Selectionnez un jour</h2></td>
</tr>
<tr height="100">
  <td align="center" width="100"><a href="programmation-1">Lundi</a></td>
  <td align="center" width="100"><a href="programmation-2">Mardi</a></td>
  <td align="center" width="100"><a href="programmation-3">Mercredi</a></td>
  <td align="center" width="100"><a href="programmation-4">Jeudi</a></td>
  <td align="center" width="100"><a href="programmation-5">Vendredi</a></td>
  <td align="center" width="100"><a href="programmation-6">Samedi</a></td>
  <td align="center" width="100"><a href="programmation-7">Dimanche</a></td>
</tr>
</table>
<?php
   }
   else {
?>
<form action="?jour=<?$_GET['jour']?>" method="post">
<table align="center">
<tr>
   <td align="center">Type</td>
   <td align="center">Heure d&eacute;but</td>
   <td align="center">Heure fin</td>
   <td align="center">Action</td>
</tr>

<?php
  $select_all_planif = "SELECT * FROM  `".$clientInfos->login."_".$stream->id."`.`planification` WHERE `jour`='".$_GET['jour']."' GROUP BY `heure_debut` ORDER BY `heure_debut`";
  $res_select_all_planif = mysql_query($select_all_planif);
  if(@mysql_num_rows($res_select_all_planif) != 0) {
    while ($liste_planif = mysql_fetch_object($res_select_all_planif)) {
?>

<tr>
   <td>
<input type="hidden" name="id" value="<?=$liste_planif->id?>" />
<select onChange="this.form.submit()" name="type_modif">
  <option value="playlist" <?=$sel=$liste_planif->type=="playlist"?"selected='selected'":"";?>>Playlist</option>
  <option value="podcast" <?=$sel=$liste_planif->type=="podcast"?"selected='selected'":"";?>>Podcast</option>
</select>
  </td>
<td><select name="hdebut">
<?php 
$a="0"; 
while ($a < 25) { 
?>
<option value="<?=$a?>" <?=$hsel=$liste_planif->heure_debut==$a?"selected":""?>><?=$a?> h</option>
<?php
$a++; 
} 
?>
</select></td>
<td><select name="hfin">
<?php 
$a="0"; 
while ($a < 25) { 
?>
<option value="<?=$a?>" <?=$hsel=$liste_planif->heure_fin==$a?"selected":""?>><?=$a?> h</option>
<?php
$a++; 
} 
?>
</select></td>

<?php
 if ($liste_planif->type=="playlist" || (isset($_POST['type_modif']) && $_POST['type_modif']=="playlist")) {
?> 
   <td>
     <select name="action">
<?php
   $reqSelectPlaylists="SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`playlist` ORDER BY `nom` ASC";
   $resultSelectPlaylist = mysql_query($reqSelectPlaylists);
   while ($listePlaylist = mysql_fetch_object($resultSelectPlaylist)) {
     echo "<option value='".$listePlaylist->id."'>".$listePlaylist->nom."</option>";
   }
?>
     </select>
  </td>
<?php
      }
 if ($liste_planif->type=="podcast" || (isset($_POST['type_modif']) && $_POST['type_modif']=="podcast")) {
  ?>
<td>
   <select name="action">
   <?php
 $reqSelectPodcasts="SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`musique` WHERE `path` LIKE '%/podcast' ORDER BY `filename` ASC";
 $resultSelectPodcast = mysql_query($reqSelectPodcasts);
 while ($listePodcast = mysql_fetch_object($resultSelectPodcast)) {
   echo "<option value='".$listePodcast->id."'>".$listePodcast->filename."</option>";
 }
   ?>
    </select>
</td>
<?php
  }
?>
  <td><input type="submit" value="Modifier" name="modif" /></td>
</tr>
<?php
     }
  }
?>

<tr>
   <td>
<select onChange="this.form.submit()" name="type">
  <option value="choix">Choisissez...</option>
  <option value="playlist" <?=$sel=$_POST['type']=="playlist"?"selected='selected'":"";?>>Playlist</option>
  <option value="podcast" <?=$sel=$_POST['type']=="podcast"?"selected='selected'":"";?>>Podcast</option>
</select>
  </td>
<?php
if (isset($_POST['type']) && $_POST['type'] != "choix") {
?>
<td><select name="hdebut"><?php $a="0"; while ($a < 25) { echo '<option value="'.$a.'">'.$a.'h</option>'; $a++; } ?></select></td>
  <td><select name="hfin"><?php $a="0"; while ($a < 25) { echo '<option value="'.$a.'">'.$a.'h</option>'; $a++; } ?></select></td>
<?php
     }
 if (isset($_POST['type']) && $_POST['type']=="playlist") {
?> 
   <td>
     <select name="action">
<?php
      $reqSelectPlaylists="SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`playlist` ORDER BY `nom` ASC";
   $resultSelectPlaylist = mysql_query($reqSelectPlaylists);
   while ($listePlaylist = mysql_fetch_object($resultSelectPlaylist)) {
     echo "<option value='".$listePlaylist->id."'>".$listePlaylist->nom."</option>";
   }
?>
     </select>
  </td>
<?php
      }
  if (isset($_POST['type']) && $_POST['type']=="podcast") {
?>
<td>
    <select name="action">
<?php
   $reqSelectPodcasts="SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`musique` WHERE `path` LIKE '%/podcast' ORDER BY `filename` ASC";
   $resultSelectPodcast = mysql_query($reqSelectPodcasts);
   while ($listePodcast = mysql_fetch_object($resultSelectPodcast)) {
     echo "<option value='".$listePodcast->id."'>".$listePodcast->filename."</option>";
   }
?>
    </select>
</td>
<?php
  }
?>
<?php
if (isset($_POST['type']) && $_POST['type'] != "choix") {
?>
  <td><input type="submit" value="Ajouter" name="ajout" /></td>
<?php
     }
?>
</tr>
</table>
</form>

<div align="center"><a href="programmation">Retour vers la programmation</a></div>

<?php
    }
      if ($retour) {
	echo $retour;
      }
?>
</div>

<?php
  }
  else
  {
    echo '<div class="boxtitle">Ma Programmation</div>
<div class="content_content"><div align="center">Vous ne poss&eacute;dez pas de stream.</div></div>';
  }
}
else
{
?>

<div class="boxtitle">Erreur</div>
   <div class="content_content">
   Vous n'etes pas identifi&eacute;.
   <div>


<?php
}
?>
