<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller{
    //加入购物车
    //status 0:成功 1:未登录 2:失败
    public function addPart(){
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        $num = $_REQUEST['num'];
        $product_id = $_REQUEST['product_id'];
        $period_id = $_REQUEST['period_id'];
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        //确认数据
        if (!$product_id || !$period_id) {
            $ret['status'] = 2;
            $ret['msg'] = '加入失败';
            $this->ajaxReturn($ret);
            die;
        }
        //查看当前操作者的cart里是否有此product_id，有则数量加$num，没有就新增一条数据
        $where['user_id'] = $user_id;
        $where['product_id'] = $product_id;
        $where['status'] = 1;
        $user_cart = M('cart')->where($where)->find();
        if ($user_cart){
            $data['product_num'] = $user_cart['product_num'] + $num?:1;
            $res_status = M('cart')->where($where)->save($data);
        }else{
            $data['user_id'] = $user_id;
            $data['product_id'] = $product_id;
            $data['product_num'] = $num?:1;
            $data['period_id'] = $period_id;
            $data['status'] = 1;
            $res_status = M('cart')->where($where)->add($data);
        }
        if ($res_status) {
            $ret['status'] = 0;
            $ret['msg'] = '加入成功';
            $this->ajaxReturn($ret);
            die;
        }else{
            $ret['status'] = 2;
            $ret['msg'] = '加入失败';
            $this->ajaxReturn($ret);
            die;
        }
    }
    
    //获取购物车列表
    public function cartList(){
        session('user_id',11);
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        $where['c.user_id'] = $user_id;
        $where['c.status'] = 1;
        $where['pe.status_period'] = 1;
        $join_a = "hyz_product AS p ON c.product_id = p.product_id";
        $join_b = "hyz_period AS pe ON c.product_id = pe.p_id";
        $order = "c.ctime desc";
        $res = M('cart')->alias("c")->join($join_a)->join($join_b)->field('c.*, pe.target_num , pe.now_num , pe.period_time , p.product_name ,p.price ,p.product_info')->where($where)->order($order)->select();
        if ($res) {
            $data = array('status'=>0,'msg'=>$res);
            $this->ajaxReturn($data);
        }else{
            $data = array('status'=>1,'msg'=>'没有商品');
            $this->ajaxReturn($data);
        }
    }


    //清空购物车
    public function cleanCart(){
        //确认用户登陆
        if (!$_SESSION['user_id']) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        M('cart')->where(array('user_id' => $_SESSION['user_id'],'status' => 1))->save(array('status' => 0));
        $ret['status'] = 0;
        $ret['msg'] = '清空成功';
        $this->ajaxReturn($ret);
        die;
    }
    
    //生成订单
    
    //订单列表
}
