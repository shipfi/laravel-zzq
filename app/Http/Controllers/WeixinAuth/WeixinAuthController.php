<?php namespace App\Http\Controllers\WeixinAuth;

use App\Http\Controllers\Controller;
use App\Library\Weixin\Qiye;
use Illuminate\Http\Request;

/**
 * Created by Zhengqian.Zhu
 * Email: zhengqian@dajiayao.cc
 * Date: 15/6/4
 */
class WeixinAuthController extends Controller
{

    protected $openid;
    protected $request;
    protected $appid;
    protected $accessToken;

    public function __construct(Request $request,Qiye $qiye)
    {
        $this->request = $request;
        $this->qy = $qiye;
    }


    public function auth()
    {
        $authInput = $this->request->only('code','state');
        if (isset($authInput['code']) && $authInput['state'] == 'cy123456') {
            $accessToken = $this->qy->getAccessToken()->access_token;
            $this->accessToken = $accessToken;
            return $this->_getUserInfo($accessToken,$authInput['code']);
        }else{
            throw new \Exception("Permission denied");
        }
    }

    private function _getUserInfo($accessToken,$code)
    {
        try{
            $result = $this->qy->getUserInfoByCode($accessToken,$code);
        }catch (\Exception $e){
            echo $e->getMessage();
            exit;
        }

        $userId = $result->UserId;
        $deviceId = $result->DeviceId;
        \Session::put('user_id',$userId);
        \Session::put('device_id',$deviceId);

        $userinfo = $this->qy->getOpenIdByUserid($this->accessToken,$userId);
        $openid = $userinfo->openid;
        \Session::put('openid',$openid);

        return $this->redirectRequestUrl();
    }

    /**
     * @跳转原始的requestUrl
     * @return mixed
     * @author: zhengqian.zhu@enstar.com
     */
    public function redirectRequestUrl()
    {
        $requestUrl = \Session::get("request_url");
        if( ! $requestUrl){
//            throw new \Exception("redirect url is null");
            echo "打开的链接非法，请从小店入口打开！";
            exit;
        }

        return redirect(\Session::get("request_url"));
    }
}