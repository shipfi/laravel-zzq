<?php namespace App\Http\Controllers;
/**
 * Created by Zhengqian.Zhu
 * Email: zhengqian@dajiayao.cc
 * Date: 15/7/17
 */

class TestController extends Controller
{
    public function index($id)
    {
        echo $id;
    }
}