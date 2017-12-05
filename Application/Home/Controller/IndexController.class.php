<?php
namespace Home\Controller;
use Think\Controller;
use LT\ThinkSDK\ThinkOauth;
class IndexController extends Controller {
    public function index(){
        $this->display();
      }
}