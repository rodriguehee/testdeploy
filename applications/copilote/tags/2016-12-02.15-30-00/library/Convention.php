<?php

class Copilote_Library_Convention extends Copilote_Library_Record
{
	/**
	 * @var array
	 */
	protected $_programmations = array() ;
	
	/**
	 * @param string $tableName
	 * @param integer $idData
	 */
	public function __construct( $tableName, $idData )
	{
		parent::__construct( $tableName, $idData ) ;
		$this->_setProgrammations() ;
		
		/* generation des programmations */
		if( $this->getAttribute( "status_prog" ) == 529 ) {
			$debut = 0 ;
			$fin = 0 ;
			
			$dateDebut = $this->getAttribute( "date_deb" ) ;
			if( ! empty( $dateDebut ) ) {
				$o = new DateTime( $dateDebut ) ;
				$debut = (int) $o->format("Y") ;
				$debut = max($debut, $this->getReference()) ;
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
	public function getReference()
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
		$project = Core_Library_Account::GetInstance()->GetCurrentProject() ;
		$dico = $project->DicoManager()->GetDico("lg01");
		
		if ($dico->id2code($this->getAttribute("fdg_statut")) == "1") {
			$commit = true;
			$pourcentage = (float) $this->getAttribute("pourcentage_fdg");
			$montant = (float) $this->getAttribute("montant_" . $aspect);
			$attribute = "recap_fdg_" . $aspect;
			$this->setAttribute($attribute, ($pourcentage * $montant) / 100);
			$this->commit();
		}
	}
	
	/**
	 * @return void
	 * @param float $cumul
	 * @param string $aspect
	 */
	public function computeProvisionChomage($cumul, $aspect)
	{
		$project = Core_Library_Account::GetInstance()->GetCurrentProject() ;
		$dico = $project->DicoManager()->GetDico("lg01");
		
		$provision = 0.0;
		
		if ($dico->id2code($this->getAttribute("ape_statut")) == "1") {
		 	$pourcentage = 0.1;
		 	$provision = $pourcentage * (float) $cumul;
		}
		
		$attribute = sprintf("recap_ape_%s", $aspect);
		$this->setAttribute($attribute, $provision);
		$this->commit();
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
		$this->commit();
	}
	
	/**
	 * @param string $aspect
	 * @param float $credits
	 * @return void
	 */
	public function setCreditConsomme($aspect, $credits)
	{
		$attribute = sprintf("recap_conso_%s", $aspect);
		$this->setAttribute($attribute, $credits);
		$this->commit();
	}
	
	/**
	 * @param string $aspect
	 * @param float $prevision
	 * @return void
	 */
	public function setCreditDisponible($aspect, $prevision)
	{
		$prevision = (float) $prevision;
		$credits = (float) $this->getAttribute("recap_credouv_" . $aspect);
		$conso = (float) $this->getAttribute("recap_conso_" . $aspect);
		$chomage = (float) $this->getAttribute("recap_ape_" . $aspect);
		$dispo = $credits - $prevision - $conso - $chomage;
		$attribute = sprintf("recap_dispo_%s", $aspect);
		$this->setAttribute($attribute, $dispo);
		$this->commit();
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
		$this->commit();
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
}