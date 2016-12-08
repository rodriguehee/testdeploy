<?php

putenv("VOO4_APPBOOTSTRAPPER=/home/rodrigue/workspace/libs/voozanoo4/2.20/src/tools/Voo4_AppBootstrapper.php");
putenv("PATH_VOO4_CORE=/home/rodrigue/workspace/libs/voozanoo4/2.20/src");
putenv("PATH_VOO4_EXTLIB=/home/rodrigue/workspace/libs/voozanoo4/2.20/libs");
putenv("APPLICATION_ENV=dev_rhe");
putenv("PATH_FARM=/home/rodrigue/workspace/workdata/glass");
putenv("PATH_ZF=/home/rodrigue/workspace/libs/ZendFramework/library");

defined('VOO4_APPBOOTSTRAPPER' ) || 
    define('VOO4_APPBOOTSTRAPPER', getenv('VOO4_APPBOOTSTRAPPER') ? getenv('VOO4_APPBOOTSTRAPPER') : null);
require_once(VOO4_APPBOOTSTRAPPER);
Voo4_AppBootstrapper::bootstrapApplication(__DIR__ . '/../..');
require_once CHEMIN_CORE . '/tools/CliApi.php';
require_once __DIR__ . "/CopiloteCliApi.php";

$codeRetour = 0;

try {
	$aArgs = $_SERVER['argv'];
	array_shift($aArgs);
	$oApi = new CopiloteCliApi($aArgs);
	$mReturn = $oApi->process();
	if (! is_null($mReturn) && 
		(is_string($mReturn) || is_numeric($mReturn))) {
		echo $mReturn . "\n";
	}
}
catch(Exception $ex) {
	echo $ex->getMessage() . "\n";
	$codeRetour = 1;
}
finally {
	exit($codeRetour);
}