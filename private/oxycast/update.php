<?php
require_once('/usr/share/php-getid3/getid3.php');

require("conf.php");

$in = array();
$out = array();

$req = mysql_query('SELECT filename,path FROM musique WHERE filename != "" OR path != ""');
while ($data = mysql_fetch_assoc($req)) 
{
	$in[] = $data['path']."/".$data['filename'];
}
//print_r($in);

function Explore($dir) 
{
  global $in;
  global $out;
	
  if ($handle = opendir($dir)) 
    {
      while (false !== ($file = readdir($handle))) 
	{
	  if ($file != "." && $file != "..") 
	    {
	      if (is_dir($dir."/".$file)) 
		{
		  Explore($dir."/".$file);
		} 
	      else 
		{
		  if (preg_match('((.*?)\.mp3)', $file) || preg_match('((.*?)\.ogg)', $file) || preg_match('((.*?)\.flac)', $file)) 
		    {
		      if (!in_array($dir."/".$file, $in)) 
			{
			  echo $dir."/".$file." ajouté\n";
			  if (preg_match('((.*?)\.mp3)', $file))
			    $titre = str_replace(".mp3", "", $file);
			  if (preg_match('((.*?)\.ogg)', $file))
			    $titre = str_replace(".ogg", "", $file);
			  if (preg_match('((.*?)\.flac)', $file))
			    $titre = str_replace(".flac", "", $file);
			  $info = explode(" - ", $titre);
			  if (mysql_num_rows(mysql_query('SELECT id FROM musique  WHERE titre = "'.$info[1].'" AND artiste = "'.$info[0].'"')) == 1) 
			    {
			      mysql_query('UPDATE musique SET path = "'.$dir.'", filename = "'.$file.'" WHERE titre = "'.$info[1].'" AND artiste = "'.$info[0].'" LIMIT 1');
			    } 
			  else 
			    {
			      mysql_query('INSERT INTO musique  ( `titre` , `artiste` , `filename` , `path` ) VALUES ( "'.$info[1].'", "'.$info[0].'", "'.$file.'", "'.$dir.'")');
			    }
							
			  $getID3 = new getID3;
			  $ThisFileInfo = $getID3->analyze($dir."/".$file);
			  getid3_lib::CopyTagsToComments($ThisFileInfo);
			  if ($ThisFileInfo["tags_html"]["id3v1"]["title"][0] != "" && $ThisFileInfo["tags_html"]["id3v1"]["artist"][0] != "") 
			    {
			      preg_match_all('(licenses\/(.*?)\/)', $ThisFileInfo["tags_html"]["id3v2"]["copyright"][0], $matches, PREG_SET_ORDER);
			      $sql = 'UPDATE musique SET album = "'.html_entity_decode($ThisFileInfo["tags_html"]["id3v1"]["album"][0]).'",
licence = "cc-'.$matches[0][1].'", titre = "'.html_entity_decode($ThisFileInfo["tags_html"]["id3v1"]["title"][0]).'",
artiste = "'.html_entity_decode($ThisFileInfo["tags_html"]["id3v1"]["artist"][0]).'", 
lien_chanson = "'.$ThisFileInfo["tags_html"]["id3v2"]["url_file"][0].'", 
lien_album = "'.$ThisFileInfo["tags_html"]["id3v2"]["url_source"][0].'" 
WHERE path = "'.$dir.'" AND filename = "'.$file.'" LIMIT 1';
								
			      mysql_query($sql);
			    }
			} 
		      else 
			{
			  $out[] = $dir."/".$file;
			}
		    }
		}
	    }
	}
      closedir($handle);
    }
}


Explore("/home/oxycast/".PATH."public/playlist");


$suppr = array_diff($in, $out);

foreach ($suppr as $key => $value) 
{
  $p = explode("/", $value);
  $filename = $p[count($p) - 1];
  $path = str_replace("/".$filename, "", $value);
  mysql_query('UPDATE musique SET path = "", filename = "" WHERE filename = "'.$filename.'" AND path = "'.$path.'" ');
  echo $filename." supprimé\n";
}
?>
