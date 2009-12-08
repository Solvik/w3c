<?php
$message="";

if (isset($_POST['send']) and $_POST['send'] != "")
{
        if (isset($_POST['login']) and trim($_POST['login']) != "") { $login=htmlentities($_POST['login']); } else { $login=""; }
        if (isset($_POST['mail']) and trim($_POST['mail']) != "") { $mail=htmlentities($_POST['mail']); } else { $mail=""; }
        
        if ($mail == "" and $login == "")
        {
            $message="Vous devez entrer votre login ou votre adresse mail.";
        }

        if ($mail != "" and $login != "")
        {
            $message="Vous devez entrer soit votre login soit votre adresse mail, mais pas les deux.";
        }

        if ($mail != "" and $message=="")
        {
            $req1 = "select login,recover FROM `clients` where mail='$mail' LIMIT 1;";
            $requete1 = mysql_query($req1);
            $recover = mysql_fetch_array($requete1);
            if ($recover['login']=="")
            {
                 $message="Cette adresse mail ne correspond &agrave; aucun compte.";
            }
            if ($recover['recover']!="0" and $recover['recover']>time()-3600 and $message=="")
            {
                $message="Cette adresse mail fait d&eacute;j&agrave; l'objet d'une demande de r&eacute;cup&eacute;ration de mot de passe. Vous devez attendre 1h entre chaque demande de mot de passe.";
            }
            if ($message=="")
            {
                $now = time();
                $req1 = "UPDATE `clients` SET recover = '$now' WHERE mail='$mail' LIMIT 1;";
                $requete1 = mysql_query($req1);
                
                $mail_txt="Bonjour,

Vous avez demandé à récupérer le mot de passe d'accès au site OxyCast.

Pour terminer la procédure, merci de vous rendre à la page :

http://www.oxycast.net/index.php?page=pass-perdu&recover=".md5($now)."

";

                $do_act=mail($mail,"OXYCAST.NET - Récupération du mot de passe",$mail_txt,"From: null@oxycast.net.\r\n"."Reply-To: null@oxycast.net\r\n");
                $message="Un message a été envoyé a l'adresse mail indiquée. Vous devriez le recevoir sous peu.";
            }
        }

        if ($login != "" and $message=="")
        {            
            $req1 = "select mail,recover FROM `clients` where login='$login' LIMIT 1;";
            $requete1 = mysql_query($req1);
            $recover = mysql_fetch_array($requete1);
            if ($recover['mail']=="")
            {                
                $message="Ce login ne correspond &agrave; aucun compte.";
            }
            if ($recover['recover']!="" && $recover['recover']!="0" and $recover['recover']>time()-3600 and $message=="")
            {
                $message="Ce login fait d&eacute;j&agrave; l'objet d'une demande de r&eacute;cup&eacute;ration de mot de passe. Vous devez attendre 1h entre chaque demande de mot de passe.";
            }
            if ($message=="")
            {
                $now = time();
                $req1 = "UPDATE `clients` SET recover = '$now' WHERE login='$login' LIMIT 1;";
                $requete1 = mysql_query($req1);

                $mail_txt="Bonjour,

Vous avez demandé à récupérer le mot de passe d'accès au site OxyCast.

Pour terminer la procédure, merci de vous rendre à la page :

http://www.oxycast.net/index.php?page=pass-perdu&recover=".md5($now)."

";

                $do_act=mail($recover['mail'],"OXYCAST.NET - Récupération du mot de passe",$mail_txt,"From: null@oxycast.net.\r\n"."Reply-To: null@oxycast.net\r\n");                
                $message="Un message a &eacute;t&eacute; envoy&eacute; a l'adresse mail correspondant au login indiqu&eacute;. Vous devriez le recevoir sous peu.";
            }
        }
        
        if ($message!="")
        {
            echo "<br /><br /><br /><div style=\"font-size: 12px; color: #FF0000; text-align: center;\"><b>$message</b></div><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><a href=\"password\"><b>Retour</b></a></div>";   
        }
}
elseif (isset($_GET['recover']) and $_GET['recover'] != "")
{
    $check = $_GET['recover'];
    $req1 = "select mail,login,recover FROM `clients` where MD5(recover)='$check' LIMIT 1;";
    $requete1 = mysql_query($req1);
    $recover = mysql_fetch_array($requete1);
    if ($recover['login']=="")
    {
        $message="La cl&eacute; de controle n'est pas valide.";
    }
    if ($recover['recover']<time()-3600)
    {
        $message="La cl&eacute; de controle n'est plus valide. Vous devez refaire une demande de mot de passe.";
    }
    if ($message=="")
    {
        $pass="";
        $pass=$pass.chr(rand(97,122));
        $pass=$pass.chr(rand(97,122));
        $pass=$pass.chr(rand(65,90));
        $pass=$pass.chr(rand(48,57));
        $pass=$pass.chr(rand(97,122));
        $pass=$pass.chr(rand(97,122));
        $pass=$pass.chr(rand(65,90));
        $pass=$pass.chr(rand(97,122));
        $pass=$pass.chr(rand(48,57));
        $pass=$pass.chr(rand(97,122));

        $req1 = "UPDATE `clients` SET recover = '0', password = MD5('$pass') WHERE MD5(recover)='$check' LIMIT 1;";
        $requete1 = mysql_query($req1);

        $message="Votre mot de passe a &eacute;t&eacute; r&eacute;initialis&eacute;. Votre nouveau mot de passe est<br /><br />$pass</br ><br />";
    }
    if ($message!="")
    {
        echo "<br /><br /><br /><div style=\"font-size: 12px; color: #FF0000; text-align: center;\"><b>$message</b></div><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><a href=\"index/\"><b>Retour</b></a></div>";   
    }
}
else
{
?>

<div class="boxtitle">Mot de passe perdu</div>
<div class="content_content">
  <p>Veuillez remplir le formulaire pour r&eacute;cup&eacute;rer votre mot de passe.</p>


<div align="center">
<br />
<form method="post" name="mdp" action="">
<label class="aligne">&nbsp;Login : </label>
<input type="text" name="login" size="16" value="" /><br /><br />
<b>OU</b><br /><br /><br />
<label class="aligne">&nbsp;Adresse mail : </label>
<input type="text" name="mail" size="16" value="" /><br /><br />
<input class="submit" type="submit" name="send" /> 
</form>
</div>

</div>

<?php } ?>
