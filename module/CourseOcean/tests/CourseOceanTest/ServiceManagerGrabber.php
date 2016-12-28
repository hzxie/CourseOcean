<?php
/**
 * 获取ZendFramework的项目配置.
 *
 * @author 谢浩哲 <cshzxie@gmail.com>
 */

namespace CourseOceanTest;
 
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
 
class ServiceManagerGrabber
{
    /**
     * 自动注入的ServiceManagerConfig对象.
     * @var ServiceManagerConfig
     */
    protected static $serviceConfig = null;

    public static function setServiceConfig($config)
    {
        static::$serviceConfig = $config;
    }

    public function getServiceManager()
    {
        $configuration = static::$serviceConfig ? : require_once './config/application.config.php';
         
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
        $serviceManager = new ServiceManager(new ServiceManagerConfig($smConfig));
        $serviceManager->setService('ApplicationConfig', $configuration);
  
        $serviceManager->get('ModuleManager')->loadModules();
         
        return $serviceManager;
    }
}