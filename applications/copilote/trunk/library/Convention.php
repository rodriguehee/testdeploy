<?php

class Copilote_Library_Convention extends Copilote_Library_Record
{
	/**
	 * @var Copilote_Library_Programmation[]
	 */
	protected $_programmations = array() ;
	
	/**
	 * @var Copilote_Library_Record[]
	 */
	protected $_suivis = array();
	
	/**
	 * @param string $tableName
	 * @param integer $idData
	 */
	public function __construct( $tableName, $idData )
	{
		parent::__construct( $tableName, $idData ) ;
		$this->_setProgrammations() ;
		$this->_setSuivisBudgetaires();
		
		/* generation des programmations */

		//if( $this->getAttribute( "status_prog" ) == 529 ) {

		  $project = Core_Library_Account::GetInstance()->GetCurrentProject();
		  $status = $project->DicoManager()->id2Code("actif_inactif", $this->getAttribute("status_prog"));

		  if( $status == 1 ) {

			$debut = 0 ;
			$fin = 0 ;
			
			$dateDebut = $this->getAttribute( "date_deb" ) ;
			if( ! empty( $dateDebut ) ) {
				$o = new DateTime( $dateDebut ) ;
				$debut = (int) $o->format("Y") ;
				$debut = max($debut, self::getReference()) ;
			}
			
			$dateFin = $this->getAttribute( "date_fin" ) ;
			if( ! empty( $dateFin ) ) {
				$o = new DateTime( $dateFin ) ;
				$fin = (int) $o->format("Y") ;
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
	 * @return integer
	 * @throws Core_Library_Exception
	 */
	public static function getReference()
	{
		$reference = Core_Library_Options::get( 'conv.pluri.ref' ) ;
		if ( $reference  === false ) {
			$reference = date("Y") ;
		}
		return (int) $reference ;
	}
	
	/**
	 * @return Copilote_Library_Programmation[]
	 */
	public function getProgrammations()
	{
		return $this->_programmations ;
	}
	
	/**
	 * @return boolean
	 */
	public function isOutOfBounds()
	{
		$aspects = array("ae", "cp");
		
		foreach ($aspects as $aspect) {
			$dispo = "recap_dispo_" . $aspect;
			$montant = "montant_" . $aspect;
			if ($this->getAttribute($dispo) > ($this->getAttribute($montant)/200)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	public function computes()
	{
		$this->computeProgrammations();
		
		foreach (array("ae", "cp") as $aspect) {
			$this->computeAnteriority($aspect);
			$this->computeFraisGestion($aspect);
			$this->computeCreditOuvert($aspect);
			$this->computeCreditConsomme($aspect);
			$this->computeProvisionChomage($aspect);
			$this->computeCreditDisponible($aspect);
		}
		
		$this->commit();
	}
	
	/**
	 * @param Copilote_Library_Demande $demande
	 */
	public function computeCurrentPrevision(Copilote_Library_Demande $demande)
	{
		foreach ($this->_programmations as $programmation) {
			if ($programmation->getAttribute("annee_conv") == self::getReference()) {
				$programmation->computePrevision($demande);
			}
		}
	}
	
	/**
	 * @return Copilote_Library_Convention
	 * @param Copilote_Library_Programmation $programmation
	 */
	public function attachProgrammation( Copilote_Library_Programmation $programmation )
	{
		$key = $programmation->getAttribute( "annee_conv" ) ;
		$programmation->setConvention($this);
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
			$programmation = new Copilote_Library_Programmation( "cplt_programmation_data", $idProg );
			$programmation->setConvention($this);
			$annee = $programmation->getAttribute( "annee_conv" ) ;
			$this->_programmations[$annee] = $programmation ; 
		}
		return $this ;
	}	
	
	/**
	 * @return Copilote_Library_Convention
	 */
	protected function _setSuivisBudgetaires()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$sql = 'SELECT id_data FROM cplt_subc_data WHERE id_conv = ?' ;
		$stmt = $db->query( $sql, $this->_id ) ;
		while( $id = $stmt->fetchColumn( 0 ) ) {
			$record = new Copilote_Library_Record( "cplt_subc_data", $id );
			$key = $record->getAttribute( "rb_num" ) ;
			$this->_suivis[$key] = $record;
		}
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Record[]
	 */
	public function getSuivisBudgetaires()
	{
		return $this->_suivis;
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
	
	/**
	 * @return Copilote_Library_Demande | null
	 */
	public function GetDemandeValidee( $annee, $statutDemande )
	{
		$query = sprintf( "
			SELECT dmnd.id_data
			FROM cplt_sv_data sv
			JOIN cplt_dmnd_data dmnd
			  ON sv.id_data = dmnd.id_suivi
			JOIN cplt_dico_data dico
			  ON dmnd.etat = dico.id_data
			WHERE dmnd.etat = '%s'
			  AND sv.annee = %d
			  AND sv.id_ub = %d
		", 	$statutDemande,
			$annee,
			$this->getAttribute( "id_ub" )
		) ;
		$project = Core_Library_Account::GetInstance()->GetCurrentProject() ;
		$row = $project->Db()->fetchRow( $query ) ;
	
		if ( empty( $row ) ) {
			return null ;
		}
		return new Copilote_Library_Demande( "cplt_dmnd_data", $row['id_data'] ) ;
	}
	
	/**
	 * @return string
	 */
	public function getModeProgrammation()
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
	 * @return void
	 * @param string $aspect
	 */
	public function computeFraisGestion($aspect)
	{
		$attribute = "recap_fdg_" . $aspect;
		
		if ($this->hasComputedFDG()) {
			$pourcentage = (float) $this->getAttribute("pourcentage_fdg");
			$montant = (float) $this->getAttribute("montant_" . $aspect);
			$this->setAttribute($attribute, ($pourcentage * $montant) / 100);
		}
	}
	
	/**
	 * @return void
	 * @param string $aspect
	 */
	public function computeProvisionChomage($aspect)
	{
		$attribute = "recap_ape_" . $aspect;
		
		if ($this->getAttribute("formule") == "ANR") {
			$this->setAttribute($attribute, 0.0);
		} 
		elseif ($this->hasComputedAPE()) {
			$this->setAttribute($attribute, 0.1 * $this->getSommePersonnel());
		}
	}
	
	/**
	 * @return float
	 */
	public function getSommePersonnel()
	{
		$somme = (float) $this->getAttribute("cout_personel_ant");
		foreach ($this->_programmations as $programmation) {
			$somme += (float) $programmation->getAttribute("cout_personnel_total");
		}
		return $somme;
	}
	
	/**
	 * @return void
	 */
	public function computeCreditOuvert($aspect)
	{
		$montant = (float) $this->getAttribute("montant_" . $aspect);
		$fraisGestion = (float) $this->getAttribute("recap_fdg_" . $aspect);
		$attribute = sprintf("recap_credouv_%s", $aspect);
		$this->setAttribute($attribute, $montant - $fraisGestion);
	}
	
	/**
	 * @param string $aspect
	 * @return void
	 */
	public function computeCreditConsomme($aspect)
	{
		$attribute = sprintf("recap_conso_%s", $aspect);
		$credits = (float) $this->getAttribute(sprintf("total_%s_ant", $aspect));
		foreach ($this->_programmations as $programmation) {
			if ($programmation->isElapsed()) {
				$credits += (float) $programmation->getAttribute(sprintf("total_%s", $aspect));
			}
		}
		$this->setAttribute($attribute, $credits);
	}
	
	/**
	 * @param string $aspect
	 * @return void
	 */
	public function computeCreditDisponible($aspect)
	{
		$credits = (float) $this->getAttribute("recap_credouv_" . $aspect);
		$conso = (float) $this->getAttribute("recap_conso_" . $aspect);
		$chomage = (float) $this->getAttribute("recap_ape_" . $aspect);
		$dispo = $credits - $conso - $chomage;
		foreach ($this->_programmations as $programmation) {
			if (! $programmation->isElapsed()) {
				$total = $programmation->getAttribute(sprintf("total_%s_co", $aspect));
				$dispo -= $total;
			}
		}
		$attribute = sprintf("recap_dispo_%s", $aspect);
		$this->setAttribute($attribute, $dispo);
	}
	
	/**
	 * @return void
	 * @param string $aspect
	 */
	public function computeAnteriority($aspect)
	{
		$anteriority  = (float) $this->getAttribute("cout_personel_ant");
		$anteriority += (float) $this->getAttribute(sprintf("cout_fonct_%s_ant", $aspect));
		$anteriority += (float) $this->getAttribute(sprintf("cout_invest_%s_ant", $aspect));
		$attribute = sprintf("total_%s_ant", $aspect);
		$this->setAttribute($attribute, $anteriority);
	}
	
	/**
	 * @return boolean
	 */
	public function hasComputedFDG()
	{
		$project = Core_Library_Account::GetInstance()->GetCurrentProject();
		$dicoLg01 = $project->DicoManager()->GetDico("lg01");
		return ($this->getAttribute("fdg_statut") == $dicoLg01->code2id("1"));
	}
	
	/**
	 * @return boolean
	 */
	public function hasComputedAPE()
	{
		$project = Core_Library_Account::GetInstance()->GetCurrentProject();
		$dicoLg01 = $project->DicoManager()->GetDico("lg01");
		return ($this->getAttribute("ape_statut") == $dicoLg01->code2id("1"));
	}
	
	/**
	 * 
	 */
	public function computeProgrammations()
	{
		foreach ($this->_programmations as $programmation) {
			assert($programmation instanceof Copilote_Library_Programmation);
			$programmation->computeTotal("ae");
			$programmation->computeTotal("cp");
			$programmation->computePrevisions();
			$programmation->commit();
		}
	}
	
	/**
	 * 
	 */
	public function computeFromDemande()
	{
		$annee = self::getReference();
		if(! array_key_exists($annee, $this->_programmations)) {
			return;
		}
		$programmation = $this->_programmations[$annee];
		$demande = $this->GetDemande($annee);
		$demandeValidee = $programmation->getDemandeValidee($demande);
		if ($demandeValidee instanceof Copilote_Library_Demande) {
			$programmation->setAttribute("cout_personnel_prev", $demandeValidee->GetMontantPersonnel($this));
			$programmation->setAttribute("cout_fonct_ae_prev", $demandeValidee->GetAutreMontant($this, "fonctionnement", "ae"));
			$programmation->setAttribute("cout_fonct_cp_prev", $demandeValidee->GetAutreMontant($this, "investissement", "cp"));
			$programmation->setAttribute("cout_invest_ae_prev", $demandeValidee->GetAutreMontant($this, "fonctionnement", "ae"));
			$programmation->setAttribute("cout_invest_cp_prev", $demandeValidee->GetAutreMontant($this, "investissement", "cp"));
		}
	}
}