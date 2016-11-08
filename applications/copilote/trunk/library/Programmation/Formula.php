<?php

abstract class Copilote_Library_Programmation_Formula
{
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @return void
	 * @param string
	 */
	public function attach($field)
	{
		if (! in_array($field, $this->_fields)) {
			$this->_fields[] = $field;
		}
	}
	
	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->_fields;
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
	abstract public function getFormula();
}