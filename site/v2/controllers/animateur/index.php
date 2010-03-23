<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Musiques.php';

if(!$compte->getStream()->hasStream())
  include 'views/stream/pas_de_stream.html';
 else
   {
     $stream = $compte->getStream();

     if (!empty($_GET['add_animateur']))
       {
	 echo "toto";
       }
     elseif (!empty($_GET['add_horaire']))
       {
	 echo "toto";
       }
     else
       include VIEW.'animateur.html';
   }
