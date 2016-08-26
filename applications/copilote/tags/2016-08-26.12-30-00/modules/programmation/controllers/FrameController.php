<?php

class Programmation_FrameController extends Core_Library_Controller_Form_Frame
{
	/**
	 * @var integer
	 */
	protected $_iConventionId = 0 ;
	
	/**
	 * Affichage du formulaire
	 */
	public function getAction()
	{
		$this->_retrieveConventionId() ;
		parent::_getFormConfiguration() ;
	}
	
	/**
	 * Refresh de dataset : autocompletion
	 */
	public function getdatasetAction()
	{
		$this->_retrieveConventionId() ;
		parent::_getDataset() ;
	}
	
	/**
	 * @param Core_Library_Event_Context $oCtx
	 */
	protected function _retrieveForm( Core_Library_Event_Context $oCtx )
	{
		
		$oForm = new Core_Library_Resource_XML_Frame_Form() ;
		$oForm->LoadFromFile( sprintf( '%s/modules/programmation/resources/form.programmation.xml', APPLICATION_PATH ) ) ;
		$oForm->SetProject( Core_Library_Account::GetInstance()->GetCurrentProject() ) ;
		$this->_hydrateForm( $oForm ) ;
		$oCtx->add( 'oForm', $oForm ) ;
	}
	
	/**
	 * Recuperation de l'identifiant de la convention
	 * @throws InvalidArgumentException
	 */
	protected function _retrieveConventionId()
	{
		$iId = $this->getRequest()->getParam( 'id_data' ) ;
		if ( ! $iId ) {
			throw new InvalidArgumentException( 'Parameter "id_data" required' );
		}
		$this->_iConventionId = $iId ;
	}
	
	/**
	 * @return string r|rw
	 * @param Copilote_Library_Ub $ub
	 */
	protected function _getMode( Copilote_Library_Ub $ub )
	{
		$user = Core_Library_Account::GetInstance()->GetCurrentUser() ;
		
		// admin technique
		if( $user->HasRole( 1 ) ) {
			return "rw" ;
		}
		
		// admin fonctionnel
		if( $user->HasRole( 2 ) ) {
			return "rw" ;
		}
		
		return "r" ;
	}
	
