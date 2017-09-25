<?php

class Document_IndexController extends Core_Library_Controller_Action
{
	/**
	 * Récuperation d'un document fourni au navigateur
	 */
	public function getAction()
	{
		$this->_helper->viewRenderer->setNoRender( true ) ;
		
		$libPath = Core_Library_Options::get( 'lib.path' ) ;
		if ( $libPath  === false ) {
			throw new Core_Library_Exception( 'Libraries path not found in ini file' );
		}
		require $libPath . "/Record.php" ;
		$record = new Copilote_Library_Record( "cplt_documents", $this->getRequest()->getParam( 'id', 0 ), "doc_id" ) ;
		
		$filename = $record->getAttribute( "doc_path" ) . $record->getAttribute( "doc_nom" ) ;
		
		if ( file_exists( $filename ) ) {
			header( "Content-Disposition: inline; filename=" . $record->getAttribute( "doc_nom" ) ) ;
			header( "Content-type: application/force-download" ) ;
			echo file_get_contents( $filename ) ;
		}
		else {
			header( "Content-type: text-plain; charset=utf-8" ) ;
			echo sprintf( "Le fichier '%s' est introuvable", $filename ) ;
		}
	}
}
