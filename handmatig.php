<?php
	require_once ('tvfunctions.php');	// Functions for TV scripts
	authorize(0);

	require_once ('tvconfig.php');		// Settings for TV scripts
	require_once ('message.php');		// Translation

	testConfiguration();			// Test if configuration of directorys is OK

	if (isset($_POST['action'])) {
	if ($_POST['action']==KKA_BROADCAST) {
		exec('/home/tv/scripts/broadcastlink.sh');
	}
	elseif ($_POST['action']==KKA_DELETE_CACHE) {
		exec('/home/tv/scripts/deletecache.sh');
	}
	elseif ($_POST['action']==KKA_DELETE_BROADCAST_CACHE) {
		exec('/home/tv/scripts/deletebroadcastcache.sh');
	}

	elseif ($_POST['action']==KKA_MISSING_MEDIA) {

	}
	elseif ($_POST['action']==KKA_BACKUP) {
		exec('/usr/bin/sudo /home/tv/scripts/backup/backup.sh');
	}
	elseif ($_POST['action']==KKA_AUDIO_LINEIN1) {
		exec('/usr/bin/sudo /home/tv/scripts/audio-wissel.sh line1');
	}
	elseif ($_POST['action']==KKA_AUDIO_LINEIN2) {
		exec('/usr/bin/sudo /home/tv/scripts/audio-wissel.sh line2');
	}
	elseif ($_POST['action']==KKA_AUDIO_OFF) {
		exec('/usr/bin/sudo /home/tv/scripts/audio-uit.sh');
	}
	
	elseif ($_POST['action']==TVA_PLAY_FILE) {
		exec('/usr/bin/sudo -u tv /home/tv/scripts/speelvideo-headless.sh "'.$_POST['filename'].'"');
	}
	elseif ($_POST['action']==TVA_STOP_PLAYER) {
		exec('/usr/bin/sudo -u root /home/tv/scripts/killvideo.sh');
	}
	elseif ($_POST['action']==TVA_COMPOSITE_LINEIN1) {
		exec('/usr/bin/sudo -u tv /home/tv/scripts/composite-headless.sh line1');
	}
	elseif ($_POST['action']==TVA_COMPOSITE_LINEIN2) {
		exec('/usr/bin/sudo -u tv /home/tv/scripts/composite-headless.sh line2');
	}
	elseif ($_POST['action']==TVA_FIREWIRE) {
		exec('/usr/bin/sudo -u tv /home/tv/scripts/firewire-headless.sh');
	}
	elseif ($_POST['action']==TVA_FIREWIRE_HYBRID) {
		exec('/usr/bin/sudo -u tv /home/tv/scripts/firewire-hybrid-headless.sh');
	}

	elseif ($_POST['action']==TVA_X_576_1080) {
		exec('/usr/bin/sudo /home/tv/scripts/resolutie/naar1080.sh');
	}
	elseif ($_POST['action']==TVA_X_1080_576) {
		exec('/usr/bin/sudo /home/tv/scripts/resolutie/naar576.sh');
	}
	elseif ($_POST['action']==TVA_X_RESTART) {
		exec('/usr/bin/sudo /home/tv/scripts/restartX.sh');
	}

	elseif ($_POST['action']==TVA_REBOOT) {
		exec('/usr/bin/sudo reboot');
	}
	elseif ($_POST['action']==TVA_POWEROFF) {
		exec('/usr/bin/sudo poweroff');
	}
	elseif ($_POST['action']==TVA_MOUNT_USB) {
		exec('/bin/mount /mnt/usb');
	}
	elseif ($_POST['action']==TVA_UMOUNT_USB) {
		exec('/bin/umount /mnt/usb');
	}
	elseif ($_POST['action']==TVA_MOUNT_DVD) {
		exec('/bin/mount /mnt/dvd');
	}
	elseif ($_POST['action']==TVA_UMOUNT_DVD) {
		exec('/bin/umount /mnt/dvd');
	}

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
			<b>Handmatige bediening, BLIJF HIER VANAF ALS JE NIET WEET WAT JE DOET!</b><br /><br />
			<fieldset class="navigation">
			<?php echo 'Gebruiker: ', $_SERVER['PHP_AUTH_USER']; ?>
			</fieldset>
			<fieldset class="main">
				<legend>Kabelkrantadmin</legend>
				<fieldset class="buttons">
					<legend>Acties</legend>
					<input type="submit" name="action" value="<?php echo KKA_DELETE_CACHE; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo KKA_DELETE_BROADCAST_CACHE; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo KKA_BROADCAST; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo KKA_MISSING_MEDIA; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo KKA_BACKUP; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo KKA_AUDIO_LINEIN1; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo KKA_AUDIO_LINEIN2; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo KKA_AUDIO_OFF; ?>" class="button" />
				</fieldset>
			</fieldset>

			<fieldset class="main">
				<legend>Televisie</legend>
				<fieldset class="buttons">
					<legend>Acties</legend>
					<input type="submit" name="action" value="<?php echo TVA_STOP_PLAYER; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_COMPOSITE_LINEIN1; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_COMPOSITE_LINEIN2; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_FIREWIRE; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_FIREWIRE_HYBRID; ?>" class="button" />


                    <?php echo @multidirtoselectExts('filename', array(M3UDIR, '/mnt/usb', '/mnt/dvd', '/mnt/dvd/VIDEO_TS'), '', false, 0, array('m3u', 'mkv', 'avi', 'mp4', 'vob', 'mov', 'flv')); ?>
					<input type="submit" name="action" value="<?php echo TVA_PLAY_FILE; ?>" class="button" />
				</fieldset>
			</fieldset>

			<fieldset class="main">
				<legend>Beheer van de PC</legend>
				<fieldset class="buttons">
					<input type="submit" name="action" value="<?php echo TVA_MOUNT_USB; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_UMOUNT_USB; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_MOUNT_DVD; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_UMOUNT_DVD; ?>" class="button" />

					<input type="submit" name="action" value="<?php echo TVA_X_576_1080; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_X_1080_576; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_X_RESTART; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_REBOOT; ?>" class="button" />
					<input type="submit" name="action" value="<?php echo TVA_POWEROFF; ?>" class="button" />
				</fieldset>
			</fieldset>
		</form>
	</body>
</html>
