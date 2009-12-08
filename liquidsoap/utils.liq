########################
#
#       OXYRADIO 
# FICHIER CONFIGURATION INCLUDE
#
########################

# GENERATEUR
def generate()
	system("/home/oxyradio/liquidsoap/generateur.php")
	3300.
end

# On applique des rewrite divers

def jingle_meta(j)
  rewrite_metadata([("artist", "OxyRadio"),
                    ("title","Jingle (OxyRadio)"),
                    ("comment","http://www.OXYRADIO.net")],j)
end

def boite_meta(j)
  rewrite_metadata([("artist", "OxyRadio"),
                    ("title","La Boite! !"),
                    ("comment","http://www.OXYRADIO.net")],j)
end

def sex_meta(j)
  rewrite_metadata([("artist", "OxyRadio"),
                    ("title","Sex and Love avec Myckeul !"),
                    ("comment","http://www.OXYRADIO.net")],j)
end

def enfants_meta(j)
  rewrite_metadata([("artist", "OxyRadio"),
                    ("title","Les Enfants du Web"),
                    ("comment","http://www.OXYRADIO.net")],j)
end

def events_meta(j)
  rewrite_metadata([("artist", "OxyRadio"),
                    ("title","Les evenements OxyRadio"),
                    ("comment","http://www.OXYRADIO.net")],j)
end

# Script traitement

def audio_process1(s)
  gain = interactive.float("compress.gain",1.5)
  ratio = 3. #interactive.float("compress.ratio",15.0)
  threshold = interactive.float("compress.threshold",-15.0)

  target = interactive.float("norm.target",-13.0)
  k_up = interactive.float("norm.kup",0.005)
  k_down = interactive.float("norm.kdn",0.1)
 
  s = normalize(target=target,k_up=k_up,k_down=k_down,s)
  s = compress(gain=gain,ratio=ratio,threshold=threshold,s)
  s
end

def audio_process(s)
 # 3-band crossover
 low = filter.iir.eq.low(frequency = 900.)
 mh = filter.iir.eq.high(frequency = 1000.)
 mid = filter.iir.eq.low(frequency = 800.)
 high = filter.iir.eq.high(frequency = 700.)
 


 # Normalize (perform AGC)
  n = normalize(target=0., window=0.03, gain_min=-10., gain_max=10., s)
 # Add back
  c = add(normalize = false,
 [ compress(attack = 100., release = 200., threshold = -20.,
 ratio = 6., gain = 6.7, knee = 0.3,
 low(n)),
 compress(attack = 100., release = 200., threshold = -20.,
 ratio = 6., gain = 6.7, knee = 0.3,
 mid(mh(n))),
 compress(attack = 100., release = 200., threshold = -20.,
 ratio = 6., gain = 6.7, knee = 0.3,
 high(n))
 ])

  # Limit the signal output to prevent clipping
  limit(attack=100., release=100., threshold=-2., rms_window=0.001, gain=0., c)

end


# Partially apply next to give it a jingle source.
# It will fade out the old source, then play the jingle.
# At the same time it fades in the new source.
def next(j,a,b)
  add(normalize=false,
          [ sequence(merge=true,
                     [ blank(duration=3.),
                       fade.initial(duration=6.,b) ]),
            sequence([fade.final(duration=9.,a),
                      j,fallback([])]) ])
end


def dole_fade (~start_next=3.,~fade_in=5.,~fade_out=5.,
                     ~width=1.,~conservative=false,~jingles,s)
  mf_high = 20.
  mf_medium = 32.
  mf_margin = 4.
  fade.out = fade.out(duration=fade_out)
  fade.in = fade.in(duration=fade_in)

  j = random(id="jrand",
             weights=[1,2],
             [jingles, blank(id="bbb",duration=1.)])
  add = fun (from,to) ->
    add(normalize=false,
        [ to,
          sequence([ j, fallback([]) ]),
          from ])

  log = log(label="dole_fade")

  def transition (a, b, ma, mb, sa, sb)
    if
      (a + mf_high >= 0. and b + mf_high >= 0.) or
      (a + mf_medium >= 0. and b + mf_medium >= 0. and abs(a - b) <= mf_margin)
    then
      log("No transition, just sequencing.")
      sequence([sa, sb])

    elsif
      a + mf_medium <= 0. and b + mf_medium >= 0. and abs(a - b) <= mf_margin
    then
      log("Using transition 1: crossed.")
      add(fade.out(sa),fade.in(sb))

    elsif
      b >= a + mf_margin and a <= mf_high
    then
      log("Using transition 2: crossed, fade-out.")
      add(fade.out(sa),sb)

    elsif
      a >= b + mf_margin and b <= mf_high
    then
      log("Using transition 3: crossed, fade-in.")
      add(sa,fade.in(sb))

    else
      log("Using transition 4: crossed, fade-in, fade-out.")
      add(fade.out(sa),fade.in(sb))
    end
  end

  smart_cross(transition,
               width=width,
               duration=start_next,conservative=conservative,
               s)
end