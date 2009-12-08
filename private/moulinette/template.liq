%include "/home/oxycast/liquidsoap/utils.liq"

# On define les trucs de base
racine = "/home/oxycast/streams/%client%-%id%/"
set("log.file.path",racine ^"logs/logfile.log")
set("log.level",5)

set("harbor.bind_addr","0.0.0.0")
set("harbor.port",%port%)
set("harbor.password","%password%")
harbor = "live.%format_live%"
emi = resample(ratio=interactive.float("test",1.005),input.harbor(logfile="",harbor))

################
# OXYRADIO Configuration
## diffusion
host = "oxycast.net"
port = 8000
user = "source"
password = "n1ntend0"
desc = "%description%"
url = "%url%"
genre = "%genre%"
nom = "%nom%"
bitrate = %bitrate%
mountpoint = "%mountpoint%.%format_output%"

def trans(j, a, b)
   add(    normalize=false,
       [   sequence(merge=true, [ j, fade.initial(duration=2., b) ]),
           fade.final(duration=1., a) ])
end

jingles = playlist.safe(reload=3600,"/home/oxycast/streams/%client%-%id%/public/jingles")

pls = request.dynamic(id="scheduler",
    fun () ->
      request(get_process_output("php /home/oxycast/streams/%client%-%id%/oxycast.php")))

# When blank/silence is detected of -28dB for more than 3 seconds then skip track/advance to next track
pls = skip_blank(threshold=-35.,length=7.,pls)
pls = dole_fade(jingles=jingles,pls)

radio = mksafe(fallback(track_sensitive = false, transitions=[trans(jingles), trans(jingles)], [emi, pls]))


################
# ICECAST Mountpoints
output.icecast.mp3(mount=mountpoint, id="liq", name=nom, bitrate=bitrate,
                host=host, port=port, user=user, password=password, description=desc, genre=genre, url=url,
                radio)
