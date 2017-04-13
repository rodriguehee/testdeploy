<?php

class Copilote_Library_Programmation_ProvisionChomage extends Copilote_Library_Programmation_Formula
{
	/**
	 * @var float
	 */
	private $_coefficient = 0.0;
	
	/**
	 * @return void
	 * @param float $coefficient
	 */
	public function setCoefficient($coefficient)
	{
		$this->_coefficient = (float) $coefficient;
	}
	
	/**
	 * @return float
	 */
	public function getCoefficient()
	{
		return $this->_coefficient;
	}
	
	/**
	 * @return string
	 */
	public function getFormula()
	{
		if (empty($this->_coefficient)) {
			return "0.0";
		}
		
		$formule = sprintf("%f*(", $this->_coefficient);
		foreach ($this->_fields as $field) {
			$formule .= sprintf('+ (1*{%s}) ', $field);
		}
		$formule .= ")";
		return $formule;
	}
	
	/**
	 * @param Copilote_Library_Programmation_Formula $formula
	 */
	public function attachFrom(Copilote_Library_Programmation_Addition $formula)
	{
		foreach ($formula->getFields() as $field) {
			$this->attach($field);
		}
	}
}