<?php
declare (strict_types=1);

namespace app\index\middleware;

use app\Request;
use Closure;
use think\Exception;

class Auth
{
    /**
     * 处理请求
     * @throws /Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $param = $request->only([
            'token' => null,
        ], 'param', 'trim');

        // 验证是否登录
        if (session('?user')) {
            return $next($request);
        }

        // 携带了 token
        if (!empty($param['token'])) {
            $result = rpcClient('Login')->validateToken($param['token']);
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

        // 本机登录地址
        $loginUrl = config('sso.login_url');

        // 登录页面放行
        if ($request->domain() . $request->url() === $loginUrl && (request()->method() === 'POST' || session('?sso'))) {
            session('sso', null);
            return $next($request);
        }

        // 检测到用户没有登录
        // 如果 session 中没有 user 和 sso 就代表没有登录
        if (!session('?user') || !session('?sso')) {
            // 前往 sso 服务器登录
            session('sso', 1);
            return redirect(config('sso.domain') . '?url=' . urlencode($loginUrl));
        }

        return $next($request);

    }
}
