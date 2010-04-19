<?php
include MODEL.'Musiques.php';
include MODEL.'Animateur.php';
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

$id = intval($_GET['id']);
if ($_GET['type'] == "animateur")
  {
    $ret = Animateur::deleteAnim($compte, $id);

    if ($ret == 1)
      include VIEW.'suppression-animateur-ok.html';
    else
      include VIEW.'suppression-animateur-fail.html';
  }
else
  Creneaux::deleteCreneaux($compte, $id);
       