<?php	
	// archivehelp.php
	// Copyright (C) 2009 St. lokale omroep Midvliet
	//
    // This program is free software: you can redistribute it and/or modify
    // it under the terms of the GNU Affero General Public License as published by
    // the Free Software Foundation, either version 3 of the License, or
    // (at your option) any later version.
	//
    // This program is distributed in the hope that it will be useful,
    // but WITHOUT ANY WARRANTY; without even the implied warranty of
    // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    // GNU Affero General Public License for more details.
	//
    // You should have received a copy of the GNU Affero General Public License
    // along with this program.  If not, see <http://www.gnu.org/licenses/>.
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
	<head>
		<title><?php echo OWNER; ?></title>
		<link rel="stylesheet" href="help.css" type="text/css" />
	</head>
	
	<body>
		<fieldset class="navigation">
		<?php echo 'Gebruiker: ', $_SERVER['PHP_AUTH_USER'], ' | '; ?>
		<a href="tvhelp.php"><?php echo "terug"; ?></a>
		</fieldset>
		<fieldset class="main">
			<legend>Help</legend>
			Op deze pagina kunnen video's van het archief naar de active directory worden verplaatst:<br><br>
			<img src="Help2Archive.png" alt="Video" /><br>
			<h1>1) Video's activeren.</h1>
			 Selecteer een video bestand door in het onderste scherm een vinkje voor het bestand te plaatsen. Druk hierna op de knop "activeren" om het video bestand naar de actieve directory te verplaatsen.<br><br>
		</fieldset>
	</body>
</html>
