<?php

class Copilote_Library_Programmation_Addition extends Copilote_Library_Programmation_Formula
{
	/**
	 * @return string
	 */
	public function getFormula()
	{
		$formule  = ""; 
		foreach ($this->_fields as $field) {
			$formule .= sprintf('+ (1*{%s}) ', $field);
		}
		return $formule;
	}
}