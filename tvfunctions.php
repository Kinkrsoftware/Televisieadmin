<?php
// tvfunctions.php
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

// Calculate time since now. Code taken from: http://nl2.php.net/manual/en/function.time.php

require_once('tvconfig.php');

function authorize($atmost) {
    $level = 2;
    
    if (in_array($_SERVER['PHP_AUTH_USER'], array('ton', 'skinkie', 'marcel'))) {
        $level = 0;
    } elseif (in_array($_SERVER['PHP_AUTH_USER'], array('henri'))) {
        $level = 1;
    } 
    
    if ($atmost < $level) {
        return header('Location: index.php');
        exit;
    }
}

function nicetime($date)
{
	if(empty($date)) {
		return "No date provided";
	}

	$period = array("seconde", "minuut", "uur", "dag", "week", "maand", "jaar", "decade");
	$periods = array("seconden", "minuten", "uren", "dagen", "weken", "maanden", "jaren", "decades");
	$lengths = array("60","60","24","7","4.35","12","10");

	$now = time();
	$unix_date = strtotime($date);

	// check validity of date
	if(empty($unix_date)) {   
		return "datum verkeerd";
	}

	// is it future date or past date
	if($now > $unix_date) {   
		$difference = $now - $unix_date;
		$tense = "geleden";

	} else {
		$difference = $unix_date - $now;
		$tense = "vanaf nu";
	}

	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}

	$difference = round($difference);

	if($difference != 1) {
		return "$difference $periods[$j]";
	} else {
		return "$difference $period[$j]";
	}
}

// Print filesize nicely
function niceSize($size)
{
	if ($size < 1024) {
		return "$size Bytes";
	} elseif ($size < 1024*1024){
		$size = round($size/1024, 1);
		return "$size KBytes";
	} elseif ($size < 1024*1024*1024) {
		$size = round($size/1024/1024, 1);
		return "$size MBytes";
	} elseif ($size < 1024*1024*1024*1024) {
		$size = round($size/1024/1024/1024, 1);
		return "$size GBytes";
	} elseif ($size < 1024*1024*1024*1024*1024) {
		$size = round($size/1024/1024/1024/1024, 1);
		return "$size TBytes";
	}
}

// Calculate delta time. Code taken from: http://nl2.php.net/date-diff
function GetDeltaTime($dtTime1, $dtTime2)
{
	$nUXDate1 = strtotime($dtTime1);
	$nUXDate2 = strtotime($dtTime2);

	$strDeltaTime = "";
	$nUXDelta = $nUXDate1 - $nUXDate2;
	if ($nUXDelta>60)
		$strDeltaTime = "" . $nUXDelta/60/60/24; // sec -> hour -> days

	$nPos = strpos($strDeltaTime, ".");
	if (nPos !== false)
		$strDeltaTime = substr($strDeltaTime, 0, $nPos + 2);

	return $strDeltaTime;
}

// Remove special characters, Code taken from: http://nl2.php.net/manual/en/function.preg-replace.php
function CleanFileName ($Raw) {
	$Raw = trim($Raw);
	$RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-.])", "(-{2,})" );
	$ReplaceWith = array("-", "", "-");
	return preg_replace($RemoveChars, $ReplaceWith, $Raw);
}

function testConfiguration() {
	$extension = "ffmpeg";
	$extension_soname = $extension . "." . PHP_SHLIB_SUFFIX;
	$extension_fullname = PHP_EXTENSION_DIR . "/" . $extension_soname;
	if(!extension_loaded($extension)) {
		die ("FOUT: Extensie $extension niet geconfigureerd\n");
	}
	if (!is_writable (CRONFILE)) {
		die ("FOUT: ".CRONFILE." is niet beschrijfbaar");
	}
	$m3udir = sprintf("%s", M3UDIR);	
	if (!is_writable ($m3udir)) {
		die ("FOUT: draaiboek directory $m3udir is niet beschrijfbaar");
	}
	$videodir = sprintf("%s", VIDEODIR);	
	if (!is_writable ($videodir)) {
		die ("FOUT: Video directory $videodir is niet beschrijfbaar");
	}
	$archivedir = sprintf("%s", ARCHIVEDIR);	
	if (!is_writable ($archivedir)) {
		die ("FOUT: Archief directory $archivedir is niet beschrijfbaar");
	}
	$capturedir = sprintf("%s", CAPTUREDIR);	
	if (!is_writable ($capturedir)) {
		die ("FOUT: Archief directory $capturedir is niet beschrijfbaar");
	}
}

