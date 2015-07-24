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
	 */
	public function setValidation()
	{
		$statut = $this->_demande->getAttribute( "etat" ) ;
		$budget = $this->_demande->getAttribute( "budget" ) ;
		$ub = $this->_demande->getUB() ;
		
		// validation demandeur avant arbitrage
		if( $statut == 476 ) {
			// validée par le demandeur
			$statut = 477 ;
			// si l'UB fait partie d'une GUB => à valider par le responsable
			if ( $ub->getAttribute( "id_gub" ) > 0 ) {
				$statut = 478 ;
			}
		}
		// validation par le responsable avant arbitrage
		elseif( $statut == 478 ) {
			$statut = 479 ;
		}
		// visée budgétaire avant arbitrage
		elseif( $statut == 479 || $statut == 477 ) {
			$statut = 480 ;
		}
		// arbitrage initial
		elseif( $statut == 480 && $budget == 489 ) {
			$statut = 481 ;
		}
		// arbitrage après constitution du budget V0
		elseif( $statut == 480 && $budget >= 490 ) {
			$statut = 476 ;
			$budget++ ;
		}
		// validation demandeur apres arbitrage
		elseif( $statut == 481 ) {
			// validée par le demandeur
			$statut = 485 ;
			// si l'UB fait partie d'une GUB => à valider par le responsable
			if ( $ub->getAttribute( "id_gub" ) > 0 ) {
				$statut = 483 ;
			}
		}
		// validation par le responsable apres arbitrage
		elseif( $statut == 483 ) {
			$statut = 485 ;
		}
		
		$this->_demande->setAttribute( "budget", $budget ) ;
		$this->_demande->setAttribute( "etat", $statut ) ;
		return $this ;
	}
	
	/**
	 * On ne génère qu'un duplicat par "etat" dans le cas où l'on cliquerait plusieurs fois sur "enregistrer"
	 * @return Copilote_Library_Workflow
	 */
	public function duplicate()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$tableName = "cplt_dmnd_data" ;
		$record = new Copilote_Library_Record( $tableName, $this->_demande->getId() ) ;
		$sql = sprintf( 'SELECT id_data FROM %s WHERE id_suivi = ? AND etat = ? AND verrou = 10 ', $tableName ) ;
		$stmt = $db->query( $sql, array( $record->getAttribute( "id_suivi" ), $record->getAttribute( "etat" ) ) ) ;
		if( ! ( $stmt->fetchColumn( 0 ) ) ) {
			$idDuplicat = $record->duplicate() ;
			$demandeDupliquee = new Copilote_Library_Record( $tableName, $idDuplicat ) ;
			$demandeDupliquee->setAttribute( "verrou", 10 )->commit() ;
		}
		return $this ;
	}
}