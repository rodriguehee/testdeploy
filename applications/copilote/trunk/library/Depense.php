<?php

/**
 *  AE : autorisation d’engagement
 *  CP : crédit de paiement
 */
class Copilote_Library_Depense extends Copilote_Library_Record
{
	/**
	 * @var Copilote_Library_Demande
	 */
	protected $_demande ;
	
	/**
	 * @return Copilote_Library_Demande
	 */
	public function getDemande()
	{
		if ( is_null( $this->_demande ) ) {
			$this->_demande = new Copilote_Library_Demande( "cplt_dmnd_data", $this->getAttribute( "id_demande" ) ) ;
		}
		return $this->_demande ;
	}
	
	/**
	 * @return Copilote_Library_Depense
	 */
	public function computeMontant()
	{
		$montant = (float) $this->getAttribute( "montant_scsp" ) + (float) $this->getAttribute( "montant_convention" ) ;
		return $this->setAttribute( "montant", $montant ) ;
	}
	
	/**
	 * @see fichier docx de Nadia relatif au découpage des montants
	 * @return Copilote_Library_Depense
	 */
	public function computeMontantSCSP()
	{
		$montant = $this->getMontant( "personnel", "rh", "ae", "scsp" ) +
			$this->getMontant( "fonctionnement", "dt", "ae", "scsp" ) +
			$this->getMontant( "fonctionnement", "sta", "ae", "scsp" ) +
			$this->getMontant( "fonctionnement", "oc", "ae", "scsp" ) +
			$this->getMontant( "fonctionnement", "pe", "ae", "scsp" ) +
			$this->getMontant( "investissement", "pe", "ae", "scsp" ) +
			$this->getMontant( "fonctionnement", "dmp", "ae", "scsp" ) +
			$this->getMontant( "investissement", "dmp", "ae", "scsp" ) 
		  /*$this->getMontant( "fonctionnement", "pe", "cp", "scsp" ) +
			$this->getMontant( "investissement", "pe", "cp", "scsp" ) +
			$this->getMontant( "fonctionnement", "dmp", "cp", "scsp" ) +
			$this->getMontant( "investissement", "dmp", "cp", "scsp" )*/ ;
		return $this->setAttribute( "montant_scsp", $montant ) ;
	}
	
	/**
	 * @see fichier docx de Nadia relatif au découpage des montants
	 * @return Copilote_Library_Depense
	 */
	public function computeMontantConvention()
	{
		$montant = $this->getMontant( "personnel", "rh", "ae", "convention" ) +
			$this->getMontant( "fonctionnement", "dt", "ae", "convention" ) +
			$this->getMontant( "fonctionnement", "sta", "ae", "convention" ) +
			$this->getMontant( "fonctionnement", "oc", "ae", "convention" ) +
			$this->getMontant( "fonctionnement", "pe", "ae", "convention" ) +
			$this->getMontant( "investissement", "pe", "ae", "convention" ) +
			$this->getMontant( "fonctionnement", "dmp", "ae", "convention" ) +
			$this->getMontant( "investissement", "dmp", "ae", "convention" ) 
		  /*$this->getMontant( "fonctionnement", "pe", "cp", "convention" ) +
			$this->getMontant( "investissement", "pe", "cp", "convention" ) +
			$this->getMontant( "fonctionnement", "dmp", "cp", "convention" ) +
			$this->getMontant( "investissement", "dmp", "cp", "convention" ) */;
		return $this->setAttribute( "montant_convention", $montant ) ;
	}
	
