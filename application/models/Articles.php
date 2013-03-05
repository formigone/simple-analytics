<?php 
class Application_Model_Articles {
	private $db;

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function __construct() {
		$this->db = $this->db = Zend_Db_Table::getDefaultAdapter();
	}

	public function getArticles() {
		return $this->db->fetchAll(
				$this->db->select()
				->from(array("ArticleAttribute" => "article_attributes"), array(
						"article_id"      => "Article.id",
						"article_title"   => "Article.title",
						"attribute_id"    => "Attribute.id",
						"attribute_title" => "Attribute.title"
						)
					)
				->join(array("Article" => "articles"), 
						"Article.id = ArticleAttribute.article_id", null)
				->join(array("Attribute" => "attributes"), 
						"Attribute.id = ArticleAttribute.article_attribute_id", null)
				->order(array("Article.title", "Attribute.title"))
			);
	}

	public function getAttributeSummary() {
		return $this->db->fetchAll(
				$this->db->select()
				->from(array("ArticleAttribute" => "article_attributes"), array(
						"total_articles"  => "SUM(1)",
						"attribute_id"    => "Attribute.id",
						"attribute_title" => "Attribute.title"
						)
					)
				->join(array("Attribute" => "attributes"),
						"Attribute.id = ArticleAttribute.article_attribute_id", null)
				->group(array("ArticleAttribute.article_attribute_id"))
				->order(array("total_articles DESC", "Attribute.title"))
		);
	}
}