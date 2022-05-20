<?php
declare (strict_types=1);

namespace app\index\controller;

use app\BaseController;
use Exception;
use think\exception\HttpResponseException;

class Login extends BaseController
{
    public function initialize()
    {
        // 已登录
        if (session('?user')) {
            // 抛出 http 异常
            throw new HttpResponseException(redirect('/'));
        }
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        if (request()->isPost() && request()->isAjax()) {
            // 获取数据
            $data = input();
            // 验证数据
            if (empty($data['username'])) {
                return json(['code' => 400, 'msg' => '请输入用户名']);
            }
            // 执行登录
            $token = rpcClient('Login')->login($data);
            $token = json_decode($token, true);

            if (isset($token['code']) && $token['code'] == 200) {
                // 登录成功
                session('user', [
                    'username' => $data['username'],
                    'token'    => $token['token'],
                ]);

                return json(['code' => 200, 'msg' => '登录成功']);
            } else {
                // 登录失败
                return json(['code' => 400, 'msg' => '登录失败']);
            }
        }

        return view();
    }
}
