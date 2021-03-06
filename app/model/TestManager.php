<?php
/**
 * Created by PhpStorm.
 * User: Tomas_Odehnal
 * Date: 25.11.2018
 * Time: 18:16
 */

namespace App\Model;


use Nette;
use Nette\Database\Context;

class TestManager extends BaseManager
{
	use Nette\SmartObject;


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	public function getTestsForList($userId) {
//		$tests['tests'] = $this->database->query('SELECT test.*, article.idarticle, article.name AS article_name FROM `test` LEFT JOIN test_has_article ON
// 					test.idtest=test_has_article.test_idtest LEFT JOIN article on test_has_article.article_idarticle=article.idarticle ORDER BY test_has_article.article_idarticle')->fetchAll();

		$t = $this->database->query('SELECT * FROM user_done_test LEFT JOIN test ON user_done_test.test_idtest=test.idtest WHERE user_iduser=? ORDER BY date DESC LIMIT 4', $userId);

		$tests['lastTests'] = false;
		$i = 1;
		while(1) {
			$tmp = $t->fetch();
			if(!$tmp) {
				break;
			}
			$tests['lastTests'][$i] = $tmp;
			$i++;
		}

		$key = 1;
		if($tests['lastTests'] != false) {
			foreach ($tests['lastTests'] as $test) {
				$tests['lastDoneTestQuestions'][$key] = $this->database->query('SELECT question.*, done_question.* FROM question RIGHT JOIN done_question ON question.idquestion=done_question.question_idquestion 
					WHERE done_question.user_done_test_test_idtest=? AND done_question.user_done_test_user_iduser=?', $test->idtest, $userId)->fetchAll();

				if (count($tests['lastDoneTestQuestions'][$key]) > 0) {
					$good = 0;
					$all = 0;
					foreach ($tests['lastDoneTestQuestions'][$key] as $question) {
						$all++;
						if ($question->answered) {
							if ($question->right_answer == $question->answer) {
								$good++;
							}
						}
					}
					$tests['lastDoneTestQuestions'][$key]['percentage'] = (int)(($good / $all) * 100);
				}
				$key++;
			}
		}

		$i = 1;
		$t = $this->database->query('SELECT test.*, article.idarticle, article.name AS article_name FROM `test` LEFT JOIN test_has_article ON
 					test.idtest=test_has_article.test_idtest LEFT JOIN article on test_has_article.article_idarticle=article.idarticle ORDER BY test_has_article.article_idarticle');
		while(1) {
			$tmp = $t->fetch();
			if(!$tmp) {
				break;
			}
			$tests['tests'][$i] = $tmp;
			if($tests['tests'][$i]->idarticle == null) {
				$tests['tests'][$i]->idarticle = 0;
			}
			$i++;
		}

		$key = 1;
		foreach($tests['tests'] as $test) {
			$tests['doneTestQuestions'][$key] = $this->database->query('SELECT question.*, done_question.* FROM question RIGHT JOIN done_question ON question.idquestion=done_question.question_idquestion 
					WHERE done_question.user_done_test_test_idtest=? AND done_question.user_done_test_user_iduser=?', $test->idtest, $userId)->fetchAll();

			if(count($tests['doneTestQuestions'][$key]) > 0) {
				$good = 0;
				$all = 0;
				foreach ($tests['doneTestQuestions'][$key] as $question) {
					$all++;
					if ($question->answered) {
						if ($question->right_answer == $question->answer) {
							$good++;
						}
					}
				}
				$tests['doneTestQuestions'][$key]['percentage'] = (int)(($good / $all) * 100);
			}
			$key++;
		}

		return $tests;
	}


	public function getTestById($id) {
		return $this->database->table(self::TABLE_TEST)
			->where(self::TEST_ID, $id)
			->fetch();
	}


	public function getTestWithEmptyQuestions($idTest, $idUser)
	{
		$test['testInfo'] = $this->database->table(self::TABLE_TEST)
			->where(self::TEST_ID, $idTest)
			->fetch();

		$i = 1;
		$t = $this->database->table(self::TABLE_QUESTION)
			->where(self::QUESTION_ID_TEST, $idTest);
		while(1) {
			$tmp = $t->fetch();
			if(!$tmp) {
				break;
			}
			$test['testQuestions'][$i] = $tmp;
			$i++;
		}


		$test['doneTestInfo'] = $this->database->table(self::TABLE_USER_DONE_TEST)
			->where(self::USER_DONE_TEST_ID_TEST, $idTest)
			->where(self::USER_DONE_TEST_ID_USER, $idUser)
			->fetch();

		if ($test['doneTestInfo'] == false) {
			$test['doneTestInfo'] = $this->database->table(self::TABLE_USER_DONE_TEST)
				->insert([
					self::USER_DONE_TEST_ID_TEST => $idTest,
					self::USER_DONE_TEST_ID_USER => $idUser
				]);
		}

		$tmp = $this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_USER, $idUser)
			->where(self::DONE_QUESTION_ID_TEST, $idTest)
			->where(self::DONE_QUESTION_OLD, 1)
			->fetch();

		if ($tmp == false) {
			$this->database->table(self::TABLE_DONE_QUESTION)
				->where(self::DONE_QUESTION_ID_USER, $idUser)
				->where(self::DONE_QUESTION_ID_TEST, $idTest)
				->update([
					self::DONE_QUESTION_OLD => 1
				]);
		}

		$i = 1;
		$t = $this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_USER, $idUser)
			->where(self::DONE_QUESTION_ID_TEST, $idTest)
			->where(self::DONE_QUESTION_OLD, 3);
		if(($tmp = $t->fetch()) == false) {
			$test['testDoneQuestions'] = false;
		}
		else {
			$test['testDoneQuestions'][$i] = $tmp;
			$i++;
		}
		while(1) {
			$tmp = $t->fetch();
			if(!$tmp) {
				break;
			}
			$test['testDoneQuestions'][$i] = $tmp;
			$i++;
		}

		if($test['testDoneQuestions'] == false) {
			for ($i = 1; $i <= count($test['testQuestions']); $i++) {
				$test['testDoneQuestions'][$i] = $this->database->table(self::TABLE_DONE_QUESTION)
					->insert([
						self::DONE_QUESTION_ID_QUESTION => $test['testQuestions'][$i]->idquestion,
						self::DONE_QUESTION_ID_TEST => $idTest,
						self::DONE_QUESTION_ID_USER => $idUser,
						self::DONE_QUESTION_ANSWERED => false,
						self::DONE_QUESTION_OLD => 3
					]);
			}
		}

		return $test;
	}


	public function saveAnswer($idUser, $idTest, $idQuestion, $answer) {
		$this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_TEST, $idTest)
			->where(self::DONE_QUESTION_ID_USER, $idUser)
			->where(self::DONE_QUESTION_ID_QUESTION, $idQuestion)
			->where(self::DONE_QUESTION_OLD, 3)
			->update([
				self::DONE_QUESTION_ANSWERED => 1,
				self::DONE_QUESTION_ANSWER => $answer
			]);
	}


