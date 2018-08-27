<?php

namespace DMS\Provider;

use Ws\Http\Request;
use Ws\Http\Request\Body;

class Local implements Method
{
    protected $_url;
    protected $_http;
    protected $_sign;
    protected $_headers = ['Accept' => 'application/json'];

    public function __construct($param)
    {
        $this->_url = $param["request_address"];
        $this->_sign = md5($param["access_key"] . $param["secret_key"]);
        $this->_http = Request::create();
    }

    /**
     * 对象转数组
     * @param $object
     * @return mixed
     */
    protected function objectToArray(&$object)
    {
        $object = json_decode(json_encode($object), true);
        return $object;
    }

    /**
     * 返回数据
     * @param $responseData
     * @return array
     */
    protected function returnData($responseData = "")
    {
        if (empty($responseData)) {
            $response = [
                "status" => 404,
                "message" => "服务器断开",
                "url" => ""
            ];
        } else {
            $responseBody = $this->objectToArray($responseData->body);
            $response = [
                "status" => $responseData->code,
                "message" => $responseBody["message"],
                "url" => $this->_url
            ];
            if ($responseData->code == 200) {
                $response["data"] = $responseBody["data"];
            } else {
                $response["data"] = [];
            }
        }
        return $response;
    }


    /**
     * @param $url
     * @return mixed
     */
    function httpCode($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // 关闭cURL资源，并且释放系统资源
        curl_close($ch);
        return $httpCode;
    }

    /**
     * 判断服务器状态
     * @return bool
     */
    public function checkStatus()
    {
        return $this->httpCode($this->_url) === 200 ? true : false;
    }

    /**
     * 获取路径
     * @param $param
     * @return array
     */
    public function getPath($param)
    {
        if ($this->checkStatus() !== false) {
            $response = $this->_http->post($this->_url . '/MediaController/get?sign=' . $this->_sign, $this->_headers, $param);
            return $this->returnData($response);
        } else {
            return $this->returnData();
        }
    }

    /**
     * 删除资源文件
     * @param $param
     * @return array
     */
    public function remove($param)
    {
        $response = $this->_http->post($this->_url . '/MediaController/remove?sign=' . $this->_sign, $this->_headers, $param);
        return $this->returnData($response);
    }
}