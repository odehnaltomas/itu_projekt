<?php
/**
 * Created by PhpStorm.
 * User: Tomas_Odehnal
 * Date: 13.10.2018
 * Time: 18:56
 */

namespace App\Presenters;


use App\Model\ArticleManager;

class ArticlePresenter extends BasePresenter
{
	private $articleManager;

	private $articleId;

	public function __construct(ArticleManager $articleManager)
	{
		$this->articleManager = $articleManager;
	}


	public function renderDefault() {
		$this->template->articles = $this->articleManager->getAllArticles();
	}


	public function actionArticle($id) {
		$this->articleId = $id;
	}


	public function renderArticle($id) {
		$this->template->article = $this->articleManager->getArticleById($id);
		$this->template->isThereTests = $this->articleManager->checkIfExistTestsToArticle($this->articleId);
		bdump($this->template->isThereTests);
	}
}