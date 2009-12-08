<?php

if (isset($_SESSION['login']))
{
/* Pages infos client */

?>

<div class="boxtitle">Espace Client</div>
<div class="content_content">
  <p>Bienvenue dans votre espace client. Vous pouvez maintenant souscrire &agrave; une de nos offres.</p>
</div>

<?php

if (isset($_GET['act']) AND  $_GET['act'] == "infos-client")
   {
?>
                <div id="example" class="post">
			<h2 class="title">Les infos clients</h2>
			<p>On fout le form pour modif les infos</p>
			<div class="story">
				<p>This is an example of a paragraph followed by a blockquote. In posuere  eleifend odio. Quisque semper augue mattis wisi. Maecenas ligula.  Pellentesque viverra vulputate enim. Aliquam erat volutpat lorem ipsum  dolorem.</p>
				<blockquote>
					<p>&ldquo;Pellentesque tristique ante ut  risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum  id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec  placerat odio.&rdquo;</p>
				</blockquote>
				<h3>Heading Level Three</h3>
				<p>This is another example of a paragraph followed by an unordered list. In posuere  eleifend odio. Quisque semper augue mattis wisi. Maecenas ligula.  Pellentesque viverra vulputate enim. Aliquam erat volutpat lorem ipsum  dolorem.</p>
				<p>An ordered list example:</p>
				<ol>
					<li>List item number one</li>
					<li>List item number two</li>
					<li>List item number thre</li>
				</ol>
			</div>
<?php 
      } 
}

else 
     {
?>

				<div class="content">
					<div class="boxtitle">Erreur</div>
					<div class="content_content">
						Vous n'etes pas identifi&eacute;.
                    </div>
                </div>



<?php
     }
?>

