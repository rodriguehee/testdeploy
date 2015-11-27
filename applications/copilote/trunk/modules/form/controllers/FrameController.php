<?php

class Form_FrameController extends Core_Library_Controller_Form_Frame
{

    protected function _get_get_render( Core_Library_Event_Context $oContext )
    {

        $bShowButtonEndDemand = false;
        $bShowButtonValidDemand = false;
        $bShowButtonValidEstimatedBudget = false;
        $bShowButtonVISA = false;
        $bShowButtonArbitrate = false;
        $bShowButtonEndEstimatedBudget = false;
        
        $oUser = Core_Library_Account::GetInstance()->GetCurrentUser() ;
        
        // Debug !!! PHP Strict Standards:  Non-static method Core_Library_User_Manager::GetUserRolesByGroup() should not be called statically !!!
        $manager = Core_Library_Account::GetInstance()->GetCurrentProject()->UserManager() ;
        $aRolesByGroup = $manager->GetUserRolesByGroup( $oUser->GetUserName() );

        $oJson = $oContext->get( 'oResourceJSON' );

        $aJson = $oJson->GetJSON();
        if ( $aJson[ 'iFormId' ] == "34" )
        {
            foreach( $aJson[ 'aDataSets' ] as $aDataSets )
            {
                // validation demandeur && validation responsable && visée budgétaire
                if( $aDataSets['id'] == "suivi" )
                {
                    $iIdGroupFiche = $aDataSets['rowdata'][0]['id_group'];
                }
            }

            //error_log('$iIdGroupFiche = ' . print_r($iIdGroupFiche, true));

            foreach ( $aRolesByGroup as $iIdGroupUser => $aGroup )
            {
                //error_log('$iIdGroupUser = ' . print_r($iIdGroupUser, true));
                if( $iIdGroupFiche == $iIdGroupUser )
                {
                    foreach ($aGroup as $aRole)
                    {
                        //error_log('$aRole["name"] = ' . print_r($aRole['name'], true));
                        if ( $aRole['name'] == 'demandeur' || $aRole['name'] == 'demandeur_simple' )
                        {
                            $bShowButtonEndDemand = true ;
                        }

                        if ( $aRole['name'] == 'valideur' )
                        {
                            $bShowButtonValidDemand = true;
                            $bShowButtonValidEstimatedBudget = true;
                        }

                        if ( $aRole['name'] == 'SB' )
                        {
                            $bShowButtonVISA = true;
                            $bShowButtonArbitrate = true;
                        }

                        if ( $aRole['name'] == 'demandeur' )
                        {
                            $bShowButtonEndEstimatedBudget = true;
                        }

                    }
                }
            }

//        error_log('$bShowButtonEndDemand = ' . print_r($bShowButtonEndDemand, true));
//        error_log('$bShowButtonValidDemand = ' . print_r($bShowButtonValidDemand, true));
//        error_log('$bShowButtonValidEstimatedBudget = ' . print_r($bShowButtonValidEstimatedBudget, true));
//        error_log('$bShowButtonVISA = ' . print_r($bShowButtonVISA, true));
//        error_log('$bShowButtonArbitrate = ' . print_r($bShowButtonArbitrate, true));
//        error_log('$bShowButtonEndEstimatedBudget = ' . print_r($bShowButtonEndEstimatedBudget, true));

            $i = 0;
            foreach ( $aJson[ 'oLayout' ][ 'content' ] as $aWidget ) {

                if ( isset( $aWidget[ 'id' ] ) ) {

//                    error_log('******************************************************************************************************');
//                    error_log('$aWidget_w = ' . print_r($aWidget['w'], true));
//                    error_log('$aWidget_pid = ' . print_r($aWidget['pid'], true));
//                    error_log('$aWidget_id = ' . print_r($aWidget['id'], true));

                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'end-demand' && !$bShowButtonEndDemand ) {
                        array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                        $i--;
                    }

                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'valid-demand' && !$bShowButtonValidDemand ) {
                        array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                        $i--;
                    }

                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'valid-estimated-budget' && !$bShowButtonValidEstimatedBudget ) {
                        array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                        $i--;
                    }

                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'visa' && !$bShowButtonVISA ) {
//                        error_log('on cache bouton visa');
//                        error_log('$i = ' . print_r($i, true));
//                        error_log('$aJson[ "oLayout" ][ "content" ][ $i ] = ' . print_r($aJson[ 'oLayout' ][ 'content' ][ $i ], true));
                        array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                        $i--;
                    }

                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'arbitrate' && !$bShowButtonArbitrate ) {
                        array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                        $i--;
                    }

                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'end-estimated-budget' && !$bShowButtonEndEstimatedBudget ) {
                        array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                        $i--;
                    }
                    
                    // saisie du budget après passage en V0, V1 et V2
                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'revision-v0' && !$bShowButtonEndDemand ) {
                    	array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                    	$i--;
                    }
                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'revision-v1' && !$bShowButtonEndDemand ) {
                    	array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                    	$i--;
                    }
                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'revision-v2' && !$bShowButtonEndDemand ) {
                    	array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                    	$i--;
                    }
                    
                    // arbitrage pour passage en V1, V2 et V3
                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'arbitrate-v1' && !$bShowButtonArbitrate ) {
                    	array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                    	$i--;
                    }
                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'arbitrate-v2' && !$bShowButtonArbitrate ) {
                    	array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                    	$i--;
                    }
                    if ( $aWidget[ 'w' ] == 'WidgetButton' && $aWidget[ 'id' ] == 'arbitrate-v3' && !$bShowButtonArbitrate ) {
                    	array_splice( $aJson[ 'oLayout' ][ 'content' ], $i, 1 );
                    	$i--;
                    }
                    
                }
                $i++;
            }

            //error_log('$aJson[ "oLayout" ][ "content" ] = ' . print_r($aJson[ 'oLayout' ][ 'content' ], true));


        }
        $oJson->SetJSON( $aJson );
        parent::_get_render( $oContext );
    }


	/**
	 * dans le cas où la ressource demandée est la demande
	 *  on vérifie que la demande a un statut qui permet la modification de la demande
	 *   sinon on redirige sur la demande en lecture seule
	 */
	protected function _getFormConfiguration()
	{
		require $this->_getLibPath() . "/Record.php" ;
		require $this->_getLibPath() . "/Demande.php" ;
		require $this->_getLibPath() . "/Depense.php" ;
		
		$user = Core_Library_Account::GetInstance()->GetCurrentUser() ;
		$formId = $this->getRequest()->getParam( 'id', 0 ) ;
		$id = $this->getRequest()->getParam( 'id_data', 0 ) ;
		
		if( 34 == $formId && $id > 0 ) {
			if( $user->HasRole( "demandeur_simple" ) ) {
				$demande = new Copilote_Library_Demande( "cplt_dmnd_data", $id ) ;
				$etat = $demande->getAttribute( "etat" ) ;
				$ok = array( 476, 481 ) ;
				if ( ! in_array( $etat, $ok ) ) {
					$this->getRequest()->setParam( 'id', 79 ) ; 
				}
			}
		}
		
		parent::_getFormConfiguration() ;
		
	}
	/**
	 * @see Core_Library_Controller_Form_Frame::_get_get_afterExecute
	 * @param Core_Library_Event_Context $oContext
	 */
	protected function _get_get_afterExecute( Core_Library_Event_Context $oContext )
	{
		//error_log( __METHOD__ ) ;
		$aParams = $this->getRequest()->getParams() ;
		$aDatasets = $oContext->get( 'aDatasets' ) ;
		//require $this->_getLibPath() . "/Record.php" ;
		//require $this->_getLibPath() . "/Depense.php" ;
        require $this->_getLibPath() . "/Arbitrage.php" ;

		foreach ( $aDatasets as $oDataset ) {
			if ( $oDataset->Id() == 'validation' && isset( $aParams['type_validation'] ) ) {
				$oDataset->GetMetaData()->OverrideFieldDefaultValue( 'type_validation', $aParams['type_validation'] ) ;
				$oDataset->GetMetaData()->OverrideFieldDefaultValue( 'date_creation', date( 'Y-m-d H:i:s' ) ) ;
			}

            // Initialisation arbitrage
            if ( $oDataset->Id() == 'bdgt' && isset( $aParams['id_demande'] ) ){
            	
            	$oDataset->GetMetaData()->OverrideFieldDefaultValue( 'type_validation', $aParams['type_validation'] ) ;

                $oArbitrage = new Copilote_Library_Arbitrage();

                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_c5', $oArbitrage->getMontantC5($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_d5', $oArbitrage->getMontantD5($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_e5', $oArbitrage->getMontantE5($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_f5', $oArbitrage->getMontantF5($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_g5', $oArbitrage->getMontantG5($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_h5',
                    $oArbitrage->getMontantC5($aParams['id_demande']) +
                    $oArbitrage->getMontantD5($aParams['id_demande']) +
                    $oArbitrage->getMontantF5($aParams['id_demande'])
                );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_i5',
                    $oArbitrage->getMontantC5($aParams['id_demande']) +
                    $oArbitrage->getMontantE5($aParams['id_demande']) +
                    $oArbitrage->getMontantG5($aParams['id_demande'])
                );

                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_c7', $oArbitrage->getMontantC7($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_d7', $oArbitrage->getMontantD7($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_e7', $oArbitrage->getMontantE7($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_f7', $oArbitrage->getMontantF7($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_g7', $oArbitrage->getMontantG7($aParams['id_demande'])  );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_h7',
                    $oArbitrage->getMontantC7($aParams['id_demande']) +
                    $oArbitrage->getMontantD7($aParams['id_demande']) +
                    $oArbitrage->getMontantF7($aParams['id_demande'])
                );
                $oDataset->GetMetaData()->OverrideFieldDefaultValue( 'montant_i7',
                    $oArbitrage->getMontantC7($aParams['id_demande']) +
                    $oArbitrage->getMontantE7($aParams['id_demande']) +
                    $oArbitrage->getMontantG7($aParams['id_demande'])
                );
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
		//error_log( __METHOD__ ) ;
		require $this->_getLibPath() . "/Record.php" ;
		require $this->_getLibPath() . "/Workflow.php" ;
		require $this->_getLibPath() . "/Demande.php" ;
		require $this->_getLibPath() . "/Depense.php" ;
		
		$wf = new Copilote_Library_Workflow() ;
		
		$data = $context->get( 'oDataJson' )->GetJSON() ;
		
		foreach( $data['data'] as $dataset ) {
			// validation demandeur && validation responsable && visée budgétaire
			if( $dataset['id'] == "validation" ) {
				foreach( $dataset['rowdata'] as $row ) {
					$wf->setDemande( new Copilote_Library_Demande( "cplt_dmnd_data", $row['id_demande'] ) ) ;
					$wf->setValidation()->getDemande()->commit() ;
					// on lance la duplication sur certains statuts
					if( in_array( $wf->getDemande()->getAttribute( "etat" ), array( 477, 478, 479, 480, 485 ) ) ) {
						$wf->duplicate() ;
					}
				}
			}
			// arbitrage
			if( $dataset['id'] == "bdgt" ) {
				foreach( $dataset['rowdata'] as $row ) {
					$wf->setDemande( new Copilote_Library_Demande( "cplt_dmnd_data", $row['id_demande'] ) ) ;
					$wf->setValidation()->getDemande()->commit() ;
					$wf->duplicate() ;
				}
			}
			
			// calcul des montants de dépense et demande
			if( in_array( $dataset['id'], array( "sta", "dt", "rh", "oc", "dmp", "pe" ) ) ) {
				foreach( $dataset['rowdata'] as $row ) {
					if( array_key_exists( "id_depense", $row ) ) {
						$depense = new Copilote_Library_Depense( "cplt_dpns_data", $row['id_depense'] ) ;
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
		//error_log( __METHOD__ ) ;
		require $this->_getLibPath() . "/Record.php" ;
		
		$project = Core_Library_Account::GetInstance()->GetCurrentProject() ;
		$notifier = new Core_Library_Project_Notifier() ;
		$user = Core_Library_Account::GetInstance()->GetCurrentUser() ;
		$varset = $project->GetVarSet( $context->get('sVarsetId') ) ;
		
		// si la suppression concerne une fiche référencée par une autre fiche on ne doit pas la supprimer
		//  on vide le tableau sur lequel va s'appliquer les "delete"
		$joins = $varset->GetJoinedData4Delete( $notifier, $user, $context->get( 'aRecordsIds' ) ) ;
		if( count( $joins ) > 0 ) {
			$context->set( "aRecordsIds", array() ) ;
			$context->set( "sWarning", "La fiche est référencée par d'autres fiches" ) ;
		}
		
		// il n'est pas possible de supprimer les dépenses sur marché
		if( "dmp" == $varset->GetVarsetName() ) {
			$context->set( "aRecordsIds", array() ) ;
			$context->set( "sWarning", "Il n'est pas possible de supprimer une dépense sur marché" ) ;
		}
		
		$depenses = array() ;
		if ( in_array( $varset->GetVarsetName(), array( "oc", "rh", "dt", "pe", "sta" ) ) ) {
		 	foreach( $context->get( 'aRecordsIds' )as $id ) {
		 		$record = new Copilote_Library_Record( $varset->DataTableName(), $id ) ;
		 		$depenses[] = $record->getAttribute( "id_depense" ) ;
			}
		}
		elseif ( "ventilation" == $varset->GetVarsetName() ) {
		 	foreach( $context->get( 'aRecordsIds' )as $id ) {
		 		$ventilation = new Copilote_Library_Record( $varset->DataTableName(), $id ) ;
		 		$tableName = "" ;
		 		if ( $ventilation->getAttribute( "id_dmp" ) > 0 ) {
		 			$foreignkey = $ventilation->getAttribute( "id_dmp" ) ;
		 			$tableName = "cplt_dmp_data" ;
		 		}
		 		elseif ( $ventilation->getAttribute( "id_pe" ) > 0 ) {
		 			$foreignkey = $ventilation->getAttribute( "id_pe" ) ;
		 			$tableName = "cplt_pe_data" ;
		 		}
		 		elseif ( $ventilation->getAttribute( "id_oc" ) > 0 ) {
		 			$foreignkey = $ventilation->getAttribute( "id_oc" ) ;
		 			$tableName = "cplt_oc_data" ;
		 		}
		 		
		 		if( ! empty( $tableName ) && ! empty( $foreignkey ) ) {
			 		$record = new Copilote_Library_Record( $tableName, $foreignkey ) ;
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
		//error_log( __METHOD__ ) ;
		$notifier = new Core_Library_Project_Notifier() ;
		$ids = $context->get( "aRecordsIds" ) ;
		if( empty( $ids ) ) {
			$notifier->AddWarning( $context->get( "sWarning" ) ) ;
			$context->set( "oNotifier", $notifier ) ;
		}
		
		require $this->_getLibPath() . "/Demande.php" ;
		require $this->_getLibPath() . "/Depense.php" ;
		
		foreach( $context->get( 'depenses' ) as $idDepense ) {
 			$depense = new Copilote_Library_Depense( "cplt_dpns_data", $idDepense ) ;
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