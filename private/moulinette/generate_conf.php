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
		      $description, $url, $genre, $nom, $bitrate, $mountpoint, $nb_jingles, $start_before)
{
  $fp = fopen($filename, 'w');
  fwrite($fp, '%include "/home/oxycast/liquidsoap/utils.liq'."\"\n\n");
  fwrite($fp, "# On define les trucs de bases\n");
  fwrite($fp, 'racine = "/home/oxycast/streams/'.$login.'-'.$idStream.'/"'."\n");
  fwrite($fp, 'set("log.file.path",racine ^"logs/logfile.log")'."\n");
  fwrite($fp, 'set("log.level",3)'."\n\n");

  /*
   ** Config Harbor
   */

  fwrite($fp, "# Config Harbor\n");
  fwrite($fp, 'set("harbor.bind_addr","0.0.0.0")'."\n");
  fwrite($fp, 'set("harbor.port",'.$port.')'."\n");
  fwrite($fp, 'set("harbor.password","'.$password.'")'."\n");
  fwrite($fp, 'harbor = "live.'.$format_live.'"'."\n");
  fwrite($fp, 'emi = input.harbor(harbor)'."\n\n");

  /*
   ** Diffusion
   */

  fwrite($fp, "############\n");
  fwrite($fp, "# Diffusion\n");
  fwrite($fp, 'host = "oxycast.net"'."\n");
  fwrite($fp, 'port = 8000'."\n");
  fwrite($fp, 'user = "source"'."\n"); 
  fwrite($fp, 'password = "ZHF7YwyrnfedE"'."\n");
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
  fwrite($fp, 'mountpoint = "'.$mountpoint.'.'.$format_output.'"'."\n");
  fwrite($fp, 'jingles_folder = "/home/oxycast/streams/'.$login.'-'.$idStream.'/public/jingles"'."\n\n");
  /*
   ** Automation, transition, jingles detection blanc
   */

  fwrite($fp,  "###############\n#Jingles and co\n");
  fwrite($fp, 'def next(j,a,b)
  add(normalize=false,
          [ sequence(merge=true,
                     [ blank(duration=3.),
                       fade.initial(duration=6.,b) ]),
            sequence([fade.final(duration=9.,a),
                      j,fallback([])]) ])
end


def jingle_fade (~start_next=5.,~fade_in=5.,~fade_out=5.,
                 ~width=1.,~conservative=false,~jingles,
                 ~period,~start_before,s)
  fade.out = fade.out(duration=fade_out)
  fade.in = fade.in(duration=fade_in)

  # Une queue pour les jingles
  jingles_queue = request.queue(id="jingles_queue")

  # Une reference sur une string representant un int
  x = ref 0

  # On definit une fonction toggle:
  def do_jingle() =
    current = !x
    if current >= period then
      x := 0
      true
    else
      x := (current+1)
      false
    end
  end

  log = log(label="jingle_fade")

  def transition (a, b, ma, mb, sa, sb)
    if do_jingle() then
      log("Transition avec jingle")
      # Récupère un jingle
      jingle = jingles()
      # Récupère sa durée
      jingle_len = file.duration(jingle)
      log("Jingle: #{jingle}, duration: #{jingle_len}")
      # On ajoute le jingle à la queue:
      ignore(server.execute("#{source.id(jingles_queue)}.push #{jingle}"))
      # On va supperposer les deux premières et dernières
      # secondes du jingle
      delay_in =
        if jingle_len >= start_before then
          jingle_len - start_before
        else
          jingle_len
        end
      delay_out =
        if fade_out >= start_before then
          fade_out - start_before
        else
          fade_out
        end
      # On crée la source jingle
      jingle = sequence(merge=true,[blank(duration=delay_out),jingles_queue])
      sb = sequence(merge=true,[blank(duration=delay_in),fade.in(sb)])
      add(normalize=false,[sb,fade.out(sa),jingle])
    else
      log("Transition sans jingle")
      # Transition de base:
      add(normalize=false,[fade.in(sb),fade.out(sa)])
    end
  end

  smart_cross(transition,
               width=width,
               duration=start_next,conservative=conservative,
               s)
end'."\n\n");


  fwrite($fp, 'def trans(j, a, b)'."\n");
  fwrite($fp, '   add(    normalize=false,'."\n");
  fwrite($fp, '       [   sequence(merge=true, [ j, fade.initial(duration=2., b) ]),'."\n");
  fwrite($fp, '           fade.final(duration=1., a) ])'."\n");
  fwrite($fp, 'end'."\n\n");
  
  fwrite($fp, 'jingles = fun () -> list.hd(get_process_lines("find "^jingles_folder^" -name \'*.mp3\' -or -name \'*.ogg\' | sort -R | head -1"))'."\n");
//   fwrite($fp, 'jingles = fun () -> get_process_output("find "^jingles_folder^" -name \'*.mp3\' -or -name \'*.ogg\' | sort -R | head -1")'."\n");
  fwrite($fp, 'pls = request.dynamic(id="scheduler",'."\n");
  fwrite($fp, '    fun () ->'."\n");
  fwrite($fp, '      request.create(list.hd(get_process_lines("php /home/oxycast/streams/'.$login.'-'.$idStream.'/oxycast.php"))))'."\n");
  fwrite($fp, 'pls = skip_blank(threshold=-35.,length=7.,pls)'."\n");
  fwrite($fp, 'pls = if jingles() != "" then jingle_fade(jingles=jingles, period='.$nb_jingles.', start_before='.$start_before.'., pls) else pls end'."\n");
  fwrite($fp, 'radio = if jingles() != "" then
  jingles_playlist = playlist.safe(reload=3600, jingles_folder)
  mksafe(fallback(track_sensitive = false, transitions=[trans(jingles_playlist), trans(jingles_playlist)], [emi, pls]))
else
  mksafe(fallback(track_sensitive = false, [emi, pls]))
end

');

  /*
   ** Mountpoints
   */

  fwrite($fp, "######################
# Mountpoint et relay

");
  if ($format_output == "mp3")
    fwrite($fp, 'output.icecast(%mp3(bitrate=160),mount=mountpoint, id="liq", name=nom,'."\n");
  elseif ($format_output == "ogg")
    fwrite($fp, 'output.icecast(%vorbis(quality=0.5), mount=mountpoint, id="liq", name=nom,'."\n");
  else
    fwrite($fp, 'output.icecast(%mp3(bitrate=160),mount=mountpoint, id="liq", name=nom,'."\n");
  fwrite($fp, '                host=host, port=port, user=user, password=password, description=desc, genre=genre, url=url,'."\n");
  fwrite($fp, '                radio)'."\n");
  fwrite($fp, "\n");

  /*
   ** On close
   */

  fclose($fp);
}

?>