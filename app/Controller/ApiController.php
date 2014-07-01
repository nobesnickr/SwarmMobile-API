<?php

require_once __DIR__ . '/Component/APIComponent.php';
//require_once __DIR__ . '/Component/ConsumerAPIComponent.php';
require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('SettingComponent', 'Controller/Component');
App::uses('OAuthComponent', 'OAuth.Controller/Component');
App::uses('AppController', 'Controller');
App::uses('Model', 'Model');
App::uses('RequestModel', 'Model');

App::uses('Location', 'Model');
App::uses('Setting', 'Model');
App::uses('SettingGroup', 'Model');

//ONE for every component that extends from APIComponent
App::uses('PortalComponent', 'Controller/Component');
App::uses('ConsumerComponent', 'Controller/Component');
App::uses('NetworkComponent', 'Controller/Component');
App::uses('OAuthClientComponent', 'Controller/Component');
App::uses('RollupComponent', 'Controller/Component');
App::uses('AnalyticsComponent', 'Controller/Component');

App::uses('UserComponent', 'Controller/Component');
App::uses('CouponComponent', 'Controller/Component');
App::uses('CampaignComponent', 'Controller/Component');
App::uses('LocationComponent', 'Controller/Component');

class APIController extends AppController {

    public $default_cache_time = 300;
    public $cache_time_exceptions = array();
    public $uses = array('Inbox');
    public $debug = false;
    public $cache = true;
    public $rollups = true;
    public $user = array('id_user' => 0, 'username' => '');
    public $endpoint = '';
    public $request_start = 0;
    public $request_end = 0;
    public $microtime = 0;
    public $response_code = 200;
    public $response_message = 'OK';
    public $params = array();
    public $helpers = array('Html', 'Session');

    // Controller Actions
    public function logout() {
        $this->Session->destroy('User');
        $this->redirect(Router::url('/login'));
    }

    public function login() {
        if ($this->request->is('post')) {
            $this->Session->destroy('User');
            $redirect = $this->request->data['redirect'];
            if ($this->Auth->login()) {
                if (empty($redirect)) {
                    $res = array();
                    $res['location_id'] = $this->Session->read("Auth.User.location_id");
                    $res['username'] = $this->Session->read("Auth.User.username");
                    $res['uuid'] = $this->Session->read("Auth.User.uuid");
                    echo json_encode($res);
                    $this->call_log();
                    exit();
                } else {
                    $this->redirect($redirect);
                }
            } else {
                if (empty($redirect)) {
                    $e = new APIException(401, 'authentication_failed', 'Supplied credentials are invalid');
                    $this->response_code = $e->error_no;
                    $this->response_message = $e->error;
                    $this->call_log();
                    $e->_displayError();
                    return false;
                }
            }
        }
    }

    public function request_client() {
        $data = array(
            'username' => $this->data['username'],
            'redirect_uri' => $this->data['redirect_uri'],
            'description' => $this->data['description']
        );
        $this->Inbox->save($data);
    }

    public function index() {
        $env = getenv('server_location');
        $this->debug = ($env != 'live');
        set_time_limit(3600);
        $this->microtime = microtime(true);
        $this->request_start = date('Y-m-d H:i:s');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Access-Control-Max-Age: 1728000");
        header("Content-Type: application/json; charset=UTF-8");
        try {
            if ($this->request->is('get')) {
                $params = $_GET;
                $this->processGET($params);
            } elseif ($this->request->is('post')) {
                $params = $_POST;
                $this->processPOST($params);
            } else {
                throw new APIException(401, 'invalid_grant', "Method Type Requested aren't granted with your access_token");
            }

            $path = func_get_args();
            $this->params = $params;
            if (!isset($path[1])) {
                $path[1] = '';
            }
            $this->endpoint = $path[0] . '/' . $path[1];
            echo json_encode($this->internalCall($path[0], $path[1], $params));
            $this->call_log();
            exit();
        } catch (OAuth2AuthenticateException $e) {
            $this->response_code = $e->getCode();
            $this->response_message = $e->getMessage();
            $this->call_log();
            $e->sendHttpResponse();
            return false;
        } catch (APIException $e) {
            $this->response_code = $e->error_no;
            $this->response_message = $e->error;
            $this->call_log();
            $e->_displayError();
            return false;
        }
    }

