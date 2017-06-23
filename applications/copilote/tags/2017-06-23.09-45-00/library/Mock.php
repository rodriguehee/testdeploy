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
}