<?php

class Copilote_Library_Depense
{
	/**
	 * @var integer
	 */
	protected $_id = 0 ;

	/**
	 * @param integer $idData
	 */
	public function __construct( $id )
	{
		$this->_id = (int) $id ;
	}

	/**
	 * @return float
	 */
	public function getMontant()
	{
		return  $this->getMontantRessourcesHumaines( true ) + 
				$this->getMontantRessourcesHumaines( false ) +
				$this->getMontantFonctionnement( true ) + 
				$this->getMontantFonctionnement( false ) +
				$this->getMontantInvestissements( true ) + 
				$this->getMontantInvestissements( false ) ;
	}
	
	/**
	 * @return float
	 * @param boolean $scsp
	 */
	public function getMontantRessourcesHumaines( $scsp )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$montant = 0 ;
		
		$fields = array() ;
		if ( $scsp ) {
			$fields[] = "rh_impscspmontant" ;
		}
		else {
			$fields[] = "rh_impc1montant" ;
			$fields[] = "rh_impc2montant" ;
		}
		
		foreach( $fields as $field ) {
			$query = sprintf( "SELECT SUM(%s) FROM cplt_rh_data WHERE id_depense = ?", $field ) ;
			$montant += (float) $db->fetchOne( $query, $this->_id ) ;			
		}
		
		return (float) $montant ;
	}
	
	/**
	 * Les dépenses de fonctionnement correspondent aux dépenses de:
-                       Organisations de colloque
-                       Déplacements temporaires
-						Stagaire
	 * @return float
	 * @param boolean $scsp
	 */
	public function getMontantFonctionnement( $scsp )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$montant = 0 ;
		
		$typeimput = 29 ;
		if ( $scsp ) {
			$typeimput = 28 ;
		}
		
		// depenses sur marché et prestations externes //
		$fields = array() ;
		$fkeys = array() ;
		
		$fields["pe"]  = "pe_invest" ;
		$fkeys["pe"] = "id_pe" ;
		
		$fields["dmp"] = "dmp_invest" ;
		$fkeys["dmp"] = "id_dmp" ;
		
		foreach( $fields as $prefix => $field ) {
			$query = sprintf( "
				SELECT SUM(v.cout)
				FROM cplt_%s_data d
				JOIN cplt_vntl_data v
				  ON d.id_data = v.%s
				WHERE d.id_depense = ?
				  AND typeinput = ?
				  AND %s = 11
			",	$prefix,
				$fkeys[$prefix],
				$field
			) ;
			$montant += (float) $db->fetchOne( $query, array( $this->_id, $typeimput ) ) ;
		}
		
		// organisation de colloques
		$query = "
			SELECT SUM(v.cout)
			FROM cplt_oc_data d
			JOIN cplt_vntl_data v
			  ON d.id_data = v.id_oc
			WHERE d.id_depense = ?
			  AND typeinput = ?
		" ;
		$montant += (float) $db->fetchOne( $query, array( $this->_id, $typeimput ) ) ;		
		
		// stagiaires 
		$fields = array() ;
		if ( $scsp ) {
			$fields[] = "sta_impscspmontant" ;
		}
		else {
			$fields[] = "sta_impc1montant" ;
			$fields[] = "sta_impc2montant" ;
		}
		foreach( $fields as $field ) {
			$query = sprintf( "SELECT SUM(%s) FROM cplt_sta_data WHERE id_depense = ?", $field ) ;
			$montant += (float) $db->fetchOne( $query, $this->_id ) ;
		}
		
		// deplacements temporaires //
		$condition = "dt_numconv > 0" ;
		if ( $scsp ) {
			$condition = "COALESCE(dt_numconv,0) = 0" ;
		}
		$query = sprintf( '
			SELECT SUM(dt_couttot)
			FROM cplt_dt_data
			WHERE id_depense = ?
			  AND %s
		', 	$condition
		) ;
		$result = $db->fetchOne( $query, $this->_id ) ;
		$montant += (float) $result ;
		
		return (float) $montant ;
	}
	
	/**
	 * @return float
	 * @param boolean $scsp
	 */
	public function getMontantInvestissements( $scsp )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$montant = 0 ;
		
		$typeimput = 29 ;
		if ( $scsp ) {
			$typeimput = 28 ;
		}
		
		$fields = array() ;
		$fkeys = array() ;
		
		$fields["pe"]  = "pe_invest" ;
		$fkeys["pe"] = "id_pe" ;
		
		$fields["dmp"] = "dmp_invest" ;
		$fkeys["dmp"] = "id_dmp" ;
		
		foreach( $fields as $prefix => $field ) {
			$query = sprintf( "
				SELECT SUM(v.cout) 
				FROM cplt_%s_data d 
				JOIN cplt_vntl_data v
				  ON d.id_data = v.%s
				WHERE d.id_depense = ?
				  AND typeinput = ?
				  AND %s = 10
			",	$prefix,
				$fkeys[$prefix],
				$field
			) ;
			$montant += (float) $db->fetchOne( $query, array( $this->_id, $typeimput ) ) ;
		}
		
		return (float) $montant ;
	}
}