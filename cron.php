<?php	
	// cron.php - Edit cron jobs for scheduling television broadcasts
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
	// This file is partly based on the package JCron Scheduler released under the 
	// GNU General Access License. And Basic TODO list (todo.php) written by 
	// Amadeus Stevenson. See acknowledgements.txt for the license of todo.php 

	require_once ('tvfunctions.php');											// Functions for TV scripts
	require_once ('tvconfig.php');												// Settings for TV scripts

	testConfiguration();																	// Test if configuration of directorys is OK


	// If cronfile doesn't exist, create one
	if (! file_exists (CRONFILE)) {
		$fp = fopen (CRONFILE , "w") or die ("FOUT: Kan ".CRONFILE." niet openen om te schrijven, bekijk de permissies");
		fwrite ($fp, "# eof\n");														// Fill the file with the eof marker
		fclose ($fp);
	}
	$chash = sha1_file (CRONFILE);												// Calculate hash (used for detecting browser reload)

	// Read the cron file in a array and count number of lines
	$data = file (CRONFILE);
	$fp = fopen (CRONFILE , "r") or die ("FOUT: Kan ".CRONFILE." niet openen om te lezen, bekijk de permissies");
	$cronlength = 0;
	foreach ($data as $line) {
		$cron[$cronlength] = $line;												// Store one line
		if (strncmp ($line, "# eof", 5) != 0 && strncmp ($line, "@", 1) != 0) {	// Is line editable?
			$croneditable[$cronlength] = TRUE;								// Line is editable by the user
		} else {
			$croneditable[$cronlength] = FALSE;								// Line isn't editable by the user
		}
		$selected[$cronlength]=FALSE;											// Initialize selected list 
		$cronlength++;																	// Number of lines is stored in cronlength
	}
	fclose ($fp);	
	
	if (isset ($_POST["submit"] ) && $_POST["chash"] == $chash) {
		$comment=$_POST["comment"];											// Get comment entered by the user
		$play=$_POST['m3u'];															// Get playlist selected by the user
		if ($_POST["submit"] == "Toevoegen") {							// User selected the "add" button
			// Process "add" button
			if (! empty ($play)) {
				$cronline = "";																// Start with an empty line
				$test=$_POST['minute'];												// Get minutes
				$counter=0;
				if ($test){																	// Are one or more minutes selected in the multiple selection box?
					foreach ($test as $t) {
						if ($counter==0)													// Make a comma separated list for the minutes
							$cronline=$cronline.$t;									// First entry in comma separated list
						else
							$cronline=$cronline.",".$t;								// Other entrys in comma separated list
						$counter++;														// Count ocurrences of minutes
					}
					$cronline=$cronline." ";											// End with a space as seperator
				} else {
					$cronline=$cronline."0 ";										// None is selected in select box
				}

				$test=$_POST['hour'];													// Get hours
				$counter=0;
				if ($test) {																	// Are one or more hours selected in the multiple selection box?
					foreach ($test as $t) {
						if ($counter==0)													// Make a comma separated list for the minutes
							$cronline=$cronline.$t;									// First entry in comma separated list
						else
							$cronline=$cronline.",".$t;								// Other entrys in comma separated list
						$counter++;														// Count ocurrences of hours
						if ($t=="*") break;												// Do not process further when "*" is selected
					}
					$cronline=$cronline." ";											// End with a space as seperator
				} else {
					$cronline=$cronline."* ";										// None is selected in select box
				}

				$test=$_POST['day'];													// Get days
				$counter=0;
				if ($test) {																	// Are one or more days selected in the multiple selection box?
					foreach ($test as $t){
						if ($counter==0)													// Make a comma separated list for the minutes
							$cronline=$cronline.$t;									// First entry in comma separated list
						else
							$cronline=$cronline.",".$t;								// Other entrys in comma separated list
						$counter++;														// Count ocurrences of days
						if ($t=="*") break;												// Do not process further when "*" is selected
					}
					$cronline=$cronline." ";											// End with a space as seperator
				} else {
					$cronline=$cronline."* ";										// None is selected in select box
				}

				$test=$_POST['month'];												// Get months
				$counter=0;
				if ($test) {																	// Are one or more months selected in the multiple selection box?
					foreach ($test as $t){
						if ($counter==0)													// Make a comma separated list for the minutes
							$cronline=$cronline.$t;									// First entry in comma separated list
						else
							$cronline=$cronline.",".$t;								// Other entrys in comma separated list
						$counter++;														// Count ocurrences of months
						if ($t=="*") break;												// Do not process further when "*" is selected
					}
					$cronline=$cronline." ";											// End with a space as seperator
				} else {
					$cronline=$cronline."* ";										// None is selected in select box
				}

				$test=$_POST['weekday'];											// Get weekdays
				$counter=0;
				$weekend=FALSE;
				$workweek=FALSE;
				if ($test) {																	// Are one or more weekdays selected in the multiple selection box?
					foreach ($test as $t){
						if ( (!(($weekend==TRUE)&&(($t=="0")||($t=="6")))) && 
						      (!(($workweek==TRUE)&&(($t=="1")||($t=="2")||($t=="3")||($t=="4")||($t=="5")))))	// Skip entrys already in weekend or workweek
						{
							if ($counter==0)												// Make a comma separated list for the minutes
								$cronline=$cronline.$t;								// First entry in comma separated list
							else
								$cronline=$cronline.",".$t;							// Other entrys in comma separated list
							$counter++;
							if ($t=="*") break;											// Do not process further when "*" is selected
							if ($t=="0,6") $weekend=TRUE;						// True when "weekend" is selected
							if ($t=="1-5") $workweek=TRUE;					// True when "workweek is selected
						}
					}
					$cronline=$cronline." ";											// End with a space as seperator
				} else {
					$cronline=$cronline."* ";										// None is selected in select box
				}

				$cronline=$cronline.VIDEOCOMMAND.M3UDIR."/".$play;	// Add command executed at the scheduled time
			}
			if (! empty ($comment)) {												// Add comment
				if (! empty ($play))
					$cronline=$cronline." ";
				$cronline=$cronline.stripslashes("# ".$comment);
			}

			// Store the new line in cron array
			if ($cronlength > 0 && $croneditable[$cronlength-1]==FALSE) {		// Is the last line editable?
				$cron[$cronlength] = $cron[$cronlength-1];				// Shift last line down
				$cron[$cronlength-1] = $cronline."\n";						// Store the new line
				$selected[$cronlength-1]=TRUE;								// Line is editable by he user
				$cronlength++;															// Increment length
			} else {
				$cron[$cronlength] = $cronline."\n";							// Store the new line as the last line
				$selected[$cronlength]=TRUE;									// New cron line is editable by the user
				$cronlength++;															// Increment length
			}
			
			// Write the file to disc
			$fp = fopen (CRONFILE, "w") or die ("FOUT: Kan ".CRONFILE." niet openen om te schrijven, bekijk de permissies");
			for ($i = 0; $i < $cronlength; $i++) {
				fwrite ($fp, $cron[$i]);
			}
			fclose ($fp) ;

		} elseif ($_POST["submit"] == "Verwijderen") {
			// Process "Remove" button
			$data = file (CRONFILE);
			$fp = fopen (CRONFILE , "w+") or die ("FOUT: Kan ".CRONFILE." niet openen om te schrijven, bekijk de permissies");
			$n = 0;
			foreach ($data as $line) {											// Repeat for every line
				if ( empty ($_POST["line"][$n])) {
					fwrite ($fp, $line);												// Write every line when checkbox is not set
				}
				$n++;
			}
			fclose ($fp);
		} elseif ($_POST["submit"] == "Omhoog") {
			// Process "Up" button
			if (! empty ($_POST["line"][0]))									// Do not shift line 0
				$croneditable[0]=FALSE;
			for ($n=0; $n<$cronlength; $n++) {
				if ((! empty ($_POST["line"][$n])) && $n>=1) {	// Checkbox is set?
					if ($croneditable[$n-1]==TRUE) {						// When previous line is editable...
						$tmp=$cron[$n];											// ...swap two lines
						$cron[$n]=$cron[$n-1];
						$cron[$n-1] =$tmp;
						$selected[$n-1]=TRUE;									// Also set previous checkbox
					} else {																// Prevent shifting out of range
						$croneditable[$n]=FALSE;
					}
				} else {
					$selected[$n]=FALSE;											// Deselect surrent checkbox
				}
				// Write the file to disc
				$fp = fopen (CRONFILE, "w") or die ("FOUT: Kan ".CRONFILE." niet openen om te schrijven, bekijk de permissies");
				for ($i = 0; $i < $cronlength; $i++) {
					fwrite ($fp, $cron[$i]);
				}
				fclose ($fp) ;
			}
		} elseif ($_POST["submit"] == "Omlaag") {
			// Process "down" button
			if (! empty ($_POST["line"][$cronlength-1]))				// Do not shit last line
				$croneditable[$cronlength-1]=FALSE;
			for ($n=$cronlength-1; $n>=0; $n--) {
				if ((! empty ($_POST["line"][$n])) && $n<$cronlength-1) {	// Checkbox is set?
					if ($croneditable[$n+1]==TRUE) {					// When next line is editable...
						$tmp=$cron[$n];											// ...swap two lines
						$cron[$n]=$cron[$n+1];
						$cron[$n+1] =$tmp;
						$selected[$n+1]=TRUE;									// Also set next checkbox
					} else {																// Prevent shifting out of range
						$croneditable[$n]=FALSE;
					}
				} else {
					$selected[$n]=FALSE;											// Deselect current checkbox
				}
				// Write the file to disc
				$fp = fopen (CRONFILE, "w") or die ("FOUT: Kan ".CRONFILE." niet openen om te schrijven, bekijk de permissies");
				for ($i = 0; $i < $cronlength; $i++) {
					fwrite ($fp, $cron[$i]);
				}
				fclose ($fp) ;
			}
		} elseif ($_POST["submit"] == "Alle") {
			// Process "every" button
			for ($n=0; $n<$cronlength; $n++) {
				$selected[$n]=TRUE;												// Select all checkboxes
			}
		} elseif ($_POST["submit"] == "Geen") {
			// Process "none" button
			for ($n=0; $n<$cronlength; $n++) {
				$selected[$n]=FALSE;												// Deselect all checkboxes
			}
		}
		$chash = sha1_file (CRONFILE);
		// This is where the cron will be installed
		exec ( 'sudo -u tv crontab '.CRONFILE );
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
			<b>TV beheer pagina, versie <?php echo VERSION;?></b>
			<fieldset class="navigation">
			<?php echo 'Gebruiker: ', $_SERVER['PHP_AUTH_USER'], ' | '; ?>
			<a href="tv.php"><?php echo "terug"; ?></a><?php echo ' | '; ?>
			<a href="help/cronhelp.php"><?php echo "help"; ?></a>
			</fieldset>
			<fieldset class="main">
				<legend>Planning</legend>
				<fieldset class="buttons">
					<legend>Acties</legend>
					<table>
						<tr>
							<td>Uur</td><td>Minuut</td><td>Dag</td><td>Maand</td><td>Dag v/d week</td><td>Playlist</td>
						</tr>
						<tr>
							<td>
								<select name="hour[]" size="10" multiple="multiple">
								<option value="*">Elk uur</option>
								<option value="0">0 = 12 AM/Middernacht</option>
								<option value="1">1 = 1 AM</option>
								<option value="2">2 = 2 AM</option>

								<option value="3">3 = 3 AM</option>
								<option value="4">4 = 4 AM</option>
								<option value="5">5 = 5 AM</option>
								<option value="6">6 = 6 AM</option>
								<option value="7">7 = 7 AM</option>
								<option value="8">8 = 8 AM</option>
								<option value="9">9 = 9 AM</option>
								<option value="10">10 = 10 AM</option>
								<option value="11">11 = 11 AM</option>

								<option value="12">12 = 12 PM/Middag</option>
								<option value="13">13 = 1 PM</option>
								<option value="14">14 = 2 PM</option>
								<option value="15">15 = 3 PM</option>
								<option value="16">16 = 4 PM</option>
								<option value="17">17 = 5 PM</option>
								<option value="18">18 = 6 PM</option>
								<option value="19">19 = 7 PM</option>
								<option value="20">20 = 8 PM</option>

								<option value="21">21 = 9 PM</option>
								<option value="22">22 = 10 PM</option>
								<option value="23">23 = 11 PM</option>
								</select>
							</td>
							
							<td>
								<select name="minute[]" size="10" multiple="multiple">
								<option value="0">00</option>
								<option value="1">01</option>
								<option value="2">02</option>
								<option value="3">03</option>
								<option value="4">04</option>
								<option value="5">05</option>
								<option value="6">06</option>
								<option value="7">07</option>

								<option value="8">08</option>
								<option value="9">09</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>

								<option value="17">17</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="20">20</option>
								<option value="21">21</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>
								<option value="25">25</option>

								<option value="26">26</option>
								<option value="27">27</option>
								<option value="28">28</option>
								<option value="29">29</option>
								<option value="30">30</option>
								<option value="31">31</option>
								<option value="32">32</option>
								<option value="33">33</option>
								<option value="34">34</option>

								<option value="35">35</option>
								<option value="36">36</option>
								<option value="37">37</option>
								<option value="38">38</option>
								<option value="39">39</option>
								<option value="40">40</option>
								<option value="41">41</option>
								<option value="42">42</option>
								<option value="43">43</option>

								<option value="44">44</option>
								<option value="45">45</option>
								<option value="46">46</option>
								<option value="47">47</option>
								<option value="48">48</option>
								<option value="49">49</option>
								<option value="50">50</option>
								<option value="51">51</option>
								<option value="52">52</option>

								<option value="53">53</option>
								<option value="54">54</option>
								<option value="55">55</option>
								<option value="56">56</option>
								<option value="57">57</option>
								<option value="58">58</option>
								<option value="59">59</option>
								</select>
							</td>
							
							<td>
								<select name="day[]" size="10" multiple="multiple">
								<option value="*">Elke dag</option>
								<option value="1">01</option>
								<option value="2">02</option>
								<option value="3">03</option>
								<option value="4">04</option>
								<option value="5">05</option>
								<option value="6">06</option>
								<option value="7">07</option>

								<option value="8">08</option>
								<option value="9">09</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>

								<option value="17">17</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="20">20</option>
								<option value="21">21</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>
								<option value="25">25</option>

								<option value="26">26</option>
								<option value="27">27</option>
								<option value="28">28</option>
								<option value="29">29</option>
								<option value="30">30</option>
								<option value="31">31</option>
								</select>							
							</td>
							
							<td>
								<select name="month[]" size="10" multiple="multiple">
								<option value="*">Elke maand</option>
								<option value="1">Januari</option>
								<option value="2">Februari</option>
								<option value="3">Maart</option>
								<option value="4">April</option>
								<option value="5">Mei</option>

								<option value="6">Juni</option>
								<option value="7">Juli</option>
								<option value="8">Augustus</option>
								<option value="9">September</option>
								<option value="10">Oktober</option>
								<option value="11">November</option>
								<option value="12">December</option>
								</select>
							</td>

							<td>
								<select name="weekday[]" size="10" multiple="multiple">
								<option value="*">Elke dag</option>
								<option value="1-5">Werkdagen</option>
								<option value="0,6">Weekend</option>
								<option value="0">Zondag</option>
								<option value="1">Maandag</option>
								<option value="2">Dinsdag</option>
								<option value="3">Woensdag</option>
								<option value="4">Donderdag</option>

								<option value="5">Vrijdag</option>
								<option value="6">Zaterdag</option>
								</select>
							</td>
							
							<td>
							<?php echo @dirtoselectExt('m3u', M3UDIR, '', true, 0, '.m3u'); ?>
							</td>
						</tr>
					</table>

					<table>
					<tr>
						<td>Commentaar:</td>
						<td><input type="text" name="comment" size="35" /></td>
					</tr>
					</table>

					<table>
						<tr>
							<td><input type="submit" name="submit" value="Toevoegen"/></td>
							<!--TODO: <td><input type="submit" name="submit" value="Bewerken"/></td>-->
							<td><input type="submit" name="submit" value="Verwijderen"/></td>
							<td><input type="submit" name="submit" value="Omhoog"/></td>
							<td><input type="submit" name="submit" value="Omlaag"/></td>
							<td><input type="submit" name="submit" value="Alle"/></td>
							<td><input type="submit" name="submit" value="Geen"/></td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="edit">
					<legend>Overzicht</legend>
					<input type="hidden" name="chash" value="<?php echo $chash ?>" />
					<pre>
					<table>
						<?php
							// TODO: Make a cron parser to show the parameters in columns
							$hour = "";
							$minute = "";
							$day = "";
							$month = "";
							$weekday = "";
							$command = "";
							$comment = "";
							echo "<tr>";
							echo "<td></td>";
							echo "<td>Uur</td><td>Minuut</td><td>Dag</td><td>Maand</td>";
							echo "<td>Weekdag</td><td>Video</td><td>Commentaar</td>";
							echo "</tr>";
							$data = file (CRONFILE);
							$n = 0;
							foreach ($data as $line) {
								echo "<tr>";
								if (strncmp ($line, "# eof", 5) != 0 && strncmp ($line, "@", 1) != 0) {
									if ($selected[$n]==FALSE)
										echo "<td><input type='checkbox' name='line[$n]' /></td>";
									else
										echo "<td><input type='checkbox' name='line[$n]' checked='true' /></td>";
								} else {
									echo "<td></td>";
								}
								if (@cronParse ($line, $hour, $minute, $day, $month, $weekday, $command, $comment) == true) {
									echo "<td>".$hour."</td>" ;
									echo "<td>".$minute."</td>" ;
									echo "<td>".$day."</td>" ;
									echo "<td>".$month."</td>" ;
									echo "<td>".$weekday."</td>" ;
									echo "<td>".substr ($command, strlen(VIDEOCOMMAND))."</td>" ;
									echo "<td>".$comment."</td>" ;
								} else {
									echo "<td colspan='7'><strong>".$line."</strong></td>" ;
								}
								echo "</tr>";
								$n++;
							}
						?>
					</table>
					</pre>
				</fieldset>
			</fieldset>
		</form>
	</body>
</html>
