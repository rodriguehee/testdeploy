<?php

class Copilote_Library_Programmation extends Copilote_Library_Record
{
	/**
	 * @var Copilote_Library_Convention
	 */
	private $_convention ;
	
	/**
	 * @param Copilote_Library_Convention $convention
	 */
	public function setConvention(Copilote_Library_Convention $convention)
	{
		$this->_convention = $convention;
	}
	
	/**
	 * @throws DomainException
	 * @throws LogicException
	 * @return Copilote_Library_Demande | Copilote_Library_Mock
	 * @param Copilote_Library_Demande | null
	 */
	public function getDemandeValidee($demande)
	{
		if (! $this->_convention instanceof Copilote_Library_Convention) {
			throw new LogicException("pas de convention définie");
		}
		
		$statutDemande = 0;
		$demandeValidee = null;
		
		if (is_null($demande)) {
			$demandeValidee = new Copilote_Library_Mock();
			$demandeValidee->setAttribute("etat", $statutDemande);
			return $demandeValidee;
		}
		
		assert($demande instanceof Copilote_Library_Demande);
		$annee = $this->getAttribute("annee_conv");
		$statutDemande = (int) $this->_getStatus($demande->getAttribute("etat"));
		
		if ($statutDemande >= 10) {
			$demandeValidee = $this->_convention->GetDemandeValidee($annee, "10");
		}
		if (is_null($demandeValidee) && $statutDemande >= 6) {
			$demandeValidee = $this->_convention->GetDemandeValidee($annee, "6");
		}
		if (is_null($demandeValidee) && $statutDemande >= 4) {
			$demandeValidee = $this->_convention->GetDemandeValidee($annee, "4");
		}
		if (is_null($demandeValidee) && $statutDemande >= 2) {
			$demandeValidee = $this->_convention->GetDemandeValidee($annee, "2");
		}
		if (is_null($demandeValidee) && $demande instanceof Copilote_Library_Demande) {
			$demandeValidee = $demande;
		}
		
		return $demandeValidee;
	}
	
	/**
	 * @return Core_Library_Dico
	 * @param integer $status
	 */
	private function _getStatus($status)
	{
		$status = (int) $status;
		if ($status > 0) {
			$project = Core_Library_Account::GetInstance()->GetCurrentProject();
			$dico = $project->DicoManager()->GetDico("lg15");
			$status = $dico->id2code($status);
		}
		return $status;
	}
	
	/**
	 * @return boolean
	 * @param integer $statut
	 */
	public function hasTotalSchedule($statut)
	{
		return true;
	}
	
	/**
	 * @return boolean
	 * @param integer $statut
	 */
	public function hasCorrections($statut)
	{
		$statut = $this->_getStatus($statut);
		
		if ($this->getAttribute("annee_conv") != $this->_convention->getReference()) {
			return false;
		}
		
		if ($statut < 10) {
			return false;
		}
		
		return true ;
	}
	
	/**
	 * @return boolean
	 * @param integer $statut
	 */
	 public function isEditableStartSchedule($statut)
	 {
	 	$statut = $this->_getStatus($statut);
	 	
	 	if ($this->getAttribute("annee_conv") > $this->_convention->getReference()) {
	 		return true;
	 	}
	 	
	 	if ($this->getAttribute("annee_conv") < $this->_convention->getReference()) {
	 		return false;
	 	}
	 	
	 	if ($statut < 2) {
	 		return true;
	 	}
	 	
	 	return false;
	 }
	 
	 /**
	  * @param Copilote_Library_Demande $demande
	  * @return void
	  */
	 public function computeTotalSchedule(Copilote_Library_Demande $demande)
	 {
	 	$demandeValidee = $this->getDemandeValidee($demande);
	 	
	 	$montantPersonnel = $demandeValidee->GetMontantPersonnel($this->_convention);
	 	$montantFonctAE = $demandeValidee->GetAutreMontant($this->_convention, "fonctionnement", "ae" );
	 	$montantFonctCP = $demandeValidee->GetAutreMontant($this->_convention, "fonctionnement", "cp" );
	 	$montantInvAE = $demandeValidee->GetAutreMontant($this->_convention, "investissement", "ae" );
	 	$montantInvCP = $demandeValidee->GetAutreMontant($this->_convention, "investissement", "cp" );
	 	$totalAE = $montantFonctAE + $montantInvAE + $montantPersonnel;
	 	$totalCP = $montantFonctCP + $montantInvCP + $montantPersonnel;
	 	
	 	foreach (range(1, 3) as $indexCorrection) {
	 		$suiviBudgetaire = $demande->getSuiviBudgetaire($this->_convention, $indexCorrection);
	 		if ($suiviBudgetaire instanceof Copilote_Library_Record) {
	 			$montantPersonnelSuivi = (float) $suiviBudgetaire->getAttribute("d_pers");
	 			$montantPersonnel += $montantPersonnelSuivi;
	 			$montantFonctionnementSuivi = (float) $suiviBudgetaire->getAttribute("d_fonct");
	 			$montantFonctAE += $montantFonctionnementSuivi;
	 			$montantFonctCP += $montantFonctionnementSuivi;
	 			$montantInvestissementSuivi = (float) $suiviBudgetaire->getAttribute("d_invest");
	 			$montantInvAE += $montantInvestissementSuivi;
	 			$montantInvCP += $montantInvestissementSuivi;
	 			$totalAE += $montantPersonnelSuivi + $montantFonctionnementSuivi + $montantInvestissementSuivi;
	 			$totalCP += $montantPersonnelSuivi + $montantFonctionnementSuivi + $montantInvestissementSuivi;
	 		}
	 	}
	 	
	 	$data = array( 
	 		"total_pers_co" => $montantPersonnel, 
	 		"total_fonc_ae_co" => $montantFonctAE, 
	 		"total_fonc_cp_co" => $montantFonctCP, 
	 		"total_invest_ae_co" => $montantInvAE, 
	 		"total_invest_cp_co" => $montantInvCP, 
	 		"total_ae_co" => $totalAE, 
	 		"total_cp_co" => $totalCP
	 	);
	 	
	 	$project = Core_Library_Account::GetInstance()->GetCurrentProject();
	 	$project->GetVarset("programmation")->SimpleUpdateData($data, (int) $this->_id);
	 }
	 
	 /**
	  * @return boolean
	  */
	 public function isElapsed()
	 {
	 	if ($this->getAttribute("annee_conv") < $this->_convention->getReference()) {
	 		return true;
	 	}
	 	
	 	return false;
	 }
	 
	 /**
	  * @return void
	  * @param string $aspect
	  */
	 public function computeTotal($aspect)
	 {
	 	$total  = (float) $this->getAttribute("cout_personnel_total");
	 	$total += (float) $this->getAttribute(sprintf("cout_fonct_%s_total", $aspect));
	 	$total += (float) $this->getAttribute(sprintf("cout_invest_%s_total", $aspect));
	 	$this->setAttribute(sprintf("total_%s", $aspect), $total);
	 	$this->commit();
	 }
}