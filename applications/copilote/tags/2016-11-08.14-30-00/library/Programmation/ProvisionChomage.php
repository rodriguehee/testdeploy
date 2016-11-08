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
		//$formule  = "f(parseFloat(";
		$formule = sprintf("%f*(", $this->_coefficient);
		foreach ($this->_fields as $field) {
			$formule .= sprintf('+ (1*{%s}) ', $field);
		}
		$formule .= ")";
		//$formule .= ").toFixed(2))f";
		return $formule;
	}
}