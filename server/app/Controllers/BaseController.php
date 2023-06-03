<?php

namespace App\Controllers;

use App\Models\ApiUsersModel;

class BaseController
{
    /**
     * Summary of apiUsersModel
     * @var 
     */
    private $apiUsersModel;
    public function __construct()
    {
        $this->apiUsersModel = new ApiUsersModel();
    }

    /** 
     * __call magic method. 
     */
    /*public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found222'));
    }*/
    /** 
     * Get URI elements. 
     * 
     * @return array 
     */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);
        return $uri;
    }
    /** 
     * Get querystring params. 
     * 
     * @return array 
     */
    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }
    /** 
     * Send API output. 
     * 
     * @param mixed $data 
     * @param string $httpHeader 
     */
    protected function sendOutput($data, $httpHeaders = array())
    {
        header_remove('Set-Cookie');
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }
    /** 
     * check API auth
     * 
     * @param mixed $data 
     * @param string $httpHeader 
     */
    protected function checkApiAuth($requestMethod)
    {
        $this->checkHeaders($requestMethod);
        $headers = apache_request_headers();

        if (!isset($headers['Auth_Key'])) {
            echo json_encode(['status' => 402, 'msg' => 'Auth_key is not present']);
            die(header('HTTP/1.1 402 Auth_key is not present'));
        }

        $this->apiUsersModel->setAuthKey($headers['Auth_Key']);
        if ($this->apiUsersModel->verifyAuthKey() != TRUE) {
            echo json_encode(['status' => 401, 'msg' => 'Unauthorized Key Used']);
            die(header('HTTP/1.1 401 Unauthorized Key Used'));
        }
        return true;
    }

    private function checkHeaders($requestMethod)
    {
        //Validating request
        if ($_SERVER['REQUEST_METHOD'] !== $requestMethod) {
            die(header('HTTP/1.1 400 Request Method is Not Valid'));
        }

        // Validating Content type
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            die(header('HTTP/1.1 204 Content type not valid'));
        }
    }

    protected function validateParameters($param)
    {
        if (empty($param)) {
            throw new \Exception("{$param} are missing", 402);
        }
        return $param;
    }
}
