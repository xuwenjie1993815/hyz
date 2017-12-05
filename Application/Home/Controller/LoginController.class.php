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
        // $error_url=U("login/index");
        $error_url='';
        $res = array('status'=>0);
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

    //验证验证码
    public function check_verify($code, $id = ''){

    }
}