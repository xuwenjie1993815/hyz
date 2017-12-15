<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $a = $_GET['aa'];
        var_dump($a);
        var_dump(json_decode($a,true));
        $this->display();
      }
}