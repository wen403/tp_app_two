<?php
declare (strict_types=1);

namespace app\middleware;

use app\Request;
use Closure;
use think\Exception;

class Auth
{
    /**
     * 处理请求
     */
    public function handle(Request $request, Closure $next)
    {
        // 是否携带 token
        if (input('?token')) {
            $result = rpcClient('login', 'decodeJWT')->decodeJWT(input('token'));
            $result = json_decode($result, true);
            if ($result['code'] === 200) {
                session('user', [
                    'username' => $result['data']['username'],
                    'token'    => $result['data']['token'],
                ]);
                return redirect('/');
            } else {
                throw new Exception('token 验证失败');
            }
        }

        if ($_SERVER['REQUEST_URI'] === '/login/index' && (\request()->method() === 'POST' || session('?sso'))) {
            session('sso', null);
            return $next($request);
        }

        // 检测到用户没有登录
        $loginUrl = config('sso.login_url');

        if (!session('?user') && !session('?sso')) {
            // 前往 sso 服务器登录
            session('sso', 1);
            return redirect(config('sso.domain') . '?url=' . urlencode($loginUrl));
        }

        return $next($request);

    }
}
