<?php

class Copilote_Library_RenderEngine
{
	/**
	 * @var string
	 */
	private $_formula = "";
	
	/**
	 * @param string $formula
	 */
	public function __construct($formula)
	{
		$this->_formula = (string) $formula;
	}
	
	/**
	 * @return string
	 */
	public function getContent()
	{
		return sprintf("~(parseFloat(%s).toFixed(2))~", $this->_formula);
	}
}