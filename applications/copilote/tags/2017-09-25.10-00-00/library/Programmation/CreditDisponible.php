<?php

class Copilote_Library_Programmation_CreditDisponible
{
	/**
	 * @var Copilote_Library_Programmation_Addition
	 */
	private $_previsionsAnnuelles;
	
	/**
	 * @var Copilote_Library_Programmation_CreditOuvert
	 */
	private $_creditOuvert;
	
	/**
	 * @var Copilote_Library_Programmation_CreditConsomme
	 */
	private $_creditConsomme;
	
	/**
	 * @var Copilote_Library_Programmation_ProvisionChomage
	 */
	private $_provisionChomage;
	
	/**
	 * @return void
	 * @param Copilote_Library_Programmation_CreditOuvert $creditOuvert
	 */
	public function setCreditOuvert(Copilote_Library_Programmation_CreditOuvert $creditOuvert)
	{
		$this->_creditOuvert = $creditOuvert;
	}
	
	/**
	 * @return void
	 * @param Copilote_Library_Programmation_CreditConsomme $creditConsomme
	 */
	public function setCreditConsomme(Copilote_Library_Programmation_CreditConsomme $creditConsomme)
	{
		$this->_creditConsomme = $creditConsomme;
	}
	
	/**
	 * @return void
	 * @param Copilote_Library_Programmation_ProvisionChomage $provisionChomage
	 */
	public function setProvisionChomage(Copilote_Library_Programmation_ProvisionChomage $provisionChomage)
	{
		$this->_provisionChomage = $provisionChomage;
	}
	
	/**
	 * @return void
	 * @param Copilote_Library_Programmation_PrevisionAnnuelle $previsionAnnuelle
	 */
	public function setPrevisionsAnnuelle(Copilote_Library_Programmation_Addition $previsionsAnnuelles)
	{
		$this->_previsionsAnnuelles = $previsionsAnnuelles;
	}
	
	/**
	 * @return string
	 */
	public function render($engine = true)
	{
		$formule  = sprintf("+ (%s)", $this->_creditOuvert->getFormula());
		$formule .= sprintf("- (%s)", $this->_creditConsomme->getFormula());
		$formule .= sprintf("- (%s)", $this->_provisionChomage->getFormula());
		$formule .= sprintf("- (%s)", $this->_previsionsAnnuelles->getFormula());
		if ($engine) {
			$renderEngine = new Copilote_Library_RenderEngine($formule);
			return $renderEngine->getContent();
		}
		return $formule;
	}
}