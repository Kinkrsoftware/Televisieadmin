<?php
  require_once ('tvconfig.php'); 
  $error = '';
  if (isset($_POST['action'])) {
  if ($_POST['action']=='Maak Gebruiker') {
    $login = trim($_POST['newuser']);
    $surname = trim($_POST['surname']);
    $givenname = trim($_POST['givenname']);
    $addictions = trim($_POST['addictions']);
    $password = trim($_POST['password']);

    if ($login != '' && $givenname != '')  {
	$passphrase = md5($login.':tvadmin.midvliet.com:'.$password);

	$fp = fopen('.htdigest', 'a');
	if ($fp) {
		fwrite($fp, $login.':tvadmin.midvliet.com:'.$passphrase."\n");
		fflush($fp);
		fclose($fp);
	} else {
		$error = 'Fout: Met aan zekerheidgrenzende waarschijnlijkheid is .htdigest niet toegankelijk om te schrijven door PHP.';
	}
//	header('Location: '.$_SERVER['PHP_SELF']);
    }
  } else if ($_POST['action']=='Verwijder Gebruiker') {
  	$login = trim($_POST['newuser']);

	if ($login != '') {
		clearstatcache();
		/* we halen de gebruiker niet uit de database, omdat de refentiele integriteit dat kwijt raakt */
		$needle = $login.':';
		$nlen   = strlen($needle);
		$fp = fopen('.htdigest', 'r');
		$contents = '';
		while (!feof($fp)) {
		  $tmp = fgets($fp);
		  if (strlen($tmp) > $nlen && substr_compare($tmp, $needle, 0, $nlen, TRUE) != 0) {
		  	$contents .= $tmp;
		  }
		}
		fclose($fp);
		file_put_contents('.htdigest', $contents, LOCK_EX);
	}
  } else if ($_POST['action']=='Wijzig Wachtwoord') {
  	$login = trim($_POST['curuser']);
	$password = trim($_POST['newpassword']);

	if ($login != '' && $password != '') {
		$passphrase = md5($login.':tvadmin.midvliet.com:'.$password);
		clearstatcache();
		/* we halen de gebruiker niet uit de database, omdat de refentiele integriteit dat kwijt raakt */

		$needle = $login.':';
		$nlen   = strlen($needle);
		$fp = fopen('.htdigest', 'r');
		$contents = '';
		while (!feof($fp)) {
		  $tmp = fgets($fp);
		  if (strlen($tmp) > $nlen && substr_compare($tmp, $needle, 0, $nlen, TRUE) != 0) {
		  	$contents .= $tmp;
		  }
		}
		fclose($fp);
		$contents.=$login.':tvadmin.midvliet.com:'.$passphrase."\n";
		file_put_contents('.htdigest', $contents, LOCK_EX);
	
	}
  }
}

  
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
    <title><?php echo OWNER; ?></title>
    <link rel="stylesheet" href="toevoegen.css" type="text/css" />
  </head>
  <body>
    <?php echo $error; ?>
    Welkom op de nieuwe beta-versie van kabelkrantadmin.<br />
    Momenteel worden wat toevoegingen gedaan aan de broncode, het kan zijn
    dat je daar iets van merkt.<br />
     
     <form method="post">
        <fieldset>
	   <legend>Gebruiker Toevoegen</legend>
	   <label for="newuser" style="display: block; width: 110px; float: left; clear: both;">Gebruikersnaam:</label> <input id="newuser" name="newuser" type="text" /> 
	   <label for="password" style="display: block; width: 110px; float: left; clear: both;">Wachtwoord:</label> <input id="password" name="password" type="password" />
	   <label for="givenname" style="display: block; width: 110px; float: left; clear: both;">Voornaam:</label> <input id="givenname" name="givenname" type="text" />
	   <label for="addictions" style="display: block; width: 110px; float: left; clear: both;">Tussenvoegsels:</label> <input id="addictions" name="addictions" type="text" />
	   <label for="surname" style="display: block; width: 110px; float: left; clear: both;">Achternaam:</label> <input id="surname" name="surname" type="text" />
	   <input name="action" type="submit" value="Maak Gebruiker" class="button" style="clear: both; width: auto;" />
	   <input name="action" type="submit" value="Verwijder Gebruiker" class="button" style="width: auto;" />
        </fieldset>
     </form>
     <form method="post">
        <fieldset>
	   <legend>Wachtwoord wijzigen</legend>
	   <label for="curuser" style="display: block; width: 110px; float: left; clear: both;">Gebruikersnaam:</label> <input id="curuser" name="curuser" type="text" />
	   <label for="newpassword" style="display: block; width: 110px; float: left; clear: both;">Wachtwoord:</label> <input id="newpassword" name="newpassword" type="password" />
	   <input name="action" type="submit" value="Wijzig Wachtwoord" class="button" style="clear: both; width: auto;" />
        </fieldset>
     </form>
     <form method="post">
        <fieldset>
	   <legend>Rechten</legend>
	   <table>
	     <tr>
	       <th>Gebruiker</th>
	     </tr>

	     <?php 
		$fp = fopen('.htdigest', 'r');
		$result = array();
		while (!feof($fp)) {
		  $tmp = fgets($fp);
		  if (strlen($tmp) > 0) {
			$waardes = split(':',$tmp);
			if (count($waardes) == 3) {
			  	$result[]  = array('login' => $waardes[0]);
			}
		  }
		}
		fclose($fp);

		if (is_array($result)) {
			foreach ($result as $entry) {
				echo '<tr><td>'.$entry['login'].'</td></tr>';
			}
		}			 
	     ?>
	     
	   </table>
	</fieldset>
     </form>
     <form method="post">
        <fieldset>
	   <legend>Acties</legend>
	   <table>
	     <tr>
	       <th>Naam</th>
	       <th>Omschrijving</th>
	     </tr>
	   </table>
	</fieldset>
     </form>

     <i>Wanneer er serieuze problemen zijn, kan er altijd gebeld worden met +31 85 7 85 31 85. Kinkrsoftware/Stefan de Konink;</i>
</body>
</html>
