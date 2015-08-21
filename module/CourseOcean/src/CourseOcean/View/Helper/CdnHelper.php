<?php

namespace CourseOcean\View\Helper;

use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager; 
use Zend\View\Helper\AbstractHelper;

/**
 * 提供CDN服务的Helper.
 * @author  谢浩哲<zjhzxhz@gmail.com>
 */
class CdnHelper extends AbstractHelper {
    /**
     * CdnHelper类的构造函数.
     * @param Request $request - Http Request对象
     * @param Array   $serviceLocator - ServiceManger对象
     */
    public function __construct(Request $request, ServiceManager $serviceLocator) {
        $this->request        = $request;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get URL on CDN servers.
     * @param  String $filePath - the relative path of assets file path
     * @return the url of assets file path
     */
    public function __invoke($filePath) {
        $config  = $this->serviceLocator->get('config');
        if( !array_key_exists('cdn', $config) ) {
            return $filePath;
        }

        $options     = $config['cdn'];
        $cdnDomain   = self::getCdnDomain($filePath, $options);
        return self::getCdnUrl($cdnDomain, $filePath);
    }

    /**
     * Use File Extension to get the domain of CDN.
     * @param  String $filePath - the relative path of assets file path
     * @return the domain of the CDN server
     */
    public function getCdnDomainUrl($filePath)  {
        $cdnDomain   = self::getCdnDomain($filePath);
        return rtrim($cdnDomain, '/');
    }

    /**
     * Use File Extension to get the domain of CDN.
     * @param  String $filePath - the relative path of assets file path
     * @param  Array $options - CDN service settings
     * @return the domain of the CDN server
     */
    private function getCdnDomain($filePath, $options) {
        

        $assetName   = basename($filePath);
        foreach( $options as $fileExt => $cdnDomain ) {
            if( preg_match('/^.*\.('.$fileExt.')$/i', $assetName) ) {
                return $cdnDomain;
            }
        }
        $cdnDomain   = $options['default'];
        return $cdnDomain;
    }

    /**
     * Get the url of assets files.
     * @param  String $cdnDomain - the domain of CDN server
     * @param  String $filePath  - the relative path of assets file path
     * @return the url of assets file path
     */
    private function getCdnUrl($cdnDomain, $filePath) {
        return  rtrim($cdnDomain, '/') . '/' . ltrim($filePath, '/');
    }

    /**
     * 获取Http Request对象.
     * @return Http Request对象
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * HTTP Request对象.
     * @var Request
     */
    protected $request;

    /**
     * Service Locator对象.
     * @var ServiceManager
     */
    protected $serviceLocator;
}