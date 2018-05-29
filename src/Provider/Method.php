<?php
namespace DMS\Provider;

interface Method
{
    function checkStatus();

    function uploadImage($param);

    function uploadVideo($param);

    function getPath($param);

    function remove($param);
}