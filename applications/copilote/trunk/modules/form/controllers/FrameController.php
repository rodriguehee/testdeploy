<?php

class Form_FrameController extends Core_Library_Controller_Form_Frame
{
	/**
	 * @see Core_Library_Controller_Form_Frame::_get_get_afterExecute
	 * @param Core_Library_Event_Context $oContext
	 */
	protected function _get_get_afterExecute( Core_Library_Event_Context $oContext )
	{
		$aParams = $this->getRequest()->getParams() ;
		$aDatasets = $oContext->get( 'aDatasets' ) ;

		foreach ( $aDatasets as $oDataset ) {
			if ( $oDataset->Id() == 'validation' && isset( $aParams['type_validation'] ) ) {
				$oDataset->GetMetaData()->OverrideFieldDefaultValue( 'type_validation', $aParams['type_validation'] ) ;
				$oDataset->GetMetaData()->OverrideFieldDefaultValue( 'date_creation', date( 'Y-m-d H:i:s' ) ) ;
			}
		}

		$oContext->set( 'aDatasets',$aDatasets ) ;
		$this->_get_afterExecute( $oContext ) ;
	}
	
	/**
	 * Mise à jour 
	 * - du statut de la demande avec duplication de la demande si nécessaire
	 * - des montants de la demande
	 * 
	 * @param Core_Library_Event_Context $context
	 */
	protected function _save_save_afterCommit( Core_Library_Event_Context $context )
	{
		require $this->_getLibPath() . "/Record.php" ;
		require $this->_getLibPath() . "/Schema.php" ;
		require $this->_getLibPath() . "/Workflow.php" ;
		require $this->_getLibPath() . "/Demande.php" ;
		require $this->_getLibPath() . "/Depense.php" ;
		require $this->_getLibPath() . "/Ub.php" ;
		
		$wf = new Copilote_Library_Workflow() ;
		
		$data = $context->get( 'oDataJson' )->GetJSON() ;
		
		foreach( $data['data'] as $dataset ) {
			// validation demandeur && validation responsable && visée budgétaire
			if( $dataset['id'] == "validation" ) {
				foreach( $dataset['rowdata'] as $row ) {
					$wf->setDemande( new Copilote_Library_Demande( $row['id_demande'] ) ) ;
					$wf->setValidation( $row['type_validation'] )->getDemande()->commit() ;
					$wf->duplicate() ;
				}
			}
			// arbitrage
			if( $dataset['id'] == "bdgt" ) {
				foreach( $dataset['rowdata'] as $row ) {
					$wf->setDemande( new Copilote_Library_Demande( $row['id_demande'] ) ) ;
					$wf->setBudget()->getDemande()->commit() ;
					$wf->duplicate() ;
				}
			}
			
			// calcul des montants de dépense et demande
			if( in_array( $dataset['id'], array( "sta", "dt", "rh", "oc", "dmp", "pe" ) ) ) {
				foreach( $dataset['rowdata'] as $row ) {
					if( array_key_exists( "id_depense", $row ) ) {
						$depense = new Copilote_Library_Depense( $row['id_depense'] ) ;
						$depense->computeMontantConvention()->computeMontantSCSP()->computeMontant()->commit() ;
						$depense->getDemande()->compute( "montant_scsp" )->compute( "montant_convention" )->compute( "montant" )->commit() ;
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
		require $this->_getLibPath() . "/Record.php" ;
		require $this->_getLibPath() . "/Schema.php" ;
		
		$varset = $context->get( 'sVarsetId' ) ;
		$depenses = array() ;
		$schema = new Copilote_Library_Schema( Core_Library_Options::get( 'resources.db.params.dbname' ) ) ;
		
		if ( in_array( $varset, array( "oc", "rh", "dt", "pe", "dmp", "sta" ) ) ) {
		 	foreach( $context->get( 'aRecordsIds' )as $id ) {
		 		$tableName = sprintf( "cplt_%s_data", $varset ) ;
		 		$record = new Copilote_Library_Record( $schema, $tableName, $id ) ;
		 		$depenses[] = $record->getAttribute( "id_depense" ) ;
			}
		}
		elseif ( "ventilation" == $varset ) {
		 	foreach( $context->get( 'aRecordsIds' )as $id ) {
		 		$ventilation = new Copilote_Library_Record( $schema, "cplt_vntl_data", $id ) ;
		 		$tableName = "" ;
		 		if ( $ventilation->getAttribute( "id_dmp" ) > 0 ) {
		 			$tableName = "cplt_dmp_data" ;
		 		}
		 		elseif ( $ventilation->getAttribute( "id_pe" ) > 0 ) {
		 			$tableName = "cplt_pe_data" ;
		 		}
		 		elseif ( $ventilation->getAttribute( "id_oc" ) > 0 ) {
		 			$tableName = "cplt_pe_data" ;
		 		}
		 		if( ! empty( $tableName ) ) {
			 		$record = new Copilote_Library_Record( $schema, $tableName, $id ) ;
			 		$depenses[] = $record->getAttribute( "id_depense" ) ;
		 		}
		 	}
		}
		$context->add( "depenses", $depenses ) ;
	}
	
	/**
	 * @param Core_Library_Event_Context $context
	 */
	protected function _delete_delete_afterDelete( Core_Library_Event_Context $context )
	{
		require $this->_getLibPath() . "/Demande.php" ;
		require $this->_getLibPath() . "/Depense.php" ;
		
		foreach( $context->get( 'depenses' )as $idDepense ) {
 			$depense = new Copilote_Library_Depense( $idDepense ) ;
 			$depense->computeMontantConvention()->computeMontantSCSP()->computeMontant()->commit() ;
 			$depense->getDemande()->compute( "montant_scsp" )->compute( "montant_convention" )->compute( "montant" )->commit() ;
	 	}
	}	
	
	/**
	 * @return string
	 */
	protected function _getLibPath()
	{
		$libPath = Core_Library_Options::get( 'lib.path' ) ;
		if ( $libPath  === false ) {
			throw new Core_Library_Exception( 'Libraries path not found in ini file' );
		}
		return $libPath ;
	}
}