<?php
if(is_online())
{
	session_unset();
	session_destroy();
	header('Location:index.php');
}
	