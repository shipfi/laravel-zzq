<?php namespace App\Http\Controllers;

use App\Library\Weixin\Qiye;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class YcController extends Controller
{

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
        return view('authorization');
    }

    /**
     * event
     *
     */
    public function event()
    {

//        \Log::info(file_get_contents("php://input"));

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
        $encodingAesKey = "fJPcFUigbfMHevAwSr3a9xg8Of4Pl3l6PgjGsI3x8w1";
        $token = "yrKNqJnhV97JnoRtB4YNYE";
        $corpId = "wxf10574ad995ce8b5";
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
            $content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;
            \Log::info("content: " . $content . "\n\n");
            // ...
            // ...
        } else {
            \Log::error("ERR: " . $errCode . "\n\n");
            //exit(-1);
        }
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



}