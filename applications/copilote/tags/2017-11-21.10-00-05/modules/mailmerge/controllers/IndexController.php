<?php

class Mailmerge_IndexController extends Core_Library_Controller_Mailmerge_Index
{
	public function execAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$aParams =  $this->getRequest()->getParams();
		if( FALSE == array_key_exists( 'id_mm', $aParams )){
			throw new Exception( "Erreur : paramètre id_mm manquant" );
		}
		elseif( FALSE == array_key_exists( 'class', $aParams )){
			throw new Exception( "Erreur : paramètre class manquant" );
		}

		$sClassName = sprintf('Mailmerge_%s', $aParams['class'] );
		if( FALSE == class_exists( $sClassName )){
			throw new Exception( "Erreur : classe manquante $sClassName" );
		}
		$iModele =  $aParams['id_mm'];

		$oMailing = new $sClassName( $iModele );
		$sPdf = $oMailing->get( $aParams );
		header('Content-Type: application/pdf');
		echo $sPdf;
		die();
	}
}