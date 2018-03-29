<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * This is VOOZANOO, a program. Contact voozanoo@epiconcept.fr, or   *
 * see http://www.voozanoo.net/ for more information.                *
 *                                                                   *
 * Copyright 2001-2013 Epiconcept                                    *
 *                                                                   *
 * This program is free software; you can redistribute it and/or     *
 * modify it under the terms of the GNU General Public License as    *
 * published by the Free Software Foundation - version 2             *
 *                                                                   *
 * This program is distributed in the hope that it will be useful,   *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of    *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     *
 * GNU General Public License in file COPYING for more details.      *
 *                                                                   *
 * You should have received a copy of the GNU General Public         *
 * License along with this program; if not, write to the Free        *
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor,       *
 * Boston, MA 02111, USA.                                            *
 *                                                                   *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
 * Ce script ne peut être exécuté dans le noyau, il doit être copié dans le
 * répertoire de l'applicatin voozanoo4.
 */
$sVersionVoo4 = '2.26';
switch ( php_uname( 'n' ) ) {
	case 'profnt1':
	case 'profnt2':
		putenv( "VOO4_APPBOOTSTRAPPER=/space/www/libs/VOOZANOO4/" . $sVersionVoo4 . "/src/tools/Voo4_AppBootstrapper.php" );
		putenv( "PATH_VOO4_CORE=/space/www/libs/VOOZANOO4/%s/src" );
		putenv( "PATH_VOO4_EXTLIB=/space/www/libs/VOOZANOO4/%s/libs" );
		putenv( "PATH_FARM=/space/www/configuration/voozanoo4" );
		putenv( "PATH_ZF=/space/www/libs/ZendFramework" );
		putenv( "APPLICATION_ENV=production" );
		break;
	case 'prefnt1':
	case 'prefnt2':
		putenv( "VOO4_APPBOOTSTRAPPER=/space/www/libs/VOOZANOO4/" . $sVersionVoo4 . "/src/tools/Voo4_AppBootstrapper.php" );
		putenv( "PATH_VOO4_CORE=/space/www/libs/VOOZANOO4/%s/src" );
		putenv( "PATH_VOO4_EXTLIB=/space/www/libs/VOOZANOO4/%s/libs" );
		putenv( "PATH_FARM=/space/www/configuration/voozanoo4" );
		putenv( "PATH_ZF=/space/www/libs/ZendFramework" );
		putenv( "APPLICATION_ENV=staging" );
		break;
	case 'PC-KGR':
		putenv( "VOO4_APPBOOTSTRAPPER=C:\\wamp\\www\\libs\\VOOZANOO4\\" . $sVersionVoo4 . "\\src\\tools\\Voo4_AppBootstrapper.php" );
		putenv( "PATH_VOO4_CORE=C:\\wamp\\www\\libs\\VOOZANOO4\\%s\\src" );
		putenv( "PATH_VOO4_EXTLIB=C:\\wamp\\www\\libs\\VOOZANOO4\\%s\\libs" );
		putenv( "PATH_FARM=/var/www/workdata" );
		putenv( "PATH_ZF=C:\\wamp\\www\\libs\\ZendFramework" );
		putenv( "APPLICATION_ENV=dev_kgr" );
		break;
	case 'PC-FIX-KGR':
		putenv( "VOO4_APPBOOTSTRAPPER=C:\\wamp\\www\\libs\\voozanoo4\\" . $sVersionVoo4 . "\\src\\tools\\Voo4_AppBootstrapper.php" );
		putenv( "PATH_VOO4_CORE=C:\\wamp\\www\\libs\\voozanoo4\\%s\\src" );
		putenv( "PATH_VOO4_EXTLIB=C:\\wamp\\www\\libs\\voozanoo4\\%s\\libs" );
		putenv( "PATH_FARM=/var/www/workdata" );
		putenv( "PATH_ZF=C:\\wamp\\www\\libs\\ZendFramework" );
		putenv( "APPLICATION_ENV=dev_kgr_home" );
		break;
	case 'DESKTOP-A0IOV6M':
		putenv( "VOO4_APPBOOTSTRAPPER=C:\\wamp\\www\\libs\\VOOZANOO4\\" . $sVersionVoo4 . "\\src\\tools\\Voo4_AppBootstrapper.php" );
		putenv( "PATH_VOO4_CORE=C:\\wamp\\www\\libs\\VOOZANOO4\\%s\\src" );
		putenv( "PATH_VOO4_EXTLIB=C:\\wamp\\www\\libs\\VOOZANOO4\\%s\\libs" );
		putenv( "PATH_FARM=/var/www/workdata" );
		putenv( "PATH_ZF=C:\\wamp\\www\\libs\\ZendFramework" );
		putenv( "APPLICATION_ENV=dev_att" );
		break;
	case 'GLV' :
		putenv("VOO4_APPBOOTSTRAPPER=/var/www/libs/voozanoo4/" . $sVersionVoo4 . "/src/tools/Voo4_AppBootstrapper.php");
		putenv("PATH_VOO4_CORE=/var/www/libs/voozanoo4/%s/src");
		putenv("PATH_VOO4_EXTLIB=/var/www/libs/voozanoo4/%s/libs");
		putenv("APPLICATION_ENV=dev_glv");
		putenv("PATH_FARM=/var/www/workdata");
		putenv("PATH_ZF=/var/www/libs/ZendFramework/1.12.18/Zend");
		break;
	case 'rodrigue-HP-ZBook-15-G2':
		putenv("VOO4_APPBOOTSTRAPPER=/home/rodrigue/workspace/libs/voozanoo4/" . $sVersionVoo4 . "/src/tools/Voo4_AppBootstrapper.php");
		putenv("PATH_VOO4_CORE=/home/rodrigue/workspace/libs/voozanoo4/%s/src");
		putenv("PATH_VOO4_EXTLIB=/home/rodrigue/workspace/libs/voozanoo4/%s/libs");
		putenv("PATH_FARM=/home/rodrigue/workspace/workdata/osea");
		putenv("PATH_ZF=/home/rodrigue/workspace/libs/ZendFramework/library");
		putenv("APPLICATION_ENV=dev_rhe");
		break;
	case 'epi-ksn':
		putenv( "VOO4_APPBOOTSTRAPPER=/var/www/libs/voozanoo4/" . $sVersionVoo4 . "/src/tools/Voo4_AppBootstrapper.php" );
		putenv( "PATH_VOO4_CORE=/var/www/libs/voozanoo4/%s/src" );
		putenv( "PATH_VOO4_EXTLIB=/var/www/libs/voozanoo4/%s/libs" );
		putenv( "PATH_FARM=/var/www/configuration/voozanoo4" );
		putenv( "PATH_ZF=/var/www/libs/ZendFramework" );
		putenv( "APPLICATION_ENV=dev_ksn_epi" );
		break;
}
defined( 'VOO4_APPBOOTSTRAPPER' ) || define( 'VOO4_APPBOOTSTRAPPER', getenv( 'VOO4_APPBOOTSTRAPPER' ) ? getenv( 'VOO4_APPBOOTSTRAPPER' ) : null );
require_once( VOO4_APPBOOTSTRAPPER );
Voo4_AppBootstrapper::bootstrapApplication( __DIR__ . '/../..' );
require_once( CHEMIN_CORE . '/tools/Voo4CliApi.php' );
require_once( dirname(__FILE__) . '/Voo4CliAppApi.php' );
try {
	//Duplicate the arguments
	$aArgs = $_SERVER[ 'argv' ];
	//Remove first argument : name of the Script
	array_shift( $aArgs );
	$oApi = new Voo4CliAppApi( $aArgs );
	$mReturn = $oApi->process();
	//If a string or a numeric were return print it, to dialog with shell/batch script
	if ( ! is_null( $mReturn ) && ( is_string( $mReturn ) || is_numeric( $mReturn ) ) ) {
		echo $mReturn . "\n";
	}
} catch ( Exception $oException ) {
	error_log( "Exception : " . $oException->getMessage() );
	error_log( $oException->getTraceAsString() );
	exit( 1 );
}
exit( 0 );
