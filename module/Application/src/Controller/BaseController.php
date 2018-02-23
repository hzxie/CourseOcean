<?php
namespace Application\Controller;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class BaseController extends AbstractActionController {
    /**
     * 获取应用的配置.
     * @return 包含应用配置的数组
     */
    private function getConfig() {
        $globalConfig = include __DIR__ . '/../../../../config/autoload/global.php';
        $localConfig  = include __DIR__ . '/../../../../config/autoload/local.php';

        return array_merge_recursive($globalConfig, $localConfig);
    }

    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    public function getResultSetArray($resultSet) {
        $returnArray = array();
        
        if ( $resultSet == null ) {
            return $returnArray;
        }
        foreach ( $resultSet as $rowSet ) {
            $rowArray = (array)$rowSet;
            array_push($returnArray, $rowArray);
        }
        return $returnArray;
    }

    /**
     * 检查用户是否已经登录.
     * @return 用户是否已经登录
     */
    public function isAllowedToAccess($allowedUserGroups = NULL) {
        $session    = new Container('co_session');
        $isLogined  = $session->offsetExists('isLogined');
        $userGroup  = $session->offsetGet('userGroupSlug');

        if ( is_array($allowedUserGroups) ) {
            return in_array($userGroup, $allowedUserGroups);
        }
        return $isLogined;
    }

    /**
     * 获取已登录用户的用户唯一标识符
     * @return 用户的用户唯一标识符
     */
    public function getLoginUserUid() {
        $session    = new Container('co_session');
        return $session->offsetGet('uid');
    }

    /**
     * 向目标邮箱发送电子邮件.
     * @param $title         [<description>]
     * @param $model         [<description>]
     * @param $templateName  [<description>]
     * @param $receiverEmail [<description>]
     */
    public function sendMail($title, $model, $templateName, $receiverEmail) {
        $resolver       = new TemplateMapResolver();
        $resolver->setMap([
            'mailTemplate' => __DIR__ . "/../../view/mails/$templateName.phtml"
        ]);
        $renderer       = new PhpRenderer();
        $renderer->setResolver($resolver);
        $viewModel      = new ViewModel();
        $viewModel->setTemplate('mailTemplate')->setVariables($model);

        $bodyPart       = new MimeMessage();
        $bodyMessage    = new MimePart($renderer->render($viewModel));
        $bodyMessage->type = 'text/html';
        $bodyPart->setParts(array($bodyMessage));

        $message        = new Message();
        $message->addFrom('noreply@infinitescript.com', 'CourseOcean')
                ->addTo($receiverEmail)
                ->setSubject($title)
                ->setBody($bodyPart)
                ->setEncoding('UTF-8');
        
        $config    = $this->getConfig();
        $transport = new Smtp();                
        $transport->setOptions(new SmtpOptions($config['mail']));
        $transport->send($message);
    }
}