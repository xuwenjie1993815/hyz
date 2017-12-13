<?php
/**
 * 用户model
 */
namespace Common\Model;
use Think\Model;
class  userModel extends Model {
    //独立用户model
    //登录 注册 信息修改 修改密码

	//登录
	public function login($username,$pass){
		// $where["user_name"] = array("eq",$username);
		// $where['telephone'] = $username;

		$where = "user_name = '$username' || tel = '$username'";

		$result = M("user")->where($where)->find();
		if (!$result) {
			$data["status"] = 1;
			$data["info"] = "用户名不存在";
			return $data;
		}

		if ($result['salt']) {
			if ($result["pass"] != md5($pass.$result['salt'])) {
				$data["status"] = 1;
				$data["info"] = "用户名或密码错误";
				return $data;
			}
		}else{
			if ($result["pass"] != $pass) {
				$data["status"] = 1;
				$data["info"] = "用户名或密码错误";
				return $data;
			}
		}

		if ($result["status"] != 1) {
			$data["status"] = 1;
			$data["info"] = "帐号已被锁定";
			return $data;
		}

		session("user_id",$result["user_id"]);
		session("user_name",$result["user_name"]);
		session("real_name",$result["real_name"]);
		$data["status"] = 0;
		$data["msg"] = "登录成功";
		$data["user_info"] = array('user_name' => $result['user_name'],'real_name' => $result['real_name'],'user_id' => $result['user_id'],'user_type' => $result['user_type']);
		return $data;
	}

	//用户信息修改
	public function edit($data){
		//数据判断
		$user_id = $data['user_id'];
		$res = M("user")->where("user_id = '$user_id'")->save($data);
		if ($res) {
			return array('status' => 0,'msg' => '操作成功');
		}
		return array('status' => 1,'msg' => '操作失败');
	}

	//用户修改登录密码
	function editPwd($data){
		if($data){
			if(!empty($data["newapass"])){
                            $salt = rand(1000,9999);;
			    $data["pass"] = md5($data["newapass"].$salt);
			    $data["salt"] = $salt;
			}
                            if(empty($data["oldpass"])){
					return array('status' => 1,'msg'=>'请输入原密码');
			    }
			    if(empty($data["newapass"])){
			     	return array('status' => 1,'msg'=>'请输入新密码');
			    }
			    if($data["newapass"]!=$data["newapass2"]){
			     	return array('status' => 1,'msg'=>'两次输入新密码不一致');
			    }
                            $res = M("user")->where(array('user_id' => $data['user_id']))->find();
                            if (!$res['salt']) {
                                    if($res['pass']!=md5($data["oldpass"])){
                                    return array('status' => 1,'msg'=>'原密码错误');
                                    }
                            }else{
                                    if($res['pass']!=md5($data["oldpass"].$res['salt'])){
                                    return array('status' => 1,'msg'=>'原密码错误');
                                    }
                            }
                            if ($data['oldpass'] == $data['newapass']) {
                                    return array('status' => 1,'msg'=>'新密码不能与近期用过密码相同');
                            }
			$res = M("user")->where(array('user_id' => $data['user_id']))->save($data);
			if($res!==false){
				return array('status' => 0,'msg'=>'操作成功');
			}else{
				return array('status' => 1,'msg'=>'操作失败');
			}
		}else{
			return array('status' => 1,'msg'=>'操作失败');
		}
	}

}