<?php
// 应用公共文件
use Hprose\Client;

/**
 * @throws Exception
 */
function rpcClient($controller)
{
    return Client::create(config('rpc.sso') . '?c=' . $controller, false);
}