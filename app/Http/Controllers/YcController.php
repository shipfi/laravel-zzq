<?php namespace App\Http\Controllers;

use App\Library\Weixin\Qiye;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class YcController extends Controller
{

    const SUIT_TICKET_KEY = "suit-ticket";

    const SUIT_ACCESSTOKEN_KEY = 'suit-access-token';

    const SUIT_PRE_AUTH_KEY = 'suit-pre-auth-code';

    const SUIT_ID = "tj92b18aa012990bdf";

    const TOKEN = "yrKNqJnhV97JnoRtB4YNYE";

    const ASE_KEY = "fJPcFUigbfMHevAwSr3a9xg8Of4Pl3l6PgjGsI3x8w1";

    const CORP_ID = 'wxf10574ad995ce8b5';

    const SUIT_SEC = "b7skTTjjvlGjP9C9qvTlsUaJCnoR4XyuNrs4df0q5I6p3-Ib3SZh2_y-rj8bLfEY";

    public function __construct(Qiye $qiye,Request $request)
    {
        $this->qy = $qiye;
        $this->request = $request;

        try{
            $accessToken = $qiye->getAccessToken();
        }catch (\Exception $e){
            echo $e->getMessage();
            exit;
        }

        $this->accessToken = $accessToken->access_token;
    }


    public function index()
    {
        $x = new \WXBizMsgCrypt(1,1,1);exit;
        $redis = Redis::connection();
        $redis->set("name",'zzz');
    }
    public function departmentsCreate()
    {

        $departments = $this->qy->getDepartments($this->accessToken);
        return view('department/create')->with('departments',$departments);

    }


    public function postDepartmentCreate()
    {
        $input = $this->request->all();
        $name = $input['name'];
        $parentId = $input['parent'];
        $result = $this->qy->appendDepartment($this->accessToken,$name,$parentId);
        if($result){
            echo "success";
        }else{
            echo "error";
        }
    }


    public function departmentsList()
    {


        try{
            $departments = $this->qy->getDepartments($this->accessToken);
        }catch (\Exception $e){
            echo $e->getMessage();
        }



        return view("department/index")->with('departments',$departments);
    }



    public function userList($departMentId)
    {
        $list = $this->qy->userList($this->accessToken,$departMentId);

        return view('department/user_index')->with('users',$list);
    }



    public function userCreate()
    {
        $departments = $this->qy->getDepartments($this->accessToken);
        return view('department/user_create')->with('departments',$departments);
    }

    public function postUserCreate()
    {
        $input = $this->request->all();
        $name = $input['name'];
        $userId = $input['user_id'];
        $wxId = $input['wx_id'];
        $departmentId = $input['department_id'];
        $gender = $input['gender'];

        $resutl = $this->qy->createUser($this->accessToken,$userId,$name,(int)$departmentId,$wxId,$gender);
        if($resutl){
            echo "success";
        }else{
            echo "fail";
        }
    }


    public function menuCreate()
    {


    }

    public function menuDestroy()
    {


    }


    public function identity()
    {

        echo  "opendId:".\Session::get('openid')."<br/>";
        echo  "deviceId:".\Session::get('device_id')."<br>";


    }

    /**
     * @身份授权
     * @author zhengqian@dajiayao.cc
     */
    public function authorization()
    {
        $suitTickt = $this->getSuitTicketFromRedis(self::SUIT_TICKET_KEY);
        if ( ! $suitTickt){
            throw new \Exception('get suit ticket from redis error');
        }
        try{
            $body = $this->qy->getSuitToken(self::SUIT_ID,self::SUIT_SEC,$suitTickt);
        }catch (\Exception $e){
            echo "get suit token".$e->getMessage();
            exit;
        }
        $suite_access_token = $body->suite_access_token;

        $this->setSuitTicketInRedis(self::SUIT_ACCESSTOKEN_KEY,$suite_access_token);


        if( ! $suite_access_token){
            exit("get suit access token error");
        }

        $body = $this->qy->getPreAuthCode($suite_access_token,self::SUIT_ID,[]);

        $preAuthCode = $body->pre_auth_code;

        if( ! $preAuthCode)
        {
            exit('get pre_auth_code error');
        }
        return view('authorization')->with('suit_ticket',$suitTickt)->with('pre_auth_code',$preAuthCode);
    }

    /**
     * event
     *
     */
    public function event()
    {

        \Log::info($this->request->getRequestUri());


        // $sReqMsgSig = HttpUtils.ParseUrl("msg_signature");
        $sReqMsgSig = $this->request->get('msg_signature');
// $sReqTimeStamp = HttpUtils.ParseUrl("timestamp");
        $sReqTimeStamp = $this->request->get('timestamp');
// $sReqNonce = HttpUtils.ParseUrl("nonce");
        $sReqNonce = $this->request->get('nonce');
// post请求的密文数据
// $sReqData = HttpUtils.PostData();
        \Log::info($sReqMsgSig);
        \Log::info($sReqTimeStamp);
        \Log::info($sReqNonce);
        $sReqData = (string)file_get_contents("php://input");

        \Log::info($sReqData);
        // 假设企业号在公众平台上设置的参数如下
        $encodingAesKey = self::ASE_KEY;
        $token = self::TOKEN;
        $corpId = self::CORP_ID;
        \Log::info($corpId);
        $wxcpt = new \WXBizMsgCrypt($token, $encodingAesKey, $corpId);

        $sMsg = "";  // 解析之后的明文
        $errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
        if ($errCode == 0) {
            // 解密成功，sMsg即为xml格式的明文
            // TODO: 对明文的处理
            // For example:
            $xml = new \DOMDocument();
            $xml->loadXML($sMsg);
            $content = $xml->getElementsByTagName('SuiteTicket')->item(0)->nodeValue;
            \Log::info("content: " . $content . "\n\n");
            // ...
            // ...
        } else {
            \Log::error("ERR: " . $errCode . "\n\n");
            //exit(-1);
        }


        $this->setSuitTicketInRedis(self::SUIT_TICKET_KEY,$content);
    }

    public function setSuitTicketInRedis($key,$ticket)
    {
        try{
            $redis = Redis::connection();
        }catch (\Exception $e){
            \Log::info(sprintf("set ticket into redis error,"));
        }


        return $redis->set($key,$ticket);
    }



    public function getSuitTicketFromRedis($key)
    {
        try{
            $redis = Redis::connection();
        }catch (\Exception $e){
            \Log::info(sprintf("set ticket into redis error,"));
        }


        return $redis->get($key);
    }


    /**
     * 授权毁掉页面
     * @author zhengqian@dajiayao.cc
     */
    public function authCallBack()
    {
        $authCode = $this->request->get('auth_code');

        $body = $this->qy->getPermanentCode($this->getSuitTicketFromRedis(self::SUIT_ACCESSTOKEN_KEY),self::SUIT_ID,$authCode);


        //设置菜单

        echo json_encode($body);
    }




}