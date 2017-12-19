#!/usr/bin/php
<?
//error_reporting(0);

$skipJSsettings = 1;
include_once '/opt/fpp/www/config.php';
include_once '/opt/fpp/www/common.php';

include_once "functions.inc.php";


define('LOCK_DIR', '/tmp/');
define('LOCK_SUFFIX', '.lock');
$pluginName = "Thingspeak";


$myPid = getmypid();

//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/".$pluginName.".log";

//re-read the settings so they are read in
$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
if (file_exists($pluginConfigFile)) {
	$pluginSettings = parse_ini_file($pluginConfigFile);
	
	logEntry("Reading in settings from file: ".$pluginConfigFile." for: ".$pluginName);
	
}


$API_TOKEN = urldecode($pluginSettings["API_TOKEN"]);
$DEBUG = urldecode($pluginSettings['DEBUG']);

$THINGSPEAK_URL = "https://api.thingspeak.com";

$ENABLED = urldecode($pluginSettings['ENABLED']);


if($DEBUG) {
	logEntry("DEBUG IS ENABLED IN CONFIG FILE");
	
	logEntry("ARGV0: ".$argv[0]);
}



if($ENABLED !== "ON") {
	logEntry("Plugin Status: DISABLED Please enable in Plugin Setup to use & Restart FPPD Daemon");

	exit(0);
}


//none at this time
$callbackRegisters = "media";
//$callbackRegisters = "playlist,media";
//var_dump($argv);

$FPPD_COMMAND = $argv[1];

//echo "FPPD Command: ".$FPPD_COMMAND."<br/> \n";

if($FPPD_COMMAND == "--list") {

	echo $callbackRegisters;
	logEntry("FPPD List Registration request: responded:". $callbackRegisters);
	exit(0);
}

if($FPPD_COMMAND == "--type") {
	logEntry("type callback requested");
	//we got a register request message from the daemon
	processCallback($argv);
	exit(0);
}

logEntry($argv[0]." called with no parameteres");
exit(0);

?>
