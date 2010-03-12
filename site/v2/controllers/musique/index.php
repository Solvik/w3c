<?php
include MODEL.'Musiques.php';

if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if($compte->getStream()->hasStream() === false) { include VIEW.'pas_de_stream.html'; }
 else {
   $stream = $compte->getStream();
	
   if(isset($_GET['update']))
     {
       exec ('curl http://'.$stream->ip_serveur.'/'.$compte->login.'-'.$stream->id.'/update.php --user '.UPDATE_USER.':'.UPDATE_PASS);
       $info = 'Mise &agrave; jour r&eacute;ussie !';
     } 

   $orderBy = array("id", "artiste", "titre", "filename");
   $sens = array("ASC", "DESC");

   if (isset($_GET['order']) && in_array($_GET['order'], $orderBy))
     {
       if (isset($_GET['sens']) && in_array($_GET['sens'], $sens))
	 $musiques = getMusicList($compte, $compte->getStream(), $_GET['order'], $_GET['sens']);
       else
	 $musiques = getMusicList($compte, $compte->getStream(), $_GET['order'], "ASC");	 
     }
   else
     $musiques = getMusicList($compte, $compte->getStream(), "id", "ASC");

   include VIEW.'musiques.html';	
 }

