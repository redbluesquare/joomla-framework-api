<?php
/**
 * Joomla! Framework Application
 *
 * @copyright  Copyright (C) 2014 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

// Application constants
define ( 'JPATH_ROOT', dirname ( __DIR__ ) );
define ( 'JPATH_CONFIGURATION', JPATH_ROOT.'/api/App/Config');
// Ensure we've initialized Composer
if (! file_exists ( JPATH_ROOT . '/api/vendor/autoload.php' )) {
	header ( 'HTTP/1.1 500 Internal Server Error', null, 500 );
	echo '<html><head><title>Server Error</title></head><body><h1>Composer Not Installed</h1><p>Composer is not set up properly, please run "composer install".</p><p>'.JPATH_ROOT.'</p></body></html>';
	
	exit ( 500 );
}

require JPATH_ROOT . '/api/vendor/autoload.php';

$container = new \Joomla\DI\Container ();
$container->registerServiceProvider(
		new App\Service\ConfigServiceProvider(JPATH_CONFIGURATION.'/config.json')
		)
		->registerServiceProvider(new App\Service\DatabaseServiceProvider());

$application = new App\App ( $container );
$application->execute(); //will execute App.php


