<?php	
	// playlist.php - Edit video files for television broadcasts
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
	require_once ('tvconfig.php');												// Settings for TV scripts
	
	testConfiguration();																	// Test if configuration of directorys is OK

	if (isset ($_FILES['userfile']['tmp_name'])) {
		// Process "Upload"
		move_uploaded_file($_FILES['userfile']['tmp_name'], VIDEODIR."/".CleanFileName($_FILES['userfile']['name']));
	}
	if (isset ($_POST["submit"] )) {
		if ($_POST["submit"] == "Verwijderen") {
			// Process "Delete" button
			foreach ($_POST as $key => $value) {
				if (strpos($key, 'line_') === 0) {
					$line = substr($key, 5);
					$filename = $_POST['file_'.$line];
					if (strpos($filename, '/') === FALSE) {
						unlink(VIDEODIR.'/'.$filename);
					}
				}
			}
		} elseif ($_POST["submit"] == "Archiveren") {
			// Process "Archive" button
			foreach ($_POST as $key => $value) {
				if (strpos($key, 'line_') === 0) {
					$line = substr($key, 5);
					$filename = $_POST['file_'.$line];
					if (strpos($filename, '/') === FALSE) {
						if (isVideo($filename)) {
							rename (VIDEODIR."/".$filename, ARCHIVEDIR."/".$filename);
							$img = sprintf("capture/%s.png", $filefile);
							if (file_exists ($img)) {
								unlink ($img);
							}
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
			<a href="help/videohelp.php"><?php echo "help"; ?></a>
			</fieldset>
			<fieldset class="main">
				<legend>Video's</legend>
				<fieldset class="buttons">
					<legend>Upload</legend>
					<input name="userfile" type="file" style="float: left;" /> <input type="submit" value="Upload" class="button" />
				</fieldset>
				<fieldset class="buttons">
					<legend>Acties</legend>
					<tr>
					<td><input type="submit" name="submit" value="Archiveren" /></td>
					<td><input type="submit" name="submit" value="Verwijderen" /></td>
					</tr>
				</fieldset>
				<fieldset class="edit">
					<legend>Actieve Video's</legend>
					<pre>
					<table>
						<?php
							$videolist = array();
							@makeVideoListFromCron(CRONFILE, $videolist);								// Make a list of all video's in active play lists
							echo "<tr>";																						// Make table header
							echo "<td></td>";
							echo "<td>Video</td>";
							echo "<td>Bestands datum</td>";
							echo "<td>Grootte</td>";
							echo "<td>Speeltijd</td>";
							echo "<td>Aspect</td>";
							echo "<td>Pixelformat</td>";
							echo "<td>Bitrate</td>";
							echo "<td>Video Codec</td>";
							echo "<td>Audio</td>";
							echo "<td>Ch</td>";
							echo "<td>Plaatje</td>";
							echo "</tr>";
							$n = 0;
							if ($handle = opendir(VIDEODIR)) {
								while (false !== ($file = readdir($handle))) {
									if (!is_dir(VIDEODIR.'/'.$file)) {
										if (isVideo($file)) {						// if file is video file
											$modified = filemtime (VIDEODIR."/".$file);						// Get modified date of video file
											$size = filesize(VIDEODIR."/".$file);									// Size of video file
											$movie = new ffmpeg_movie (VIDEODIR."/".$file, false);	// open movie with ffmpeg
											$duration = $movie->getDuration();									// Get duration
											$height = $movie->getFrameHeight();								// Get aspect ratio
											$width = $movie->getFrameWidth();
											$pixelformat = $movie->getPixelFormat();						// Get pixel format string
											$bitrate = $movie->getBitrate();										// Get bit rate
											$videocodec = $movie->getVideoCodec();						// Get video codec
											$channels = @$movie->getAudioChannels();						// Get number of channels
											if ($channels > 0)
												$audiocodec = $movie->getAudioCodec();						// Get audio codec
											$img = sprintf("%s/%s.png", CAPTUREDIR, $file);				// Make a filename for capture image
											if (file_exists ($img)) {
												$capturemodified = filemtime ($img);							// Get modification date of capture image
												if ($capturemodified < $modified) {
													unlink ($img);																// Delete captue if video is newer than caputure
												}
											}
											if (!file_exists ($img)) {														// Make if capture does not exists
												$ff_frame = $movie->getFrame(73);								// Get frame 73
												if (!$ff_frame)
													$ff_frame = $movie->getFrame(1);
												
												if ($ff_frame) {
													$gd_image = $ff_frame->toGDImage();						// Convert to GDI
													if ($gd_image) {
														$gd_thumb = imagecreatetruecolor(VIDEOTHUMBRESOLUTIONW, VIDEOTHUMBRESOLUTIONH);
														imagecopyresampled($gd_thumb, $gd_image, 0, 0, 0, 0, VIDEOTHUMBRESOLUTIONW, VIDEOTHUMBRESOLUTIONH, imagesx($gd_image), imagesy($gd_image));
														
														imagedestroy($gd_image);									// Delete GDimage
														imagepng($gd_thumb, $img);								// Convert to PNG and store in "capture" dir
														imagedestroy($gd_thumb);									// Delete GDimage
													}
												} 
											} 

											echo "<tr>";																		// Make table header
											if (@inArrayStr($file, $videolist)==false) {							// Is video in playlist (php in_array doesn't work)
												echo "<td><input type='checkbox' name='line_$n' /><input type='hidden' name='file_$n' value='$file' /></td>";
											} else {
												echo "<td></td>";
											}
											echo "<td>$file</td>";														// File name
											echo "<td style=\"text-align: right\">".date("d-m-Y H:i", $modified)."</td>";			// Modification date of file
											echo "<td style=\"text-align: right\">".@niceSize($size)."</td>";								// Size of file
											if ($duration>=3600)															// Duration
												printf ("<td style=\"text-align: right\">%02d:%02d:%02d</td>", floor($duration/3600), floor(($duration/60)%60), floor($duration%60));
											else
												printf ("<td style=\"text-align: right\">%02d:%02d</td>", floor(($duration/60)%60), floor($duration%60));
											echo "<td>$width x $height</td>";									// Aspect ratio
											echo "<td>$pixelformat</td>";											// Pixel format string
											echo "<td>$bitrate</td>";												// Bitrate
											echo "<td>$videocodec</td>";											// Video codec
											if ($channels > 0) {
												echo "<td>$audiocodec</td>";											// Audio codec
												echo "<td>$channels</td>";												// Number of channels
											} else {
												echo '<td colspan="2"><span style="color: #f00; font-weight: bold;">GEEN GELUID</span></td>';
											}
											if (file_exists ($img))															// Display if capure exists
												echo "<td><img src=\"$img\" width=\"100\" height=\"75\" border=\"1\" alt=\"Screen Capture\" /></td>";										
											$n++;
											echo "</tr>\n";
										}
									}
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
