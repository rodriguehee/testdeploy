<?php

abstract class Copilote_Library_Programmation_Formula
{
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
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
		$renderEngine = new Copilote_Library_RenderEngine($this->getFormula());
		return $renderEngine->getContent();
	}
	
	/**
	 * @return string
	 */
	abstract public function getFormula();
}