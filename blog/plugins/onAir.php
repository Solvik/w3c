<?php
/*
Plugin Name: Oxycast On Air
Plugin URI: http://oxycast.net/
Description: Affiche le titre en cours de lecture.
Author: Martin 'MoAdiB' GUIBERT
Version: 1
Author URI: http://moadib.net/
*/
function onAIR() 
{
  global $wpdb;
  $lastSongs = $wpdb->get_results("SELECT artiste,titre,dernier_passage FROM musique ORDER BY dernier_passage LIMIT 0,1");
  echo '<ul>';
  foreach ($lastSongs as $lastSong) {
	echo '<li>'.$lastSong->artiste.' - '.$lastSong->titre.'</li>';
  }
  echo '</ul>';
}

function widget_onAIR($args) {
  extract($args);
  $options = get_option('widget_onAIR');
  echo $before_widget;
  echo $before_title.$options['title'].$after_title;
  onAIR();
  echo $after_widget;
}

function widget_onAIR_control() {
	$options = get_option('widget_onAIR');
	if (!is_array($options))
	{
		$options = array('title'=>'Titre en cours:');
	}
	if ($_POST['onAIR-submit']) {
		$options['title'] = strip_tags(stripslashes($_POST['onAIR-title']));
		update_option('widget_onAIR', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);

	echo '<p style="text-align:right;">';
	echo '<label for="onAIR-title">' . __('Titre:') . ' <input style="width: 200px;" id="onAIR-title" name="onAIR-title" type="text" value="'.$title.'" /></label>';
	echo '</p>';
	echo '<input type="hidden" id="onAIR-submit" name="onAIR-submit" value="1" />';
}

function onAIR_init()
{
  register_sidebar_widget(__('Oxycast onAIR'), 'widget_onAIR');
  register_widget_control(array('Oxycast onAIR', 'widget_onAIR'), 'widget_onAIR_control', 300, 100);  
}
add_action("plugins_loaded", "onAIR_init");
?>