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
		$ub = $this->_demande->getUB() ;
		
		// validation demandeur avant arbitrage
		if( $statut == 476 ) {
		  error_log ('Validation demandeur 1 : '.$statut);
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
		elseif( $statut == 480 ) {
			$statut = 481 ;
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
		// revision v0
		elseif( $statut == 485 ) {
			$statut = 499 ;
		}
		// arbitrage pour v1
		elseif( $statut == 499 ) {
			$statut = 500 ;
		}
		// revision v1
		elseif( $statut == 500 ) {
			$statut = 502 ;
		}
		// arbitrage pour v2
		elseif( $statut == 502 ) {
			$statut = 503 ;
		}
		// revision v2
		elseif( $statut == 503 ) {
			$statut = 505 ;
		}
		// arbitrage pour v3
		elseif( $statut == 505 ) {
			$statut = 506 ;
		}
		
		error_log ('Validation demandeur 2 : '.$statut);
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