	/**
	 * @return float
	 * @param string $nature "personnel" | "fonctionnement" | "investissement"
	 * @param string $typeDepense "rh" | "dt" | "sta" | "oc" | "pe" | "dmp" 
	 * @param string $aspect "ae" | "cp"
	 * @param string $imputation "scsp" | "convention"
	 */
	public function getMontant( $nature, $typeDepense, $aspect, $imputation )
	{
		$nature = trim( strtolower( $nature ) ) ;
		$imputation = trim( strtolower( $imputation ) ) ;
		$typeDepense = trim( strtolower( $typeDepense ) ) ;
		$aspect = trim( strtolower( $aspect ) ) ;
		
		if ( "personnel" == $nature && "scsp" == $imputation ) {
			return $this->_getMontantPersonnelScsp() ;
		}
		if( "personnel" == $nature && "convention" == $imputation ) {
			return $this->_getMontantPersonnelConvention() ;
		}
		
		if ( "fonctionnement" == $nature && "dt" == $typeDepense && "scsp" == $imputation ) {
			return $this->_getMontantFonctionnementDtScsp() ;
		}
		if ( "fonctionnement" == $nature && "dt" == $typeDepense && "convention" == $imputation ) {
			return $this->_getMontantFonctionnementDtConvention() ;
		}
		
		if ( "fonctionnement" == $nature && "sta" == $typeDepense && "scsp" == $imputation ) {
			return $this->_getMontantFonctionnementStaScsp() ;
		}
		if ( "fonctionnement" == $nature && "sta" == $typeDepense && "convention" == $imputation ) {
			return $this->_getMontantFonctionnementStaConvention() ;
		}
		
		if ( "fonctionnement" == $nature && "oc" == $typeDepense && "scsp" == $imputation ) {
			return $this->_getMontantOc( 28 ) ;
		}
		if ( "fonctionnement" == $nature && "oc" == $typeDepense && "convention" == $imputation ) {
			return $this->_getMontantOc( 29 ) ;
		}
		
		if ( "fonctionnement" == $nature && "pe" == $typeDepense && "scsp" == $imputation && "ae" == $aspect) {
			return $this->_getMontantPe( "cout", 11, 28 ) ;
		}
		if ( "fonctionnement" == $nature && "pe" == $typeDepense && "scsp" == $imputation && "cp" == $aspect) {
			return $this->_getMontantPe( "cout_cp", 11, 28 ) ;
		}
		if ( "fonctionnement" == $nature && "pe" == $typeDepense && "convention" == $imputation && "ae" == $aspect) {
			return $this->_getMontantPe( "cout", 11, 29 ) ;
		}
		if ( "fonctionnement" == $nature && "pe" == $typeDepense && "convention" == $imputation && "cp" == $aspect) {
			return $this->_getMontantPe( "cout_cp", 11, 29 ) ;
		}
		
		if ( "fonctionnement" == $nature && "dmp" == $typeDepense && "scsp" == $imputation && "ae" == $aspect) {
			return $this->_getMontantDmp( "cout", 11, 28 ) ;
		}
		if ( "fonctionnement" == $nature && "dmp" == $typeDepense && "scsp" == $imputation && "cp" == $aspect) {
			return $this->_getMontantDmp( "cout_cp", 11, 28 ) ;
		}
		if ( "fonctionnement" == $nature && "dmp" == $typeDepense && "convention" == $imputation && "ae" == $aspect) {
			return $this->_getMontantDmp( "cout", 11, 29 ) ;
		}
		if ( "fonctionnement" == $nature && "dmp" == $typeDepense && "convention" == $imputation && "cp" == $aspect) {
			return $this->_getMontantDmp( "cout_cp", 11, 29 ) ;
		}
		
		if ( "investissement" == $nature && "pe" == $typeDepense && "scsp" == $imputation && "ae" == $aspect) {
			return $this->_getMontantPe( "cout", 10, 28 ) ;
		}
		if ( "investissement" == $nature && "pe" == $typeDepense && "scsp" == $imputation && "cp" == $aspect) {
			return $this->_getMontantPe( "cout_cp", 10, 28 ) ;
		}
		if ( "investissement" == $nature && "pe" == $typeDepense && "convention" == $imputation && "ae" == $aspect) {
			return $this->_getMontantPe( "cout", 10, 29 ) ;
		}
		if ( "investissement" == $nature && "pe" == $typeDepense && "convention" == $imputation && "cp" == $aspect) {
			return $this->_getMontantPe( "cout_cp", 10, 29 ) ;
		}
		
		if ( "investissement" == $nature && "dmp" == $typeDepense && "scsp" == $imputation && "ae" == $aspect) {
			return $this->_getMontantDmp( "cout", 10, 28 ) ;
		}
		if ( "investissement" == $nature && "dmp" == $typeDepense && "scsp" == $imputation && "cp" == $aspect) {
			return $this->_getMontantDmp( "cout_cp", 10, 28 ) ;
		}
		if ( "investissement" == $nature && "dmp" == $typeDepense && "convention" == $imputation && "ae" == $aspect) {
			return $this->_getMontantDmp( "cout", 10, 29 ) ;
		}
		if ( "investissement" == $nature && "dmp" == $typeDepense && "convention" == $imputation && "cp" == $aspect) {
			return $this->_getMontantDmp( "cout_cp", 10, 29 ) ;
		}
		
		error_log( "Calcul du montant non produit pour les arguments [%s]", implode( ", ", func_get_args() ) ) ;
		return 0.0 ;
	}
	
