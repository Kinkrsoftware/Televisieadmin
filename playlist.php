<?php	
	// playlist.php - Edit M3U playlists for television broadcasts
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

	session_start();
	require_once ('tvfunctions.php');											// Functions for TV scripts
	require_once ('tvconfig.php');												// Settings for TV scripts

	testConfiguration();																	// Test if configuration of directorys is OK

	if (!isset($_POST['m3uplaylist'])) {															// If playlist is empty load one
		
		if ($dh = opendir(M3UDIR)) {
			while (($file = readdir($dh)) !== false) {
				if (!is_dir(M3UDIR.'/'.$file)) {
					$fileext = substr($file, -4);										// Get the last characters from the name
					if (strncmp ($fileext, '.m3u', 4) == 0) {				// if file has correct extension
						$playlistfile=$file;												// TODO: Load the last date
					}
				}
			}
		}
	} else {
		$playlistfile=$_POST['m3uplaylist'];
	}
	$_SESSION['m3u']=$playlistfile;												// Store the current playlist in the session
	$phash = sha1_file ( M3UDIR."/".$playlistfile );						// Calculate hash (used for detecting browser reload)

	if (file_exists (M3UDIR."/".$playlistfile)) {								// If playlist file exists
		// Read the playlist file in a array and count number of lines
		$data = file (M3UDIR."/".$playlistfile);
		$fp = fopen (M3UDIR."/".$playlistfile , "r") or die ("FOUT: Kan $playlistfile niet openen om te lezen, bekijk de permissies");
		$playlistlength = 0;
		$playdate[0] = '';																	// Make date of first item empty
		foreach ($data as $line) {
			if (strncmp ($line, "#EXTM3U", 7) != 0 && strncmp ($line, "vlc://quit", 8) != 0) {	// Is line editable?
				$playlisteditable[$playlistlength] = TRUE;					// Line is editable by the user
			} else {
				$playlisteditable[$playlistlength] = FALSE;					// Line isn't editable by the user
			}
			if (strncmp ($line, "#DATE:", 6) == 0 ) {
				$playdate[$playlistlength] = $line;								// Store the date
			} else {
				$playlist[$playlistlength] = $line;								// Store one line
				$selected[$playlistlength]=FALSE;								// Initialize selected list 
				$playlistlength++;														// Number of lines is stored in playlistlength
				$playdate[$playlistlength] = '';									// Make date of next item empty
			}
		}
		fclose ($fp);
	}

	if (isset($_POST['videofile']) && isset ($_POST["submit"] ) && $_POST["phash"] == $phash) {
		$videofile=$_POST['videofile'];												// Get videofile to add
		if ($_POST["submit"] == "Toevoegen" && file_exists (M3UDIR."/".$playlistfile)) {
			// Process "add" button, store the new line in playlist array
			if ($playlisteditable[0]==FALSE)
				$start = 1;																			// if first line is not editable insert at 2nd line
			else
				$start = 0;
			if ($playlistlength > 0) {
				for ($i=($playlistlength-1); $i>=$start; $i--) {
					$playdate[$i+1] = $playdate[$i];									// Shift line down
					$playlist[$i+1] = $playlist[$i];											// Shift line down
				}
				$playdate[$start] = "#DATE:".date("d-m-Y H:i:s")."\n";		// Store the new date
				$playlist[$start] = VIDEODIR."/".$videofile."\n";					// Store the new line
				$selected[$start]=TRUE;														// Line is editable by he user
				$playlistlength++;																// Increment length
			} else {
				$playdate[0] = "#DATE:".date("d-m-Y H:i:s")."\n";				// Store the new date
				$playlist[0] = VIDEODIR."/".$videofile."\n";							// Store the new line as the last line
				$selected[0]=TRUE;															// New line is editable by the user
				$playlistlength++;																// Increment length
			}

			// Write the file to disc
			$fp = fopen (M3UDIR."/".$playlistfile, "w") or die ("FOUT: Kan $playlistfile niet openen om te schrijven, bekijk de permissies");
			for ($i = 0; $i < $playlistlength; $i++) {
				if ($playdate[$i] != '') {
					fwrite ($fp, $playdate[$i]);												// Write date to file
				}
				fwrite ($fp, $playlist[$i]);													// Write video to file
			}
			fclose ($fp) ;
		}
		if ($_POST["submit"] == "Verwijderen" && file_exists (M3UDIR."/".$playlistfile)) {
			// Process "delete" button
			$data = file (M3UDIR."/".$playlistfile);
			$fp = fopen (M3UDIR."/".$playlistfile , "w+" ) or die ("FOUT: Kan $playlistfile niet openen om te schrijven, bekijk de permissies");
			$n = 0;
			foreach ($data as $line) {														// Repeat for every line
				if (empty ($_POST["line"][$n])) {
					fwrite ($fp, $line);															// Write every line when checkbox is not set
				}
				if (strncmp ($line, "#DATE:", 6) != 0)								// Also delete DATE lines
					$n++;
			}
			fclose ($fp);
		} elseif ($_POST["submit"] == "Omhoog") {
			// Process "Up" button
			if (! empty ($_POST["line"][0]))												// Do not shift line 0
				$playlisteditable[0]=FALSE;
			for ($n=0; $n<$playlistlength; $n++) {
				if ((! empty ($_POST["line"][$n])) && $n>=1) {				// Checkbox is set?
					if ($playlisteditable[$n-1]==TRUE) {								// When previous line is editable...
						$tmp=$playdate[$n];													// ...swap two lines
						$playdate[$n]=$playdate[$n-1];
						$playdate[$n-1] =$tmp;
						$tmp=$playlist[$n];														// ...swap two lines
						$playlist[$n]=$playlist[$n-1];
						$playlist[$n-1] =$tmp;
						$selected[$n-1]=TRUE;												// Also set previous checkbox
					} else {																			// Prevent shifting out of range
						$playlisteditable[$n]=FALSE;
					}
				} else {
					$selected[$n]=FALSE;														// Deselect surrent checkbox
				}
				// Write the file to disc
				$fp = fopen (M3UDIR."/".$playlistfile, "w") or die ("FOUT: Kan $playlistfile niet openen om te schrijven, bekijk de permissies");
				for ($i = 0; $i < $playlistlength; $i++) {
					if ($playdate[$i] != '') {
						fwrite ($fp, $playdate[$i]);											// Write date to file
					}
					fwrite ($fp, $playlist[$i]);												// Write video to file
				}
				fclose ($fp) ;
			}
		} elseif ($_POST["submit"] == "Omlaag") {
			// Process "down" button
			if (! empty ($_POST["line"][$playlistlength-1]))						// Do not shit last line
				$playlisteditable[$playlistlength-1]=FALSE;
			for ($n=$playlistlength-1; $n>=0; $n--) {
				if ((! empty ($_POST["line"][$n])) && $n<$playlistlength-1) {	// Checkbox is set?
					if ($playlisteditable[$n+1]==TRUE) {								// When next line is editable...
						$tmp=$playdate[$n];													// ...swap two lines
						$playdate[$n]=$playdate[$n+1];
						$playdate[$n+1] =$tmp;
						$tmp=$playlist[$n];														// ...swap two lines
						$playlist[$n]=$playlist[$n+1];
						$playlist[$n+1] =$tmp;
						$selected[$n+1]=TRUE;												// Also set next checkbox
					} else {																			// Prevent shifting out of range
						$playlisteditable[$n]=FALSE;
					}
				} else {
					$selected[$n]=FALSE;														// Deselect current checkbox
				}
				// Write the file to disc
				$fp = fopen (M3UDIR."/".$playlistfile, "w") or die ("FOUT: Kan $playlistfile niet openen om te schrijven, bekijk de permissies");
				for ($i = 0; $i < $playlistlength; $i++) {
					if ($playdate[$i] != '') {
						fwrite ($fp, $playdate[$i]);											// Write date to file
					}
					fwrite ($fp, $playlist[$i]);												// Write video to file
				}
				fclose ($fp) ;
			}
		} elseif ($_POST["submit"] == "Alle") {
			// Process "every" button
			for ($n=0; $n<$playlistlength; $n++) {
				$selected[$n]=TRUE;															// Select all checkboxes
			}
		} elseif ($_POST["submit"] == "Geen") {
			// Process "none" button
			for ($n=0; $n<$playlistlength; $n++) {
				$selected[$n]=FALSE;															// Deselect all checkboxes
			}
		}
		$phash = sha1_file (M3UDIR."/".$playlistfile);
	}

	if (isset($_POST['newplaylist']) && isset($_POST['submit']) && $_POST["submit"] == "Nieuw Draaiboek") {
		$newplaylistfile=$_POST['newplaylist'];									// Get the name of a new M3U playlist
		// Process "New Playlist" button
		$newplaylistfile = CleanFileName($newplaylistfile);				// Replace special characters
		$newplaylistfile = $newplaylistfile.".m3u";								// Reate new playlist
		$fp = fopen (M3UDIR."/".$newplaylistfile, "w") or die ("FOUT: Kan ".M3UDIR."/".$newplaylistfile." niet openen om te schrijven, bekijk de permissies");
		fwrite ($fp, "#EXTM3U\n");													// Write 1st line: #EXTM3U
		fwrite ($fp, "vlc://quit\n");														// Write last line: vlc://quit
		fclose ($fp );
		$_POST['m3uplaylist'] = $newplaylistfile;								// Set fields
		$playlistfile = $newplaylistfile;												// Set fields
		$phash = sha1_file(M3UDIR."/".$playlistfile);							// Calculate hash
	}
	
	if (isset($_POST['submit']) && $_POST["submit"] == "Verwijder Draaiboek") {
		// Process "Delete Playlist" button
		unlink (M3UDIR."/".$playlistfile);												// Remove the file
		$_POST['m3uplaylist'] = "";														// Clear fields
		$playlistfile = "";																		// Clear fields
		$phash = sha1_file (M3UDIR."/".$playlistfile);							// Clear hash
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
			<a href="tv.php"><?php echo "terug"; ?></a><?php echo ' | '; ?>
			<a href="help/playlisthelp.php"><?php echo "help"; ?></a>
			</fieldset>
			<fieldset class="main">
				<legend>Draaiboeken</legend>
				<fieldset class="buttons">
					<legend>Nieuw</legend>
					<table>
						<tr>
						<td><input type="name" name="newplaylist" size="15" /></td>
						<td><input type="submit" name="submit" value="Nieuw Draaiboek" /></td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="buttons">
					<legend>Bewerken</legend>
					<table>
						<tr>
						<td><?php echo @dirtoselectExt('m3uplaylist', M3UDIR, $_SESSION['m3u'], false, 0, '.m3u'); ?></td>
						<td><input type="submit" name="submit" value="Bewerk Draaiboek" /></td>
						<!--TODO: <td><input type="submit" name="submit" value="Verwijder Draaiboek" /></td>-->
						</tr>
					</table>
				</fieldset>
				<fieldset class="edit">
					<legend>Draaiboek</legend>
					<input type="hidden" name="phash" value="<?php echo $phash ?>" />
					<pre>
					<table>
						<?php
							if (file_exists(M3UDIR."/".$playlistfile)) {
								echo "<tr>";																			// Make table header
								echo "<td></td>";
								echo "<td>Video</td>";
								echo "<td>Draait sinds (datum en tijd)</td>";
								echo "<td>Aantal dagen</td>";
								echo "<td>Speeltijd</td>";
								echo "</tr>";
								$currentdatetime = date("d-m-Y H:i:s");
								$data = file ( M3UDIR."/".$playlistfile );
								$n = 0;
								$datetime = " ";
								$total = 0;
								foreach ( $data as $line ) {													// Print table
									if (strncmp ($line, "#DATE:", 6) == 0 ) {							// If line is date, store and print in next round
										$datetime = substr($line, 6, -1);
									} else {																				// If line isn't a date then print
										echo "<tr>";
										if (strncmp ($line, "#EXTM3U", 7) != 0 && strncmp ($line, "vlc://quit", 8) != 0) {
											$movie = new ffmpeg_movie (trim($line), false);		// Open movie with ffmpeg
											$duration = $movie->getDuration();							// Get duration
											$total += $duration;													// Calculate total duration
											
											if ($selected[$n]==FALSE)											// Print box (selected or not)
												echo "<td><input type='checkbox' name='line[$n]' /></td>";
											else
												echo "<td><input type='checkbox' name='line[$n]' checked='true' /></td>";
											echo "<td>".$line."</td>" ;										// Print video line
											if ($datetime!=" ") {													// Print date and delta date if date is available
												echo "<td>".$datetime."</td>";							// Print date
												echo "<td>".@GetDeltaTime($currentdatetime, $datetime)."</td>";
											} else {
												echo "<td></td><td></td>";
											}
											if ($duration>=3600)
												printf ("<td>%02d:%02d:%02d</td>", floor($duration/3600), floor(($duration/60)%60), floor($duration%60));
											else
												printf ("<td>   %02d:%02d</td>", floor(($duration/60)%60), floor($duration%60));
										} else {																			// Print non editable lines without checkbox
											echo "<td></td>";
											echo "<td>".$line."</td>" ;
											echo "<td></td>";
											echo "<td></td>";
										}
										echo "</tr>";
										$n++;																				// Next line
										$datetime = " ";
									}
								}
								echo "<tr><td></td><td></td><td></td><td></td>";
								printf ("<td>%02d:%02d:%02d (totaal)</td></tr>", floor($total/3600), floor(($total/60)%60), floor($total%60));
							} else {
									echo "<tr>";
									echo "<td>geen draaiboek ingeladen</td>";
									echo "</tr>";
							}
						?>
					</table>
					</pre>
				</fieldset>
				<fieldset class="buttons">
					<legend>Draaiboek Acties</legend>
					<table>
						<tr>
							<td><?php echo @dirtoselectMime('videofile', VIDEODIR, '', false, 0, 'video'); ?></td>
							<td><input type="submit" name="submit" value="Toevoegen" /></td>
							<td><input type="submit" name="submit" value="Verwijderen" /></td>
							<td><input type="submit" name="submit" value="Omhoog"/></td>
							<td><input type="submit" name="submit" value="Omlaag"/></td>
							<td><input type="submit" name="submit" value="Alle"/></td>
							<td><input type="submit" name="submit" value="Geen"/></td>
						</tr>
					</table>
				</fieldset>
			</fieldset>
		</form>
	</body>
</html>
