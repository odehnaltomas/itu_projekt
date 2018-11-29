<?php
/**
 * Created by PhpStorm.
 * User: Tomas_Odehnal
 * Date: 13.10.2018
 * Time: 18:56
 */

namespace App\Presenters;


use App\Model\TestManager;
use Nette\Http\Session;

class TestPresenter extends BasePresenter
{
	private $testManager;

	private $testId;

	private $sessionTest;

	private $timer;

	public function __construct(TestManager $testManager, Session $session)
	{
		$this->testManager = $testManager;
		$this->sessionTest = $session->getSection('test');
	}

	public function actionStart($id) {
		$this->testId = $id;
		$this->sessionTest->currentQuestion = 1;
		if(!$this->user->isLoggedIn()) {
			$this->redirect("Sign:testLogIn");
		}
	}

	public function renderStart($id) {
		$this->template->testInfo = $this->testManager->getTestById($id);
	}

	public function actiontestStatistic($id) {
		$this->testId = $id;
		if(!$this->user->isLoggedIn()) {
			$this->redirect("Sign:testLogIn");
		}
	}

	public function rendertestStatistic($id) {
		$this->template->testInfo = $this->testManager->getTestById($id);
		$this->template->doneTestInfo = $this->testManager->getDoneTestInfo($this->user->getId(), $this->testId);
		$this->template->testTime = $this->template->testInfo->time -  $this->template->doneTestInfo['doneTestInfo']->remain_time;
		$this->template->date = $this->template->doneTestInfo['doneTestInfo']->date;

		$good = 0;
		$bad = 0;
		foreach ($this->template->doneTestInfo['questions'] as $key => $question) {
			if($question->right_answer == $this->template->doneTestInfo['testDoneQuestions'][$key]->answer) {
				$good++;
			}
			else {
				$bad++;
			}
		}
		$this->template->good = $good;
		$this->template->bad = $bad;

		$this->template->percentage = $good / count($this->template->doneTestInfo['testDoneQuestions']) * 100;
	}

	public function actionTest($id) {
		$this->testId = $id;
	}
	public function renderTest() {
		$this->template->testQuestions = $this->testManager->getTestWithEmptyQuestions($this->testId, $this->user->getId());
		$this->template->currentQuestion = $this->sessionTest->currentQuestion;
		$this->template->timer = $this->timer;
		bdump($this->template->testQuestions);
		bdump($this->timer);
	}

	public function renderDefault() {
		if(!$this->user->isLoggedIn()) {
			$this->redirect("Sign:testLogIn");
		}
		$this->template->tests = $this->testManager->getTestsForList($this->user->getId());
	}


	public function handlePreviousQuestion() {
		if($this->isAjax()) {
			$this->sessionTest->currentQuestion--;
			$this->redrawControl('testSnippet');
		}
	}


	public function handleNextQuestion() {
		if($this->isAjax()) {
			$this->sessionTest->currentQuestion++;
			$this->redrawControl('testSnippet');
		}
	}


	public function handleChangeQuestion($question) {
		if($this->isAjax()) {
			$this->sessionTest->currentQuestion = $question;
			$this->redrawControl('testSnippet');
		}
	}


	public function handleSaveAnswer($idQuestion, $answer) {
		if($this->isAjax()) {
			$this->testManager->saveAnswer($this->user->getId(), $this->testId, $idQuestion, $answer);
			$this->redrawControl('testSnippet');
		}
	}


	public function handleRemoveAnswer($idQuestion, $answer) {
		if($this->isAjax()) {
			$this->testManager->removeAnswer($this->user->getId(), $this->testId, $idQuestion, $answer);
			$this->redrawControl('testSnippet');
		}
	}


	public function handleSendTest($timer) {
		if($this->isAjax()) {
			$this->testManager->saveDoneTest($this->user->getId(), $this->testId, $timer);
			$this->redirect('Test:testDone', $this->testId);
		}
	}


	public function actionTestDone($id) {
		$this->testId = $id;
		$this->sessionTest->currentQuestion = 1;
	}

	public function renderTestDone($id) {
		$this->template->testInfo = $this->testManager->getTestById($id);
		$this->template->doneTestInfo = $this->testManager->getDoneTestInfo($this->user->getId(), $this->testId);
		$this->template->testTime = $this->template->testInfo->time -  $this->template->doneTestInfo['doneTestInfo']->remain_time;
		$this->template->date = $this->template->doneTestInfo['doneTestInfo']->date;

		$num = 0;
		foreach ($this->template->doneTestInfo['questions'] as $key => $question) {
			if($question->right_answer == $this->template->doneTestInfo['testDoneQuestions'][$key]->answer) {
				$num++;
			}
		}

		$this->template->percentage = $num / count($this->template->doneTestInfo['testDoneQuestions']) * 100;
	}
}