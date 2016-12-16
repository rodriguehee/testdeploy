<?php

class Copilote_Library_Programmation_CellFabric
{
	/**
	 * @var DOMDocument
	 */
	private $document ;
	
	/**
	 * @var string
	 */
	private $class = "cel-1 rowx2" ;
	
	/**
	 * @var string 
	 */
	private $mode ;
	
	/**
	 * @param DOMDocument $document
	 * @param string $mode
	 */
	public function __construct( DOMDocument $document, $mode = "r" )
	{
		$this->document = $document ;
		$this->mode = $mode ;
	}
	
	/**
	 * @return DOMElement
	 * @param string $content
	 */
	public function getStaticText( $content )
	{
		$cellElement = $this->document->createElement( "box" ) ;
		$cellElement->setAttribute( "class", $this->class ) ;
		$staticTextElement = $this->document->createElement( "statictext" ) ;
		$staticTextElement->appendChild( $this->document->createTextNode( $content ) ) ;
		$cellElement->appendChild( $staticTextElement ) ;
		return $cellElement ;
	}
	
	/**
	 * @return DOMElement
	 * @param string $datasetName
	 * @param string $fieldName
	 * @param boolean $readonly
	 */
	public function getInputText( $datasetName, $fieldName, $readonly = false )
	{
		if( $this->mode == "r" ) {
			$readonly = true ;
		}
		
		if( $readonly ) {
			$formula =  sprintf( "f({%s.%s})f", $datasetName, $fieldName ) ;
			return $this->getStaticText( $formula ) ;
		}
		
		$cellElement = $this->document->createElement( "box" ) ;
		$cellElement->setAttribute( "class", $this->class ) ;
		$valueElement = $this->document->createElement( "value" ) ;
		$valueElement->setAttribute( "dataset", $datasetName ) ;
		$valueElement->setAttribute( "field", $fieldName ) ;
		$valueElement->setAttribute( "mode", "rw" ) ;
		$cellElement->appendChild($valueElement) ;
		return $cellElement ;
	}
}