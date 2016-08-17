<?php

class Copilote_Library_Schema
{
	/**
	 * @var string
	 */
	protected $_schema = "" ;
	
	/**
	 * @var array
	 */
	protected $_constraints = array() ;
	
	/**
	 * @param string $schema
	 */
	public function __construct( $schema )
	{
		$this->_schema = (string) $schema ;
	}
	
	/**
	 * @return Copilote_Library_Schema
	 */
	public function setConstraints()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		
		$query = sprintf( "SELECT constraint_name, column_name FROM information_schema.key_column_usage WHERE constraint_schema = '%s' ", $this->_schema ) ;
		$keyColumnUsage = $db->fetchPairs( $query ) ;
		
		$query = sprintf( "SELECT referenced_table_name, constraint_name, table_name FROM information_schema.referential_constraints WHERE constraint_schema = '%s' ", $this->_schema ) ;
		$stmt = $db->query( $query ) ;
		$stmt->setFetchMode( Zend_Db::FETCH_NUM ) ;
		while ( list( $refTableName, $constraintName, $childTableName ) = $stmt->fetch() ) {
			
			if ( ! array_key_exists( $refTableName, $this->_constraints ) ) {
				$this->_constraints[$refTableName] = array() ;
			}
			$this->_constraints[$refTableName][$childTableName] = $keyColumnUsage[$constraintName] ;
		}
		
		return $this ;
	}
	
	/**
	 * @return array
	 * @param string $tableName
	 */
	public function getConstraints( $tableName )
	{
		if ( ! array_key_exists( $tableName, $this->_constraints ) ) {
			return array() ;
		}
		return $this->_constraints[$tableName] ;
	}
}