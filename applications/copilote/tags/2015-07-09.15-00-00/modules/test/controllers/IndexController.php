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
			require $libPath . "/Schema.php" ;
			
			$id = $this->getRequest()->getParam( "id", 0 ) ;
			
			$schema = new Copilote_Library_Schema( Core_Library_Options::get( 'resources.db.params.dbname' ) ) ;
			$schema->setConstraints() ;
			$record = new Copilote_Library_Record( $schema, "cplt_dmnd_data", $id ) ;
			$record->duplicate() ;
			$record->attachAttribute( "verrou", 10 ) ;
			$record->commit() ;
			
			echo sprintf( "Duplication de la demande [%d] réalisée", $id ) ;
		}
		catch( Exception $ex ) {
			echo $ex->getMessage() ;
		}
	}
}