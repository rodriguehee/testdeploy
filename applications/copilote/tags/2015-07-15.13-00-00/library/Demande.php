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
	 * @var Copilote_Library_Ub
	 */
	protected $_ub ;
	
	/**
	 * @param integer $idData
	 */
	public function __construct( $id )
	{
		$this->_id = (int) $id ;
		$this->_setAttributes()->_setDepenses() ;
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->_id ;
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
	 * @return Copilote_Library_Depense
	 * @param string $key
	 * @param string $value
	 */
	public function setAttribute( $key, $value )
	{
		$this->_attributes[$key] = $value ;
		return $this ;
	}
	
	/**
	 * @return string
	 * @param string $key
	 */
	public function getAttribute( $key )
	{
		if( array_key_exists( $key, $this->_attributes ) ) {
			return $this->_attributes[$key] ;
		}
		
		throw new DomainException( sprintf( "Attribut '%s' introuvable", $key ) ) ;
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
		return $this ;
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
			$this->_ub = new Copilote_Library_Ub( $id ) ;
		}
		return $this->_ub ;
	}
}