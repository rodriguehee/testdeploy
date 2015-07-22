<?php

class Mainframe_IndexController extends Core_Library_Controller_MainFrame_Index
{
	public function init()
	{
		parent::init() ;
		
		$sGridUrl = Core_Library_Options::get( 'lib.grid.url' ) ;
		
		$this->view->headLink()->prependStylesheet( $sGridUrl . 'styles/jqx.bootstrap.css' ) ;
		$this->view->headLink()->prependStylesheet( $sGridUrl . 'styles/jqx.base.css' ) ;
		
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxcore.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxdata.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxdata.export.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxbuttons.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxscrollbar.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxmenu.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxlistbox.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxcombobox.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxdropdownlist.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxdropdownbutton.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxwindow.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxpanel.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxcheckbox.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxnumberinput.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxdatetimeinput.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxcalendar.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.selection.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.columnsresize.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.edit.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.filter.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.sort.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.pager.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.grouping.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.export.js' ) ;
		$this->view->headScript()->appendFile( $sGridUrl . 'jqxgrid.aggregates.js' ) ;
	}
}
