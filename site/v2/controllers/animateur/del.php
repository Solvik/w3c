<?php
include MODEL.'Musiques.php';
include MODEL.'Animateur.php';
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

$animId = intval($_GET['id']);
$ret = Animateurs::deleteAnim($compte, $animId);

if ($ret == 1)
  include VIEW.'suppression-animateur-ok.html';
else
  include VIEW.'suppression-animateur-fail.html';

       