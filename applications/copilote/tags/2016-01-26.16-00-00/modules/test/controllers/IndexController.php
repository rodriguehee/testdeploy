<?php

class Test_IndexController extends Core_Library_Controller_Action
{
	/**
	 * test de la duplication d'une demande
	 *  accessible via l'URL "copilote/test/index/index/id/9999"
	 */
	public function indexAction()
	{
		$this->_helper->viewRenderer->setNoRender( true ) ;
		header( "Content-type: text/plain; charset=utf-8" ) ;
		
		try {
			$libPath = Core_Library_Options::get( 'lib.path' ) ;
			if ( $libPath  === false ) {
				throw new Core_Library_Exception( 'Libraries path not found in ini file' );
			}
			require $libPath . "/Record.php" ;
			
			$id = $this->getRequest()->getParam( "id", 0 ) ;
			$tableName = "cplt_dmnd_data" ;
			$record = new Copilote_Library_Record( $tableName, $id ) ;
			$idDuplicat = $record->duplicate() ;
			$duplicat = new Copilote_Library_Record( $tableName, $id ) ;
			$duplicat->setAttribute( "verrou", 10 )->commit() ;
			
			echo sprintf( "Duplication de la demande [%d] réalisée => [%d]", $id, $idDuplicat ) ;
		}
		catch( Exception $ex ) {
			echo $ex->getMessage() ;
		}
	}
}