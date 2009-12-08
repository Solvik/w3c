<?php

if ($_GET['act'] == "irc")
{
  echo '<div align="center"><iframe src="http://webchat.freenode.net/?nick=oxy'.rand(1000,9000).'&channels=oxycast" width="647" height="400"></iframe></div>';
}

else
{
?>
<div class="boxtitle">Support</div>
<div class="content_content">

      <p>En cas de probl&egrave;me merci de contacter:</p>
          <ul>
          <li>Facturation: <b><a href="mailto:facturation@oxycast.net">facturation@oxycast.net</a></b></li>
          <li>Technique: <b><a href="mailto:technique@oxycast.net">technique@oxycast.net</a></b></li>
          <li>Commercial: <b><a href="mailto:commercial@oxycast.net">commercial@oxycast.net</a></b></li>
          </ul>
          <p>Ou sur IRC:</p>
          <ul>
      <li><b><a href="irc://irc.freenode.net/oxycast">#oxycast</a></b> sur irc.freenode.net</li>
      <li><a href="irc">via l'applet</a></li>
      </ul>


</div>

<?php
}
?>
