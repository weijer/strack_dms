<?php
namespace DMS\Provider;

use Ws\Http\Request;
use Ws\Http\Request\Body;
use JJG\Ping;

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
    protected function objectToArray(&$object) {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
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
     * 判断服务器状态
     * @return mixed
     * @throws \Exception
     * @throws \JJG\InvalidArgumentException
     */
    public function checkStatus()
    {
        $replaceHttp = preg_replace('/(https|http|ftp|rtsp|mms)?:\/\//', '', $this->_url);
        $replacePort = preg_replace('/:[0-9]+/', '', $replaceHttp);
        $ping = new Ping($replacePort);
        return $ping->ping();
    }

    /**
     * 上传图片
     * @param $param
     * @return array
     */
    public function uploadImage($param)
    {
        $body = Body::multipart($param["data"], $param["file"]);
        $response = $this->_http->post($this->_url . '/convert_image/?sign=' . $this->_sign, $this->_headers, $body);
        return $this->returnData($response);
    }

    /**
     * 上传视频
     * @param $param
     * @return array
     */
    public function uploadVideo($param)
    {
        $body = Body::multipart($param["data"], $param["file"]);
        $response = $this->_http->post($this->_url . '/convert_video/?sign=' . $this->_sign, $this->_headers, $body);
        return $this->returnData($response);
    }

    /**
     * 获取路径
     * @param $param
     * @return array
     * @throws \Exception
     * @throws \JJG\InvalidArgumentException
     */
    public function getPath($param)
    {
        if ($this->checkStatus() !== false) {
            $response = $this->_http->post($this->_url . '/get_file_path?sign=' . $this->_sign, $this->_headers, $param);
            return $this->returnData($response);
        } else {
            return $this->returnData();
        }
    }

    /**
     * 获取视频队列
     * @param array $param
     * @return array
     * @throws \Exception
     * @throws \JJG\InvalidArgumentException
     */
    public function getVideoQueue($param = [])
    {
        if ($this->checkStatus() !== false) {
            $response = $this->_http->post($this->_url . '/get_queue_video?sign=' . $this->_sign, $this->_headers, $param);
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
        $response = $this->_http->post($this->_url . '/remove_file?sign=' . $this->_sign, $this->_headers, $param);
        return $this->returnData($response);
    }

    /**
     * 重新提交转码
     * @param $param
     * @return array
     */
    public function resubmitQueue($param)
    {
        $response = $this->_http->post($this->_url . '/requeue?sign=' . $this->_sign, $this->_headers, $param);
        return $this->returnData($response);
    }


}