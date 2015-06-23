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
					if( is_numeric( $idDemande ) ) {
						$montant = $this->computeMontantDemande( $idDemande ) ;
						$this->setMontantDemande( $idDemande, $montant ) ;
					}
				}
			}
		}
	}
	
	/**
	 * @param Core_Library_Event_Context $context
	 */
	protected function _delete_delete_beforeDelete( Core_Library_Event_Context $context )
	{
		$varset = $context->get( 'sVarsetId' ) ;
		
		if ( in_array( $varset, array( "oc", "rh", "dt", "pe", "dmp" ) ) ) {
			$demandes = array() ;
			foreach( $context->get( 'aRecordsIds' )as $id ) {
				$idDemande = $this->getIdDemande( $varset, $id ) ;
				if ( ! in_array( $idDemande, $demandes ) ) {
					$demandes[] = $idDemande ;
				}
			}
			$context->add( "demandes", $demandes ) ;
		}
		elseif ( "ventilation" == $varset ) {
			$demandes = array() ;
			foreach( $context->get( 'aRecordsIds' )as $id ) {
				$idDemande = $this->getIdDemandeFromVentilation( $id ) ;
				if ( ! in_array( $idDemande, $demandes ) ) {
					$demandes[] = $idDemande ;
				}
			}
			$context->add( "demandes", $demandes ) ;
		}
	}
	
	/**
	 * @param Core_Library_Event_Context $context
	 */
	protected function _delete_delete_afterDelete( Core_Library_Event_Context $context )
	{
		$varset = $context->get( 'sVarsetId' ) ;
	
		if ( in_array( $varset, array( "oc", "rh", "dt", "pe", "dmp", "ventilation" ) ) ) {
			foreach( $context->get( 'demandes' )as $idDemande ) {
				$montant = $this->computeMontantDemande( $idDemande ) ;
				$this->setMontantDemande( $idDemande, $montant ) ;
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
	 * @param integer $idDepense
	 */
	public function computeMontantDepense( $idDepense )
	{
		$total = 0 ;
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		
		$ventilations = array(
			"id_oc" => "oc",
			"id_pe" => "pe",
			"id_dmp" => "dmp",
			"id_rh" => "rh",
		) ;
		foreach( $ventilations as $foreignKey => $varsetPrefix ) {
			$query = sprintf( "
				SELECT SUM(v.cout)
				FROM cplt_%s_data d
				JOIN cplt_vntl_data v
				ON d.id_data = v.%s
				WHERE d.id_depense = ?
			", 	$varsetPrefix,
				$foreignKey
			) ;
			$result = $db->fetchOne( $query, $idDepense ) ;
			$total += (float) $result ;
		}
		
		$depenses = array(
			"dt_couttot" => "dt",
			"rh_couttot" => "rh",
		) ;
		foreach( $depenses as $fieldName => $varsetPrefix ) {
			$query = sprintf( '
				SELECT SUM(%s)
				FROM cplt_%s_data
				WHERE id_depense = ?
			', 	$fieldName,
				$varsetPrefix
			) ;
			$result = $db->fetchOne( $query, $idDepense ) ;
			$total += (float) $result ;
		}
		
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
	
	/**
	 * @return integer
	 * @param integer $id
	 */
	public function getIdDemandeFromVentilation( $id )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$sql = '
			SELECT id_oc as oc, id_pe as pe, id_dmp as dmp, id_rh as rh
			FROM cplt_vntl_data v
			WHERE v.id_data = ?
		' ;
		$result = $db->fetchAll( $sql, $id ) ;
		$row = $result[0] ;
		foreach( $row as $key => $value ) {
			if( ! empty( $value ) ) {
				return $this->getIdDemande( $key, $value ) ;
			}
		}
		throw new LogicException( sprintf( "Ventilation '%s' orpheline !!!", $id ) ) ;
	}
		
	/**
	 * @return integer
	 * @param string $varset
	 * @param integer $id
	 */
	public function getIdDemande( $varset, $id )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$sql = sprintf( '
			SELECT d.id_demande 
			FROM cplt_dpns_data d
			JOIN cplt_%s_data t
			  ON d.id_data = t.id_depense
			WHERE t.id_data = ?
		', 	$varset
		) ;
		$result = (int) $db->fetchOne( $sql, $id ) ;
		return $result ;
	}
}