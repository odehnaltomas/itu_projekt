<?php
/**
 * Created by PhpStorm.
 * User: Tomas_Odehnal
 * Date: 29.11.2018
 * Time: 23:52
 */

namespace App\Model;



use Nette\Database\Context;
use Nette\SmartObject;

class ArticleManager extends BaseManager
{
	use SmartObject;


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	public function getAllArticles() {
		return $this->database->table(self::TABLE_ARTICLE)->fetchAll();
	}
}