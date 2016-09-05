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
		
		$aOptions = $oDomDoc->getElementsByTagName( 'option' ) ;
		$oOptionBtn = null ;
		$oOptionTxt = null ;
		foreach( $aOptions as $oOption ) {
			if( "show_on" == $oOption->getAttribute( "option_name" ) ) {
				if( "false" == $oOption->getAttribute( "value" ) ) {
					$oOptionBtn = $oOption ;
				}
			}
			if( "hide_on" == $oOption->getAttribute( "option_name" ) ) {
				if( "false" == $oOption->getAttribute( "value" ) ) {
					$oOptionTxt = $oOption ;
				}
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
		
		$fCurrentAE = 0.0 ;
		$fCurrentCP = 0.0 ;
		
		$reference = Core_Library_Options::get( 'conv.pluri.ref' ) ;
		if ( $reference  === false ) {
			throw new Core_Library_Exception( 'Reference for programmation not found in ini file' );
		}
		
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
			$demandeValidee = null ;
			
			$aFieldsAE[] = sprintf( "{%s%d.total_ae}", $programmationAlias, $annee ) ;
			$aFieldsCP[] = sprintf( "{%s%d.total_cp}", $programmationAlias, $annee ) ;
			
			/**
			 * statut = 0 => pas de demande
			 * statut = 1 => en cours de saisie
			 * statut > 1 => validé et au-delà
			 * @var integer
			 */
			$statutDemande = 0 ;
			if ( $demande instanceof Copilote_Library_Demande ) {
				$dico = $oProject->DicoManager()->GetDico( "lg15" ) ;
				$statutDemande = (int) $dico->id2Code( $demande->getAttribute( "etat" ) ) ;
			}
			
			if( $statutDemande > 1 ) {
				$demandeValidee = $convention->GetDemandeValidee( $annee ) ;
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
					$oValue->setAttribute( "mode", "rw" ) ;
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
			
			$fTotalPersonnel = 0.0 ;
			$fTotalFonctionnementAE = 0.0 ;
			$fTotalFonctionnementCP = 0.0 ;
			$fTotalInvestissementAE = 0.0 ;
			$fTotalInvestissementCP = 0.0 ;
				
			// Layout : Prevision Initiale si demande validée
			if ( $statutDemande > 1 ) {
				$fMontantPersonnel = $demandeValidee->GetMontantPersonnel( $convention ) ;
				$fMontantFonctAE = $demandeValidee->GetAutreMontant( $convention, "fonctionnement", "ae" ) ;
				$fMontantFonctCP = $demandeValidee->GetAutreMontant( $convention, "fonctionnement", "cp" ) ;
				$fMontantInvAE = $demandeValidee->GetAutreMontant( $convention, "investissement", "ae" ) ;
				$fMontantInvCP = $demandeValidee->GetAutreMontant( $convention, "investissement", "cp" ) ;
				/*$fMontantPersonnel = $demande->GetMontantPersonnel( $convention ) ;
				$fMontantFonctAE = $demande->GetAutreMontant( $convention, "fonctionnement", "ae" ) ;
				$fMontantFonctCP = $demande->GetAutreMontant( $convention, "fonctionnement", "cp" ) ;
				$fMontantInvAE = $demande->GetAutreMontant( $convention, "investissement", "ae" ) ;
				$fMontantInvCP = $demande->GetAutreMontant( $convention, "investissement", "cp" ) ;*/
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
				
				$fTotalPersonnel += $fMontantPersonnel ;
				$fTotalFonctionnementAE += $fMontantFonctAE ;
				$fTotalFonctionnementCP += $fMontantFonctCP ;
				$fTotalInvestissementAE += $fMontantInvAE ;
				$fTotalInvestissementCP += $fMontantInvCP ;
			}
			
			// Layout : Ajustements t1, t2 et t3 si demande validée
			if( $statutDemande > 1 ) {
				foreach( range( 1, 3 ) as $indexAjustement ) {
					$suiviBudgetaire = $demande->getSuiviBudgetaire( $convention, $indexAjustement ) ;
					
					if( $suiviBudgetaire instanceof Copilote_Library_Record ) {
						$oBoxRow = $oDomDoc->createElement( "box" ) ;
						$oBoxRow->setAttribute( "class", "row rowx2" ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "" ) ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Ajustement T" . $indexAjustement ) ) ;
						$fMontantPersonnel = (float) $suiviBudgetaire->getAttribute( "d_pers" ) ;
						$fMontantFonctionnement = (float) $suiviBudgetaire->getAttribute( "d_fonct" ) ;
						$fMontantInvestissement = (float) $suiviBudgetaire->getAttribute( "d_invest" ) ;
						$fTotal = $fMontantPersonnel + $fMontantFonctionnement + $fMontantInvestissement ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantPersonnel ) ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantFonctionnement ) ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantFonctionnement ) ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantInvestissement ) ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fMontantInvestissement ) ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotal ) ) ;
						$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotal ) ) ;
						$oBoxTable->appendChild( $oBoxRow ) ;
						
						$fTotalPersonnel += $fMontantPersonnel ;
						$fTotalFonctionnementAE += $fMontantFonctionnement ;
						$fTotalFonctionnementCP += $fMontantFonctionnement ;
						$fTotalInvestissementAE += $fMontantInvestissement ;
						$fTotalInvestissementCP += $fMontantInvestissement ;
					}
				}
			}
			
			// Layout : Total si demande validée
			if( $statutDemande > 1 ) {
				$oBoxRow = $oDomDoc->createElement( "box" ) ;
				$oBoxRow->setAttribute( "class", "row rowx2" ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "" ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, "Total prévision" ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalPersonnel ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalFonctionnementAE ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalFonctionnementCP ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalInvestissementAE ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalInvestissementCP ) ) ;
				$fTotalAE = $fTotalPersonnel + $fTotalFonctionnementAE + $fTotalInvestissementAE ;
				$fTotalCP = $fTotalPersonnel + $fTotalFonctionnementCP + $fTotalInvestissementCP ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalAE ) ) ;
				$oBoxRow->appendChild( $this->getTextCell( $oDomDoc, $fTotalCP ) ) ;
				$oBoxTable->appendChild( $oBoxRow ) ;
				
				if( $reference == $programmation->getAttribute( "annee_conv" ) ) {
					$fCurrentAE += $fTotalAE ;
					$fCurrentCP += $fTotalCP ;
				}
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
		// arrondi à 2 decimales : Math.round(x * 100) / 100
		$sFormule = sprintf( "f( Math.round(100 * ({c.credits_ouvert} - %s - %f) ) /100 )f", implode( " - ", $aFieldsAE ), $fCurrentAE ) ;
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
		// arrondi à 2 decimales : Math.round(x * 100) / 100
		$sFormule = sprintf( "f( Math.round(100 * ({c.credits_ouvert} - %s - %f) ) /100 )f", implode( " - ", $aFieldsCP ), $fCurrentCP ) ;
		$oText->appendChild( $oDomDoc->createTextNode( $sFormule ) ) ;
		$oBoxCell->appendChild( $oText ) ;
		$oBoxRow->appendChild( $oBoxCell ) ;
		
		$oBoxRecap->appendChild( $oBoxRow ) ;
		
		/* 
		 * Condition d'affichage du bouton "save" 
		 *  pour contraindre l'enregistrement d'un crédit solvable
		 */
		$aShowOnValue = array() ;
		$sPattern = "( ( {c.credits_ouvert} - %s - %f ) >= 0 )" ;
		$aShowOnValue[] = sprintf( $sPattern, implode( " - ", $aFieldsAE ), $fCurrentAE ) ; 
		$aShowOnValue[] = sprintf( $sPattern, implode( " - ", $aFieldsCP ), $fCurrentCP ) ; 
		$oOptionBtn->setAttribute( "value", implode( "&&", $aShowOnValue ) ) ;
		$oOptionTxt->setAttribute( "value", implode( "&&", $aShowOnValue ) ) ;
		
		$oForm->SetContent( $oDomDoc->saveXML() ) ;
		//error_log( $oDomDoc->saveXML() ) ;
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