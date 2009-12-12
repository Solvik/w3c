<?php
class miniSite
{
	/* ****
	VAR
	**** */
	private $userId = '';
	private $userLogin = '';
	private $userStream = '';
	private $userMail = '';
	private $dbName = '';
	private $sitePath = '';
	private $dbPass = '';
	private $dbUser = '';
	private $siteURL = '';
	private $siteName = '';

	/* ****
	Config VAR
	**** */
	// TODO: INSTALL A LOCAL SVN UP TO DATE WITH OUR PLUGIN IN IT
	private $svn = ' http://svn.automattic.com/wordpress/tags/2.8.4/';
	private $siteBasePath = '/home/oxycast/blog/';
	private $siteBaseUrl = 'http://blog.oxycast.net/';
	
	function __construct($user,$siteN) {
		$this->userId = $user;
		$this->siteName = $siteN;
		
        $select = "SELECT id FROM `streams` WHERE `clientId` = '".$user."'";
        $result = mysql_query ($select);
        $this->userStream = mysql_fetch_object($result)->id;

        $select = "SELECT login,mail,password FROM `clients` WHERE `id` = '".$user."'";
        $result = mysql_query ($select);
        $ret = mysql_fetch_object($result);
        $this->userLogin = $ret->login;
        $this->userMail = $ret->mail;
        $this->dbPass = $ret->password;
        
        $this->dbName = $this->userLogin.'_'.$this->userStream;
        $this->sitePath = $this->siteBasePath.$siteN.'/';
        $this->dbUser = $this->userLogin;
        $this->siteURL = $this->siteBaseUrl.$siteN;
    }
    
    function install() {
    	// Creating site dir
    	//if (system('mkdir '.$this->sitePath) == false) return false; // ERROR CREATING THE USER DIR
    	exec('mkdir '.$this->sitePath);
    	
    	// Getting last update from svn 
    	//if (system('svn co '.$svn.' '.$this->sitePath) == false) return false; // ERROR GETTING SVN FILE
    	exec('cp -R '.$this->siteBasePath.'wordpress/* '.$this->sitePath);
    	
    	// Writing config file
    	$fp = fopen($this->sitePath.'wp-config.php', 'w');
    	fwrite($fp, "<");
		fwrite($fp, "?php\n");
		fwrite($fp, "// ** MySQL settings ** //\n");
		fwrite($fp, "define('DB_NAME', '".$this->dbName."'); // The name of the database\n");
		fwrite($fp, "define('DB_USER', '".$this->dbUser."'); // Your MySQL username\n");
		fwrite($fp, "define('DB_PASSWORD', '".$this->dbPass."'); // ...and password\n");
		fwrite($fp, "define('DB_HOST', 'localhost');  // 99% chance you wont need to change this value\n");
		fwrite($fp, "define('DB_CHARSET', 'utf8');\n");
		fwrite($fp, "define('DB_COLLATE', '');\n");
		fwrite($fp, "define('SECRET_KEY', '".$this->userMail.$this->dbName.$this->sitePath."');\n");
		fwrite($fp, "define('WPLANG', 'fr_FR');\n");
		fwrite($fp, "define('ABSPATH', dirname(__FILE__).'/');\n");
		fwrite($fp, "require_once(ABSPATH.'wp-settings.php');\n");
		fwrite($fp, "?");
		fwrite($fp, ">\n");
		fclose($fp);
		
		mysql_query("UPDATE `clients` SET `blog` = '".$this->siteURL."' WHERE `id` = '".$this->userId."'");
		
		return $this->mail('inst');
    }
    
    function update() {
    	if (!system(escapeshellcmd('svn update'.$this->sitePath))) return false; // ERROR UPDATING SVN FILE
    	return $this->mail('maj');
    }
    
    function mail($type) {
	     $to      = $this->userMail;
	     $subject = ($type=='inst')?'Installation de votre mini-site':'Mise à jour de votre mini-site';
	     $message = 'Bonjour '.$this->userLogin.',';
	     if ($type=='inst') {
	     	$message .= 'Vous avez récemment demandé l\'installation d\'un stream sur OxyCast,'."\n";
	     	$message .= 'un mini-site pour ce stream à automatiquement été installé.'."\n";
	     	$message .= 'Il vous faut maintenant le configurer en vous rendant sur '.$this->siteURL.''."\n";
	     	$message .= 'Nous vous remercions pour votre confiance. L\'équipe d\'OxyCast.'."\n";
	     }
	     else {
	     	$message .= 'Afin d\'assurer une sécurité optimal de vos données les mini-sites sont regulièrement mis à jour.';
	     	$message .= 'Une mise à jour de votre site a eu lieu à l\instant. Nous vous invitons à verifier que tout est en ordre en vous rendant sur '.$this->siteURL.'.';
	     	$message .= 'Nous vous remercions pour votre confiance. L\'équipe d\'OxyCast.';
	     }
	     $headers = 'From: admin@oxycast.org' . "\r\n" .
	     'Content-Type: text/plain; charset="UTF-8"'. '\r\n' .
	     'Reply-To: admin@oxycast.org' . "\r\n" .
	     'X-Mailer: PHP/' . phpversion();
	     return mail($to, $subject, $message, $headers);
    }
}
?>