<?php
/**
 * Created by PhpStorm.
 * User: Tomas_Odehnal
 * Date: 30.11.2018
 * Time: 15:18
 */

namespace App\Model;


use Nette\Database\Context;
use Nette\SmartObject;

class StatisticManager extends BaseManager
{
	use SmartObject;


	/** @var \Nette\Database\Context */
	private $database;


	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	public function getTestAndUsers($testId) {
		$test['info'] = $this->database->table(self::TABLE_TEST)
			->where(self::TEST_ID, $testId)
			->fetch();

		$questions = array();

		$i = 1;
		$t = $this->database->table(self::TABLE_QUESTION)
			->where(self::QUESTION_ID_TEST, $testId);
		while(1) {
			$tmp = $t->fetch();
			if(!$tmp) {
				break;
			}
			$questions[$i] = $tmp;
			$i++;
		}


		$test['questionCount'] = count($questions);

		$users = $this->database->table(self::TABLE_USER_DONE_TEST)
			->where(self::USER_DONE_TEST_ID_TEST, $testId)
			->fetchAll();

		$doneQuestions = false;
		$test['percentage'] = null;
		$index = 1;
		foreach($users as $user) {
			for($i=1; $i <= $test['questionCount']; $i++) {
				$doneQuestions[$i] = $this->database->table(self::TABLE_DONE_QUESTION)
					->where(self::DONE_QUESTION_ID_TEST, $testId)
					->where(self::DONE_QUESTION_ID_QUESTION, $questions[$i]->idquestion)
					->where(self::DONE_QUESTION_OLD, 0)
					->where(self::DONE_QUESTION_ID_USER, $user->iduser)
					->fetch();
			}

			if($doneQuestions != false) {
				$good = 0;
				$all = 0;
				for ($i = 1; $i <= $test['questionCount']; $i++) {
					$all++;
					if ($doneQuestions[$i]->answered) {
						if ($questions[$i]->right_answer == $doneQuestions[$i]->answer) {
							$good++;
						}
					}
				}
			}

			$us = $this->database->table(self::TABLE_USER)
				->where(self::USER_COLUMN_ID, $user->iduser)
				->fetch();

			$test['userLogin'][$index] = $us->login;
			$test['userDoneTest'][$index] = $user;
			$test['percentage'][$index] = (int)(($good / $test['questionCount']) * 100);

			$index++;
		}

		return $test;
	}


	public function getAllTests() {
		return $this->database->table(self::TABLE_TEST)
			->fetchAll();
	}

}