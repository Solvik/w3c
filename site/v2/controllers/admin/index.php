<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if ($compte->isAdmin != 1)
  {
    include 'global/accueil.php';
  }
else
  {

    //
    // La faut recuperer les donnes des clients et opopop :)
    //

    include VIEW.'admin.html';
  }