	/**
	 * @param Core_Library_Resource_Context $oForm
	 * 
	 * Les formules "f()f" sont complexes à cause de la présence de 
	 *  2 opérateurs "moins" qui s'annulent en remplacement des additions,
	 *   pour éviter que Javascript ne concatène selon le contexte 
	 *    et pour afficher toujours un résultat décimal !!!
	 */
	protected function _hydrateForm( Core_Library_Resource_XML_Frame_Form $oForm )
	{
		require $this->_getLibPath() . "/Record.php" ;
		require $this->_getLibPath() . "/Ub.php" ;
		require $this->_getLibPath() . "/Demande.php" ;
		require $this->_getLibPath() . "/Depense.php" ;
		require $this->_getLibPath() . "/Convention.php" ;
		require $this->_getLibPath() . "/Programmation.php" ;

		$oProject = Core_Library_Account::GetInstance()->GetCurrentProject() ;
	
		$oDomDoc = new DOMDocument( '1.0' ) ;
		$oDomElt = dom_import_simplexml( $oForm->GetSimpleXMLObject() ) ;
		$oDomElt = $oDomDoc->importNode( $oDomElt, true ) ;
		$oDomDoc->appendChild( $oDomElt ) ;
		
		// Ajout de l'identifiant de la convention dans le chemin du getdataset
		$oGetdatasetPathElt = $oDomDoc->getElementsByTagName( 'getdataset_path' )->item( 0 );
		/* @var $oGetdatasetPathElt DOMElement */
		$oGetdatasetPathElt->nodeValue = preg_replace(
			'/\{id_data\}/',
			$this->_iConventionId,
			$oGetdatasetPathElt->nodeValue
		) ;
		
		$aDataStructureList = $oDomDoc->getElementsByTagName( 'data_structure' ) ;
		$oDataStructure = $aDataStructureList->item( 0 ) ;
		assert( $oDataStructure instanceof DOMElement ) ;
		
		$aBox = $oDomDoc->getElementsByTagName( 'box' ) ;
		foreach( $aBox as $oBoxTable ) {
			if( "tableau_previsionnel" == $oBoxTable->getAttribute( "id" ) ) {
				break ;
			}
		}
		foreach( $aBox as $oBoxRecap ) {
			if( "tableau_recap" == $oBoxRecap->getAttribute( "id" ) ) {
				break ;
			}
		}
					
		$id = $this->getRequest()->getParam( 'id_data', 0 ) ;
		$convention = new Copilote_Library_Convention( "cplt_conv_data", $id ) ;
		$ub = new Copilote_Library_Ub( "cplt_ub_data", $convention->getAttribute( "id_ub" ) ) ;
		
		// droits sur les conventions et programmation (r|rw)
		$mode = $this->_getMode( $ub ) ;
		
		$programmationColumns = array( 
			"id_data", 
			"cout_personnel_total",
			"cout_fonct_ae_total",
			"cout_fonct_cp_total",
			"cout_invest_ae_total",
			"cout_invest_cp_total",
			"total_ae",
			"total_cp",
		) ;
		$previsionColumns = array(
			"cout_personnel_prev",
			"cout_fonct_ae_prev",
			"cout_fonct_cp_prev",
			"cout_invest_ae_prev",
			"cout_invest_cp_prev",
		) ;
		$programmationAlias = "p" ;
		
		$suiviColumns = array(
			"rb1_a_p_conv",
			"rb2_a_p_conv",
			"rb3_a_p_conv",
			"rb1_a_f_conv_ae",
			"rb2_a_f_conv_ae",
			"rb3_a_f_conv_ae",
			"rb1_a_f_conv_cp",
			"rb2_a_f_conv_cp",
			"rb3_a_f_conv_cp",
			"rb1_a_i_conv_ae",
			"rb2_a_i_conv_ae",
			"rb3_a_i_conv_ae",
			"rb1_a_i_conv_cp",
			"rb2_a_i_conv_cp",
			"rb3_a_i_conv_cp",
		) ;
		$suiviAlias = "sb" ;
		
		$convColumns = array(
			"cout_personel_ant",
			"cout_fonct_ae_ant",
			"cout_fonct_cp_ant",
			"cout_invest_ae_ant",
			"cout_invest_cp_ant",
			"total_ae_ant",
			"total_cp_ant",
		) ;
		
		$aFieldsAE = array() ;
		$aFieldsAE[] = "{c.total_ae_ant}" ;
		$aFieldsCP[] = "{c.total_cp_ant}" ;
		
		// anteriorite (dans le code à cause des droits)
		$oBoxRow = $oDomDoc->createElement( "box" ) ;
		$oBoxRow->setAttribute( "class", "row rowx2" ) ;
			
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-1 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$oText->appendChild( $oDomDoc->createTextNode( "Antériorité" ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-1 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$oText->appendChild( $oDomDoc->createTextNode( "Cumul crédits consommés" ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		foreach( $convColumns as $columnName ) {
			$oBoxCell = $oDomDoc->createElement( "box" ) ;
			$oBoxCell->setAttribute( "class", "cel-1 rowx2" ) ;
			$oValue = $oDomDoc->createElement( "value" ) ;
			$oValue->setAttribute( "dataset", "c" ) ;
			$oValue->setAttribute( "field", $columnName ) ;
			$oValue->setAttribute( "mode", $mode ) ;
			$oBoxCell->appendChild( $oValue ) ;
			$oBoxRow->appendChild( $oBoxCell ) ;
		}
		
		$oBoxTable->appendChild( $oBoxRow ) ;
		
		// Pour chaque période programmée
		foreach( $convention->getProgrammations() as $programmation ) {
			assert( $programmation instanceof Copilote_Library_Programmation ) ;
			$annee = $programmation->getAttribute( "annee_conv" ) ;
			$demande = $convention->GetDemande( $annee ) ;
			
			$aFieldsAE[] = sprintf( "{%s%d.total_ae}", $programmationAlias, $annee ) ;
			$aFieldsCP[] = sprintf( "{%s%d.total_cp}", $programmationAlias, $annee ) ;
			
			/**
			 * statut = 0 => pas de demande
			 * statut = 1 => en cours de saisie
			 * statut > 1 => validé et au-delà
			 * @var integer
			 */
			$statutDemande = 0 ;
			if( $demande instanceof Copilote_Library_Demande ) {
				$dico = $oProject->DicoManager()->GetDico( "lg15" ) ;
				$statutDemande = (int) $dico->id2Code( $demande->getAttribute( "etat" ) ) ;
			}
			
			// DataStructure : programmation
			$varset = $oProject->GetVarset( "programmation" ) ;
			
			$oDataQuery = $oDomDoc->createElement( "dataquery" ) ;
			$oDataQuery->setAttribute( "id", sprintf( "%s%d", $programmationAlias, $annee ) ) ;
			$oDataQuery->setAttribute( "table_name", $varset->DataTableName() ) ;
			$oDataQuery->setAttribute( "varset_name", $varset->GetVarsetName() ) ;
			$oDataQuery->setAttribute( "table_alias", $programmationAlias ) ;
			$oDataQuery->setAttribute( "mode", $mode ) ;
			
			foreach( $programmationColumns as $columnName ) {
				$oColumn = $oDomDoc->createElement( "column_simple" ) ;
				$oColumn->setAttribute( "field_name", $columnName ) ;
				$oColumn->setAttribute( "table_name", $programmationAlias ) ;
				$oDataQuery->appendChild( $oColumn ) ;
			}
			
			foreach( $previsionColumns as $columnName ) {
				$oColumn = $oDomDoc->createElement( "column_simple" ) ;
				$oColumn->setAttribute( "field_name", $columnName ) ;
				$oColumn->setAttribute( "table_name", $programmationAlias ) ;
				$oDataQuery->appendChild( $oColumn ) ;
			}
			
			$oCondition = $oDomDoc->createElement( "condition" ) ;
			$oCondition->setAttribute( "sql", "{p.id_conv} = {c.id_data}" ) ;
			$oField = $oDomDoc->createElement( "field" ) ;
			$oField->setAttribute( "field_name", "id_conv" ) ;
			$oField->setAttribute( "table_name", $programmationAlias ) ;
			$oField->setAttribute( "alias", "p.id_conv" ) ;
			$oCondition->appendChild( $oField ) ;
			$oVariable = $oDomDoc->createElement( "variable" ) ;
			$oVariable->setAttribute( "alias", "c.id_data" ) ;
			$oVariable->setAttribute( "default", "NULL" ) ;
			$oEntry = $oDomDoc->createElement( "entry" ) ;
			$oEntry->setAttribute( "type", "dataset" ) ;
			$oEntry->setAttribute( "name", "c" ) ;
			$oEntry->setAttribute( "field", "id_data" ) ;
			$oEntry->setAttribute( "row", "current" ) ;
			$oVariable->appendChild( $oEntry ) ;
			$oCondition->appendChild( $oVariable ) ;
			$oDataQuery->appendChild( $oCondition ) ;
			
			$oCondition = $oDomDoc->createElement( "condition" ) ;
			$oCondition->setAttribute( "sql", "{p.annee_conv} = " . $annee ) ;
			$oField = $oDomDoc->createElement( "field" ) ;
			$oField->setAttribute( "field_name", "annee_conv" ) ;
			$oField->setAttribute( "table_name", $programmationAlias ) ;
			$oField->setAttribute( "alias", "p.annee_conv" ) ;
			$oCondition->appendChild( $oField ) ;
			$oDataQuery->appendChild( $oCondition ) ;
			$oDataStructure->appendChild( $oDataQuery ) ;
			
			// DataStructure : suivi_bud si demande validée
			/*if( $statutDemande > 1 ) {
				$varset = $oProject->GetVarset( "suivi_bud" ) ;
				
				$oDataQuery = $oDomDoc->createElement( "dataquery" ) ;
				$oDataQuery->setAttribute( "id", sprintf( "%s%d", $suiviAlias, $annee ) ) ;
				$oDataQuery->setAttribute( "table_name", $varset->DataTableName() ) ;
				$oDataQuery->setAttribute( "varset_name", $varset->GetVarsetName() ) ;
				$oDataQuery->setAttribute( "table_alias", $suiviAlias ) ;
				$oDataQuery->setAttribute( "mode", "r" ) ;
	
				foreach( $suiviColumns as $columnName ) {
					$oColumn = $oDomDoc->createElement( "column_simple" ) ;
					$oColumn->setAttribute( "field_name", $columnName ) ;
					$oColumn->setAttribute( "table_name", $suiviAlias ) ;
					$oDataQuery->appendChild( $oColumn ) ;
				}
				
				foreach( range( 1, 3 ) as $indexAjustement ) {
					$sMotif = "COALESCE({perso}, 0) + COALESCE({fonc}, 0) + COALESCE({invest}, 0)" ;
					$oColumn = $oDomDoc->createElement( "column" ) ;
					$oColumn->setAttribute( "sql", $sMotif ) ;
					$oColumn->setAttribute( "alias", sprintf( "rb%d_total_ae", $indexAjustement ) ) ;
					$oColumn->setAttribute( "type", "string" ) ;
					$oField = $oDomDoc->createElement( "field" ) ;
					$oField->setAttribute( "field_name", sprintf( "rb%d_a_p_conv", $indexAjustement ) ) ;
					$oField->setAttribute( "table_name", $suiviAlias ) ;
					$oField->setAttribute( "alias", "perso" ) ;
					$oColumn->appendChild( $oField ) ;
					$oField = $oDomDoc->createElement( "field" ) ;
					$oField->setAttribute( "field_name", sprintf( "rb%d_a_f_conv_ae", $indexAjustement ) ) ;
					$oField->setAttribute( "table_name", $suiviAlias ) ;
					$oField->setAttribute( "alias", "fonc" ) ;
					$oColumn->appendChild( $oField ) ;
					$oField = $oDomDoc->createElement( "field" ) ;
					$oField->setAttribute( "field_name", sprintf( "rb%d_a_i_conv_ae", $indexAjustement ) ) ;
					$oField->setAttribute( "table_name", $suiviAlias ) ;
					$oField->setAttribute( "alias", "invest" ) ;
					$oColumn->appendChild( $oField ) ;
					$oDataQuery->appendChild( $oColumn ) ;
					
					$oColumn = $oDomDoc->createElement( "column" ) ;
					$oColumn->setAttribute( "sql", $sMotif ) ;
					$oColumn->setAttribute( "alias", sprintf( "rb%d_total_cp", $indexAjustement ) ) ;
					$oColumn->setAttribute( "type", "string" ) ;
					$oField = $oDomDoc->createElement( "field" ) ;
					$oField->setAttribute( "field_name", sprintf( "rb%d_a_p_conv", $indexAjustement ) ) ;
					$oField->setAttribute( "table_name", $suiviAlias ) ;
					$oField->setAttribute( "alias", "perso" ) ;
					$oColumn->appendChild( $oField ) ;
					$oField = $oDomDoc->createElement( "field" ) ;
					$oField->setAttribute( "field_name", sprintf( "rb%d_a_f_conv_cp", $indexAjustement ) ) ;
					$oField->setAttribute( "table_name", $suiviAlias ) ;
					$oField->setAttribute( "alias", "fonc" ) ;
					$oColumn->appendChild( $oField ) ;
					$oField = $oDomDoc->createElement( "field" ) ;
					$oField->setAttribute( "field_name", sprintf( "rb%d_a_i_conv_cp", $indexAjustement ) ) ;
					$oField->setAttribute( "table_name", $suiviAlias ) ;
					$oField->setAttribute( "alias", "invest" ) ;
					$oColumn->appendChild( $oField ) ;
					$oDataQuery->appendChild( $oColumn ) ;
				}
				
				$oCondition = $oDomDoc->createElement( "condition" ) ;
				$oField = $oDomDoc->createElement( "field" ) ;
				$sFieldName = "id_demande" ;
				$oField->setAttribute( "field_name", $sFieldName ) ;
				$oField->setAttribute( "table_name", $suiviAlias ) ;
				$sFieldAlias = sprintf( "%s.%s", $suiviAlias, $sFieldName ) ;
				$oField->setAttribute( "alias", $sFieldAlias ) ;
				$oCondition->setAttribute( "sql", sprintf( "{%s} = %d", $sFieldAlias, $demande->getId() ) ) ;
				$oCondition->appendChild( $oField ) ;
				$oDataQuery->appendChild( $oCondition ) ;
				
				$oDataStructure->appendChild( $oDataQuery ) ;
			}*/
			
			// Layout : programmation
			$oBoxRow = $oDomDoc->createElement( "box" ) ;
			$oBoxRow->setAttribute( "class", "row rowx2" ) ;
			$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Année " . $annee ) ) ;
			$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Cumul crédits" ) ) ;
			
			foreach( $programmationColumns as $columnName ) {
				if( "id_data" != $columnName ) {
					$oBoxCell = $oDomDoc->createElement( "box" ) ;
					$oBoxCell->setAttribute( "class", "cel-1 rowx2" ) ;
					$oValue = $oDomDoc->createElement( "value" ) ;
					$oValue->setAttribute( "dataset", sprintf( "%s%d", $programmationAlias, $annee ) ) ;
					$oValue->setAttribute( "field", $columnName ) ;
					$oValue->setAttribute( "mode", $mode ) ;
					$oBoxCell->appendChild( $oValue ) ;
					$oBoxRow->appendChild( $oBoxCell ) ;
				}
			}
			$oBoxTable->appendChild( $oBoxRow ) ;
			
			// Layout : Prevision Initiale si pas de demande validée
			if( $statutDemande < 2 ) {
				$oBoxRow = $oDomDoc->createElement( "box" ) ;
				$oBoxRow->setAttribute( "class", "row rowx2" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "" ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Prévision initiale" ) ) ;
				
				foreach( $previsionColumns as $columnName ) {
					$oBoxCell = $oDomDoc->createElement( "box" ) ;
					$oBoxCell->setAttribute( "class", "cel-1 rowx2" ) ;
					$oValue = $oDomDoc->createElement( "value" ) ;
					$oValue->setAttribute( "dataset", sprintf( "%s%d", $programmationAlias, $annee ) ) ;
					$oValue->setAttribute( "field", $columnName ) ;
					$oValue->setAttribute( "mode", $mode ) ;
					$oBoxCell->appendChild( $oValue ) ;
					$oBoxRow->appendChild( $oBoxCell ) ;
				}
				
				foreach( array( "ae", "cp" ) as $aspect ) {
					$sFormule = sprintf( 
						'f(-(-{%1$s%2$d.cout_personnel_prev} - {%1$s%2$d.cout_fonct_%3$s_prev} - {%1$s%2$d.cout_invest_%3$s_prev}))f',
						$programmationAlias,
						$annee,
						$aspect
					) ; 
					$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $sFormule ) ) ;
				}
				$oBoxTable->appendChild( $oBoxRow ) ;
			}
			
			// Layout : Prevision Initiale si demande validée
			if ( $statutDemande > 1 ) {
				$fMontantPersonnel = $demande->GetMontantPersonnel( $convention ) ;
				$fMontantFonctAE = $demande->GetAutreMontant( $convention, "fonctionnement", "ae" ) ;
				$fMontantFonctCP = $demande->GetAutreMontant( $convention, "fonctionnement", "cp" ) ;
				$fMontantInvAE = $demande->GetAutreMontant( $convention, "investissement", "ae" ) ;
				$fMontantInvCP = $demande->GetAutreMontant( $convention, "investissement", "cp" ) ;
				$fTotalAE = $fMontantFonctAE + $fMontantInvAE + $fMontantPersonnel ;
				$fTotalCP = $fMontantFonctCP + $fMontantInvCP + $fMontantPersonnel ;
				
				$oBoxRow = $oDomDoc->createElement( "box" ) ;
				$oBoxRow->setAttribute( "class", "row rowx2" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "" ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Prévision initiale" ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantPersonnel ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantFonctAE ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantFonctCP ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantInvAE ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantInvCP ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalAE ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalCP ) ) ;
				$oBoxTable->appendChild( $oBoxRow ) ;
			}
			
			// Layout : Ajustements t1, t2 et t3 si demande validée
			if( $statutDemande > 1 ) {
				$suiviBudgetaire = $demande->getSuiviBudgetaire() ;
				
				foreach( range( 1, 3 ) as $indexAjustement ) {
					$oBoxRow = $oDomDoc->createElement( "box" ) ;
					$oBoxRow->setAttribute( "class", "row rowx2" ) ;
					$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "" ) ) ;
					$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Ajustement T" . $indexAjustement ) ) ;
					$leftString = "rb" . $indexAjustement ;
					$totalAE = 0.0 ;
					$totalCP = 0.0 ;
					
					foreach( $suiviColumns as $columnName ) {
						if( $leftString == substr( $columnName, 0, 3 ) ) {
							$textContent = $suiviBudgetaire->getAttribute( $columnName ) ;
							if( substr( $columnName, -2 ) == "ae" ) {
								$totalAE += (float) $textContent ;
							} 
							elseif( substr( $columnName, -2 ) == "cp" ) {
								$totalCP += (float) $textContent ;
							} 
							else{
								$totalAE += (float) $textContent ;
								$totalCP += (float) $textContent ;
							}
							$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $textContent ) ) ;
						}
					}
					
					$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $totalAE ) ) ;
					$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $totalCP ) ) ;
					$oBoxTable->appendChild( $oBoxRow ) ;
				}
			}
			
			// Layout : Total si demande validée
			if( $statutDemande > 1 ) {
				$oBoxRow = $oDomDoc->createElement( "box" ) ;
				$oBoxRow->setAttribute( "class", "row rowx2" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "" ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Total prévision" ) ) ;
				$fTotalPerso = $this->getSubTotal( $suiviBudgetaire, $suiviColumns, "p_conv" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalPerso ) ) ;
				$fTotalFoncAE = $this->getSubTotal( $suiviBudgetaire, $suiviColumns, "f_conv_ae" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalFoncAE ) ) ;
				$fTotalFoncCP = $this->getSubTotal( $suiviBudgetaire, $suiviColumns, "f_conv_cp" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalFoncCP ) ) ;
				$fTotalInvAE = $this->getSubTotal( $suiviBudgetaire, $suiviColumns, "i_conv_ae" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalInvAE ) ) ;
				$fTotalInvCP = $this->getSubTotal( $suiviBudgetaire, $suiviColumns, "i_conv_cp" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalInvCP ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalPerso + $fTotalFoncAE + $fTotalInvAE ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalPerso + $fTotalFoncCP + $fTotalInvCP ) ) ;
				$oBoxTable->appendChild( $oBoxRow ) ;
			}
		}
		
		// Layout : Recap
		$oBoxRow = $oDomDoc->createElement( "box" ) ;
		$oBoxRow->setAttribute( "class", "row rowx2" ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$oText->appendChild( $oDomDoc->createTextNode( "Montant AE" ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$oText->appendChild( $oDomDoc->createTextNode( "f({c.credits_ouvert})f" ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$sFormule = sprintf( "f(-(-%s))f", implode( " - ", $aFieldsAE ) ) ;
		$oText->appendChild( $oDomDoc->createTextNode( $sFormule ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$sFormule = sprintf( "f({c.credits_ouvert} - %s)f", implode( " - ", $aFieldsAE ) ) ;
		$oText->appendChild( $oDomDoc->createTextNode( $sFormule ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxRecap->appendChild( $oBoxRow ) ;
		
		$oBoxRow = $oDomDoc->createElement( "box" ) ;
		$oBoxRow->setAttribute( "class", "row rowx2" ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$oText->appendChild( $oDomDoc->createTextNode( "Montant CP" ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$oText->appendChild( $oDomDoc->createTextNode( "f({c.credits_ouvert})f" ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$sFormule = sprintf( "f(-(-%s))f", implode( " - ", $aFieldsCP ) ) ;
		$oText->appendChild( $oDomDoc->createTextNode( $sFormule ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-2 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$sFormule = sprintf( "f({c.credits_ouvert} - %s)f", implode( " - ", $aFieldsCP ) ) ;
		$oText->appendChild( $oDomDoc->createTextNode( $sFormule ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxRecap->appendChild( $oBoxRow ) ;
		
		$oForm->SetContent( $oDomDoc->saveXML() ) ;
		//error_log( $oDomDoc->saveXML() ) ;
	}
	
	/**
	 * @return float
	 * @param Copilote_Library_Record $suiviBudgetaire
	 * @param array $suiviColumns
	 * @param string $suffixe
	 */
	public function getSubTotal( Copilote_Library_Record $suiviBudgetaire, array $suiviColumns, $suffixe )
	{
		$montant = 0.0 ;
		$length = strlen( $suffixe ) ;
		
		foreach( $suiviColumns as $columnName ) {
			if( substr( $columnName, - $length ) == $suffixe ) {
				$montant += $suiviBudgetaire->getAttribute( $columnName ) ;
			}
		}
		
		return $montant ;
	}
	
	/**
	 * @return DomNode
	 * @param DOMDocument $oDomDoc
	 * @param string $text
	 */
	public function getTextCell( DOMDocument $oDomDoc, $text )
	{
		$oBoxCell = $oDomDoc->createElement( "box" ) ;
		$oBoxCell->setAttribute( "class", "cel-1 rowx2" ) ;
		$oText = $oDomDoc->createElement( "statictext" ) ;
		$oText->appendChild( $oDomDoc->createTextNode( $text ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		return $oBoxCell ;
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