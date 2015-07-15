<?php

class Copilote_Library_Ub
{
	/**
	 * @var string
	 */
	protected $_tableName = "cplt_ub_data" ;
	
	/**
	 * @var integer
	 */
	protected $_id = 0 ;
	
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
}