<?php
/*
Plugin Name: Oxycast Last Played
Plugin URI: http://oxycast.org/
Description: Affiche les derniers titres de votre stream oxycast.
Author: Martin 'MoAdiB' GUIBERT
Version: 1
Author URI: http://moadib.net/
*/
function lastPlayed() 
{
  global $wpdb;
  $lastSongs = $wpdb->get_results("SELECT artiste,titre,dernier_passage FROM $wpdb->musique ORDER BY dernier_passage LIMIT 1,10";
  echo '<ul>';
  foreach ($lastSongs as $lastSong) {
	echo '<li>'.$lastSong->artiste.' - '$lastSong->titre.'</li>';
  }
  echo '</ul>';
}

function widget_lastPlayed($args) {
  extract($args);
  echo $before_widget;
  echo $before_title.'Derniers Titres'.$after_title;
  lastPlayed();
  echo $after_widget;
}

function lastPlayed_init()
{
  register_sidebar_widget(__('Oxycast Last Played'), 'widget_lastPlayed');     
}
add_action("plugins_loaded", "lastPlayed_init");
?>