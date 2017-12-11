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
}