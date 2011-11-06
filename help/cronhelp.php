<?php	
	// cronhelp.php
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
			<img src="Help2Cron.png" alt="Video" /><br>
			<h1>1) Programmeren.</h1>
			Klik in het bovenste scherm op een uur, minuut, dag, maand en/of dag van de week waarop het draaiboek moet worden afgespeeld door het systeem. Als er geen keuze wordt gemaakt in een kolom wordt elk uur of maand of dag geprogrammeerd. Met behulp van de Ctrl en Shift toets kunnen ook meerdere tijdstipen of dagen geselecteerd worden.<br>Kies een draaiboek en vul eventueel een commentaar regel in. Druk tenslotte op de knop "toevoegen" om een programma regel toe te voegen.<br><br>
			<h1>2) Verwijderen.</h1>
			Een programma regel kan worden verwijderd door een vinkje voor de programma regel te plaatsen. Druk daarna op de knop "verwijderen".<br><br>
			<h1>3) Omhoog / Omlaag.</h1>
			Een programma regel kan 1 regel omhoog of 1 regel omlaag worden verplaast door een vinkje voor de programma regel te plaatsen. Druk daarna op de knop "omhoog" of "omlaag" om de regel te verplaatsen.<br><br>
			<h1>4) Alles selecteren / niets selecteren.</h1>
			Door op de knop "Alle" te drukken worden alle programma regels geselecteerd (vinkje voor de regel). Door op de knop "Geen" te drukken worden alle regels gedeselecteerd.<br><br>
		</fieldset>
	</body>
</html>
