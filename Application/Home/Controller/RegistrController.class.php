<?php
namespace Home\Controller;
use Think\Controller;
use LT\ThinkSDK\ThinkOauth;
class RegistrController extends Controller {
	//注册流程
    public function index(){
       $phone = I('phone');
       $type = I('type');
       $job = I('job');
       $pwd = I('pwd');
       $confirmPwd = I('confirmPwd');
       $code = I('code');
       $pattern = '/^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/';
       $check_phone = preg_match($pattern, $phone);
       if($check_phone == 0){
            $data = array(
                'status'=>1,
                'msg'=>'电话号码格式有误',
                );
           $this->ajaxReturn($data);
        }
        if(!$type){
            $data = array(
                'status'=>2,
                'msg'=>'注册类型不能为空',
                );
           $this->ajaxReturn($data);
        }
        if(!$job){
            $data = array(
                'status'=>3,
                'msg'=>'职位不能不能为空',
                );
           $this->ajaxReturn($data);
        }
        if(!$pwd){
            $data = array(
                'status'=>4,
                'msg'=>'密码不能为空',
                );
           $this->ajaxReturn($data);
        }
        if(!$code){
            $data = array(
                'status'=>5,
                'msg'=>'验证码不能为空',
                );
           $this->ajaxReturn($data);
        }
        if($pwd != $confirmPwd){
            $data = array(
                'status'=>6,
                'msg'=>'两次密码不一致',
                );
           $this->ajaxReturn($data);
        }
        //获取session里面的验证码
        $session_code = session('telCode');
        if ($code != $session_code) {
        	$data = array(
                'status'=>7,
                'msg'=>'手机验证码不对',
                );
           $this->ajaxReturn($data);
        }
        //判断是否注册过
        $check = M('user')->where(array('tel'=>$phone))->find();
        if ($check) {
        	$data = array(
                'status'=>8,
                'msg'=>'手机已被注册',
                );
           $this->ajaxReturn($data);
        }
        //添加数据到数据库
        $salt= rand(1000,9999);
        $insert = array(
        		'tel'=>$phone,
        		'user_type'=>$type,
        		'job'=>$job,
        		'pass'=>md5($pwd.$salt),
        		'ctime'=>time(),
        		'salt'=>$salt,
        		'status'=>1,
        	);
        $res = M('user')->add($insert);
        if ($res) {
        	$data = array(
                'status'=>0,
                'msg'=>'注册成功',
                );
           $this->ajaxReturn($data);
        }else{
        	$data = array(
                'status'=>9,
                'msg'=>'注册失败',
                );
           $this->ajaxReturn($data);
        }
    }
}