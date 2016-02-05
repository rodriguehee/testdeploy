<?php

// Add the core library to the autoloader
require_once 'Zend/Loader/Autoloader.php';
require_once 'Zend/Loader/Autoloader/Resource.php';
$autoloader = new Zend_Loader_Autoloader_Resource(array(
	'namespace' => 'Core',
	'basePath' => CHEMIN_CORE,
	'resourceTypes' => array(
		'library' => array(
			'namespace' => 'Library',
			'path'      => 'library',
		),
		'plugin' => array(
			'namespace' => 'Plugin',
			'path'      => 'plugins',
		),
	)
));
    
class Bootstrap extends Core_Library_Account_Bootstrap
{
}