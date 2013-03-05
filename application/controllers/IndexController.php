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

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function sendBeaconAction() {
		// TODO: Verify that request is from the same user logged in as it says in SA-UI (user id)

		$request = $this->getRequest();
		$impressions = new Application_Model_Impressions();

		$article = $request->getParam("saai");
		$user = $request->getParam("saui");
		$date = $request->getParam("said");

		$impressions->addImpression($user, $article, $date);

		header("X-SA-Debug - Article: {$article}");
		header("X-SA-Debug - User: {$user}");
		header("X-SA-Debug - Date: {$date}");

		header("Content-type: image/gif");
		echo base64_decode("R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");
		exit;
	}

	public function getUserSummaryAction() {
		$request = $this->getRequest();
		$impressions = new Application_Model_Impressions();

		$user = $request->getParam("saui");
		$data = $impressions->getUserSummary($user);

		header("Content-type: application/json");
		echo json_encode($data);
		exit;
	}
	
	public function getUserRecommendationsAction() {
		$request = $this->getRequest();
		$impressions = new Application_Model_Impressions();

		$lastVisitDate = "2013-01-01 00:00:00";

		$user = $request->getParam("saui");
		$data = $impressions->getUserRecommendations($user, $lastVisitDate);

		header("Content-type: application/json");
		echo json_encode(array("articles" => $data));
		exit;
	}
}