    // Internal functions
    private function call_log() {
        $this->request_end = date('Y-m-d H:i:s');
        $oModel = new Model(false, 'calls', 'mongodb');
        $call = array(
            'id_user' => $this->user['id_user'],
            'username' => $this->user['username'],
            'endpoint' => $this->endpoint,
            'request_start' => $this->request_start,
            'request_end' => $this->request_end,
            'response_time' => microtime(true) - $this->microtime,
            'response_code' => $this->response_code,
            'response_message' => $this->response_message,
            'params' => $this->params
        );
        $oModel->save($call);
    }

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        if (!empty($this->request)) {
            if ($request->is('post')) {
                $this->rollups = false;
                $this->cache = false;
            } elseif ($this->request->is('get')) {
                if (isset($_GET['norollups'])) {
                    $norollups = in_array($_GET['norollups'], ['1', 1, 'yes', true], true);
                    $this->rollups = !$norollups;
                }
                if (isset($_GET['nocache'])) {
                    $nocache = in_array($_GET['nocache'], ['1', 1, 'yes', true], true);
                    $this->cache = !$nocache;
                }
            }
        }
    }

    public function processGET($params = array()) {
        if (!$this->debug) {
            $this->user = $this->authenticate($params['access_token']);
        }
        return true;
    }

    public function processPOST($params = array()) {
        if (!$this->debug) {
            $this->user = $this->authenticate($params['access_token']);
        }
        $this->cache = false;
        $this->rollups = false;
        return true;
    }

    public function internalCall($component, $method, $params) {
        unset($params['access_token']);
        unset($params['norollups']);
        unset($params['nocache']);
        $classname = ucfirst($component) . 'Component';
        if (class_exists($classname)) {
            $oComponent = new $classname($this->request, $this->cache, $this->rollups);
            $result = $this->getPreviousResult($component, $method, $params);
            if ($result === false) {
                $result = $oComponent->$method($params);
                $this->cache($component, $method, $params, $result);
            }
            return $result;
        }
        throw new APIException(404, 'endpoint_not_found', "The requested reference type don't exists");
    }

    private function getPreviousResult($component, $method, $params) {
        unset($params['access_token']);
        unset($params['norollups']);
        unset($params['nocache']);
        unset($params['rollup']);
        /*
        if ($this->cache) {
            $filename = $this->getCacheFilePath($component, $method, $params);
            if (file_exists($filename)) {
                $cache_time = (isset($this->cache_time_exceptions[$component][$method])) ? $this->cache_time_exceptions[$component][$method] : $this->default_cache_time;
                if (time() - filemtime($filename) <= $cache_time) {
                    include $filename;
                    return $result;
                }
            }
        }         
         */
        if ($this->rollups && $component == 'location') {
            $oModel = new Model(false, 'cache', 'mongodb');
            $conditions = array("params" => array());
            foreach ($params as $k => $v) {
                $conditions['params'][$k] = $v;
            }
            $conditions['params']['endpoint'] = $component . '/' . $method;
            $aRes = $oModel->find('first', array('conditions' => $conditions, 'order' => array('_id' => -1)));
            if (isset($aRes['Model'])) {
                unset($aRes['Model']['id']);
                unset($aRes['Model']['params']);
                unset($aRes['Model']['modified']);
                unset($aRes['Model']['created']);
                $this->cache($component, $method, $params, $aRes['Model'], true);
                return $aRes['Model'];
            }
        }
        return false;
    }

    private function getCacheFilePath($component, $method, $params) {
        $component = strtolower($component);
        $method = strtolower($method);
        $path = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls' . DS . $component . DS . $method;
        $tmp = '';
        foreach ($params as $k => $v) {
            $tmp .= $k . ':' . $v;
        }
        return $path . DS . md5($tmp) . '.cache';
    }

    private function createCacheFolders($component, $method) {
        $component = strtolower($component);
        $method = strtolower($method);
        $path = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls';
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path = $path . DS . $component;
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path .= DS . $method;
        if (!file_exists($path)) {
            mkdir($path);
        }
    }

    public function authenticate($accessToken = '') {
        $oOAuth = new OAuthComponent(new ComponentCollection());
        $oOAuth->OAuth2->verifyAccessToken($accessToken);
        return $oOAuth->user();
    }

    private function cache($component, $method, $params, $result, $from_mongo = false) {
        if (!empty($result) && $component . '/' . $method != 'location/data') {
            unset($params['access_token']);
            unset($params['norollups']);
            unset($params['nocache']);
            unset($params['rollup']);
            if (
                isset($params['start_date']) &&
                isset($params['end_date']) &&
                $params['start_date'] != $params['end_date']
            ) {
                return;
            }
            /*
            if ($this->cache) {
                  $this->createCacheFolders($component, $method);
                  $cache_file = $this->getCacheFilePath($component, $method, $params);
                  $handle = fopen($cache_file, 'w+');
                  fwrite($handle, '<?php $result = ' . var_export($result, true) . ';?>');
                  fclose($handle);
            }             
             */
            if ($this->rollups) {
                if (!$from_mongo && $component == 'location' && $method != 'data') {
                    $oModel = new Model(false, 'cache', 'mongodb');
                    $result['params'] = array();
                    $conditions = array("params" => array());
                    foreach ($params as $k => $v) {
                        $result['params'][$k] = $v;
                        $conditions['params'][$k] = $v;
                    }
                    $result['params']['endpoint'] = $component . '/' . $method;
                    $conditions['params']['endpoint'] = $component . '/' . $method;
                    $aRes = $oModel->find('first', array('conditions' => $conditions, 'order' => array('_id' => -1)));
                    if (empty($aRes)) {
                        $oModel->save($result);
                    } else {
                        //throw new APIException(500, 'duplicated_cache', "This request is already cached");
                    }
                }
            }
        }
    }

}

class APIException extends Exception {

    public $error_no;
    public $error;
    public $description;

    public function __construct($error_no, $error, $description) {
        parent::__construct($error_no);
        $this->error_no = $error_no;
        $this->error = $error;
        $this->description = $description;
    }

    public function _displayError() {
        header("Cache-Control: no-store");
        header("HTTP/1.1 {$this->error_no}");
        echo json_encode(
                array(
                    'error' => $this->error,
                    'error_description' => $this->description
                )
        );
        die();
    }

}
