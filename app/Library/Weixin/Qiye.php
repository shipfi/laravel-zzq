<?php namespace App\Library\Weixin;

use \Requests;

class Qiye
{

    const GET_ACCESS_TOKEN = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s";

    const GET_DEPARTMENTS_LIST = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=%s&id=%s";

    const APPEND_DEPARTMENT = "https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=%s";

    const APPEND_USER = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=%s";

    const USER_LIST = "https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=%s&department_id=%s&fetch_child=%s&status=%s";

    const GET_AUTH_USER_INFO_BY_CODE = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s";

    const GET_OPENID_BY_USERID = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=%s";

    const GET_USERINFO_BY_OPOENID = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=%s';

    const GET_SUIT_TOKEN = "https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token";

    const GET_PRE_AUTHCODE = "https://qyapi.weixin.qq.com/cgi-bin/service/get_pre_auth_code?suite_access_token=%s";

    public function getAccessToken()
    {
        $corpId = config('wx_qy.corpID');
        $corpSecret = config('wx_qy.privileges.xiaoming.secret');

        $url = sprintf(self::GET_ACCESS_TOKEN,$corpId,$corpSecret);

        $result = Requests::get($url);

        if($result->status_code !== 200){
            throw new \Exception("get access token error");
        }

        return json_decode($result->body);

    }


    public function getDepartments($accessToken,$departmentId=null)
    {
        $url = sprintf(self::GET_DEPARTMENTS_LIST,$accessToken,$departmentId);
        $result = Requests::get($url);

        if($result->status_code !== 200){
            throw new \Exception("get access token error");
        }

        $body = json_decode($result->body);
        if($body->errcode != '0'){
            throw new \Exception("get access token error");
        }

        return $body->department;
    }

    public function appendDepartment($accessToken,$name,$parentId,$order=null,$id=null)
    {
        $options = [
            'name'=>$name,
            'parentid'=>$parentId,
            'order'=>$order,
            'id'=>$id
        ];
        $url = sprintf(self::APPEND_DEPARTMENT,$accessToken);
        $result = Requests::post($url,array(),json_encode($options));
        $body = json_decode($result->body);
        if($body->errcode != '0'){
            throw new \Exception("append error");
        }
        return true;
    }



    public function createUser($accessToken,$userId,$name,$department,$weixinId,$gender='',$position=null,$mobile=null,$email=null,$avatarMediaid=null,$extattr=null)
    {
        $url = sprintf(self::APPEND_USER,$accessToken);
        $options = [
            "userid"=>$userId,
            "name"=>$name,
            "department" => $department,
            "position"=>$position,
            "mobile" => $mobile,
            "gender" => $gender,
            "email" => $email,
            "weixinid" => $weixinId,
            "avatar_mediaid" => $avatarMediaid,
            "extattr" => $extattr
        ];
//        print_r($options);exit;

        $result = Requests::post($url,array(),json_encode($options));
        $body = json_decode($result->body);
        if($body->errcode != '0'){
            throw new \Exception("append error");
        }
        return true;

    }


    public function userList($accessToken,$departmentId)
    {
        $url = sprintf(self::USER_LIST,$accessToken,$departmentId,0,0);
        $result = Requests::get($url);

        $body = json_decode($result->body);

        if($body->errcode != '0'){
            throw new \Exception("user list error");
        }

        return $body->userlist;
    }


    public function getUserInfoByCode($accessToken,$code)
    {
        $url =  sprintf(self::GET_AUTH_USER_INFO_BY_CODE,$accessToken,$code);
        $result = Requests::get($url);

        return json_decode($result->body);
    }

    public function getOpenIdByUserid($accessToken,$userid,$agentid=null)
    {
        $url = sprintf(self::GET_OPENID_BY_USERID,$accessToken);

        $options = [
            "userid" => $userid,
            "agentid" => $agentid
        ];

        $result = Requests::post($url,array(),json_encode($options));

        $body = json_decode($result->body);
        if($body->errcode != '0'){
            throw new \Exception("append error");
        }

        return $body;
    }

    /**
     * @对外接口，获取用户信息
     * @param null
     * @return mixed
     */
    public function getUserInfo($accessToken,$openid,$lang="zh_CN")
    {
        $api = sprintf(self::GET_USERINFO_BY_OPOENID,$accessToken,$openid,$lang);
        $result = Requests::get($api);
        print_r($result);exit;
        $body = json_decode($result->body);
        if($body->errcode != '0'){
            throw new \Exception("get user info error");
        }

        return $body;
    }


    /**
     * 返回套件令牌
     * @param $suidId
     * @param $suidSec
     * @param $suitTicket
     * @return mixed
     * @throws \Exception
     * @author zhengqian@dajiayao.cc
     */
    public function getSuitToken($suidId,$suidSec,$suitTicket)
    {
        $api = self::GET_SUIT_TOKEN;
        $data = [
            'suite_id'=>$suidId,
            'suite_secret'=>$suidSec,
            'suite_ticket'=>$suitTicket
        ];
        $result = Requests::post($api,[],$data);
        $body = json_decode($result->body);
        if($body->errcode != '0'){
            throw new \Exception("append error");
        }

        return $body;

    }


    /**
     * 获取预授权码
     * @param $suitAccessToken
     * @param $suitId
     * @param array $appids
     * @return mixed
     * @throws \Exception
     * @author zhengqian@dajiayao.cc
     */
    public function getPreAuthCode($suitAccessToken,$suitId,array $appids)
    {

        $api = sprintf(self::GET_PRE_AUTHCODE,$suitAccessToken);

        $data = [
            'suite_id'=>$suitId,
            'appid'=>$appids
        ];
        $result = Requests::post($api,[],$data);
        $body = json_decode($result->body);
        if($body->errcode != '0'){
            throw new \Exception("append error");
        }

        return $body;
    }


}
