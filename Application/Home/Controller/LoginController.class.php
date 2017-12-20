<?php   
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    
    //登录
    //接收参数 username userpwd verify_code
    public function checkLogin(){
        $username = I('param.username');
        $pass = I('param.userpwd');
        $verify = I('param.verify_code');
        if($verify){
            if(!$this->check_verify($verify)){
                $res['status'] = 2;
                $res['msg'] = '验证码错误';
            }
        }
        $data = D('User')->login($username,$pass);
        if($data['status']==0){
            $res['status'] = 0;
            $res['log_name'] = $data['user_info']['user_name'];
            $res['user_info'] = $data['user_info'];
            $res['msg'] = '登录成功';
        }else{
            $res['status'] = 1;
            $res['msg'] = '账号或密码错误,登录失败';
        }
        $this->ajaxReturn($res);
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
                $where['qq'] = $login_id;
                break;
            default:
                break;
        }
        $user_info = M('user')->where($where)->find();
        if (!$user_info) {
            $data["status"] = 1;
            $data["msg"] = '账户未绑定';
            $this->ajaxReturn($data);
            die;
        }
        $data["status"] = 0;
        $data["msg"] = '登陆成功';
        $data["user_info"] = array('user_id' => $user_info['user_id'],'user_name' => $user_info['user_name'],'real_name' => $user_info['real_name'],'tel' => $user_info['tel'],'qq' => $user_info['qq'],'user_img' => $user_info['user_img']);
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
                $data['qq'] = $login_id;
                break;
            default:
                break;
        }
        $ret = M('user')->where($where)->save($data);
        $user_info = M('user')->where($where)->find();
        if ($ret) {
            $data["status"] = 0;
            $data["msg"] = '绑定成功';
            $data["user_info"] = array('user_id' => $user_info['user_id'],'user_name' => $user_info['user_name'],'real_name' => $user_info['real_name'],'tel' => $user_info['tel'],'qq' => $user_info['qq'],'user_img' => $user_info['user_img']);
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
    
    //修改密码
    public function editPwd() {
        $user_id = $_POST['user_id'];
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        $oldpass = $_POST['oldpass'];
        $newapass = $_POST['newapass'];
        $newapass2 = $_POST['newapass2'];
        if (!$oldpass || !$newapass || !$newapass2) {
            $ret['status'] = 2;
            $ret['msg'] = '缺少参数';
            $this->ajaxReturn($ret);
            die;
        }
        $data['user_id'] = $user_id;
        $data['oldpass'] = $oldpass;
        $data['newapass'] = $newapass;
        $data['newapass2'] = $newapass2;
        $res = D('User')->editPwd($data);
        $this->ajaxReturn($res);
    }
    
}