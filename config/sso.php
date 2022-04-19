<?php

return [
    // sso 地址
    'domain'    => 'http://sso.test/login/index',
    // 自己的登录地址
    'login_url' => request()->domain() . '/login/index',
];