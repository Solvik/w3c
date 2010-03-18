<?php

if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if($compte->getStream()->hasStream() === false) { include VIEW.'pas_de_stream.html'; }
else
{
	$stream = $compte->getStream();
	if(isset($_POST['modifier']))
	{
		if (!empty($_POST['nom']) AND !empty($_POST['mountpoint']) AND !empty($_POST['password']))
		{
			if(!Stream::exist('nom', $_POST['nom'], $compte->getStream()->nom))
			{
				if(!Stream::exist('mountpoint', $_POST['mountpoint'], $compte->getStream()->mountpoint))
				{
					if(ereg("^[[:alnum:]]+$", trim($_POST['nom'])) AND ereg("^[[:alnum:]]+$", trim($_POST['mountpoint'])) AND ereg("^[[:alnum:]]+$", trim($_POST['password'])))
					{
						//Enfin !
						$stream = $compte->getStream();
						$stream->nom			= htmlspecialchars(trim($_POST['nom']));
						$stream->mountpoint		= htmlspecialchars(trim($_POST['mountpoint']));
						$stream->password		= htmlspecialchars(trim($_POST['password']));
						$stream->description	= htmlspecialchars($_POST['description']);
						$stream->genre			= htmlspecialchars($_POST['genre']);
						$stream->url			= htmlspecialchars($_POST['url']);
						if($_POST['format_live'] == 'mp3' OR $_POST['format_live'] == 'ogg')
							$stream->format_live    = $_POST['format_live'];
						else $stream->format_live   = 'mp3';
						if($_POST['format_output'] == 'mp3' OR $_POST['format_output'] == 'ogg')
							$stream->format_output    = $_POST['format_output'];
						else $stream->format_output   = 'mp3';
						$stream->status = 'change_password';
						$stream->nb_jingles = intval($_POST['nb_jingles']);
						$stream->start_before = intval($_POST['start_before']);
						$stream->skip_blank_sec = intval($_POST['skip_blank_sec']);
						$stream->skip_blank_db = intval($_POST['skip_blank_db']);
						$stream->skip_blank_mail = trim($_POST['skip_blank_mail']);
						$stream->save();
						
						//echo '<p style="text-align: center;color: green;"><strong>Les modifications ont &eacute;t&eacute; effectu&eacute;es.</strong></p>';
						
						$erreur = 'Les modifications ont &eacute;t&eacute; effectu&eacute;es.';
						include VIEW.'stream.html';
					} else {
						$erreur = 'Le nom du stream, mountpoint et le mot de passe doivent &ecirc;tre uniquement compos&eacute;s de caract&egrave;res alphanum&eacute;riques (sans espaces).';
						include VIEW.'stream.html';
					}
				} else {
					$erreur = 'Le mountpoint est d&eacute;j&agrave; utilis&eacute;.';
					include VIEW.'stream.html';
				}
			} else {
				$erreur = 'Le nom du stream est d&eacute;j&agrave; utilis&eacute;.';
				include VIEW.'stream.html';
			}
		} else {
			$erreur = 'Veuillez remplir tous les champs marqu&eacute; d\'une &eacute;toile.';
			include VIEW.'stream.html';
		}
	} else {
		$erreur = 'Veuillez remplir tous les champs marqu&eacute; d\'une &eacute;toile.';
		include VIEW.'stream.html';
	}
}

