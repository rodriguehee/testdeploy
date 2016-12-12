<?php

class Copilote_Library_Programmation_Document
{
	/**
	 * @var DOMDocument
	 */
	private $document ;
	
	/**
	 * Core_Library
	 */
	public function __construct(Core_Library_Resource_XML_Frame_Form $oForm) 
	{
		$this->document = new DOMDocument( '1.0' ) ;
		$oDomElt = dom_import_simplexml( $oForm->GetSimpleXMLObject() ) ;
		$oDomElt = $this->document->importNode( $oDomElt, true ) ;
		$this->document->appendChild( $oDomElt ) ;
	}
	
	/**
	 * @return Copilote_Library_Programmation_Document
	 * @param integer $idData
	 */
	public function setDatasetPath($idData)
	{
		$oGetdatasetPathElt = $this->document->getElementsByTagName( 'getdataset_path' )->item( 0 );
		/* @var $oGetdatasetPathElt DOMElement */
		$oGetdatasetPathElt->nodeValue = preg_replace(
			'/\{id_data\}/',
			$idData,
			$oGetdatasetPathElt->nodeValue
		) ;
		return $this ;
	}
	
	/**
	 * @return DOMDocument
	 */
	public function getDomDocument()
	{
		return $this->document ;
	}
	
	/**
	 * @return DOMElement
	 */
	public function getDataStructureElement()
	{
		$aDataStructureList = $this->document->getElementsByTagName( 'data_structure' ) ;
		return $aDataStructureList->item( 0 ) ;
	}
	
	/**
	 * @return DOMElement
	 * @throws LogicException
	 */
	public function getOptionElement($boxElementId)
	{
		$boxElt = $this->getBoxElement($boxElementId);
		$optionElt = null;
		foreach ($boxElt->getElementsByTagName("option") as $optionElt) {
			assert($optionElt instanceof DOMElement);
			if ($optionElt->getAttribute("option_name") == "show_on") {
				break;
			}
		}
		if (! $optionElt instanceof DOMElement) {
			throw new LogicException("Element introuvable");
		}
		return $optionElt;
	}
	
	/**
	 * @return DOMElement
	 * @param string $boxId
	 * @throws LogicException
	 */
	public function getBoxElement($boxId)
	{
		$aBox = $this->document->getElementsByTagName( 'box' ) ;
		foreach( $aBox as $oBoxElement ) {
			if( $boxId == $oBoxElement->getAttribute( "id" ) ) {
				return $oBoxElement ;
			}
		}
		throw new LogicException(sprintf("'%s' introuvable dans la source XML", $boxId));
	}
}