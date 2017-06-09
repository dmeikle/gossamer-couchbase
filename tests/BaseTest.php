<?php

namespace tests;

use Gossamer\Core\Datasources\DBConnection;
use Gossamer\Core\Datasources\DBConnectionAdapter;
use Gossamer\Core\Datasources\RestDataSource;
use Gossamer\Core\Http\HttpRequest;
use Gossamer\Core\Http\HttpResponse;
use Gossamer\Core\Http\RequestParams;
use Gossamer\Core\System\SiteParams;
use core\views\JSONView;
use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Neith\Logging\MonologLogger;
use Gossamer\Pesedget\Database\DatasourceFactory;
use Gossamer\Set\Utils\Container;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Gossamer\Pesedget\Entities\EntityManager;


class BaseTest extends \PHPUnit_Framework_TestCase {

    const GET = 'GET';
    const POST = 'POST';

    private $container = null;

    protected function getHttpRequest($uri, $requestParams = '', $requestMethod) {
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';

        if($requestMethod == 'POST') {
            parse_str($requestParams, $_POST);
            $_SERVER['QUERY_STRING'] ='';
        }else{
            $_SERVER['QUERY_STRING'] = $requestParams;
        }
        $this->setURI($uri);
        $request = new HttpRequest($this->getRequestParams(), $this->getSiteParams());

        return $request;
    }

    protected function getSiteParams() {
        $params = new SiteParams();
        $params->setConfigPath('/var/www/binghan/rest4/app/config/');

        return $params;
    }

    public function getRequestParams() {
        $requestParams = new \Gossamer\Core\Http\RequestParams();

        $requestParams->setHeaders(getallheaders());
       // $requestParams->setPost($_POST);
       // $requestParams->setQuerystring($_GET);
       // $requestParams->setServer($_SERVER);
        $requestParams->setLayoutType('CLI');
        $requestParams->setMethod('GET');

        return $requestParams;
    }

    protected function getHttpResponse() {
        return new HttpResponse();
    }

    protected function getLogger() {

        $logger = new MonologLogger('phpUnitTest');
        $logger->pushHandler(new StreamHandler(__SITE_PATH . "/../logs/phpunit.log", Logger::DEBUG));


        return $logger;
    }

    public function setRequestMethod($method) {
        define("__REQUEST_METHOD", $method);
    }

    public function setURI($uri) {
        if(!defined('__URI')) {
            define('__URI', $uri);
        }
        if(!defined('__REQUEST_URI')) {
            define("__REQUEST_URI", $uri . '/');
        }

    }

//    public function getDBConnection() {
//
//        $conn = new \Gossamer\Pesedget\Database\DBConnection($this->getCredentials());
//
//        return $conn;
//    }


protected function getDatasourceFactory() {
    $df = new DatasourceFactory();
    
}
    protected function getRestConnection($datasourceKey){

        $rest = new RestDataSource($this->getLogger());
        $rest->setDatasourceKey($datasourceKey);
        return $rest;
    }

    protected function getCredentials() {
        $credentials = array();
        $credentials['baseUrl'] = 'http://127.0.0.1:8060';
        $credentials['format'] = 'json';
        $credentials['headers']['serverName'] = 'vancouver';
        $credentials['headers']['serverAuth'] = '$1$lIDKkGiyJVn2bZSQdxwEYW0';
        
        return $credentials;
    }
    
    

    protected function getContainer($httpRequest, $httpResponse, $requestMethod = 'GET', $ymlKey = 'phpunit_test') {
        if(is_null($this->container )) {
            $this->container = new Container();
            $entityManager = new EntityManager($this->getDBCredentialsFile());
            //instantiate the database entity manager
            $this->container->set('EntityManager',  $entityManager);

            $eventDispatcher = new EventDispatcher($this->getLogger(), $httpRequest, $httpResponse, $requestMethod, $ymlKey );
            $this->container->set('EventDispatcher', $eventDispatcher);
            $logger= $this->getLogger();
            $this->container->set('Logger', $logger);
            $this->container->set('HttpRequest', $httpRequest);
        }

        return $this->container;
    }

    protected function getView($ymlKey, $request, $response) {
        $array = array();
        $logger = $this->getLogger();

        $view = new PHPUnitView($logger,$ymlKey,$array,$request, $response);
        $view->setContainer($this->getContainer($request,$response));

        return $view;
    }
    
    private function getDBCredentialsFile() {
        return array(
            'datasource1' => array(
                'class' => 'Gossamer\Nephthys\Rest\GenericRestClient',
                'credentials' => array(
                    'baseUrl' => 'http://127.0.0.1:8060',
                    'format' => 'json',
                    'headers' => array(
                        'serverName' => 'vancouver',
                        'serverAuth' => '$1$lIDKkGiyJVn2bZSQdxwEYW0'
                    )
                )
            ),
            'default' => 'datasource1'
            );    
    
    } 

}
