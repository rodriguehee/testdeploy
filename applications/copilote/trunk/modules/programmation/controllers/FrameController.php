<?php

class Programmation_FrameController extends Core_Library_Controller_Form_Frame
{
	/**
	 * @var integer
	 */
	protected $_iConventionId = 0;
	
	/**
	 * Affichage du formulaire
	 */
	public function getAction()
	{
		$this->_retrieveIdData();
		parent::_getFormConfiguration();
	}
	
	/**
	 * Refresh de dataset : autocompletion
	 */
	public function getdatasetAction()
	{
		$this->_retrieveIdData();
		parent::_getDataset();
	}
	
	/**
	 * @see Core_Library_Controller_Action::_save_beforeCommit()
	 * @param Core_Library_Event_Context $oContext
	 */
	protected function _save_afterCommit(Core_Library_Event_Context $oContext)
	{
		$this->_includeClasses();
		
		$json = $oContext->get('oDataJson');
		assert($json instanceof Core_Library_Resource_JSON);
		$content = json_decode($json->GetContent());
		$convention = null;
		
		foreach ($content->data as $object) {
			if ($object->id == "c") {
				$row = current($object->rowdata);
				$convention = new Copilote_Library_Convention("cplt_conv_data", $row->id_data);
			}
		}
		
		if ($convention instanceof Copilote_Library_Convention) {
			$convention->computeProgrammations();
			foreach (array("ae", "cp") as $aspect) {
				$convention->computeAnteriority($aspect);
				$convention->computeFraisGestion($aspect);
				$convention->computeCreditOuvert($aspect);
				$convention->computeCreditConsomme($aspect);
				$convention->computeProvisionChomage($aspect);
				$convention->computeCreditDisponible($aspect);
			}
			$convention->commit();
		}
	}
	
	/**
	 * @return void
	 * @throws InvalidArgumentException
	 */
	protected function _retrieveIdData()
	{
		$iId = $this->getRequest()->getParam('id_data');
		if (! $iId) {
			throw new InvalidArgumentException('Parameter "id_data" required');
		}
		$this->_iConventionId = $iId;
	}
	
	/**
	 * @param Core_Library_Event_Context $oCtx
	 */
	protected function _retrieveForm( Core_Library_Event_Context $oCtx )
	{
		
		$form = new Core_Library_Resource_XML_Frame_Form();
		$filename = sprintf('%s/modules/programmation/resources/form.programmation.xml', APPLICATION_PATH);
		$form->LoadFromFile($filename);
		$form->SetProject(Core_Library_Account::GetInstance()->GetCurrentProject());
		$this->_hydrateForm($form);
		$oCtx->add('oForm', $form);
	}
	
