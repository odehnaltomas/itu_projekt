<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new Route('test/test/[<id>]', array('presenter' => 'Test', 'action' => 'test'));
		$router[] = new Route('test/testStatistic/[<id>]', array('presenter' => 'Test', 'action' => 'testStatistic'));
		$router[] = new Route('test/start/[<id>]', array('presenter' => 'Test', 'action' => 'start'));
		$router[] = new Route('test/testDone/[<id>]', array('presenter' => 'Test', 'action' => 'testDone'));
		$router[] = new Route('article/article/[<id>]', array('presenter' => 'Article', 'action' => 'article'));
		$router[] = new Route('<presenter>/<action>', 'Homepage:default');
		return $router;
	}
}
