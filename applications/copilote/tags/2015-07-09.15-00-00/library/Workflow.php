<?php

class Copilote_Library_Workflow 
{
	/**
	 * @var Copilote_Library_Demande
	 */
	protected $_demande ;
	
	/**
	 * @return Copilote_Library_Workflow
	 * @param Copilote_Library_Demande $demande
	 */
	public function setDemande( Copilote_Library_Demande $demande )
	{
		$this->_demande = $demande ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Demande
	 */
	public function getDemande()
	{
		return $this->_demande ;
	}
	
	/**
	 * @return Copilote_Library_Workflow
	 * @param integer $typeValidation
	 */
	public function setValidation( $typeValidation )
	{
		$statut = $this->_demande->getAttribute( "etat" ) ;
		
		if( $typeValidation == 486 && $statut < 478 ) {
			$statut = 478 ;
		}
		if( $typeValidation == 487 && $statut == 478 ) {
			$statut = 479 ;
		}
		if( $typeValidation == 488 && $statut == 479 ) {
			$statut = 480 ;
		}
		$this->_demande->setAttribute( "etat", $statut ) ;
		return $this ;
	}
}