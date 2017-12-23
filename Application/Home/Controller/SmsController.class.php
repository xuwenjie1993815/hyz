<?php
namespace Home\Controller;
use Think\Controller;
class SmsController extends Controller {
	public function sms_send()
	{
	  $phone= $_POST['phne'];
	  $sign = $_POST['sign'];
	  $time = $_POST['time'];
	  $pattern = '/^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/';
      $check_phone = preg_match($pattern, $phone);
      if($check_phone == 0){
            $data = array(
                'status'=>1,
                'msg'=>'电话号码格式有误',
                );
           $this->ajaxReturn($data);
      }
      if (!$sign) {
      	$data = array(
                'status'=>1,
                'msg'=>'签名不能为空',
                );
           $this->ajaxReturn($data);
      }
      if (!$time) {
      	$data = array(
                'status'=>1,
                'msg'=>'时间不能为空',
                );
           $this->ajaxReturn($data);
      }
	  $mysign = md5($phone.$time.C('SMS_KEY'));
	  if ($sign !=$mysign) {
	  	$data = array(
                'status'=>1,
                'msg'=>'签名错误',
                );
           $this->ajaxReturn($data);
	  }
	  $code = rand(1000,9999);
	  $content ='尊敬的用户,您的验证码为'.$code;
	  $res = D('Support')->sms($phone,$content);
	  if ($res==100) {
	  	$ctime =time();
	  	$info = array(
	  			'tel'=>$phone,
	  			'code'=>$code,
	  			'ctime'=>$ctime,
	  			'expiration_time'=>$ctime+600
	  		);
	  	$result = M('sms_records')->add($info);
	  	if ($result) {
	  		$data = array(
                'status'=>0,
                'msg'=>'发送成功',
                );
           $this->ajaxReturn($data);
	  	}else{
	  		$data = array(
                'status'=>1,
                'msg'=>'发送失败',
                );
           $this->ajaxReturn($data);
	  	}
	  }else{
	  	$data = array(
                'status'=>1,
                'msg'=>'发送失败',
                );
           $this->ajaxReturn($data);
	  }
	}
}