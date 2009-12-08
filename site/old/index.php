<?php

session_start();

include('includes/config.php');
include('includes/fonctions.php');

$db = mysql_connect($host,$user,$password);
mysql_select_db($base, $db);


if (isset($_GET["page"]) and $_GET["page"] != '') { $page=htmlentities($_GET["page"]); } else { $page="accueil"; }
if (isset($_GET["act"]) and $_GET["act"] != '') { $act=htmlentities($_GET["act"]); } else { $act=""; }
if (isset($_GET["id"]) and $_GET["id"] != '') { $id=htmlentities($_GET["id"]); } else { $id=""; }


// on teste si le visiteur a soumis le formulaire de connexion  
if (isset($_POST['connexion'])) { 
   if ((isset($_POST['login']) && !empty($_POST['login'])) && (isset($_POST['pass']) && !empty($_POST['pass']))) { 
      
      // on teste si une entrée de la base contient ce couple login / pass 
      $sql = 'SELECT count(*) FROM clients WHERE login="'.mysql_escape_string($_POST['login']).'" AND password="'.md5(mysql_escape_string($_POST['pass'])).'" AND cle_conf = \'\''; 
      $req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
      $data = mysql_fetch_array($req); 
     
      mysql_free_result($req);
      
      // si on obtient une réponse, alors l'utilisateur est un membre 
      if ($data[0] == 1) { 
         $_SESSION['login'] = $_POST['login'];
	 //on verifie si la personne est admin
	 $requete = 'SELECT * FROM `clients` WHERE `login`="'.mysql_escape_string($_POST['login']).'" AND `admin` = "1"';
	 $select_membre = mysql_query($requete);
	 if (mysql_num_rows($select_membre) == 1) {
	   // si la personne est admin on fait une variable
	   $_SESSION['is_admin'] = 1;
	 }
         header('Location: index.php?page=admin'); 
         exit(); 
      }
      // si on ne trouve aucune réponse, le visiteur s'est trompé soit dans son login, soit dans son mot de passe 
      elseif ($data[0] == 0) { 
         $erreur = 'Compte inexistant ou non activ&eacute;.'; 
      }
	  
      // sinon, alors la, il y a un gros problème :) 
      else { 
         $erreur = 'Problème dans la base de données : plusieurs membres ont les mêmes identifiants de connexion.'; 
      } 

   } 
   else { 
      $erreur = 'Au moins un des champs est vide.'; 
   }  
}  

if(isset($_GET['deconnexion'])) {
  session_unset();
  session_destroy();
  header('Location:index.php');
}

$pages_available = array('news', 'admin', 'support', 'inscription', 'support', 'modif-infos', 'pass-perdu', 'offres', 'deconnexion', 'cgu', 'apropos', 'stream', 'root', 'playlist', 'musique', 'programmation', 'contact', 'mon-site');

include('includes/inc.header.php');
@mysql_close($db); //on affiche pas d'erreur si la connexion n'a pas ete fermee


include('includes/inc.footer.php');

?>
