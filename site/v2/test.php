<?php
error_reporting(E_ALL);
include 'global/init.php';
include 'models/Member.class.php';
include 'models/Stream.class.php';
include 'models/Commande.class.php';


$account = new member(Member::EXISTANT, 'alfo2');
$account->pass = base64_encode(hash('ripemd160', pack('H*', sha1(trim('wdFivuWazerty:O a big salt !')))));
$account->save();

$commande = new Commande(0);
$commande->create(73, 'paypal', 'waiting', '50.00', 1, 'email@mail.com');
/*echo '<pre>';
echo var_dump($account);
$stream = new Stream(0);
echo var_dump($stream->hasStream());
echo '</pre>';*/
//$pass = 'password';
//echo base64_encode(hash('ripemd160', pack('H*', sha1(trim('wdFivuW'.$pass.':O a big salt !')))));
//echo $account->getStream()->ip_serveur;
/*echo '<pre>';
echo var_dump($account).'</pre>';

$account->mail = 'mail';
$account->save();

echo '<pre>';
echo var_dump($account).'</pre>';*/
//echo $account->id;
//echo $account->login;