	public function removeAnswer($idUser, $idTest, $idQuestion, $answer) {
		$this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_TEST, $idTest)
			->where(self::DONE_QUESTION_ID_USER, $idUser)
			->where(self::DONE_QUESTION_ID_QUESTION, $idQuestion)
			->where(self::DONE_QUESTION_OLD, 3)
			->update([
				self::DONE_QUESTION_ANSWERED => 0,
				self::DONE_QUESTION_ANSWER => null
			]);
	}


	public function saveDoneTest($idUser, $idTest, $timer) {
		$this->database->table(self::TABLE_USER_DONE_TEST)
			->where(self::USER_DONE_TEST_ID_USER, $idUser)
			->where(self::USER_DONE_TEST_ID_TEST, $idTest)
			->update([
				self::USER_DONE_TEST_REMAIN_TIME => $timer,
				self::USER_DONE_TEST_DATE => date('Y-m-d H:i:s')
			]);

		$this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_USER, $idUser)
			->where(self::DONE_QUESTION_ID_TEST, $idTest)
			->where(self::DONE_QUESTION_OLD, 1)
			->delete();

		$this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_USER, $idUser)
			->where(self::DONE_QUESTION_ID_TEST, $idTest)
			->where(self::DONE_QUESTION_OLD, 3)
			->update([
				self::DONE_QUESTION_OLD => 0
			]);
	}


	public function getDoneTestInfo($userId, $testId) {
		$test['doneTestInfo'] = $this->database->table(self::TABLE_USER_DONE_TEST)
			->where(self::USER_DONE_TEST_ID_TEST, $testId)
			->where(self::USER_DONE_TEST_ID_USER, $userId)
			->fetch();

		$i = 1;
		$t = $this->database->table(self::TABLE_QUESTION)
			->where(self::QUESTION_ID_TEST, $testId);

		while(1) {
			$tmp = $t->fetch();
			if(!$tmp) {
				break;
			}
			$test['questions'][$i] = $tmp;
			$i++;
		}

		$i = 1;
		$t = $this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_USER, $userId)
			->where(self::DONE_QUESTION_ID_TEST, $testId)
			->where(self::DONE_QUESTION_OLD, 0);

		while(1) {
			$tmp = $t->fetch();
			if(!$tmp) {
				break;
			}
			$test['testDoneQuestions'][$i] = $tmp;
			$i++;
		}

		return $test;
	}


	public function rollbackTest($userId, $testId) {
		$this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_USER, $userId)
			->where(self::DONE_QUESTION_ID_TEST, $testId)
			->where(self::DONE_QUESTION_OLD, 3)
			->delete();

		$this->database->table(self::TABLE_DONE_QUESTION)
			->where(self::DONE_QUESTION_ID_USER, $userId)
			->where(self::DONE_QUESTION_ID_TEST, $testId)
			->where(self::DONE_QUESTION_OLD, 1)
			->update([
				self::DONE_QUESTION_OLD => 0
			]);
	}
}