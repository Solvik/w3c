<?php

if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if($compte->getStream()->hasStream() === false) { include VIEW.'pas_de_stream.html'; }
else {
	$stream = $compte->getStream();
	include VIEW.'stream.html';
}

