<?php

class Copilote_Library_Programmation_CreditOuvert extends Copilote_Library_Programmation_Formula
{
	/**
	 * @var string
	 */
	private $_reference = "";
	
	/**
	 * @var Copilote_Library_Programmation_Multiplication
	 */
	private $_fraisDeGestion;
	
	/**
	 * @return void
	 * @param string $reference
	 */
	public function setReference($reference)
	{
		$this->_reference = (string) $reference;
	}
	
	/**
	 * @return void
	 * @param Copilote_Library_Programmation_Multiplication $fraisDeGestion
	 */
	public function setFraisDeGestion(Copilote_Library_Programmation_Multiplication $fraisDeGestion)
	{
		$this->_fraisDeGestion = $fraisDeGestion;
	}
	
	/**
	 * @return string 
	 */
	public function getFormula()
	{
		$formule  = sprintf("(1 * {%s}) - (%s)", $this->_reference, $this->_fraisDeGestion->getFormula());
		return $formule;
	}
}