function dirtoselectMime($name, $dir, $active = '', $empty = false, $maxdate = 0, $mime = 'video') {
	$templates = array();
	if ($empty===true) $templates[] = '';
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (!is_dir($dir.'/'.$file)) {
					if ($mime=='' || ($mime == 'video' && isVideo(VIDEODIR."/".$file))) {
						if ($maxdate == 0 || (filectime($dir.'/'.$file) > $maxdate)) {
							$templates[]=$file;
						}
					}
				}
			}
			closedir($dh);
		}
	}
	sort($templates);
	$result = '';
	if ($active != '' && !in_array($active, $templates)) {
		$result = '<input type="text" name="'.$name.'" value="'.$active.'" />';
	} else {
		$result = '<select name="'.$name.'">';
		foreach ($templates as $key => $value)
			$result.='<option value="'.$value.'"'.($value==$active?' selected="1"':'').'>'.$value.'</option>';
		$result .= '</select>';
	}
	return $result;
}

function multidirtoselectExts($name, $dirs, $active = '', $empty = false, $maxdate = 0, $cmpext = array('m3u'), $append = array()) {
	$templates = array();
	if ($empty===true) $templates[] = '';
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $path = $dir.'/'.$file;
                    if (!is_dir($path)) {
                        $fileext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        if (in_array ($fileext, $cmpext)) {						// if file has correct extension
                            if ($maxdate == 0 || (filectime($path) > $maxdate)) {
                                $templates[]=$path;
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
    }

	sort($templates);
    $templates = array_merge($templates, $append);

	$result = '';
	if ($active != '' && !in_array($active, $templates)) {
		$result = '<input type="text" name="'.$name.'" value="'.$active.'" />';
	} else {
		$result = '<select name="'.$name.'">';
		foreach ($templates as $key => $value)
			$result.='<option value="'.$value.'"'.($value==$active?' selected="1"':'').'>'.$value.'</option>';
		$result .= '</select>';
	}
    return $result;
}



function dirtoselectExt($name, $dir, $active = '', $empty = false, $maxdate = 0, $cmpext = '.m3u') {
	$templates = array();
	if ($empty===true) $templates[] = '';
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (!is_dir($dir.'/'.$file)) {
					$fileext = substr($file, (strlen($cmpext)*-1));											// Get the last characters from the name
					if (strncmp ($fileext, $cmpext, strlen($cmpext)) == 0) {						// if file has correct extension
						if ($maxdate == 0 || (filectime($dir.'/'.$file) > $maxdate)) {
							$templates[]=$file;
						}
					}
				}
			}
			closedir($dh);
		}
	}
	sort($templates);
	$result = '';
	if ($active != '' && !in_array($active, $templates)) {
		$result = '<input type="text" name="'.$name.'" value="'.$active.'" />';
	} else {
		$result = '<select name="'.$name.'">';
		foreach ($templates as $key => $value)
			$result.='<option value="'.$value.'"'.($value==$active?' selected="1"':'').'>'.$value.'</option>';
		$result .= '</select>';
	}
	return $result;
}

