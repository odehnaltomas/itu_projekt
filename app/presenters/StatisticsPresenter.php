<?php
/**
 * Created by PhpStorm.
 * User: Tomas_Odehnal
 * Date: 13.10.2018
 * Time: 18:57
 */

namespace App\Presenters;


use App\Model\StatisticManager;

class StatisticsPresenter extends BasePresenter
{
	private $statisticManager;

	private $currentTestId = 1;


	public function __construct(StatisticManager $statisticManager)
	{
		$this->statisticManager = $statisticManager;
	}


	public function actionDefault() {
		if(!$this->user->isLoggedIn()) {
			$this->redirect("Statistics:statisticsLogIn");
		}
	}


	public function renderDefault() {
		$this->template->tests = $this->statisticManager->getAllTests();
		$test = $this->statisticManager->getTestAndUsers($this->currentTestId);
		$per = $test['percentage'];
		if($per != null) {
			arsort($test['percentage']);
		}
		$this->template->test = $test;
	}


	public function handleChangeTest($idTest) {
		if($this->isAjax()) {
			$this->currentTestId = $idTest;
			$this->redrawControl('statSnippet');
		}
	}
}