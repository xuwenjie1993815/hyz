<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller{
    //加入购物车
    //status 0:成功 1:未登录 2:失败
    public function addCart(){
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
            $data = array('status'=>2,'msg'=>'没有商品');
            $this->ajaxReturn($data);
        }
    }


    //清空购物车
    public function cleanCart(){
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        //确认用户登陆
        if (!$user_id) {
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
    
    //结算生成订单,订单确认
    //根据user_id查询其购物车信息，返回付款信息
    //商品信息数据格式 array(product_id:num)
    /**
     *
     */
    public function confirm_order(){
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        $order_type = $_POST['order_type'];
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        if ($order_type = 1) {
            $where['c.user_id'] = $user_id;
            $where['c.status'] = 1;
            $where['pe.status_period'] = 1;
            $join_a = "hyz_product AS p ON c.product_id = p.product_id";
            $join_b = "hyz_period AS pe ON c.product_id = pe.p_id";
            $order = "c.ctime desc";
            $res = M('cart')->alias("c")->join($join_a)->join($join_b)->field('c.*, pe.target_num , pe.now_num , pe.period_time, pe.period_price , p.product_name ,p.product_info')->where($where)->order($order)->select();
            $price = 0;
            $product_num = 0;
            $model = M();
            $db_prefix = C('DB_PREFIX');
            try{
                $model->startTrans();
                foreach ($res as $k => $v){
                    //返回总金额
                    $price += $v['period_price']*$v['product_num'];
                    $product_num += $v['product_num'];
                    //生成未支付的订单
                    $order_data['order_sn'] = D('Support')->orderNumber();
                    $order_data['user_id'] = $user_id;
                    $order_data['order_type'] = $order_type;
                    $order_data['order_product_id'] = $v['product_id'];
                    $order_data['product_num'] = $v['product_num'];
                    $order_data['order_money'] = $v['period_price']*$v['product_num'];
                    $order_data['order_status'] = 0;
                    $order_data['order_time'] = time();
                    $order_data['addressee'] = $user_info['real_name']?:$user_info['user_name'];
                    $order_data['province'] = $user_info['province'];
                    $order_data['city'] = $user_info['city'];
                    $order_data['county'] = $user_info['county'];
                    $order_data['address'] = $_POST['address'];
                    $order_data['tel'] = $user_info['tel'];
                    $order_data['order_note'] = $user_info['order_note'];
                    $r = $model->table($db_prefix . 'order')->add($order_data);
                    if (!$r) throw_exception('操作失败');
                }
                $model->commit();
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg' => '操作成功',
                    'order_info' => array('order_price' => $price,'product_num' => $product_num,'product_name' => $res[0]['product_name']),
                ));
            }catch (Exception $e){
                $model->rollback();
                $this->ajaxReturn(array(
                    'status' => 1,
                    'msg' => '操作失败',
                ));
            }
        }
        
        
        
        
    }
    
    //订单支付todo
    public function submit_order(){
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        $order_ids = $_POST['order_ids'];
        $pay_type = $_POST['pay_type'];
        
    }
    //点赞订单列表
    //活动订单列表
    //商品订单列表(全部，已参与，待兑奖，我的评论)
    //order_status 1已参与商品订单 2已兑奖商品订单 （为null则获取全部订单）
    public function orderList(){
        $order_status = $_POST['order_status'];
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        $where['o.user_id'] = $user_id;
        $where['pe.status_period'] = 1;
        $where['o.order_type'] = 1;
        if ($order_status){
            $where['o.order_status'] = $order_status;
        }else{
            $where['o.order_status'] != 3;
        }
        $join_a = "hyz_product AS p ON o.order_product_id = p.product_id";
        $join_b = "hyz_period AS pe ON o.order_product_id = pe.p_id";
        $order = "o.order_time desc";
        $res = M('order')->alias("o")->join($join_a)->join($join_b)->field('o.*, pe.target_num , pe.now_num , pe.period_time , p.product_name ,p.price ,p.product_info,p.images')->where($where)->order($order)->select();
        foreach ($res as $k => $v){
            $data[$k]['num'] = $v['product_num'];//商品数量,支持数量
            $data[$k]['period_time'] = $v['period_time'];//活动期数
            $data[$k]['images'] = $v['images'];//商品图片
            $data[$k]['period_name'] = $v['period_name'];//活动名
            $data[$k]['order_price'] = $v['period_price'] * $v['product_num'];//金额
        }
        if (!$data) {
            $ret['status'] = 1;
            $ret['msg'] = '空列表';
            $this->ajaxReturn($ret);
            die;
        }else{
            $ret['status'] = 0;
            $ret['msg'] = '获取成功';
            $ret['order_lisr'] = $data;
            $this->ajaxReturn($ret);
            die;
        }
    }


}
