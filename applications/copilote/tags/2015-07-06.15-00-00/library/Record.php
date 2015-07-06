<?php

class Copilote_Library_Record
{
	/**
	 * @var string
	 */
	protected $_tableName = "" ;
	
	/**
	 * @var integer
	 */
	protected $_id = 0 ;
	
	/**
	 * @var array
	 */
	protected $_attributes = array() ;
	
	/**
	 * @var array
	 */
	protected $_children = array() ;
	
	/**
	 * @var string
	 */
	protected $_foreignKey = "" ;
	
	/**
	 * @var  Copilote_Library_Schema 
	 */
	protected $_schema ;
	
	/**
	 * @param string $idData
	 * @param integer $idData
	 */
	public function __construct( Copilote_Library_Schema $schema, $tableName, $id )
	{
		$this->_schema = $schema ;
		$this->_tableName = (string) $tableName ;
		$this->_id = (int) $id ;
		$this->_setAttributes() ;
		$this->_setChildren() ;
	}
	
	/**
	 * @return Copilote_Library_Record
	 * @param string $key
	 * @param string $value
	 */
	public function attachAttribute( $key, $value )
	{
		$this->_attributes[$key] = (string) $value ;
		return $this ;
	}
	
	/**
	 * @param string $key
	 * @return Copilote_Library_Record
	 */
	public function detachAttribute( $key )
	{
		if( array_key_exists( $key, $this->_attributes ) ) {
			unset( $this->_attributes[$key] ) ;
		}
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Record
	 */
	protected function _setAttributes()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$query = sprintf( "SELECT * FROM %s WHERE id_data = %d", $this->_tableName, $this->_id ) ;
		$this->_attributes = $db->fetchRow( $query ) ;
		
		if ( ! is_array( $this->_attributes ) ) {
			throw new LogicException( sprintf( "Record [%d] introuvable pour la table '%s'", $this->_id, $this->_tableName ) ) ;
		}
		$this->detachAttribute( "id_data" ) ;
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Record
	 * @param string $columnName
	 */
	public function setForeignKey( $columnName )
	{
		$this->_foreignKey = (string) $columnName ;
		return $this ;
	}
	
	/**
	 * @return string
	 */
	public function getForeignKey()
	{
		return $this->_foreignKey ;
	}
	
	/**
	 * @return Copilote_Library_Record
	 */
	protected function _setChildren()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		
		$constraints = $this->_schema->getConstraints( $this->_tableName ) ;
		foreach( $constraints as $tableName => $foreignKeyColumn ) {
			$query = sprintf( "SELECT id_data FROM %s WHERE %s = %d ", $tableName, $foreignKeyColumn, $this->_id ) ;
			$ids = $db->fetchCol( $query ) ;
			foreach( $ids as $id ) {
				$child = new Copilote_Library_Record( $this->_schema, $tableName, $id ) ;
				$child->setForeignKey( $foreignKeyColumn ) ;
				$this->_children[] = $child ;
			}
		}
		
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Record
	 */
	public function duplicate()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		
		$sets = array() ;
		foreach( $this->_attributes as $column => $value ) {
			if( "" !== $value && ! is_null( $value ) ) {
				$sets[] = sprintf( "%s = %s", $column, $db->quote( $value ) ) ;
			}
		}
		
		$query = sprintf( "INSERT INTO %s SET %s ", $this->_tableName, implode( ", ", $sets ) ) ;
		error_log( $query ) ;
		$db->query( $query ) ;
		$id = $db->lastInsertId() ;
		
		foreach( $this->_children as $child ) {
			$child->attachAttribute( $child->getForeignKey(), $id ) ;
			$child->duplicate() ;
		}
		
		return $this ;
	}
	
	/**
	 * @return Copilote_Library_Record
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
}