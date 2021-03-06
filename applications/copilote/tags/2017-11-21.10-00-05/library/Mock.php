<?php

class Copilote_Library_Mock
{
	/**
	 * @var array
	 */
	protected $_attributes = array();
	
	/**
	 * @param string $key
	 * @param string $value
	 * @return Copilote_Library_Mock
	 */
	public function setAttribute($key, $value)
	{
		$this->_attributes[$key] = (string) $value;
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return string
	 * @throws DomainException
	 */
	public function getAttribute($key)
	{
		if( array_key_exists($key, $this->_attributes)) {
			return $this->_attributes[$key];
		}
		throw new DomainException(sprintf( "Attribut '%s' introuvable", $key));
	}
	
	/**
	 * @return integer
	 * @param Copilote_Library_Convention $convention
	 */
	public function GetMontantPersonnel(Copilote_Library_Convention $convention)
	{
	    return 0;
	}
	
	/**
	 * @return float
	 * @param Copilote_Library_Convention $convention
	 * @param string $nature
	 * @param string $aspect
	 */
	public function GetAutreMontant(Copilote_Library_Convention $convention, $nature, $aspect)
	{
	    return 0.0 ;
	}
}