	/**
	 * @return float
	 */
	protected function _getMontantPersonnelScsp()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		return (float) $db->fetchOne( "SELECT SUM( rh_impscsptot ) FROM cplt_rh_data WHERE id_depense = ?", $this->_id ) ;
	}
	
	/**
	 * @return float
	 */
	protected function _getMontantPersonnelConvention()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		return (float) $db->fetchOne( "SELECT SUM( COALESCE(rh_impc1tot, 0) + COALESCE(rh_impc2tot, 0) ) FROM cplt_rh_data WHERE id_depense = ?", $this->_id ) ;
	}
	
	/**
	 * @return float
	 */
	protected function _getMontantFonctionnementDtScsp()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		return (float) $db->fetchOne( "SELECT SUM( dt_couttot ) FROM cplt_dt_data WHERE id_depense = ? AND dt_numconv IS NULL", $this->_id ) ;
	}
	
	/**
	 * @return float
	 */
	protected function _getMontantFonctionnementDtConvention()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		return (float) $db->fetchOne( "SELECT SUM( dt_couttot ) FROM cplt_dt_data WHERE id_depense = ? AND dt_numconv > 0", $this->_id ) ;
	}
	
	/**
	 * @return float
	 */
	protected function _getMontantFonctionnementStaScsp()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		return (float) $db->fetchOne( "SELECT SUM( sta_impscsptot ) FROM cplt_sta_data WHERE id_depense = ?", $this->_id ) ;
	}
	
	/**
	 * @return float
	 */
	protected function _getMontantFonctionnementStaConvention()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		return (float) $db->fetchOne( "SELECT SUM( COALESCE(sta_impc1tot, 0) + COALESCE(sta_impc2tot, 0) ) FROM cplt_sta_data WHERE id_depense = ?", $this->_id ) ;
	}
	
	/**
	 * @return float
	 */
	protected function _getMontantOc( $typeImputation )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		return (float) $db->fetchOne( "SELECT SUM( v.cout ) FROM cplt_oc_data oc JOIN cplt_vntl_data v ON oc.id_data = v.id_oc WHERE v.typeinput = ? AND id_depense = ?", array( $typeImputation, $this->_id ) ) ;
	}
	
	/**
	 * @param string $field "cout" | "cout_cp"
	 * @param integer $invest fonctionnemenent => 11 | investissement => 10
	 * @param integer $typeImputation SCSP => 28 | Convention => 29
	 * @return float
	*/
	protected function _getMontantPe( $field, $invest, $typeImputation )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$query = sprintf( "SELECT SUM(v.%s) FROM cplt_pe_data pe JOIN cplt_vntl_data v ON pe.id_data = v.id_pe WHERE v.typeinput = ? AND v.invest = ? AND id_depense = ?", $field ) ;
		return (float) $db->fetchOne( $query, array( $typeImputation, $invest, $this->_id ) ) ;
	}
	
	/**
	 * @param string $field "cout" | "cout_cp"
	 * @param integer $invest fonctionnemenent => 11 | investissement => 10
	 * @param integer $typeImputation SCSP => 28 | Convention => 29
	 * @return float
	 */
	protected function _getMontantDmp( $field, $invest, $typeImputation )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$query = sprintf( "SELECT SUM(v.%s) FROM cplt_dmp_data dmp JOIN cplt_vntl_data v ON dmp.id_data = v.id_dmp WHERE v.typeinput = ? AND v.invest = ? AND id_depense = ?", $field ) ;
		return (float) $db->fetchOne( $query, array( $typeImputation, $invest, $this->_id ) ) ;
	}
}