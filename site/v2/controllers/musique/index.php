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
	
	$musiques = getMusicList($compte, $compte->getStream());
	include VIEW.'musiques.html';	

}

