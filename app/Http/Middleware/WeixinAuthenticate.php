<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use \Config;


class WeixinAuthenticate {

    const SNSAPI_BASE = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=%s#wechat_redirect";


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


            $openid = Session::get('openid');
            if( ! $openid){
                \Log::info("not found openid in session");
                $appid = Config::get("weixin.seller.appid");

                Session::put('request_url',$request->getUri());
                $authUrl = route("wx-auth");
                \Log::info($authUrl);
                $snsapi_base = sprintf(self::SNSAPI_BASE,config('wx_qy.corpID'),urlencode($authUrl),'cy123456');
                return redirect($snsapi_base);
            }
            \Log::info("success found openid in session");
        return $next($request);
    }

}
