<?php

function existance_champ($champ,$retour_faux,$retour_vrai="") {
        if (!isset($champ) || trim($champ)=="") {
                return $retour_faux;
		}
        else
        {
		return $retour_vrai;
        }
}

function test_email($mail,$retour_faux,$retour_vrai="")
{
	if(!preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]_?])*@[[:alnum:]]([-.]?[[:alnum:]])+\.([a-z]{2,6})$`', $mail)) {
                return $retour_faux;
        }
        else
	{
                return $retour_vrai;
		}
}

function contenu_champ($champ,$retour_faux,$retour_vrai="")
{
        if(!preg_match('/^[a-zA-Z0-9]+$/', $champ)) {
                return $retour_faux;
        }
        else
        {
                return $retour_vrai;
        }
}

?>