	/**
	 * @return void
	 * @param Core_Library_Resource_Context $form
	 */
	protected function _hydrateForm( Core_Library_Resource_XML_Frame_Form $form )
	{
		$this->_includeClasses() ;
		
		$document = new Copilote_Library_Programmation_Document($form);
		$document->setDatasetPath($this->_iConventionId);
		$convention = new Copilote_Library_Convention("cplt_conv_data", $this->_iConventionId);
		$cellFabric = new Copilote_Library_Programmation_CellFabric($document->getDomDocument(), $convention->getModeProgrammation());
		$rowFabric = new Copilote_Library_Programmation_RowFabric($document->getDomDocument());
		$dataqueryFabric = new Copilote_Library_Programmation_DataqueryFabric($document->getDomDocument());
		
		$formuleCAE = new Copilote_Library_Programmation_CreditConsomme();
		$formuleCCP = new Copilote_Library_Programmation_CreditConsomme();
		$formulePx = new Copilote_Library_Programmation_Addition();
		$formuleZAE = new Copilote_Library_Programmation_Addition();
		$formuleZCP = new Copilote_Library_Programmation_Addition();
		
		$formuleCCAE = new Copilote_Library_Programmation_Addition();
		$formuleCCAE->attach("c.cout_personel_ant");
		$formuleCCAE->attach("c.cout_fonct_ae_ant");
		$formuleCCAE->attach("c.cout_invest_ae_ant");
		$formuleCAE->attach($formuleCCAE);
		$formulePx->attach("c.cout_personel_ant");
		$formuleCCCP = new Copilote_Library_Programmation_Addition();
		$formuleCCCP->attach("c.cout_personel_ant");
		$formuleCCCP->attach("c.cout_fonct_cp_ant");
		$formuleCCCP->attach("c.cout_invest_cp_ant");
		$formuleCCP->attach($formuleCCCP);
		$rowAnteriority = $rowFabric->getElement();
		$rowAnteriority->appendChild($cellFabric->getStaticText("Antériorité"));
		$rowAnteriority->appendChild($cellFabric->getStaticText("Crédits consommés"));
		$rowAnteriority->appendChild($cellFabric->getInputText("c", "cout_personel_ant"));
		$rowAnteriority->appendChild($cellFabric->getInputText("c", "cout_fonct_ae_ant"));
		$rowAnteriority->appendChild($cellFabric->getInputText("c", "cout_fonct_cp_ant"));
		$rowAnteriority->appendChild($cellFabric->getInputText("c", "cout_invest_ae_ant"));
		$rowAnteriority->appendChild($cellFabric->getInputText("c", "cout_invest_cp_ant"));
		$rowAnteriority->appendChild($cellFabric->getStaticText($formuleCCAE->render()));
		$rowAnteriority->appendChild($cellFabric->getStaticText($formuleCCCP->render()));
		$document->getBoxElement("tableau_previsionnel")->appendChild($rowAnteriority);
		
		foreach ($convention->getProgrammations() as $programmation) {
			$annee = $programmation->getAttribute("annee_conv");
			$demande = $convention->GetDemande($annee);
			$demandeValidee = $programmation->getDemandeValidee($demande);
			$dataQuery = $dataqueryFabric->getElement($annee, $convention->getModeProgrammation());
			$document->getDataStructureElement()->appendChild($dataQuery);
			$datasetName = sprintf("%s%d", $dataqueryFabric->getAlias(), $annee);
			
			$formulePerso = new Copilote_Library_Programmation_Addition();
			$formuleFoncAE = new Copilote_Library_Programmation_Addition();
			$formuleFoncCP = new Copilote_Library_Programmation_Addition();
			$formuleInvAE = new Copilote_Library_Programmation_Addition();
			$formuleInvCP = new Copilote_Library_Programmation_Addition();
				
			$rowSchedule = $rowFabric->getElement();
			$rowSchedule->appendChild($cellFabric->getStaticText("Année " . $annee));
			$rowSchedule->appendChild($cellFabric->getStaticText("Prévision initiale"));
			if ($programmation->isEditableStartSchedule($demandeValidee->getAttribute("etat"))) { 
				$formulePrevAE = new Copilote_Library_Programmation_Addition();
				$formulePrevAE->attach($datasetName . ".cout_personnel_prev");
				$formulePrevAE->attach($datasetName . ".cout_fonct_ae_prev");
				$formulePrevAE->attach($datasetName . ".cout_invest_ae_prev");
				$formulePrevCP = new Copilote_Library_Programmation_Addition();
				$formulePrevCP->attach($datasetName . ".cout_personnel_prev");
				$formulePrevCP->attach($datasetName . ".cout_fonct_cp_prev");
				$formulePrevCP->attach($datasetName . ".cout_invest_cp_prev");
				$rowSchedule->appendChild($cellFabric->getInputText($datasetName, "cout_personnel_prev"));
				$rowSchedule->appendChild($cellFabric->getInputText($datasetName, "cout_fonct_ae_prev"));
				$rowSchedule->appendChild($cellFabric->getInputText($datasetName, "cout_fonct_cp_prev"));
				$rowSchedule->appendChild($cellFabric->getInputText($datasetName, "cout_invest_ae_prev"));
				$rowSchedule->appendChild($cellFabric->getInputText($datasetName, "cout_invest_cp_prev"));
				$rowSchedule->appendChild($cellFabric->getStaticText($formulePrevAE->render()));
				$rowSchedule->appendChild($cellFabric->getStaticText($formulePrevCP->render()));
				$document->getBoxElement("tableau_previsionnel")->appendChild($rowSchedule);
				$formulePerso->attach($datasetName . ".cout_personnel_prev");
				$formuleFoncAE->attach($datasetName . ".cout_fonct_ae_prev");
				$formuleFoncCP->attach($datasetName . ".cout_fonct_cp_prev");
				$formuleInvAE->attach($datasetName . ".cout_invest_ae_prev");
				$formuleInvCP->attach($datasetName . ".cout_invest_cp_prev");
			}
			else { 
				$montantPersonnel = $demandeValidee->GetMontantPersonnel($convention);
				$montantFonctAE = $demandeValidee->GetAutreMontant($convention, "fonctionnement", "ae");
				$montantFonctCP = $demandeValidee->GetAutreMontant($convention, "fonctionnement", "cp");
				$montantInvAE = $demandeValidee->GetAutreMontant($convention, "investissement", "ae");
				$montantInvCP = $demandeValidee->GetAutreMontant($convention, "investissement", "cp");
				$totalAE = $montantFonctAE + $montantInvAE + $montantPersonnel;
				$totalCP = $montantFonctCP + $montantInvCP + $montantPersonnel;
				$rowSchedule->appendChild($cellFabric->getStaticText($montantPersonnel));
				$rowSchedule->appendChild($cellFabric->getStaticText($montantFonctAE));
				$rowSchedule->appendChild($cellFabric->getStaticText($montantFonctCP));
				$rowSchedule->appendChild($cellFabric->getStaticText($montantInvAE));
				$rowSchedule->appendChild($cellFabric->getStaticText($montantInvCP));
				$rowSchedule->appendChild($cellFabric->getStaticText($totalAE));
				$rowSchedule->appendChild($cellFabric->getStaticText($totalCP));
				$formulePerso->increment($montantPersonnel);
				$formuleFoncAE->increment($montantFonctAE);
				$formuleFoncCP->increment($montantFonctCP);
				$formuleInvAE->increment($montantInvAE);
				$formuleInvCP->increment($montantInvCP);
			}
			$document->getBoxElement("tableau_previsionnel")->appendChild($rowSchedule);
			
			if ($programmation->hasCorrections($demandeValidee->getAttribute("etat"))) {
				foreach (range( 1, 3 ) as $indexCorrection) {
					$suiviBudgetaire = $demande->getSuiviBudgetaire($convention, $indexCorrection);
					if ($suiviBudgetaire instanceof Copilote_Library_Record) {
						$montantPersonnel = (float) $suiviBudgetaire->getAttribute("d_pers");
						$montantFonctionnement = (float) $suiviBudgetaire->getAttribute("d_fonct");
						$montantInvestissement = (float) $suiviBudgetaire->getAttribute("d_invest");
						$total = $montantPersonnel + $montantFonctionnement + $montantInvestissement;
						$rowCorrection = $rowFabric->getElement();
						$rowCorrection->appendChild($cellFabric->getStaticText(""));
						$rowCorrection->appendChild($cellFabric->getStaticText("Ajustement T" . $indexCorrection));
						$rowCorrection->appendChild($cellFabric->getStaticText($montantPersonnel));
						$rowCorrection->appendChild($cellFabric->getStaticText($montantFonctionnement));
						$rowCorrection->appendChild($cellFabric->getStaticText($montantFonctionnement));
						$rowCorrection->appendChild($cellFabric->getStaticText($montantInvestissement));
						$rowCorrection->appendChild($cellFabric->getStaticText($montantInvestissement));
						$rowCorrection->appendChild($cellFabric->getStaticText($total));
						$rowCorrection->appendChild($cellFabric->getStaticText($total));
						$document->getBoxElement("tableau_previsionnel")->appendChild($rowCorrection);
						$formulePerso->increment($montantPersonnel);
						$formuleFoncAE->increment($montantFonctionnement);
						$formuleFoncCP->increment($montantFonctionnement);
						$formuleInvAE->increment($montantInvestissement);
						$formuleInvCP->increment($montantInvestissement);
					}
				}
			}
			
			// TotalSchedule
			$formuleTotalAE = new Copilote_Library_Programmation_Addition();
			$formuleTotalCP = new Copilote_Library_Programmation_Addition();
			$formuleTotalAE->attachFrom($formulePerso);
			$formuleTotalAE->attachFrom($formuleFoncAE);
			$formuleTotalAE->attachFrom($formuleInvAE);
			$formuleTotalCP->attachFrom($formulePerso);
			$formuleTotalCP->attachFrom($formuleFoncCP);
			$formuleTotalCP->attachFrom($formuleInvCP);
			if ($annee >= Copilote_Library_Convention::getReference()) {
				$formuleZAE->attachFrom($formuleTotalAE);
				$formuleZCP->attachFrom($formuleTotalCP);
			}
			$rowSum = $rowFabric->getElement();
			$rowSum->appendChild($cellFabric->getStaticText(""));
			$rowSum->appendChild($cellFabric->getStaticText("Total prévision"));
			$rowSum->appendChild($cellFabric->getStaticText($formulePerso->render()));
			$rowSum->appendChild($cellFabric->getStaticText($formuleFoncAE->render()));
			$rowSum->appendChild($cellFabric->getStaticText($formuleFoncCP->render()));
			$rowSum->appendChild($cellFabric->getStaticText($formuleInvAE->render()));
			$rowSum->appendChild($cellFabric->getStaticText($formuleInvCP->render()));
			$rowSum->appendChild($cellFabric->getStaticText($formuleTotalAE->render()));
			$rowSum->appendChild($cellFabric->getStaticText($formuleTotalCP->render()));
			$document->getBoxElement("tableau_previsionnel")->appendChild($rowSum);
		
			$formuleCCAEx = new Copilote_Library_Programmation_Addition();
			$formuleCCAEx->attach($datasetName . ".cout_personnel_total");
			$formuleCCAEx->attach($datasetName . ".cout_fonct_ae_total");
			$formuleCCAEx->attach($datasetName . ".cout_invest_ae_total");
			$formuleCCCPx = new Copilote_Library_Programmation_Addition();
			$formuleCCCPx->attach($datasetName . ".cout_personnel_total");
			$formuleCCCPx->attach($datasetName . ".cout_fonct_cp_total");
			$formuleCCCPx->attach($datasetName . ".cout_invest_cp_total");
			$rowProgram = $rowFabric->getElement();
			$rowProgram->appendChild($cellFabric->getStaticText(""));
			$rowProgram->appendChild($cellFabric->getStaticText("Crédits consommés"));
			$rowProgram->appendChild($cellFabric->getInputText($datasetName, "cout_personnel_total"));
			$rowProgram->appendChild($cellFabric->getInputText($datasetName, "cout_fonct_ae_total"));
			$rowProgram->appendChild($cellFabric->getInputText($datasetName, "cout_fonct_cp_total"));
			$rowProgram->appendChild($cellFabric->getInputText($datasetName, "cout_invest_ae_total"));
			$rowProgram->appendChild($cellFabric->getInputText($datasetName, "cout_invest_cp_total"));
			$rowProgram->appendChild($cellFabric->getStaticText($formuleCCAEx->render()));
			$rowProgram->appendChild($cellFabric->getStaticText($formuleCCCPx->render()));
			$document->getBoxElement("tableau_previsionnel")->appendChild($rowProgram);
			if ($programmation->isElapsed()) {
				$formuleCAE->attach($formuleCCAEx);
				$formuleCCP->attach($formuleCCCPx);
			}
			$formulePx->attach($datasetName . ".cout_personnel_total");
		}
		
		$formuleAPEAE = new Copilote_Library_Programmation_ProvisionChomage();
		$formuleAPECP = new Copilote_Library_Programmation_ProvisionChomage();
		if ($convention->getAttribute("formule") == "ANR") {
			$formuleAPEAE->setCoefficient(0.0);
			$formuleAPECP->setCoefficient(0.0);
		} elseif ($convention->hasComputedAPE()) {
			$formuleAPEAE->setCoefficient(0.1);
			$formuleAPEAE->attachFrom($formulePx);
			$formuleAPECP->setCoefficient(0.1);
			$formuleAPECP->attachFrom($formulePx);
		} else {
			$formuleAPEAE->setCoefficient(1.0);
			$formuleAPEAE->attach("c.recap_ape_ae");
			$formuleAPECP->setCoefficient(1.0);
			$formuleAPECP->attach("c.recap_ape_cp");
		}
		
		$rowAE = $rowFabric->getElement() ;
		$rowAE->appendChild($cellFabric->getStaticText("Montant AE")); 
		$rowAE->appendChild($cellFabric->getInputText("c", "montant_ae", true));
		$formuleOAE = new Copilote_Library_Programmation_CreditOuvert();
		$formuleOAE->setReference("c.montant_ae");
		$formuleFDGAE = new Copilote_Library_Programmation_Multiplication();
		$formuleOAE->setFraisDeGestion($formuleFDGAE);
		if ($convention->hasComputedFDG()) {
			$formuleFDGAE->setCoefficient(0.01);
			$formuleFDGAE->attach("c.montant_ae");
			$formuleFDGAE->attach("c.pourcentage_fdg");
			$rowAE->appendChild($cellFabric->getStaticText($formuleFDGAE->render()));
		} else {
			$formuleFDGAE->attach("c.recap_fdg_ae");
			$rowAE->appendChild($cellFabric->getInputText("c", "recap_fdg_ae"));
		}
		$rowAE->appendChild($cellFabric->getStaticText($formuleOAE->render()));
		$rowAE->appendChild($cellFabric->getStaticText($formuleCAE->render()));
		if ($formuleAPEAE->getCoefficient() == 1) {
			$rowAE->appendChild($cellFabric->getInputText("c", "recap_ape_ae"));
		} else {
			$rowAE->appendChild($cellFabric->getStaticText($formuleAPEAE->render()));
		}
		$formuleVAE = new Copilote_Library_Programmation_CreditDisponible();
		$formuleVAE->setCreditConsomme($formuleCAE);
		$formuleVAE->setCreditOuvert($formuleOAE);
		$formuleVAE->setProvisionChomage($formuleAPEAE);
		$formuleVAE->setPrevisionsAnnuelle($formuleZAE);
		$rowAE->appendChild($cellFabric->getStaticText($formuleVAE->render()));
		$document->getBoxElement("tableau_recap")->appendChild($rowAE);
		
		$rowCP = $rowFabric->getElement();
		$rowCP->appendChild($cellFabric->getStaticText("Montant CP"));
		$rowCP->appendChild($cellFabric->getInputText("c", "montant_cp", true));
		$formuleOCP = new Copilote_Library_Programmation_CreditOuvert();
		$formuleOCP->setReference("c.montant_cp");
		$formuleFDGCP = new Copilote_Library_Programmation_Multiplication();
		$formuleOCP->setFraisDeGestion($formuleFDGCP);
		if ($convention->hasComputedFDG()) {
			$formuleFDGCP->setCoefficient(0.01);
			$formuleFDGCP->attach("c.montant_cp");
			$formuleFDGCP->attach("c.pourcentage_fdg");
			$rowCP->appendChild($cellFabric->getStaticText($formuleFDGCP->render()));
		}
		else {
			$formuleFDGCP->attach("c.recap_fdg_cp");
			$rowCP->appendChild($cellFabric->getInputText("c", "recap_fdg_cp"));
		}
		$rowCP->appendChild($cellFabric->getStaticText($formuleOCP->render()));
		$rowCP->appendChild($cellFabric->getStaticText($formuleCCP->render()));
		if ($formuleAPECP->getCoefficient() == 1) {
			$rowCP->appendChild($cellFabric->getInputText("c", "recap_ape_cp"));
		} else {
			$rowCP->appendChild($cellFabric->getStaticText($formuleAPECP->render()));
		}
		$formuleVCP = new Copilote_Library_Programmation_CreditDisponible();
		$formuleVCP->setCreditConsomme($formuleCCP);
		$formuleVCP->setCreditOuvert($formuleOCP);
		$formuleVCP->setProvisionChomage($formuleAPECP);
		$formuleVCP->setPrevisionsAnnuelle($formuleZCP);
		$rowCP->appendChild($cellFabric->getStaticText($formuleVCP->render()));
		$document->getBoxElement("tableau_recap")->appendChild($rowCP);
		
		$submitFormula = new Copilote_Library_Programmation_SubmitFormula();
		$submitFormula->setCreditDisponible("ae", $formuleVAE);
		$submitFormula->setCreditDisponible("cp", $formuleVCP);
		$optionSubmit = $document->getOptionElement("submit-box");
		$optionSubmit->setAttribute("value", $submitFormula->render("<=", "&&"));
		$optionTopMsg = $document->getOptionElement("top-message");
		$optionTopMsg->setAttribute("value", $submitFormula->render(">", "||"));
		$optionBottomMsg = $document->getOptionElement("bottom-message");
		$optionBottomMsg->setAttribute("value", $submitFormula->render(">", "||"));
		
		$form->SetContent($document->getDomDocument()->saveXML());
	}
	
