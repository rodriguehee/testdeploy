<?php

class Jqwidget_ExportController extends Core_Library_Controller_Action
{
	/**
	 * Code requis pour faire fonctionner le module 
	 *  d'export de JQWidgets
	 */
	public function savefileAction()
	{
		$this->_helper->viewRenderer->setNoRender( true ) ;
		
		$mimeTypes = array(
			'xls' => 'application/vnd.ms-excel',
			'xml' => 'application/xml',
			'html' => 'text/html',
			'csv' => 'text/plain',
			'tsv' => 'text/plain',
			'json' => 'text/plain',
			'array' => 'text/plain'
		) ;
		error_log( print_r( $_POST, true ) ) ;
		if ( isset( $_POST['format'] ) && 
			 isset( $_POST['filename'] ) && 
			 isset( $_POST['content'] ) ) {
			$filename = $_POST['filename'] ;
			$format = $_POST['format'] ;
			$content = stripslashes( $_POST['content'] ) ;
			$fullName = $filename . '.' . $format ;
			header( 'Pragma: public' ) ;
			header( 'Expires: 0' ) ;
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ) ;
			header( 'Cache-Control: private', false ) ;
			header( 'Content-Type: ' . $mimeTypes[$format] ) ;
			header( 'Content-Disposition: attachment; filename="' . $fullName . '"' ) ;
			echo $content ;
			exit() ;
		}
		
		throw new Core_Library_Exception( "400 Bad Request" ) ;
	}
}
