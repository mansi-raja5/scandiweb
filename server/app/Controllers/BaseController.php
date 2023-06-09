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

        if (!isset($headers['auth_key'])) {
            echo json_encode(['status' => 402, 'msg' => 'Auth_key is not present']);
            header('HTTP/1.1 402 Auth_key is not present');
            die;
        }

        $this->apiUsersModel->setAuthKey($headers['auth_key']);
        if (!$this->apiUsersModel->verifyAuthKey()) {
            echo json_encode(['status' => 401, 'msg' => 'Unauthorized Key Used']);
            header('HTTP/1.1 401 Unauthorized Key Used');
            die;
        }
        return true;
    }

    private function checkHeaders($requestMethod)
    {
        //Validating request
        if ($_SERVER['REQUEST_METHOD'] !== $requestMethod) {
            header('HTTP/1.1 400 Request Method is Not Valid');
            die;
        }

        //print_r($_SERVER);exit;
        
        // Validating Content type
        /*if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            header('HTTP/1.1 204 Content type not valid');
            die;
        }*/
    }

    protected function validateParameters($param)
    {
        if (empty($param)) {
            throw new \Exception("{$param} are missing", 402);
        }
        return $param;
    }
}