	/**
	 * @return void
	 */
	private function _includeClasses()
	{
		$libPath = $this->_getLibPath();
		require $libPath . "/Record.php";
		require $libPath . "/Mock.php";
		require $libPath . "/Ub.php";
		require $libPath . "/Demande.php";
		require $libPath . "/Depense.php";
		require $libPath . "/Convention.php";
		require $libPath . "/RenderEngine.php";
		require $libPath . "/Programmation.php";
		require $libPath . "/Programmation/Formula.php";
		require $libPath . "/Programmation/SubmitFormula.php";
		require $libPath . "/Programmation/ProvisionChomage.php";
		require $libPath . "/Programmation/CreditOuvert.php";
		require $libPath . "/Programmation/CreditConsomme.php";
		require $libPath . "/Programmation/Addition.php";
		require $libPath . "/Programmation/Multiplication.php";
		require $libPath . "/Programmation/CreditDisponible.php";
		require $libPath . "/Programmation/CellFabric.php";
		require $libPath . "/Programmation/RowFabric.php";
		require $libPath . "/Programmation/DataqueryFabric.php";
		require $libPath . "/Programmation/Document.php";
	}
	
	/**
	 * @return string
	 * @throws Core_Library_Exception
	 */
	private function _getLibPath()
	{
		$libPath = Core_Library_Options::get( 'lib.path' ) ;
		if ( $libPath  === false ) {
			throw new Core_Library_Exception( 'Libraries path not found in ini file' );
		}
		return $libPath ;
	}
}