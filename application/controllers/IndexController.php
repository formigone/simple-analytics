<?php

// ------------------------------------------------------------
//
// ------------------------------------------------------------
class IndexController extends Zend_Controller_Action {

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function indexAction() {
		$articles = new Application_Model_Articles();
		$users = new Application_Model_Users();

		$this->view->articles = $articles->getArticles();
		$this->view->attributes = $articles->getAttributeSummary();
		$this->view->users = $users->getUsers();
	}
}
