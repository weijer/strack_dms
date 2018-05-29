<?php
namespace DMS;

use DMS\Provider\Local;

class Server
{
    //服务商
    protected $_service;

    public function __construct($cndSetting)
    {
        switch ($cndSetting["provider"]) {
            case "local":
                //本地CDN
                $this->_service = new Local($cndSetting);
                break;
        }
    }

    /**
     * 上传图片
     * @param $param
     * @return array
     */
    public function uploadImage($param)
    {
        return $this->_service->uploadImage($param);
    }

    /**
     * 上传视频
     * @param $param
     * @return array
     */
    public function uploadVideo($param)
    {
        return $this->_service->uploadVideo($param);
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
        return $this->_service->getPath($param);
    }

    /**
     * 删除资源文件
     * @param $param
     * @return array
     */
    public function remove($param)
    {
        return $this->_service->remove($param);
    }
}
