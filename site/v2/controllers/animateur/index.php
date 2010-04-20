<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Animateur.php';

if(!$compte->getStream()->hasStream())
  include 'views/stream/pas_de_stream.html';
 else
   {
     $stream = $compte->getStream();
     include VIEW.'animateur.html';
   }
