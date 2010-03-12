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

   $orderBy = array('id', 'artiste', 'titre', 'filename');
   $sens = array('asc', 'desc');

   if (!empty($_GET['order']) AND in_array($_GET['order'], $orderBy))
     {
       if (!empty($_GET['sens']) AND in_array($_GET['sens'], $sens))
	 $musiques = getMusicList($compte, $compte->getStream(), $_GET['order'], $_GET['sens']);
       else
	 $musiques = getMusicList($compte, $compte->getStream(), $_GET['order'], 'ASC');	 
     }
   else
     $musiques = getMusicList($compte, $compte->getStream());

   include VIEW.'musiques.html';	
 }

