<?php   
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
		if($_SESSION["user_id"]){
			$this->redirect("Index");
		}else{
            $this->display();
		}
    }
    
    //登录
    //接收参数 username userpwd verify_code
    public function checkLogin(){
        $username = I('param.username');
        $pass = I('param.userpwd');
        $verify = I('param.verify_code');
        // $error_url=U("login/index");
        $error_url='';
        $res = array('status'=>1);
        //如果连续登录失败四次以上则验证验证码
        $val = session('login_n');
        $val++;
        session('login_n', $val);
        if($val >= 4){
            if(!$this->check_verify($verify)){
                if(IS_AJAX){
                    $res['msg'] = '验证码错误';
                    $this->ajaxReturn($res);
                }else{
                    $this->error('验证码错误', $error_url);
                }
            }
        }

        $data = D('User')->login($username,MD5($pass),$pass);
        if($data['status']==1){
            $url=U("Index/index");
            if(!empty($_POST['jump_url'])){
                $url=$_POST['jump_url'];
            }

            if(IS_AJAX){
                $res['msg'] = '登录成功';
                $this->ajaxReturn($res);
            }else{
                $this->success("登录成功",$url);
            }
        }else{
            if(IS_AJAX){
                $res['msg'] = $data["info"];
                $this->ajaxReturn($res);
            }else{
                if($val == 3) $this->redirect('login/index');
                else $this->error($data["info"], $error_url);
            }
        }
    }
    
    //第三方登陆
    //login_type 1：微信 2：QQ
    //login_id 微信openid或者QQid
    //返回参数 status=>0:登陆成功 1:请绑定手机
    //通过openid 或者 QQid 找到对应用户，有则直接登陆，没有则要求绑定手机号，如其绑定手机号已存在则绑定到对应user，如不存在则提示注册
    public function otherLogin(){
        $login_type = I('param.login_type'); 
        $login_id = I('param.login_id'); 
        $where['status'] = 1;
        switch ($login_type) {
            case 1:
                $where['openid'] = $login_id;
                break;
            case 2:
                $where['qqid'] = $login_id;
                break;
            default:
                break;
        }
        $user_info = M('user')->where($where)->find();
        if (!$user_info) {
            $data["status"] = 1;
            $data["msg"] = '请绑定手机';
            $this->ajaxReturn($data);
            die;
        }
        session("user_id",$user_info["user_id"]);
        session("user_name",$user_info["user_name"]);
        session("real_name",$user_info["real_name"]);
        $data["status"] = 0;
        $data["msg"] = '登陆成功';
        $data["log_name"] = $user_info["user_name"];
        $this->ajaxReturn($data);
    }
    
    //账号绑定
    public function bangding(){
        $login_type = I('param.login_type'); 
        $login_id = I('param.login_id');
        $tel = I('param.tel');
        //如其绑定手机号已存在则绑定到对应user并登录，如不存在提示注册
        $where['tel'] = $tel;
        $where['status'] = 1;
        $user = M('user')->where($where)->find();
        if (!$user) {
            $data["status"] = 1;
            $data["msg"] = '请先注册';
            $this->ajaxReturn($data);
            die;
        }
        switch ($login_type) {
            case 1:
                $data['openid'] = $login_id;
                break;
            case 2:
                $data['qqid'] = $login_id;
                break;
            default:
                break;
        }
        $ret = M('user')->where($where)->save($data);
        if ($ret) {
            session("user_id",$user["user_id"]);
            session("user_name",$user["user_name"]);
            session("real_name",$user["real_name"]);
            $data["status"] = 0;
            $data["msg"] = '登陆成功';
            $data["log_name"] = $user["user_name"];
            $this->ajaxReturn($data);
        }
    }

    //生成验证码
    Public function verify(){
        import('Org.Util.Verify');
        $Verify = new \Verify([
            'fontSize' => 15,
            'length' => 4,
            'codeSet' => '0123456789',
            'useNoise' => false,
        ]);
        $Verify->entry();
    }
    
    //检测验证码是否正确
    function check_verify($code, $id = ''){
        import('Org.Util.Verify');
        $Verify = new \Verify();
        return $Verify->check($code, $id);
    }
}