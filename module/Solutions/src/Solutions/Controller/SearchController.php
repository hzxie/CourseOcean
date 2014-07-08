<?php

namespace Solutions\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Helper\BasePath;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class SearchController extends AbstractActionController
{
    /**
     * Default method to call in the controller.
     * 
     * @return a ViewModel object which contains HTML content
     */
    public function pageAction()
    {
        $NUMBER_OF_LECTURES_PER_PAGE = 10;
        $pageNumber                  = $this->params()->fromRoute('param', 1);
        return array(
        );
    }
}
