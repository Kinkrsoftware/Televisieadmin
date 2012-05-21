<?php	
	// archive.php - List the archived video files
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
	//
	// This file is partly based on Basic TODO list (todo.php) written by 
	// Amadeus Stevenson. See acknowledgements.txt for the license of todo.php 

	require_once ('tvfunctions.php');											// Functions for TV scripts
    authorize(1);

	require_once ('tvconfig.php');												// Settings for TV scripts
	
	testConfiguration();																	// Test if configuration of directorys is OK

	if (isset ($_POST["submit"] )) {
		if ($_POST["submit"] == "Verwijderen") {
			// Process "Delete" button
			foreach ($_POST as $key => $value) {
				if (strpos($key, 'line_') === 0) {
					$line = substr($key, 5);
					$filename = $_POST['file_'.$line];
					if (strpos($filename, '/') === FALSE) {
						unlink(ARCHIVEDIR.'/'.$filename);
					}
				}
			}
		} elseif ($_POST["submit"] == "Activeren") {
			// Process "Archive" button
			foreach ($_POST as $key => $value) {
				if (strpos($key, 'line_') === 0) {
					$line = substr($key, 5);
					$filename = $_POST['file_'.$line];
					if (strpos($filename, '/') === FALSE) {
						if (isVideo($filename)) {
							rename (ARCHIVEDIR."/".$filename, VIDEODIR."/".$filename);
						}
					}
				}
			}
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
		<form method="post" enctype="multipart/form-data">
			<b>TV beheer pagina, versie <?php echo VERSION;?></b><br /><br />
			<fieldset class="navigation">
			<?php echo 'Gebruiker: ', $_SERVER['PHP_AUTH_USER'], ' | '; ?>
			<a href="tv.php"><?php echo "terug"; ?></a><?php echo ' | '; ?>
			<a href="help/archivehelp.php"><?php echo "help"; ?></a>
			</fieldset>
			<fieldset class="main">
				<fieldset class="buttons">
					<legend>Acties</legend>
					<tr>
					<td><input type="submit" name="submit" value="Activeren" /></td>
					<td><input type="submit" name="submit" value="Verwijderen" /></td>
					</tr>
				</fieldset>
				<fieldset class="edit">
					<legend>Video's in archief</legend>
					<pre>
					<table>
						<?php
							echo "<tr>";																						// Make table header
							echo "<td></td>";
							echo "<td>Video</td>";
							echo "<td>Datum</td>";
							echo "<td>Grootte</td>";
							echo "<td>Speeltijd</td>";
							echo "<td>Toelichting</td>";
							echo "</tr>";
							$n = 0;
							if ($handle = opendir(ARCHIVEDIR)) {
								while (false !== ($file = readdir($handle))) {
									if (!is_dir(ARCHIVEDIR.'/'.$file)) {
										if (isVideo($file)) {							// if file is video file

											$modified = filemtime (ARCHIVEDIR."/".$file);						// Get modified date of video file
											$size = filesize(ARCHIVEDIR."/".$file);									// Get file size
											$movie = new ffmpeg_movie (ARCHIVEDIR."/".$file, false);	// Loaf movie with ffmpeg
											$duration = $movie->getDuration();										// Get duration

											echo "<tr>";																			// Make table header
											echo "<td><input type='checkbox' name='line_$n' /><input type='hidden' name='file_$n' value='$file' /></td>";

											echo "<td>$file</td>";															// Print file name
											echo "<td style=\"text-align: right\">".date("d-m-Y", $modified)."</td>";					// Modification date
											echo "<td style=\"text-align: right\">".@niceSize($size)."</td>";									// File size
											if ($duration>=3600)																// Print duration
												printf ("<td style=\"text-align: right\">%02d:%02d:%02d</td>", floor($duration/3600), floor(($duration/60)%60), floor($duration%60));
											else
												printf ("<td style=\"text-align: right\">%02d:%02d</td>", floor(($duration/60)%60), floor($duration%60));
											echo "</tr>\n";
										}
									}
									$n++;
								}
								closedir($handle);
							}
						?>
					</table>
					</pre>
				</fieldset>
			</fieldset>
		</form>
	</body>
</html>
