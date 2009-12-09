<?php

/*
 ** Generation des fichiers de configuration liquidsoap
 ** TODO : prendre en charge: 
 ** - relay
 ** - modification detection blanc
 */

function generateConf($filename,
		      $login, $idStream, $port, $password,
		      $format_live, $format_output,
		      $description, $url, $genre, $nom, $bitrate, $mountpoint)
{
  $fp = fopen($filename, 'w');
  fwrite($fp, '%include "/home/oxycast/liquidsoap/utils.liq'."\"\n\n");
  fwrite($fp, "# On define les trucs de bases\n");
  fwrite($fp, 'racine = "/home/oxycast/streams/'.$login.'-'.$idStream.'/"'."\n");
  fwrite($fp, 'set("log.file.path",racine ^"logs/logfile.log")'."\n");
  fwrite($fp, 'set("log.level",5)'."\n\n");

  /*
   ** Config Harbor
   */

  fwrite($fp, "# Config Harbor\n");
  fwrite($fp, 'set("harbor.bind_addr","0.0.0.0")'."\n");
  fwrite($fp, 'set("harbor.port",'.$port.')'."\n");
  fwrite($fp, 'set("harbor.password","'.$password.'")'."\n");
  fwrite($fp, 'harbor = "live.'.$format_live.'"'."\n");
  fwrite($fp, 'emi = resample(ratio=interactive.float("test",1.005),input.harbor(logfile="",harbor))'."\n\n");

  /*
   ** Diffusion
   */

  fwrite($fp, "############\n");
  fwrite($fp, "# Diffusion\n");
  fwrite($fp, 'host = "oxycast.net"'."\n");
  fwrite($fp, 'port = 8000'."\n");
  fwrite($fp, 'user = "source"'."\n"); 
  fwrite($fp, 'password = "n1ntend0"'."\n");
  if ($description != "")
    fwrite($fp, 'desc = "'.$description.'"'."\n");
  else
    fwrite($fp, 'desc = "Stream par OxyCast.net"'."\n");
  if ($url != "")
    fwrite($fp, 'url = "'.$url.'"'."\n");
  else
    fwrite($fp, 'url = "www.OXYCAST.net"'."\n");
  fwrite($fp, 'genre = "'.$genre.'"'."\n"); 
  fwrite($fp, 'nom = "'.$nom.'"'."\n"); 
  fwrite($fp, 'bitrate = '.$bitrate.''."\n");
  fwrite($fp, 'mountpoint = "'.$mountpoint.'.'.$format_output.'"'."\n\n");

  /*
   ** Automation, transition, jingles detection blanc
   */


  fwrite($fp, "######################\n");
  fwrite($fp, "# Automation + extras\n");
  fwrite($fp, 'def trans(j, a, b)'."\n");
  fwrite($fp, '   add(    normalize=false,'."\n");
  fwrite($fp, '       [   sequence(merge=true, [ j, fade.initial(duration=2., b) ]),'."\n");
  fwrite($fp, '           fade.final(duration=1., a) ])'."\n");
  fwrite($fp, 'end'."\n\n");
  fwrite($fp, 'jingles = playlist.safe(reload=3600,"/home/oxycast/streams/'.$login.'-'.$idStream.'/public/jingles")'."\n");
  fwrite($fp, 'pls = request.dynamic(id="scheduler",'."\n");
  fwrite($fp, '    fun () ->'."\n");
  fwrite($fp, '      request(get_process_output("php /home/oxycast/streams/'.$login.'-'.$idStream.'/oxycast.php")))'."\n");
  fwrite($fp, 'pls = skip_blank(threshold=-35.,length=7.,pls)'."\n");
  fwrite($fp, 'pls = dole_fade(jingles=jingles,pls)'."\n");
  fwrite($fp, 'radio = mksafe(fallback(track_sensitive = false, transitions=[trans(jingles), trans(jingles)], [emi, pls]))'."\n\n");

  /*
   ** Mountpoints
   */

  fwrite($fp, "######################\n");
  fwrite($fp, "# Mountpoint et relay\n");
  if ($format_output == "mp3")
    fwrite($fp, 'output.icecast.mp3(mount=mountpoint, id="liq", name=nom, bitrate=bitrate,'."\n");
  elseif ($format_output == "ogg")
    fwrite($fp, 'output.icecast.vorbis(mount=mountpoint, id="liq", name=nom, quality=5.,'."\n");
  else
    fwrite($fp, 'output.icecast.mp3(mount=mountpoint, id="liq", name=nom, bitrate=bitrate,'."\n");
  fwrite($fp, '                host=host, port=port, user=user, password=password, description=desc, genre=genre, url=url,'."\n");
  fwrite($fp, '                radio)'."\n");
  fwrite($fp, "\n");

  /*
   ** On close
   */

  fclose($fp);
}
?>