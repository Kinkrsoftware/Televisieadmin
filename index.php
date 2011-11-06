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
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
	<head>
		<title><?php echo OWNER;?></title>
		<link rel="stylesheet" href="tv.css" type="text/css" />
	</head>
	
	<body>
		<form method="post" enctype="multipart/form-data">
			<b>TV Upload pagina, versie <?php echo VERSION;?></b><br /><br />
			<fieldset class="navigation">
			<?php echo 'Gebruiker: ', $_SERVER['PHP_AUTH_USER']; ?>
			</fieldset>
			<fieldset class="main">
				<legend>Video's</legend>
				<fieldset class="buttons">
					<legend>Upload</legend>
					<input name="userfile" type="file" style="float: left;" /> <input type="submit" value="Upload" class="button" />
				</fieldset>
			</fieldset>
		</form>
	</body>
</html>
