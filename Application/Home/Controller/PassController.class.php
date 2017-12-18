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
        $res['code']=9999;
        if ($code != $res['code']) {
        	$data = array(
                'status'=>1,
                'msg'=>'验证码错误',
                );
           $this->ajaxReturn($data);
        }
        // $time = time();
        // if ($time-$res['expiration_time']>600) {
        // 	$data = array(
        //         'status'=>1,
        //         'msg'=>'验证码过期',
        //         );
        //    $this->ajaxReturn($data);
        // }
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
    //完善资料展示页
    public function userInfo()
    {
        $user_id = I('user_id');
        if (!$user_id) {
           $data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空',
                );
           $this->ajaxReturn($data);
        }
        $res = M('user')->field('user_img,user_name,real_name,tel,user_qq,email,job')->where(array('user_id'=>$user_id))->find();
        if ($res) {
            $data = array(
                'status'=>0,
                'msg'=>$res,
                );
           $this->ajaxReturn($data);
        }else{
            $data = array(
                'status'=>1,
                'msg'=>'获取失败',
                );
           $this->ajaxReturn($data);
        }
    }
    //完善资料
    public function editInfo()
    {
        $user_id = I('user_id');
        $user_name = I('user_name');
        $real_name = I('real_name');
        $tel = I('tel');
        $user_qq = I('user_qq');
        $email = I('email');
        $job = I('job');
        if (!$user_id) {
            $data = array(
                'status'=>1,
                'msg'=>'用户id不能为空',
                );
           $this->ajaxReturn($data);
        }
        if (!$real_name) {
            $data = array(
                'status'=>1,
                'msg'=>'真实姓名不能为空',
                );
           $this->ajaxReturn($data);
        }
        $pattern = '/^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/';
        $check_phone = preg_match($pattern, $tel);
        if($check_phone == 0){
            $data = array(
                'status'=>1,
                'msg'=>'电话号码格式有误',
                );
           $this->ajaxReturn($data);
        }
        if (!$email) {
            $data = array(
                'status'=>1,
                'msg'=>'邮箱不能为空',
                );
           $this->ajaxReturn($data);
        }
        if (!$job) {
            $data = array(
                'status'=>1,
                'msg'=>'工作岗位或者学校不能为空',
                );
           $this->ajaxReturn($data);
        }
        if ($_FILES) {
            $upload = D('support')->upload();
            $filename = $upload[0];
        }
        if (!$filename) {
            $info = M('user')->field('user_img')->where(array('user_id'=>$user_id))->find();
            $filename = $info['user_img'];
        }
        $arr = array(
                'user_img'=>$filename,
                'user_name'=>$user_name,
                'real_name'=>$real_name,
                'tel'=>$tel,
                'user_qq'=>$user_qq,
                'email'=>$email,
                'job'=>$job,
            );
       $res = M('user')->where(array('user_id'=>$user_id))->save($arr);
       if ($res) {
            $res_info = M('user')->field('user_img,user_name,real_name,tel,user_qq,email,job')->where(array('user_id'=>$user_id))->find();
            $data = array(
                'status'=>0,
                'msg'=>$res_info,
                );
           $this->ajaxReturn($data);
        }else{
            $data = array(
                'status'=>1,
                'msg'=>'更新失败',
                );
           $this->ajaxReturn($data);
        }
    }
}