<?php

// This work is licensed under the MIT License - http://www.opensource.org/licenses/mit-license.php


// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

	$yourEmail = "francisco.velez@bccrs.org"; // the email address you wish to receive these mails through
	$yourWebsite = "http://erinandfrank.com"; // the name of your website
	$thanksPage = 'thankyou.html'; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
	$maxPoints = 4; // max points a person can hit before it refuses to submit - recommend 4


// --- DO NOT EDIT BELOW HERE -----------------------

$error_msg = null;
$result = null;

function isBot() {
	$bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot");
	
	$isBot = false;
	foreach ($bots as $bot)
	if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
		$isBot = true;

	if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
		$isBot = true;
	
	exit("Bots not allowed.</p>");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	function clean($data) {
		$data = trim(stripslashes(strip_tags($data)));
		return $data;
	}
	
	$points = (int)0;
	
	$badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "porn", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur");
	$exploits = array("content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

	foreach ($badwords as $word)
		if (strpos($_POST['comments'], $word) !== false)
			$points += 2;
	
	foreach ($exploits as $exploit)
		if (strpos($_POST['comments'], $exploit) !== false)
			$points += 2;
	
	if (strpos($_POST['comments'], "http://") !== false || strpos($_POST['comments'], "www.") !== false)
		$points += 2;
	if (isset($_POST['nojs']))
		$points += 1;
	if (preg_match("/(<.*>)/i", $_POST['comments']))
		$points += 2;
	if (strlen($_POST['name']) < 3)
		$points += 1;
	if (strlen($_POST['comments']) < 15 || strlen($_POST['comments'] > 1500))
		$points += 2;

	foreach ($_POST as $key => $value)
		$_POST[$key] = trim($value);
	
	if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['comments'])) {
		$error_msg .= "Name, e-mail and comments are required fields. \n";
	} elseif (strlen($_POST['name']) > 15) {
		$error_msg .= "The name field is limited at 15 characters. Your first name or nickname will do! \n";
	} elseif (!preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['name']))) {
		$error_msg .= "The name field must not contain special characters. \n";
	} elseif (!preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email']))) {
		$error_msg .= "That is not a valid e-mail address. \n";
	} elseif (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['url']))
		$error_msg .= "Invalid website url.";
	
	if ($error_msg == NULL && $points <= $maxPoints) {
		$subject = "Automatic Form Email";

		$message = "You received this e-mail message through your website: \n\n";
		foreach ($_POST as $key => $val) {
			$message .= ucwords($key) . ": " . clean($val) . "\r\n";
		}
		$message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
		$message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
		$message .= 'Points: '.$points;

		if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
			$headers   = "From: $yourEmail \r\n";
			$headers  .= "Reply-To: {$_POST['email']}";
		} else {
			$headers   = "From: $yourWebsite <$yourEmail> \r\n";
			$headers  .= "Reply-To: {$_POST['email']}";
		}

		if (mail($yourEmail,$subject,$message,$headers)) {
			if (!empty($thanksPage)) {
				header("Location: $thanksPage");
				exit;
			} else {
				$result = 'Your mail was successfully sent.';
			}
		} else {
			$error_msg = 'Your mail could not be sent this time.';
		}
	} else {
		if (empty($error_msg))
			$error_msg = 'Your mail looks too much like spam, and could not be sent this time. ['.$points.']';
	}
}
function get_data($var) {
	if (isset($_POST[$var]))
		echo htmlspecialchars($_POST[$var]);
}
?>
