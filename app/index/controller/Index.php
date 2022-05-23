<?php

namespace app\index\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return view();
    }

    public function logout()
    {
        session(null);

        return json(['code' => 200, 'msg' => '登出成功！', 'url' => config('sso.domain') . '/sso/login/logout?url=' . config('sso.login_url')]);
    }
}
