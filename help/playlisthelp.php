<?php	
	// playlisthelp.php
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
			Op deze pagina worden de draaiboeken geprogrammeerd:<br><br>
			<h1>1) Nieuw draaiboek.</h1>
			<img src="Help2PlaylistNew.png" alt="Video" /><br>
			Een nieuw draaiboek kan worden gemaakt door in het "nieuw veld" een naam in te vullen. Druk vervolgens op de knop "Nieuw draaiboek".
			<br><br>
			<h1>2) Draaiboek bewerken.</h1>
			<img src="Help2PlaylistEdit.png" alt="Video" /><br>
			Een draaiboek kan worden bewerkt door in het "bewerk veld" een draaiboek te selecteren. Druk vervolgens op de knop "bewerk draaiboek". Hierna kan in het veld "draaiboek" de regels van het draaiboek worden aangepast.
			<br><br>
			<h1>3) Toevoegen.</h1>
			<img src="Help2Playlist.png" alt="Video" /><br>
			Selecteer een video bestand in het "draaiboek acties veld". Door op de knop "toevoegen" te drukken wordt een regel aan de bovenkant toegevoegd.
			<br><br>
			<h1>4) Verwijderen.</h1>
			Een draaiboek regel kan worden verwijderd door een vinkje voor de regel te plaatsen. Druk daarna op de knop "verwijderen".<br><br>
			<h1>5) Omhoog / Omlaag.</h1>
			Een draaiboek regel kan 1 regel omhoog of 1 regel omlaag worden verplaast door een vinkje voor de regel te plaatsen. Druk daarna op de knop "omhoog" of "omlaag" om de regel te verplaatsen.<br><br>
			<h1>6) Alles selecteren / niets selecteren.</h1>
			Door op de knop "Alle" te drukken worden alle regels geselecteerd (vinkje voor de regel). Door op de knop "Geen" te drukken worden alle regels gedeselecteerd.<br><br>
		</fieldset>
	</body>
</html>
