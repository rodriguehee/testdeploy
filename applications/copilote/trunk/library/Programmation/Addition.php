<?php

class Copilote_Library_Programmation_Addition extends Copilote_Library_Programmation_Formula
{
	/**
	 * @var float
	 */
	private $offset = 0;
	
	/**
	 * @return string
	 */
	public function getFormula()
	{
		$formule  = ""; 
		foreach ($this->_fields as $field) {
			$formule .= sprintf('+ (1*{%s}) ', $field);
		}
		$formule .= sprintf('+ (1*%s)', $this->offset);
		return $formule;
	}
	
	/**
	 * @param float $offset
	 */
	public function increment($amount)
	{
		$this->offset += (float) $amount;
	}
	
	/**
	 * @return float
	 */
	public function getOffset()
	{
		return $this->offset;
	}
	
	/**
	 * @param Copilote_Library_Programmation_Formula $formula
	 */
	public function attachFrom(Copilote_Library_Programmation_Addition $formula)
	{
		foreach ($formula->getFields() as $field) {
			$this->attach($field);
		}
		
		$this->offset += $formula->getOffset();
	}
}