// Count the files in a directory with extension filter
function countFilesExt($dir, $extension) {
	$count = 0;
	if (is_dir($dir)) {
		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if (!is_dir($dir.'/'.$file)) {
					$fileext = substr($file, strlen($extension)*-1);								// Get the last characters from the name
					if (strncmp ($fileext, $extension, strlen($extension)) == 0) {	// if file has correct extension
						$count++;
					}
				}
			}
			closedir($handle);
		}
	}
	return $count;
}

// Count the files in a directory with mime filter
function countFilesMime($dir, $mime, &$size, &$duration, &$count) {
	$size = 0;
	$duration = 0;
	$count = 0;
	if (is_dir($dir)) {
		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if (!is_dir($dir.'/'.$file)) {
					$mimetype = mime_content_type($dir."/".$file);					// Get mimetype
					// This function is deprecated, should use finfo_file() //
					//if (strncmp ($mimetype, $mime, strlen($mime)) == 0) {	// if file is video file
					$size += filesize($dir."/".$file);
					//							$movie = new ffmpeg_movie ($dir."/".$file, false);
					//							$duration += $movie->getDuration();
					$count++;
					//}
				}
			}
			closedir($handle);
		}
	}
}

function makeVideoListFromCron($file, &$videolist) {
	$data = file ($file);
		$extension = '.m3u';
	foreach ($data as $line) {
		if (@cronParse ($line, $hour, $minute, $day, $month, $weekday, $command, $comment) == true) {
			$m3ufile = substr (substr ($command, strlen(VIDEOCOMMAND)), 0, -1);
			$fileext = substr($m3ufile, strlen($extension)*-1);								// Get the last characters from the name
			if (strncmp ($fileext, $extension, strlen($extension)) == 0) {			// if file has correct extension
				addToVideoList($m3ufile, $videolist);
			}
		}
	}
}

function addToVideolist($file, &$videolist) {
	$data = file ($file);
		$fp = fopen ($file , "r") or die ("FOUT: Kan $file niet openen om te lezen, bekijk de permissies");
		foreach ($data as $line) {
			if (strncmp ($line, "#", 1) != 0 && strncmp ($line, "vlc:quit", 8) != 0) {	// Is this a line with a video?
				$videolist[]=$line;
			}
		}
	fclose ($fp);
}

// Try to find a string (needle) in an array of strings (haystack)
function inArrayStr($needle, $haystack) {
	$found = false;
	for ($i=0; $i<count($haystack)&&$found==false; $i++) {
		if (stristr($haystack[$i], $needle)) {
			$found = true;
		}
	}
	return $found;
}

function cronParse ($line, &$hour, &$minute, &$day, &$month, &$weekday, &$command, &$comment) {
	$valid = false;
	$delim = " \n\t";
	$hour = "";
	$minute = "";
	$day = "";
	$month = "";
	$weekday = "";
	$command = "";
	$comment = "";
	if (strncmp ($line, "@", 1) != 0 && strncmp ($line, "#", 1) != 0) {
		$tok = strtok($line, $delim);
		if ($tok !== false) {
			$minute = $tok;
			$tok = strtok($delim);
			if ($tok !== false) {
				$hour = $tok;
				$tok = strtok($delim);
				if ($tok !== false) {
					$day = $tok;
					$tok = strtok($delim);
					if ($tok !== false) {
						$month = $tok;
						$tok = strtok($delim);
						if ($tok !== false) {
							$weekday = $tok;
							$tok = strtok("#");
							if ($tok !== false) {
								$command = $tok;
								$valid = true;
								$tok = strtok("\n");
								if ($tok !== false) {
									$comment = $tok;
								}
							}
						}
					}
				}
			}
		}
	} else {
		$comment = $line;																	// Line is a comment or '@' line
	}
	return $valid;
}

function isVideo($filename) {
	$path_info = pathinfo($filename);
	return in_array(strtolower($path_info['extension']), array('vob', 'avi', 'mov', 'wmv', 'ogg', 'mpg', 'm2ts', 'mkv', 'mp4', 'dv', 'flv'));

}
?>
