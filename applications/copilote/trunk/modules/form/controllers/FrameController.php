<?php

class Form_FrameController extends Core_Library_Controller_Form_Frame
{
	/**
	 * @param Core_Library_Event_Context $context
	 */
	protected function _save_save_afterCommit( Core_Library_Event_Context $context )
	{
		$data = $context->get( 'oDataJson' )->GetJSON() ;
		
		foreach( $data['data'] as $dataset ) {
			if( $dataset['id'] == "dmnd" ) {
				foreach( $dataset['rowdata'] as $row ) {
					$idDemande = $row['id_data'] ;
					$montant = $this->computeMontantDemande( $idDemande ) ;
					$this->setMontantDemande( $idDemande, $montant ) ;
				}
			}
		}
	}

	/**
	 * @param integer $idDemande
	 * @param float $montant
	 */
	public function setMontantDemande( $idDemande, $montant )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$db->update( "cplt_dmnd_data", array( "montant" => (float) $montant ), "id_data = " . $idDemande ) ;
	}
	
	/**
	 * @return float
	 * @param integer $idDemande
	 */
	public function computeMontantDemande( $idDemande )
	{
		$total = 0 ;
		foreach( $this->getIdDepenses( $idDemande ) as $idDepense ) {
			$montant = $this->computeMontantDepense( $idDepense ) ;
			$this->setMontantDepense( $idDepense, $montant ) ;
			$total += $montant ;
		}
		return $total ;
	}
	
	/**
	 * @param integer $idDemande
	 * @param float $montant
	 */
	public function setMontantDepense( $idDepense, $montant )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$db->update( "cplt_dpns_data", array( "montant" => (float) $montant ), "id_data = " . $idDepense ) ;
	}
	
	/**
	 * @return float
	 * @param integer $idDemande
	 */
	public function computeMontantDepense( $idDepense )
	{
		$total = 0 ;
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		
		// organisation de colloques
		$result = $db->fetchOne( '
			SELECT SUM(voc.cout)
			FROM cplt_oc_data oc 
			JOIN cplt_vntl_data voc
			  ON oc.id_data = voc.id_oc
			WHERE id_depense = ?
		',	$idDepense 
		) ;
		$total += (float) $result ;
		
		// deplacements temporaires
		$result = $db->fetchOne( '
			SELECT SUM(dt.dt_couttot)
			FROM cplt_dt_data dt
			WHERE id_depense = ?
		',	$idDepense
		) ;
		$total += (float) $result ;
		
		return $total ;
	}
	
	/**
	 * @return array
     * @param integer $idDemande
	 */
	public function getIdDepenses( $idDemande )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$db->setFetchMode( Zend_Db::FETCH_OBJ ) ;
		$sql = 'SELECT id_data FROM cplt_dpns_data WHERE id_demande = ?' ;
		$result = $db->fetchCol( $sql, $idDemande ) ;
		return $result ;
	}
}