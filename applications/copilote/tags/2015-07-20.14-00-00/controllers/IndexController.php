<?php

/**
 * IndexController - The default controller class
 *
 * @author
 * @version
 */

class IndexController extends Core_Library_Controller_Action
{
	public function init() {
		parent::init();

		$this->_assignCommonViewVariables();
	}
	/**
	 * The default action - show the home page
	 */
    public function indexAction()
    {

    }
}
