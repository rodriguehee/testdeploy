<?php

class Copilote_Library_Demande 
{
	/**
	 * @var string
	 */
	protected $_tableName = "cplt_dmnd_data" ;
	
	/**
	 * @var integer
	 */
	protected $_id = 0 ;
	
	/**
	 * @var array
	 */
	protected $_depenses = array() ;
	
	/**
	 * @var array
	 */
	protected $_attributes = array() ;
	
	/**
	 * @param integer $idData
	 */
	public function __construct( $id )
	{
		$this->_id = (int) $id ;
		$this->_setAttributes() ;
	}
	
	/**
	 * @return Copilote_Library_Demande
	 */
	protected function _setAttributes()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$query = sprintf( "SELECT * FROM %s WHERE id_data = %d", $this->_tableName, $this->_id ) ;
		$this->_attributes = $db->fetchRow( $query ) ;
		if ( ! is_array( $this->_attributes ) ) {
			throw new LogicException( sprintf( "Record [%d] introuvable pour la table '%s'", $this->_id, $this->_tableName ) ) ;
		}
		unset( $this->_attributes['id_data'] ) ;
		return $this ;
	}
		
	/**
	 * @return integer
	 */
	public function getStatut()
	{
		return $this->_attributes['etat'] ;
	}
	
	/**
	 * @return Copilote_Library_Demande
	 * @param integer $statut
	 */
	public function setStatut( $statut )
	{
		$this->_attributes['etat'] = $statut ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Demande
	 */
	public function commit()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$sets = array() ;
		foreach( $this->_attributes as $column => $value ) {
			if( ! is_null( $value ) ) {
				$sets[] = sprintf( "%s = %s", $column, $db->quote( $value ) ) ;
			}
			else {
				$sets[] = sprintf( "%s = null", $column ) ;
			}
		}
		$query = sprintf( "UPDATE %s SET %s WHERE id_data = %d", $this->_tableName, implode( ", ", $sets ), $this->_id ) ;
		$db->query( $query ) ;
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
	 * @return float
	 */
	public function getMontant()
	{
		$total = (float) 0 ;
		foreach( $this->_depenses as $depense ) {
			$depense instanceof Copilote_Library_Depense ;
			$total += (float) $depense->getMontant() ;
		}
		return $total ;
	}
	
	/**
	 * @return Copilote_Library_Demande
	 */
	public function synchronize()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$db->update( 
			$this->_tableName, 
			array( 
				"montant" => $this->getMontant() 
			), 
			"id_data = " . $this->_id 
		) ;
	}
}