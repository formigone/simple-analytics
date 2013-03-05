<?php 
class Application_Model_Impressions {
	private $db;

	public function __construct() {
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}

	public function addImpression($user, $article, $date) {
		return $this->db->insert("impressions", array(
				"user_id" => $user,
				"article_id" => $article,
				"date" => $date
				)
			);
	}
	
	/**
	 * Get top three most viewed articles for a given category since a given date
	 */
	private function getMostRelevantArticles($category, $date, $limit = 3) {
		if (!$category || !is_numeric($category) || !is_numeric($limit))
			return false;

		return $this->db->fetchAll(
				$this->db->select()
				->from(array("Impression" => "impressions"), array(
						"total_impressions" => "SUM(1)",
						"id" => "Article.id",
						"title" => "Article.title",
						"date" => "Impression.date"
						)
					)
				->join(array("Article" => "articles"),
						"Article.id = Impression.article_id")
				->where("Article.id IN(?)", new Zend_Db_Expr("(
						SELECT Article.id FROM article_attributes aa 
						JOIN articles Article on Article.id = aa.article_id 
						WHERE aa.article_attribute_id = {$category}
						)")
					)
				->where("Impression.date >= ?", $date)
				->group(array("Impression.article_id"))
				->order(array("total_impressions DESC", "Impression.date DESC"))
				->limit($limit)
			);
	}

	/**
	 * Get most viewed category for a given user.
	 * How this current algorithm is calculates the most viewed category:
	 *   1. Take 5 articles that the user read at least 3 days ago
	 *   2. Sum up all the individual categories associated with each article
	 *   3. Select the most popular category from among those articles
	 */
	private function getFavoriteCategory($user) {
		if (!is_numeric($user))
			return false;

		$lookBackLimit = 5;

		// Only sample articles that the user read at least this many days in the past, as per the current algorithm
		// TODO: Adjust this number when more data is available. A negative number means days since today, and a positive number refers to days from today (some date in the future)
		$days = +3;
		$lastDayToSample = date("y-m-d 00:00:00", time() + (60 * 60 * 24 * $days));

		return $this->db->fetchRow(
				$this->db->select()
				->from(array("ArticleAttribute" => "article_attributes"), array(
						"id"    => "Attribute.id",
						"category_title" => "Attribute.title",
						"total_articles"  => "SUM(1)"
						)
					)
				->join(array("Attribute" => "attributes"),
						"Attribute.id = ArticleAttribute.article_attribute_id", null)
//				->join(array("Impression" => "impressions"),
//						"Impression.article_id = ArticleAttribute.article_id", null)

				// Only look at a limited number of impressions, as per the current algorithm (only sample the last 5 or so articles read)
				->join(array("Impression" => new Zend_Db_Expr("(
							SELECT * FROM impressions 
							WHERE user_id = {$user}
							ORDER BY date DESC
							LIMIT {$lookBackLimit}
							)")),
							"Impression.article_id = ArticleAttribute.article_id", null)
				->where("Impression.user_id = ?", $user)
				->Where("Impression.date < '{$lastDayToSample}'")
				->group(array("ArticleAttribute.article_attribute_id"))
				->order(array("total_articles DESC"))
		);
	}

	/**
	 * Return a list of articles relevant to a user that were published since the user's last visit to the site
	 * 
	 * @param int $user
	 * @param date $lastVisitDate "y-m-d h:i:s"
	 * 
	 * @return array
	 */
	public function getUserRecommendations($user, $lastVisitDate) {
		$category = $this->getFavoriteCategory($user);

		return $this->getMostRelevantArticles($category["id"], $lastVisitDate);
	}

	public function getUserSummary($user) {
		$articles = $this->db->fetchAll(
				$this->db->select()
				->from(array("Impression" => "impressions"), array(
						"article_id" => "Impression.article_id",
						"title" => "Article.title"
						)
					)
				->join(array("Article" => "articles"), 
						"Article.id = Impression.article_id")
				->where("Impression.user_id = ?", $user)
				->order("Article.id")
			);

		return array("articles" => $articles, "total_articles" => count($articles));
	}
}