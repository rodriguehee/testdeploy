<?php

class Copilote_Library_Programmation_DataqueryFabric
{
	/**
	 * @var DOMDocument
	 */
	private $document ;
	
	/**
	 * @var array
	 */
	private $columns = array(
		"id_data",
		"cout_fonct_ae_prev",
		"cout_fonct_cp_prev",
		"cout_fonct_ae_total",
		"cout_fonct_cp_total",
		"cout_invest_ae_prev",
		"cout_invest_cp_prev",
		"cout_invest_ae_total",
		"cout_invest_cp_total",
		"cout_personnel_prev",
		"cout_personnel_total",
		"total_ae",
		"total_cp",
		"total_pers_co",
		"total_fonc_ae_co",
		"total_fonc_cp_co",
		"total_invest_ae_co",
		"total_invest_cp_co",
		"total_ae_co",
		"total_cp_co",
	) ;
	
	/**
	 * @var string
	 */
	private $varsetName = "programmation" ;
	
	/**
	 * @param DOMDocument $document
	 */
	public function __construct( DOMDocument $document )
	{
		$this->document = $document ;
	}
	
	/**
	 * @return string
	 */
	public function getAlias()
	{
		return substr($this->varsetName, 0, 1) ;
	}
	
	/**
	 * @return DOMElement
	 * @param integer $annee
	 * @param string $mode
	 */
	public function getElement($annee, $mode)
	{
		$oProject = Core_Library_Account::GetInstance()->GetCurrentProject() ;
		$varset = $oProject->GetVarset($this->varsetName) ;
		$programmationAlias = $this->getAlias() ;
		
		$oDataQuery = $this->document->createElement( "dataquery" ) ;
		$oDataQuery->setAttribute( "id", sprintf( "%s%d", $programmationAlias, $annee ) ) ;
		$oDataQuery->setAttribute( "table_name", $varset->DataTableName() ) ;
		$oDataQuery->setAttribute( "varset_name", $varset->GetVarsetName() ) ;
		$oDataQuery->setAttribute( "table_alias", $programmationAlias ) ;
		$oDataQuery->setAttribute( "mode", $mode ) ;
			
		foreach( $this->columns as $columnName ) {
			$oColumn = $this->document->createElement( "column_simple" ) ;
			$oColumn->setAttribute( "field_name", $columnName ) ;
			$oColumn->setAttribute( "table_name", $programmationAlias ) ;
			$oDataQuery->appendChild( $oColumn ) ;
		}
			
		$oCondition = $this->document->createElement( "condition" ) ;
		$oCondition->setAttribute( "sql", "{p.id_conv} = {c.id_data}" ) ;
		$oField = $this->document->createElement( "field" ) ;
		$oField->setAttribute( "field_name", "id_conv" ) ;
		$oField->setAttribute( "table_name", $programmationAlias ) ;
		$oField->setAttribute( "alias", "p.id_conv" ) ;
		$oCondition->appendChild( $oField ) ;
		$oVariable = $this->document->createElement( "variable" ) ;
		$oVariable->setAttribute( "alias", "c.id_data" ) ;
		$oVariable->setAttribute( "default", "NULL" ) ;
		$oEntry = $this->document->createElement( "entry" ) ;
		$oEntry->setAttribute( "type", "dataset" ) ;
		$oEntry->setAttribute( "name", "c" ) ;
		$oEntry->setAttribute( "field", "id_data" ) ;
		$oEntry->setAttribute( "row", "current" ) ;
		$oVariable->appendChild( $oEntry ) ;
		$oCondition->appendChild( $oVariable ) ;
		$oDataQuery->appendChild( $oCondition ) ;
			
		$oCondition = $this->document->createElement( "condition" ) ;
		$oCondition->setAttribute( "sql", "{p.annee_conv} = " . $annee ) ;
		$oField = $this->document->createElement( "field" ) ;
		$oField->setAttribute( "field_name", "annee_conv" ) ;
		$oField->setAttribute( "table_name", $programmationAlias ) ;
		$oField->setAttribute( "alias", "p.annee_conv" ) ;
		$oCondition->appendChild( $oField ) ;
		$oDataQuery->appendChild( $oCondition ) ;
		return $oDataQuery ;
	}
}