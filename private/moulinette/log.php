<?php


function write_log($string)
{
  /*
   ** On set le nom du fichier de log
   */
  $log_file = "mainlog.log";
 
  /*
   ** on ouvre, on ecrit on ferme
   */
  $fd = fopen($log_file, "a+");
  fwrite($fd, date("r").": ");
  fwrite($fd, $string);
  fclose($fd);
}

?>