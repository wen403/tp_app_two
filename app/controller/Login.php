<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;

class Login extends BaseController
{
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
            $token = rpcClient('login', 'login')->login($data);
            $token = json_decode($token, true);

            if (isset($token['code']) && $token['code'] == 200) {
                // 登录成功
                session('user', [
                    'username' => $data['username'],
                    'token'    => $token['token'],
                ]);

                // 去 sso 验证这个 token 是否有效
                $loginUrl = config('sso.domain') . '?token=' . $token['token'] . '&url=' . request()->domain();

                return json(['code' => 200, 'msg' => '登录成功', 'url' => $loginUrl]);
            } else {
                // 登录失败
                return json(['code' => 400, 'msg' => '登录失败']);
            }
        }

        return view();
    }
}
