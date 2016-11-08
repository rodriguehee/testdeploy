<?php

class Copilote_Library_Demande extends Copilote_Library_Record
{
	/**
	 * @var array
	 */
	protected $_depenses = array() ;
	
	/**
	 * @var Copilote_Library_Record
	 */
	protected $_ub ;
	
	/**
	 * @param string $tableName
	 * @param integer $idData
	 */
	public function __construct( $tableName, $id )
	{
		parent::__construct( $tableName, $id ) ;
		$this->_setDepenses() ;
	}
	
	/**
	 * @return Copilote_Library_Demande
	 */
	protected function _setDepenses()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$sql = 'SELECT id_data FROM cplt_dpns_data WHERE id_demande = ?' ;
		$stmt = $db->query( $sql, $this->_id ) ;
		while( $idDepense = $stmt->fetchColumn( 0 ) ) {
			$this->_depenses[] = new Copilote_Library_Depense( "cplt_dpns_data", $idDepense ) ;
		}
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Demande
	 * @param string $field
	 */
	public function compute( $field )
	{
		$montant = (float) 0 ;
		foreach( $this->_depenses as $depense ) {
			$depense instanceof Copilote_Library_Depense ;
			$montant += (float) $depense->getAttribute( $field ) ;
		}
		$this->setAttribute( $field, $montant ) ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Ub
	 */
	public function getUB()
	{
		if ( empty( $this->_ub ) ) {
			$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
			$sql = 'SELECT ub.id_data FROM cplt_ub_data ub JOIN cplt_sv_data sv ON sv.id_ub = ub.id_data WHERE sv.id_data = ?' ;
			$id = (int) $db->fetchOne( $sql, $this->getAttribute( "id_suivi" ) ) ;
			$this->_ub = new Copilote_Library_Record( "cplt_ub_data", $id ) ;
		}
		return $this->_ub ;
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
		$montant = 0.0 ;
		
		foreach( $this->_depenses as $depense ) {
			assert( $depense instanceof Copilote_Library_Depense ) ;
			$montant += $depense->getMontant( $nature, $typeDepense, $aspect, $imputation ) ;
		}
		
		return $montant ;
	}
	
	/**
	 * @return float
	 * @param Copilote_Library_Convention $convention
	 */
	public function GetMontantPersonnel( Copilote_Library_Convention $convention )
	{
		$fMontant = 0.0 ;
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		
		foreach( range( 1, 2 ) as $index ) {
			$query = sprintf( "
				SELECT SUM(rh_impc%dtot) 
				FROM cplt_rh_data rh
				JOIN cplt_dpns_data dp ON rh.id_depense = dp.id_data
				WHERE dp.id_demande = %d 
				  AND rh.rh_impc%d = %d
			", 	$index, 
				$this->getId(),
				$index,
				$convention->getId()
			) ;
			$row = $db->fetchRow( $query ) ;
			if( ! empty( $row ) ) {
				$fMontant += (float) current( $row ) ;
			}
		}
		
		return $fMontant ;
	}
	
	/**
	 * @return float
	 * @param Copilote_Library_Convention $convention
	 * @param string $nature "fonctionnement" | "investissement"
	 * @param string $aspect "ae" | "cp"
	 */
	public function GetAutreMontant( Copilote_Library_Convention $convention, $nature, $aspect )
	{
		$fMontant = 0.0 ;
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		
		$invest = 10 ;
		if( "fonctionnement" == $nature ) {
			$invest = 11 ;
		}
		
		$field = "cout" ;
		if( "cp" == $aspect ) {
			$field = "cout_cp" ;
		}
		
		$prefixes = array( "pe", "dmp" ) ;
		if( $nature == "fonctionnement" ) {
			$prefixes[] = "oc" ;
		}
		
		// ventilations
		foreach( $prefixes as $prefix ) {
			$query = sprintf( "
				SELECT SUM(v.%s) 
				FROM cplt_dpns_data dp
				JOIN cplt_%s_data t ON dp.id_data = t.id_depense
				JOIN cplt_vntl_data v ON t.id_data = v.id_%s 
				WHERE v.invest = %d 
				  AND dp.id_demande = %d
				  AND v.convention = %d
			",  $field, 
				$prefix,
				$prefix,
				$invest,
				$this->getId(),
				$convention->getId() 
			) ;
			$row = $db->fetchRow( $query ) ;
			if( ! empty( $row ) ) {
				$fMontant += (float) current( $row ) ;
			}
		}
		
		if( $nature == "fonctionnement" ) {
			// sta 
			foreach( range( 1, 2 ) as $index ) {
				$query = sprintf( "
					SELECT SUM(sta.sta_impc%dtot)
					FROM cplt_dpns_data dp
					JOIN cplt_sta_data sta 
					  ON dp.id_data = sta.id_depense
					WHERE dp.id_demande = %d
					  AND sta.sta_impc%d = %d
				",  $index, 
					$this->getId(), 
					$index,
					$convention->getId()
				) ;
				$row = $db->fetchRow( $query ) ;
				if( ! empty( $row ) ) {
					$fMontant += (float) current( $row ) ;
				}
			}
			
			// dt 
			$query = sprintf( "
				SELECT SUM(dt.dt_couttot)
				FROM cplt_dpns_data dp
				JOIN cplt_dt_data dt 
				  ON dp.id_data = dt.id_depense
				WHERE dp.id_demande = %d
				  AND dt.dt_numconv = %d
			",  $this->getId(),
				$convention->getId()
			) ;
			$row = $db->fetchRow( $query ) ;
			if( ! empty( $row ) ) {
				$fMontant += (float) current( $row ) ;
			}
		}
		
		return $fMontant ;
	}
	
	/**
	 * @return Copilote_Library_Record
	 * @param Copilote_Library_Convention $convention
	 * @param integer $index
	 */
	public function GetSuiviBudgetaire( Copilote_Library_Convention $convention, $index )
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$sql = 'SELECT id_data FROM cplt_subc_data WHERE id_demande = ? AND id_conv = ? AND rb_num = ?' ;
		$id = (int) $db->fetchOne( $sql, array( $this->getId(), $convention->getId(), $index ) ) ;
		if( $id > 0 ) {
			return new Copilote_Library_Record( "cplt_subc_data", $id ) ;
		}
		return null ;
	}
}