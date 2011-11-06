<?php	
	// tvhelp.php
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
		<a href="../tv.php"><?php echo "terug"; ?></a>
		</fieldset>
		<fieldset class="main">
			<legend>Help</legend>
			Het programmeren van TV uitzendingen gaat in drie stappen:<br><br>
			<h1>1) Het beheren van de video bestanden en gearchiveerde video bestanden.</h1>
			<img src="Help1Video.png" alt="Video" /><br>
			Klik op de knop "Bewerk Video" om video's te uploaden. Deze video bestanden worden in de active directory geplaatst. Ook kunnen video's naar het archief worden verplaatst. In dit vak is informatie te vinden over het geheugengebruik van de harde schijven en het aantal video bestanden in de active directory en het archief. Met de knop "Bewerk Archief Video" kunnen gearchiveerde video bestanden in de actieve directory geplaatst worden. <br><a href="videohelp.php"><?php echo "Klik hier voor meer informatie over het beheren van video bestanden"; ?></a>.<br><a href="archivehelp.php"><?php echo "Klik hier voor meer informatie over het archiveren van video bestanden"; ?></a>.<br><br>
			<h1>2) Het maken van draaiboeken.</h1>
			<img src="Help1Playlist.png" alt="Video" /><br>
			De tweede stap is het maken van draaiboeken. Een draaiboek bevat de volgorde van de af te spelen video's. Video's die in de actieve directory staan kunnen in een draaiboek worden opgenomen. Dit vak bevat informatie over het aantal draaiboeken in het systeem. <br><a href="playlisthelp.php"><?php echo "Klik hier voor meer informatie over het maken en bewerken van draaiboeken"; ?></a>.<br><br>
			<h1>3) Het programmeren van draaiboeken.</h1>
			<img src="Help1Cron.png" alt="Video" /><br>
			De laatste stap is het programmeren van een draaiboek zodat dit draaiboek op de ingeprogrammeerde tijd wordt afgespeeld. Dit vak bevat de datum en tijd waarop de planning is aangepast. <br><a href="cronhelp.php"><?php echo "Klik hier voor meer informatie over het beheren van de planning"; ?></a>.
		</fieldset>
	</body>
</html>
