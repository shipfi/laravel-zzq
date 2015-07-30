<?php namespace App\Http\Controllers;

use App\Library\Weixin\Qiye;
use Illuminate\Http\Request;


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

//
//        $xml = "<xml><ToUserName><![CDATA[tj92b18aa012990bdf]]></ToUserName>
//<Encrypt><![CDATA[emyaw9cS6ic7paSb7NvkiDNmVOXwtS/tz7IrnxXay+e3P7FRw1HD17CHNqJGZNMxJGAy9FAz0LQ69WpjG+RhXOk5vVpaBj76/lwL2JimdMzXSwyYAzXWNpoBS/Fn1b0YxH22n5i8H0zW0QzCMCtsx3bixeRWQog2zWM4LiPxUJkhv+9QOjC2HvJl930N0t3d80SSlyKQFtMD9UHOlJLsC6IEuR/QdT9Ws+1/JgDG3T/pJO6n7UX1nuZ+zb5UMXmh7fJxx0jq+eRvh+bY27kTWjSrmVQ0E3tOjd464JxwZU73GWlfTklCqZd67iXUeFvhis8jPNT7gBoArgUr2bEjd0bGuWQ1PkzoIYynnaz77nFJlR02MkgeympOVFGeuiMr]]></Encrypt>
//<AgentID><![CDATA[]]></AgentID>
//</xml>";
//
//        $xmlPaser = new \XMLParse();
//        $obj = $xmlPaser->extract($xml);
//
//        print_r($obj);
//        exit;
        \Log::info(file_get_contents("php://input"));
        \Log::info($_GET);
    }

}