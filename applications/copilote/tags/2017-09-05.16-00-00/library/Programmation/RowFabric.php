<?php

class Copilote_Library_Programmation_RowFabric
{
	/**
	 * @var DOMDocument
	 */
	private $document ;
	
	/**
	 * @var string
	 */
	private $class = "row rowx2" ;

	/**
	 * @param DOMDocument $document
	 */
	public function __construct( DOMDocument $document )
	{
		$this->document = $document ;
	}

	/**
	 * @return DOMElement
	 */
	public function getElement()
	{
		$rowElement = $this->document->createElement("box") ;
		$rowElement->setAttribute("class", $this->class) ;
		return $rowElement ;
	}
}