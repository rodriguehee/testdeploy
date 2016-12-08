<?php

class Copilote_Library_Programmation_SubmitFormula
{
	/**
	 * @var Copilote_Library_Programmation_CreditDisponible[]
	 */
	private $credits = array();
	
	/**
	 * @param string $aspect
	 * @param Copilote_Library_Programmation_CreditDisponible $formule
	 */
	public function setCreditDisponible($aspect, Copilote_Library_Programmation_CreditDisponible $credit)
	{
		$this->credits[$aspect] = $credit;
	}
	
	/**
	 * @return string
	 * @param string $operator
	 */
	public function render($operator)
	{
		$formules = array();
		foreach ($this->credits as $aspect => $credit) {
			$formules[] = sprintf("(Math.abs(%s) %s  ((1 * {c.montant_%s})/200))", $credit->render(false), $operator, $aspect);
		}
		return implode(" && ", $formules);
	}
}