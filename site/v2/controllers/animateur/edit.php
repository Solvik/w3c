<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Animateur.php';

if (isset($_GET['id']))
    $id = intval($_GET['id']);
if (isset($_POST['edit']))
  {
    // Si le type est animateur
    if (isset($_GET['type']) && $_GET['type'] == "animateur")
      {
	$animateur = new Animateur($compte, $id);
	if ($animateur)
	  {
	    $animateur->name = htmlspecialchars($_POST['animateur']);
	    $animateur->password = htmlspecialchars($_POST['password']);
	    $animateur->save();

	    echo "success";
	    //include 'views/animateur/edit-success.html';
	  }
	else
	  echo "lol";
      }
    // Sinon c'est un creneaux
    else
      {
	// on update le creneaux lolz
      }
  }
 else
   {
     $animateur = new Animateur($compte, $id);
     if ($animateur)
       include VIEW.'edit-animateur.html';
   }
