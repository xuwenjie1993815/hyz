<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller{
    //加入购物车
    //status 0:成功 1:未登录 2:失败
    public function addPart(){
        $product_id = $_REQUEST['product_id'];
        $period_id = $_REQUEST['period_id'];
        //确认用户登陆
        if (!$_SESSION['user_id']) {
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
        //查看当前操作者的cart里是否有此product_id，有则数量加一，没有就新增一条数据
        $where['user_id'] = $_SESSION['user_id'];
        $where['product_id'] = $product_id;
        $where['status'] = 1;
        $user_cart = M('cart')->where($where)->find();
        if ($user_cart){
            $data['product_num'] = $user_cart['product_num'] + 1;
            $res_status = M('cart')->where($where)->save($data);
        }else{
            $data['user_id'] = $_SESSION['user_id'];
            $data['product_id'] = $product_id;
            $data['product_num'] = 1;
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
