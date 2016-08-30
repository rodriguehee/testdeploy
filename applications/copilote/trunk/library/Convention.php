<?php

class Copilote_Library_Convention extends Copilote_Library_Record
{
	/**
	 * @var array
	 */
	protected $_programmations = array() ;
	
	/**
	 * @param string $tableName
	 * @param integer $idData
	 */
	public function __construct( $tableName, $idData )
	{
		parent::__construct( $tableName, $idData ) ;
		$this->_setProgrammations() ;
		
		/* generation des programmations */
		if( $this->getAttribute( "status_prog" ) == 529 ) {
			$debut = 0 ;
			$fin = 0 ;
			
			$dateDebut = $this->getAttribute( "date_deb" ) ;
			if( ! empty( $dateDebut ) ) {
				$o = new DateTime( $dateDebut ) ;
				$debut = $o->format( "Y" ) ;
				
				$reference = Core_Library_Options::get( 'conv.pluri.ref' ) ;
				if ( $reference  === false ) {
					throw new Core_Library_Exception( 'Reference for programmation not found in ini file' );
				}
				$debut = max( $debut, $reference ) ;
			}
			
			$dateFin = $this->getAttribute( "date_fin" ) ;
			if( ! empty( $dateFin ) ) {
				$o = new DateTime( $dateFin ) ;
				$fin = $o->format( "Y" ) ;
			}
			if( $debut > 0 && $fin > 0 ) {
				while( $debut <= $fin ) {
					if( ! array_key_exists( $debut, $this->_programmations ) ) {
						$this->addProgrammation( $debut ) ;
					}
					$debut++ ;
				}
			}
		}
	}
	
	/**
	 * @return array
	 */
	public function getProgrammations()
	{
		return $this->_programmations ;
	}
	
	/**
	 * @return Copilote_Library_Convention
	 * @param Copilote_Library_Programmation $programmation
	 */
	public function attachProgrammation( Copilote_Library_Programmation $programmation )
	{
		$key = $programmation->getAttribute( "annee_conv" ) ;
		$this->_programmations[$key] = $programmation ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Convention
	 */
	protected function _setProgrammations()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$sql = 'SELECT id_data FROM cplt_programmation_data WHERE id_conv = ?' ;
		$stmt = $db->query( $sql, $this->_id ) ;
		while( $idProg = $stmt->fetchColumn( 0 ) ) {
			$programmation = new Copilote_Library_Programmation( "cplt_programmation_data", $idProg ) ;
			$annee = $programmation->getAttribute( "annee_conv" ) ;
			$this->_programmations[$annee] = $programmation ; 
		}
		return $this ;
	}	
	
	/**
	 * @return Copilote_Library_Convention
	 * @param integer $annee
	 */
	public function addProgrammation( $annee ) 
	{
		$project = Core_Library_Account::GetInstance()->GetCurrentProject() ;
		$varset = $project->GetVarSet( "programmation" ) ;
		$data = array( "annee_conv" => $annee, "id_conv" => $this->_id ) ;
		$result = $varset->SimpleInsertData( $data ) ;
		$programmation = new Copilote_Library_Programmation( "cplt_programmation_data", $result['insert_id'] ) ;
		$this->attachProgrammation( $programmation ) ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Demande | null
	 */
	public function GetDemande( $annee )
	{
		$query = sprintf( "
			SELECT dmnd.id_data
			FROM cplt_sv_data sv
			JOIN cplt_dmnd_data dmnd
			  ON sv.id_data = dmnd.id_suivi
			WHERE dmnd.verrou = 11
			  AND sv.annee = %d
			  AND sv.id_ub = %d
		", 	$annee,
			$this->getAttribute( "id_ub" )
		 ) ;
		$project = Core_Library_Account::GetInstance()->GetCurrentProject() ;
		$row = $project->Db()->fetchRow( $query ) ;
		
		if ( empty( $row ) ) {
			return null ;
		}
		return new Copilote_Library_Demande( "cplt_dmnd_data", $row['id_data'] ) ;
	}
}