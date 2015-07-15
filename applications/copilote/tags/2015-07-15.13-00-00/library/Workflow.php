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
		
		// validation demandeur
		if( $typeValidation == 486 && $statut == 476 ) {
			// validée par le demandeur
			$statut = 477 ;
			// si l'UB fait partie d'une GUB => à valider par le responsable
			$ub = $this->_demande->getUB() ;
			if ( $ub->getAttribute( "id_gub" ) > 0 ) {
				$statut = 478 ;
			}
		}
		// validation par le responsable
		if( $typeValidation == 487 && $statut == 478 ) {
			$statut = 479 ;
		}
		// visée budgétaire
		if( $typeValidation == 488 && ( $statut == 479 or $statut == 477 ) ) {
			$statut = 480 ;
		}
		$this->_demande->setAttribute( "etat", $statut ) ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Workflow
	 */
	public function setBudget()
	{
		$statut = 481 ;
		$this->_demande->setAttribute( "etat", $statut ) ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Workflow
	 */
	public function duplicate()
	{
		$schema = new Copilote_Library_Schema( Core_Library_Options::get( 'resources.db.params.dbname' ) ) ;
		$schema->setConstraints() ;
		$record = new Copilote_Library_Record( $schema, "cplt_dmnd_data", $this->_demande->getId() ) ;
		$idDuplicat = $record->duplicate() ;
		$demandeDupliquee = new Copilote_Library_Demande( $idDuplicat ) ;
		$demandeDupliquee->setAttribute( "verrou", 10 )->commit() ;
		return $this ;
	}
}