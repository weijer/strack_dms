<?php
namespace DMS\Provider;

interface Method
{
    function checkStatus();

    function getPath($param);

    function remove($param);
}