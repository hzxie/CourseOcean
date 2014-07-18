<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * 应用程序的Controller, 用于完成应用程序中的公共事务.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class HomeController extends AbstractActionController
{
	public function indexAction()
	{
		return array();
	}

	public function searchAction()
	{
		$keyword = $this->params()->fromQuery('keyword');
		return array(
			'keyword'	=> $keyword,
		);
	}

	public function getSearchResultAction()
	{
		$keyword  = $this->params()->fromQuery('keyword');
		$category = $this->params()->fromQuery('category');

		$result   = array(
			'isSuccessful'	=> false,
		);
		$response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
	}

	public function getSearchResultTotalPagesAction()
	{
		$result   = array(
			'isSuccessful'	=> false,
			'totalPages'	=> 0,
		);
		$response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
	}
}