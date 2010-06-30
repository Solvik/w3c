<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Animateur.php';

if (isset($_GET['id']))
    $id = intval($_GET['id']);
if (isset($_POST['edit']))
{
    // Type = animateur
    if (isset($_GET['type']) && $_GET['type'] == "animateur")
    {
		$animateur = new Animateur($compte, $id);
		if ($animateur)
		{
			$animateur->name = htmlspecialchars($_POST['animateur']);
			$animateur->password = htmlspecialchars($_POST['password']);
			$animateur->save();

			header('Location: animateur');
		}
		else
		  echo 'erreur';
    }
	/*elseif ($_GET['type']) && $_GET['type'] == "creneau") //TODO: Creneaux
    { }*/
}
else
{
    $animateur = new Animateur($compte, $id);
    if ($animateur)
		include VIEW.'edit-animateur.html';
}
