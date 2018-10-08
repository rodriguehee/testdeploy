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
	 * @var string
	 */
	protected $_primaryKey = "" ;
	
	/**
	 * @param string $tableName
	 * @param integer $idData
	 */
	public function __construct( $tableName, $id, $primaryKey = "id_data" )
	{
		$this->_tableName = (string) $tableName ;
		$this->_id = (int) $id ;
		$this->_primaryKey = (string) $primaryKey ;
		$this->_setAttributes() ;
		$this->_setChildren() ;
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->_id ;
	}
	
	/**
	 * @return Copilote_Library_Record
	 * @param string $key
	 * @param string $value
	 */
	public function setAttribute( $key, $value )
	{
		$this->_attributes[$key] = (string) $value ;
		return $this ;
	}
	
	/**
	 * @return boolean
	 * @param unknown $key
	 */
	public function hasAttribute($key)
	{
		return array_key_exists($key, $this->_attributes);
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	public function getAttribute( $key )
	{
		if($this->hasAttribute($key)) {
			return $this->_attributes[$key] ;
		}
		throw new DomainException( sprintf( "Attribut '%s' introuvable", $key ) ) ;
	}
	
	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	/**
	 * @return Copilote_Library_Record
	 */
	protected function _setAttributes()
	{
		$db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
		$query = sprintf( "SELECT * FROM %s WHERE %s = %d", $this->_tableName, $this->_primaryKey, $this->_id ) ;
		$this->_attributes = $db->fetchRow( $query ) ;
		
		if ( ! is_array( $this->_attributes ) ) {
			throw new LogicException( sprintf( "Record [%d] introuvable pour la table '%s'", $this->_id, $this->_tableName ) ) ;
		}
		unset( $this->_attributes[$this->_primaryKey] ) ;
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
		$constraints = $this->_getConstraints( $this->_tableName ) ;
		foreach( $constraints as $tableName => $foreignKeyColumn ) {
			$query = sprintf( "SELECT id_data FROM %s WHERE %s = %d ", $tableName, $foreignKeyColumn, $this->_id ) ;
			$ids = $db->fetchCol( $query ) ;
			foreach( $ids as $id ) {
				$child = new Copilote_Library_Record( $tableName, $id ) ;
				$child->setForeignKey( $foreignKeyColumn ) ;
				$this->_children[] = $child ;
			}
		}
		return $this ;
	}
	
	/**
	 * @return array
	 * @param string $tableName
	 */
	protected function _getConstraints( $tableName ) 
	{
		$constraints = array() ;
		$constraints['cplt_ub_data'] = array (
			'cplt_act_data' => 'id_ub',
			'cplt_conv_data' => 'id_ub',
			'cplt_sv_data' => 'id_ub',
			'cplt_ub_data' => 'id_gub',
		) ;
		$constraints['cplt_dmnd_data'] = array(
			'cplt_bdgt_data' => 'id_demande',
			'cplt_dpns_data' => 'id_demande',
			'cplt_vld_data' => 'id_demande',
		) ;
		$constraints['cplt_sv_data'] = array(
			'cplt_dmnd_data' => 'id_suivi'
		) ;
		$constraints['cplt_dpns_data'] = array(
			'cplt_dmp_data' => 'id_depense',
			'cplt_dt_data' => 'id_depense',
			'cplt_gci_data' => 'id_depense',
			'cplt_mi_data' => 'id_depense',
			'cplt_oc_data' => 'id_depense',
			'cplt_pe_data' => 'id_depense',
			'cplt_rh_data' => 'id_depense',
			'cplt_si_data' => 'id_depense',
			'cplt_sta_data' => 'id_depense',
		) ;
		$constraints['cplt_id_data'] = array(
			'cplt_dt_data' => 'dt_identite',
			'cplt_gci_data' => 'gci_identite',
			'cplt_mi_data' => 'mi_identite',
			'cplt_rh_data' => 'rh_identite',
		) ;
		$constraints['cplt_conv_data'] = array(
			'cplt_dt_data' => 'dt_numconv',
			'cplt_rh_data' => 'rh_impc1',
			'cplt_sta_data' => 'sta_impc1',
			'cplt_vntl_data' => 'convention',
		) ;
		$constraints['cplt_pays_data'] = array(
			'cplt_dt_data' => 'dt_lieudest',
			'cplt_mi_data' => 'mi_destouresid',
		) ;
		$constraints['cplt_pj_axis'] = array(
			'cplt_pj_group' => 'id_axis'
		) ;
		$constraints['cplt_pj_group'] = array(
			'cplt_pj_group_link' => 'id_group_parent',
			'cplt_pj_old_passwords' => 'id_group',
			'cplt_pj_token' => 'id_group',
			'cplt_ub_data' => 'id_structure',
		) ;
		$constraints['cplt_pj_role'] = array(
			'cplt_pj_group_link' => 'id_role'
		) ;
		$constraints['cplt_pj_group_link'] = array(
			'cplt_pj_group_mode' => 'id_group_link'
		) ;
		$constraints['cplt_pj_varset'] = array(
			'cplt_pj_group_mode' => 'id_varset'
		) ;
		$constraints['cplt_dmp_data'] = array(
			'cplt_vntl_data' => 'id_dmp'
		) ;
		$constraints['cplt_oc_data'] = array(
			'cplt_vntl_data' => 'id_oc'
		) ;
		$constraints['cplt_pe_data'] = array(
			'cplt_vntl_data' => 'id_pe'
		) ;
		
		if ( ! array_key_exists( $tableName, $constraints ) ) {
			return array() ;
		}
		return $constraints[$tableName] ;
	}
	
	/**
	 * @return integer
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
		$db->query( $query ) ;
		$id = $db->lastInsertId() ;
		
		foreach( $this->_children as $child ) {
			$child->setAttribute( $child->getForeignKey(), $id ) ;
			$child->duplicate() ;
		}
		
		return $id ;
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
		
		$query = sprintf( "UPDATE %s SET %s WHERE %s = %d", $this->_tableName, implode( ", ", $sets ), $this->_primaryKey, $this->_id ) ;
		$db->query( $query ) ;
		return $this ;
	}

	/**
	 * @Return 
	 */
	public function get_parametres( $parametre )
	{
  	   $dbo = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
	   $sql = "SELECT ".$parametre." FROM cplt_parametres_data" ;
	   $paramr  = $dbo->fetchOne( $sql, $parametre ) ;
	   return $paramr;
	}

}

