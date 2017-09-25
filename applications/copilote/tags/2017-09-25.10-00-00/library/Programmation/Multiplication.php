<?php

class Copilote_Library_Programmation_Multiplication extends Copilote_Library_Programmation_Formula
{
	/**
	 * @var float
	 */
	private $_coefficient = 1.0;
	
	/**
	 * @return void
	 * @param float $coefficient
	 */
	public function setCoefficient($coefficient)
	{
		$this->_coefficient = (float) $coefficient;
	}
	
	/**
	 * @return string
	 */
	public function getFormula()
	{
		$formule = implode(" * ", $this->_getElements());
		return $formule;
	}
	
	/**
	 * @return array
	 */
	private function _getElements()
	{
		$elements = array();
		$elements[] = $this->_coefficient;
		foreach ($this->_fields as $field) {
			$elements[] = sprintf("{%s}", $field);
		}
		return $elements;
	}
}