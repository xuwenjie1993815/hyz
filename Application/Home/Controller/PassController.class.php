<?php
namespace Home\Controller;
use Think\Controller;
class PassController extends Controller {
	//修改密码
	public function changePassword()
	{
		$old = I('old_pwd');
		$new_pwd1 = I('pwd_one');
		$new_pwd2 = I('pwd_two');
		$user_id = I('user_id');
		if (!$old) {
			$data = array(
                'status'=>1,
                'msg'=>'原密码不能为空',
                );
           $this->ajaxReturn($data);
		}
		if (!$new_pwd1) {
			$data = array(
                'status'=>1,
                'msg'=>'新密码不能为空',
                );
           $this->ajaxReturn($data);
		}
		if (!$new_pwd2) {
			$data = array(
                'status'=>1,
                'msg'=>'确认密码不能为空',
                );
           $this->ajaxReturn($data);
		}
		if (!$user_id) {
			$data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空',
                );
           $this->ajaxReturn($data);
		}
		$user = M('user')->field('salt,pass')->where(array('user_id'=>$user_id))->find();
		$old_pwd = md5($old.$user['salt']);
		if ($old_pwd !=$user['pass']) {
			$data = array(
                'status'=>1,
                'msg'=>'旧密码不正确',
                );
           $this->ajaxReturn($data);
		}
		if ($new_pwd1 !=$new_pwd2) {
			$data = array(
                'status'=>1,
                'msg'=>'新密码两次不一致',
                );
           $this->ajaxReturn($data);
		}
		$pass = md5($new_pwd2.$user['salt']);
		if ($pass == $old_pwd) {
			$data = array(
                'status'=>1,
                'msg'=>'新旧密码一致',
                );
           $this->ajaxReturn($data);
		}
		$res = M('user')->where(array('user_id'=>$user_id))->save(array('pass'=>$pass));
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>'修改成功',
                );
           $this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'修改失败',
                );
           $this->ajaxReturn($data);
		}
	}
	//忘记密码
	public function forgotPwd()
	{
		$phone = I('phone');
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
        if (!$code) {
        	$data = array(
                'status'=>1,
                'msg'=>'验证码不能为空',
                );
           $this->ajaxReturn($data);
        }
        $res  =M('sms_records')->field('code,expiration_time')->where(array('phone'=>$phone,'sms_type'=>1))->find();
        if ($code != $res['code']) {
        	$data = array(
                'status'=>1,
                'msg'=>'验证码错误',
                );
           $this->ajaxReturn($data);
        }
        $time = time();
        if ($time-$res['expiration_time']>600) {
        	$data = array(
                'status'=>1,
                'msg'=>'验证码过期',
                );
           $this->ajaxReturn($data);
        }
        $data = array(
                'status'=>0,
                'msg'=>'验证成功',
                );
           $this->ajaxReturn($data);
	}
	//重置密码
	public function resetPwd()
	{
		$phone = I('phone');
		$pwd = I('pwd');
		$pwd_1 = I('confirm_pwd');
		$pattern = '/^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/';
        $check_phone = preg_match($pattern, $phone);
        if($check_phone == 0){
            $data = array(
                'status'=>1,
                'msg'=>'电话号码格式有误',
                );
           $this->ajaxReturn($data);
        }
        if (!$pwd) {
        	$data = array(
                'status'=>1,
                'msg'=>'密码不能为空',
                );
           $this->ajaxReturn($data);
        }
        if ($pwd != $pwd_1) {
        	$data = array(
                'status'=>1,
                'msg'=>'两次密码不一致',
                );
           $this->ajaxReturn($data);
        }
        $res = M('user')->field('user_id,pass,salt')->where(array('tel'=>$phone))->find();
        if (!$res) {
        	$data = array(
                'status'=>1,
                'msg'=>'该号码还没有注册',
                );
           $this->ajaxReturn($data);
        }
        $salt = $res['salt'];
        $new_pwd = md5($pwd.$salt);
        $old_pwd = $res['pass'];
        if ($new_pwd == $old_pwd) {
        	$data = array(
                'status'=>1,
                'msg'=>'新旧密码一致',
                );
           $this->ajaxReturn($data);
        }
        $info = M('user')->where(array('tel'=>$phone))->save(array('pass'=>$new_pwd));
        if ($info) {
        	$data = array(
                'status'=>0,
                'msg'=>'重置成功',
                );
           $this->ajaxReturn($data);
        }else{
        	$data = array(
                'status'=>1,
                'msg'=>'重置失败',
                );
           $this->ajaxReturn($data);
        }
	}
}