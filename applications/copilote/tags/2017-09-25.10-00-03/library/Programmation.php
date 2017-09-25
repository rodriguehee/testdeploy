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
		
		// pour l'historique cela dépend du statut
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
		
		if ($this->getAttribute("annee_conv") != Copilote_Library_Convention::getReference()) {
			return false;
		}
		
		if ($statut < 10) {
			return false;
		}
		
		return true ;
	}
	
	/**
	 * @return boolean
	 * @param mixed $demande (Copilote_Library_Mock|Copilote_Library_Demande)
	 */
	 public function isEditableStartSchedule($demande)
	 {
	     $anneePlusUn = Copilote_Library_Convention::getReference() + 1;
	     if ($this->getAttribute("annee_conv") <= $anneePlusUn) {
	         return false;
	     }
	     
	     return ($demande instanceof Copilote_Library_Mock);
	 }
	 
	 /**
	  * @return boolean
	  */
	 public function isElapsed()
	 {
	 	if ($this->getAttribute("annee_conv") < Copilote_Library_Convention::getReference()) {
	 		return true;
	 	}
	 	
	 	return false;
	 }
	 
	 /**
	  * @return void
	  */
	 public function computePrevisions()
	 {
	 	$annee = $this->getAttribute("annee_conv");
	 	$demande = $this->_convention->GetDemande($annee);
	 	$demandeValidee = $this->getDemandeValidee($demande);
	 	
	 	$totalPersonnel = 0.0;
	 	$totalFonctAE = 0.0;
	 	$totalFonctCP = 0.0;
	 	$totalInvestAE = 0.0;
	 	$totalInvestCP = 0.0;
	 	
	 	if ($this->isEditableStartSchedule($demandeValidee)) {
	 		$totalPersonnel += $this->getAttribute("cout_personnel_prev");
	 		$totalFonctAE += $this->getAttribute("cout_fonct_ae_prev");
	 		$totalFonctCP += $this->getAttribute("cout_fonct_cp_prev");
	 		$totalInvestAE += $this->getAttribute("cout_invest_ae_prev");
	 		$totalInvestCP += $this->getAttribute("cout_invest_cp_prev");
	 	}
	 	else {
	 		$totalPersonnel += $demandeValidee->GetMontantPersonnel($this->_convention);
	 		$totalFonctAE += $demandeValidee->GetAutreMontant($this->_convention, "fonctionnement", "ae");
	 		$totalFonctCP += $demandeValidee->GetAutreMontant($this->_convention, "fonctionnement", "cp");
	 		$totalInvestAE += $demandeValidee->GetAutreMontant($this->_convention, "investissement", "ae");
	 		$totalInvestCP += $demandeValidee->GetAutreMontant($this->_convention, "investissement", "cp");
	 	}
	 	
	 	if ($this->hasCorrections($demandeValidee->getAttribute("etat"))) {
	 		foreach (range( 1, 3 ) as $indexCorrection) {
	 			$suiviBudgetaire = $demande->getSuiviBudgetaire($this->_convention, $indexCorrection);
	 			if ($suiviBudgetaire instanceof Copilote_Library_Record) {
			 		$totalPersonnel += (float) $suiviBudgetaire->getAttribute("d_pers");
			 		$totalFonctAE += (float) $suiviBudgetaire->getAttribute("d_fonct");
			 		$totalFonctCP += (float) $suiviBudgetaire->getAttribute("d_fonct");
			 		$totalInvestAE += (float) $suiviBudgetaire->getAttribute("d_invest");
			 		$totalInvestCP += (float) $suiviBudgetaire->getAttribute("d_invest");
	 			}
	 		}
	 	}
	 	
	 	$this->setAttribute("total_pers_co", $totalPersonnel);
	 	$this->setAttribute("total_fonc_ae_co", $totalFonctAE);
	 	$this->setAttribute("total_fonc_cp_co", $totalFonctCP);
	 	$this->setAttribute("total_invest_ae_co", $totalInvestAE);
	 	$this->setAttribute("total_invest_cp_co", $totalInvestCP);
	 	$this->setAttribute("total_ae_co", $totalFonctAE + $totalInvestAE + $totalPersonnel);
	 	$this->setAttribute("total_cp_co", $totalFonctCP + $totalInvestCP + $totalPersonnel);
	 }
	 
	 /**
	  * @param Copilote_Library_Demande $demande
	  */
	 public function computePrevision(Copilote_Library_Demande $demande)
	 {
	 	$personnel = $demande->GetMontantPersonnel($this->_convention);
	 	$fonctionnementAE = $demande->GetAutreMontant($this->_convention, "fonctionnement", "ae");
	 	$fonctionnementCP = $demande->GetAutreMontant($this->_convention, "fonctionnement", "cp");
	 	$investissementAE = $demande->GetAutreMontant($this->_convention, "investissement", "ae");
	 	$investissementCP = $demande->GetAutreMontant($this->_convention, "investissement", "cp");
	 	$this->setAttribute("cout_personnel_prev", $personnel);
	 	$this->setAttribute("cout_fonct_ae_prev", $fonctionnementAE);
	 	$this->setAttribute("cout_fonct_cp_prev", $fonctionnementCP);
	 	$this->setAttribute("cout_invest_ae_prev", $investissementAE);
	 	$this->setAttribute("cout_invest_cp_prev", $investissementCP);
	 	$this->commit();
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
	 }
}