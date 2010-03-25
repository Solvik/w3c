<?php

function getAnim(Member $compte, Stream $stream)
{
  @$pdo = UserDS::getInstance($compte->login."_".$stream->id);
  $query = 'SELECT * FROM `animateur`';
  $anim = array();

  $i = 0;
  foreach ($pdo->query($query) as $result)
    {
      $anim[$i] = array('id'	=> $result['id'],
			'nom'	=> utf8_encode($result['nom']));
      $i++;
    }
  return ($anim);
}