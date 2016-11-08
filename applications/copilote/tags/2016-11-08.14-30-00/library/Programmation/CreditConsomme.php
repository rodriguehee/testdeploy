<?php

class Copilote_Library_Programmation_CreditConsomme
{
	/**
	 * @var array
	 */
	private $_additions = array();
	
	/**
	 * @return void
	 * @param Copilote_Library_Programmation_Addition $addition
	 */
	public function attach(Copilote_Library_Programmation_Addition $addition)
	{
		$this->_additions[] = $addition;
	}
	
	/**
	 * @return string
	 */
	public function render()
	{
		return sprintf("f(parseFloat(%s).toFixed(2))f", $this->getFormula());
	}
	
	/**
	 * @return string
	 */
	public function getFormula()
	{
		$formule  = "";
		
		foreach ($this->_additions as $addition) {
			assert($addition instanceof Copilote_Library_Programmation_Addition);
			foreach ($addition->getFields() as $field) {
				$formule .= sprintf('+ (1*{%s}) ', $field);
			}
		}
		
		return $formule;
	}
}