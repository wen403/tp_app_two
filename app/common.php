<?php
// 应用公共文件
use Hprose\Client;

/**
 * @throws Exception
 */
function rpcClient($controller, $action)
{
    return Client::create("http://sso.test/$controller/$action/", false);
}