<?php	
	// tv.php - Script for scheduling television broadcasts
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

	require_once ('tvfunctions.php');											// Functions for TV scripts
    authorize(1);

	require_once ('tvconfig.php');												// Settings for TV scripts

	testConfiguration();																	// Test if configuration of directorys is OK

	if (file_exists (CRONFILE)) {
		$modified = filemtime (CRONFILE);									// Get modified date of cron file if file exists
	} else {
		$modified = "NOT AVAILABLE";
	}
	if (isset($_POST['action'])) {
		if ($_POST["action"] == "Bewerk Video") {
			header('Location: video.php');
		}
		if ($_POST["action"] == "Bewerk Archief Video") {
			header('Location: archive.php');
		}
		if ($_POST["action"] == "Bewerk Draaiboeken") {
			header('Location: playlist.php');
		}
		if ($_POST["action"] == "Bewerk Planning") {
			header('Location: cron.php');
		}
	}
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
	<head>
		<title><?php echo OWNER; ?></title>
		<link rel="stylesheet" href="tv.css" type="text/css" />
	</head>
	
	<body>
		<form method="post">
			<b>TV beheer pagina, versie <?php echo VERSION;?></b><br /><br />
			<fieldset class="navigation">
			<?php echo 'Gebruiker: ', $_SERVER['PHP_AUTH_USER'], ' | '; ?>
			<a href="help/tvhelp.php"><?php echo "help"; ?></a>
			</fieldset>
			<fieldset class="main">
				<legend>TV Uitzendingen</legend>
				<br />
				<fieldset class="edit">
					<legend>Video's</legend>
						<?php
							@countFilesMime(VIDEODIR, 'video', &$size, &$duration, &$count);
							echo "Aantal actieve video's: $count";
							echo " (".@niceSize($size).")";
							echo "<br />";
							@countFilesMime(ARCHIVEDIR, 'video', &$size, &$duration, &$count);
							echo "Aantal gearchiveerde video's: $count";
							echo " (".@niceSize($size).")";
							echo "<br />";
							define("DISK","/"); 									// The Drive/Mount point/Directory
							$total = disk_total_space(DISK); 			// Get the total diskspace
							$free = disk_free_space(DISK);			// Get free space
							$used = $total - $free; 							// Find used space
							echo "Totale schijfruimte: ".@niceSize($total)."<br />";
							echo "Gebruikte schijfruimte: ".@niceSize($used)."<br />";
							echo "Vrije schijfruimte: ".@niceSize($free);
						?>
						<br />
					<hr />
					<input type="submit" name="action" value="Bewerk Video" />
					<input type="submit" name="action" value="Bewerk Archief Video" />
				</fieldset>
				<br />

				<fieldset class="edit">
					<legend>Draaiboeken</legend>
						<?php echo "Aantal draaiboeken: ".@countFilesExt(M3UDIR, '.m3u'); ?>
						<br />
					<hr />
					<input type="submit" name="action" value="Bewerk Draaiboeken" />
				</fieldset>
				<br />

				<fieldset class="edit">
					<legend>Planning</legend>
						<?php if ($modified!="NOT AVAILABLE") {
							echo "Planning aangepast op: ";
							echo date("d-m-Y H:i:s", $modified );
							echo ", ~ ";
							echo nicetime( date("Y-m-d H:i", $modified ) );
							echo " geleden";
						}
						?>
						<br />
					<hr />
					<input type="submit" name="action" value="Bewerk Planning" />
				</fieldset>
			</fieldset>
			<br />
<!--
			<fieldset class="main">
				<legend>Schijven</legend>
				<br />
				<fieldset class="edit">
					<legend>Overzicht</legend>
					<table>
						<tr><th>Mountpunt</th><th>Gebruikt</th><th>Vrije ruimte</th></tr>
						<tr><td>/mnt/media</td><td></td><td></td></tr>
					</table>
				</fieldset>
				<fieldset class="buttons">
					<legend>Acties (/mnt/media)</legend>
					<input type="submit" name="action-umount" value="Ontkoppel" />
					<input type="submit" name="action-mount" value="Koppel" />
				</fieldset>
				<fieldset class="edit">
					<legend>Opmerkingen</legend>
					Ontkoppel een schijf voordat hij uit de PC wordt gehaald, danwel
					de USB/Firewire kabel wordt verwijderd.
				</fieldset>
			</fieldset>
-->
		</form>
	</body>
</html>
