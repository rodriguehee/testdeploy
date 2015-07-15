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
			$this->_depenses[] = new Copilote_Library_Depense( $idDepense ) ;
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
}