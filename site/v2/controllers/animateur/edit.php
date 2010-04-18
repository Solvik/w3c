<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

$id = intval($_GET['id']);
if (isset($_POST['edit']))
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
}
else
{
	$animateur = new Animateur($compte, $id);
	if ($animateur) include VIEW.'edit.html';
}
