<?php
/*
Plugin Name: Oxycast Last Played
Plugin URI: http://oxycast.net/
Description: Affiche les n derniers titres de votre stream oxycast.
Author: Martin 'MoAdiB' GUIBERT
Version: 1
Author URI: http://moadib.net/
*/
function lastPlayed() 
{
  global $wpdb;
  $options = get_option('widget_lastPlayed');
  $lastSongs = $wpdb->get_results("SELECT artiste,titre,dernier_passage FROM musique ORDER BY dernier_passage LIMIT 1,".$options['limit']);
  echo '<ul>';
  foreach ($lastSongs as $lastSong) {
	echo '<li>'.$lastSong->artiste.' - '.$lastSong->titre.'</li>';
  }
  echo '</ul>';
}

function widget_lastPlayed($args) {
  extract($args);
  $options = get_option('widget_lastPlayed');
  echo $before_widget;
  echo $before_title.$options['title'].$after_title;
  lastPlayed();
  echo $after_widget;
}

function widget_lastPlayed_control() {
	$options = get_option('widget_lastPlayed');
	if (!is_array($options))
	{
		$options = array('title'=>'Derniers Titres',
						 'limit'=>'10');
	}
	if ($_POST['lastPlayed-submit']) {
		$options['title'] = strip_tags(stripslashes($_POST['lastPlayed-title']));
		if (is_numeric($_POST['lastPlayed-limit']))
			$options['limit'] = $_POST['lastPlayed-limit'];
		update_option('widget_lastPlayed', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
	$limit = $options['limit'];
	
	echo '<p style="text-align:right;">';
	echo '<label for="lastPlayed-title">' . __('Titre:') . ' <input style="width: 200px;" id="lastPlayed-title" name="lastPlayed-title" type="text" value="'.$title.'" /></label>';
	echo '<label for="lastPlayed-limit">' . __('Nombre de titre maximum:') . ' <input style="width: 200px;" id="lastPlayed-limit" name="lastPlayed-limit" type="text" value="'.$limit.'" /></label>';
	echo '</p>';
	echo '<input type="hidden" id="lastPlayed-submit" name="lastPlayed-submit" value="1" />';
}

function lastPlayed_init()
{
  register_sidebar_widget(__('Oxycast Last Played'), 'widget_lastPlayed');
  register_widget_control(array('Oxycast Last Played', 'widget_lastPlayed'), 'widget_lastPlayed_control', 300, 100);  
}
add_action("plugins_loaded", "lastPlayed_init");
?>