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
    
    //删除购物车商品
    public function delCart() {
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        $cart_id = $_POST['cart_id'];
        if (!$cart_id) {
            $ret['status'] = 2;
            $ret['msg'] = '缺少参数';
            $this->ajaxReturn($ret);
            die;
        }
        $cart_info = M('cart')->where(array('cart_id' => $cart_id))->find();
        if (!$cart_info) {
            $ret['status'] = 3;
            $ret['msg'] = '参数错误';
            $this->ajaxReturn($ret);
            die;
        }
        if ($cart_info['status'] == 0) {
            $ret['status'] = 4;
            $ret['msg'] = '此商品已删除';
            $this->ajaxReturn($ret);
            die;
        }
        $cart_ret = M('cart')->where(array('cart_id' => $cart_id))->save(array('status' => 0));
        if ($cart_ret) {
            $ret['status'] = 0;
            $ret['msg'] = '删除成功';
            $this->ajaxReturn($ret);
            die;
        }  else {
            $ret['status'] = 5;
            $ret['msg'] = '删除失败';
            $this->ajaxReturn($ret);
            die;
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
    
    //购物车结算生成订单,订单确认
    //根据user_id查询其购物车信息，返回付款信息
    //商品信息数据格式 array(product_id:num)
    public function cart_order(){
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        $where['c.user_id'] = $user_id;
        $where['c.status'] = 1;
        $where['pe.status_period'] = 1;//判断这期活动是否已经过期
        $join_a = "hyz_product AS p ON c.product_id = p.product_id";
        $join_b = "hyz_period AS pe ON c.product_id = pe.p_id";
        $order = "c.ctime desc";
        $res = M('cart')->alias("c")->join($join_a)->join($join_b)->field('c.*, pe.target_num , pe.now_num , pe.period_time, pe.period_price , p.product_name ,p.product_info')->where($where)->order($order)->select();
        if (!$res) {
            $ret['status'] = 2;
            $ret['msg'] = '购物车为空';
            $this->ajaxReturn($ret);
            die;
        }
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
                $order_data['order_type'] = 1;
                $order_data['period_time'] = $v['period_time'];
                $order_data['order_product_id'] = $v['product_id'];
                $order_data['product_num'] = $v['product_num'];
                $order_data['order_money'] = $v['period_price']*$v['product_num'];
                $order_data['order_status'] = 0;
                $order_data['order_time'] = time();
                $order_data['addressee'] = $user_info['real_name']?:$user_info['user_name'];
                $order_data['province'] = $user_info['province'];
                $order_data['city'] = $user_info['city'];
                $order_data['county'] = $user_info['county'];
                $order_data['address'] = $user_info['address'];
                $order_data['tel'] = $user_info['tel'];
                $order_data['order_note'] = $user_info['order_note'];
                $r = $model->table($db_prefix . 'order')->add($order_data);
                if (!$r) throw_exception('操作失败');
                //生成订单成功  购物车清空
                M('cart')->where(array('user_id' => $_SESSION['user_id'],'status' => 1))->save(array('status' => 0));
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
                'status' => 3,
                'msg' => '操作失败',
            ));
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
    //订单列表(全部，已参与，待兑奖，我的评论)
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
       
        //有三种类型的商品  商品抽奖订单（关联product period order）  活动抽奖订单（apply order activity）  点赞抽奖订单（apply order activity）
        //商品抽奖订单
        $join_a = "hyz_product AS p ON o.order_product_id = p.product_id";
        $join_b = "hyz_period AS pe ON o.order_product_id = pe.p_id";
        $order = "o.order_time desc";
        $where['o.user_id'] = $user_id;
        $where['pe.status_period'] = 1;
        $where['o.order_type'] = 1;//商品抽奖订单
        if ($order_status !== 'All'){
            $where['o.order_status'] = $order_status;
        }else{
            $where['o.order_status'] != 3;
        }
        $res = M('order')->alias("o")->join($join_a)->join($join_b)->field('o.*, pe.* , p.product_name ,p.price ,p.product_info,p.images')->where($where)->order($order)->select();
        //列表需要数据 订单title order_info(类型  数量  金额  图片  时间  状态)   order_id order_img  order_price order_sn order_time 
        foreach ($res as $k => $v){
            $data[$k]['title'] = $v['period_name'];//title
            $data[$k]['images'] = $v['images'];//商品图片
            $data[$k]['order_price'] = $v['order_money'];//金额
            $data[$k]['product_num'] = $v['product_num'];//数量
            $data[$k]['period_time'] = $v['period_time'];//活动期数
            $data[$k]['order_status'] = $v['order_status'];//订单状态
            $data[$k]['order_time'] = $v['order_time'];//订单时间
            $data[$k]['order_type'] = $v['order_type'];//订单类型
        }
        
        //活动抽奖订单apply order activity
        $join_a = "hyz_apply AS a ON a.order_id = o.order_id";
        $join_b = "hyz_activity AS ac ON ac.activity_id = o.activity_id";
        $order = "o.order_time desc";
        $where_apply['a.apply_type'] = 1;
        $where_apply['o.user_id'] = $user_id;
        $where_apply['o.order_type'] = 2;//参与活动订单
        $field_apply = 'o.*, ac.*,a.*';
        $res_apply = M('order')->alias("o")->join($join_a)->join($join_b)->field($field_apply)->where($where_apply)->order($order)->select();
        foreach ($res_apply as $k => $v){
            $data_apply[$k]['title'] = $v['activity_name'];//title
            $data_apply[$k]['images'] = $v['images'];//商品图片
            $data_apply[$k]['order_price'] = $v['order_money'];//金额
            $data_apply[$k]['product_num'] = $v['product_num'];//数量
            $data_apply[$k]['period_time'] = $v['period_time'];//活动期数
            $data_apply[$k]['order_status'] = $v['order_status'];//订单状态
            $data_apply[$k]['order_time'] = $v['order_time'];//订单时间
            $data_apply[$k]['order_type'] = $v['order_type'];//订单类型
        }
        //点赞抽奖订单apply order activity
        $join_a = "hyz_apply AS a ON a.apply_id = o.apply_id";
        $where_apply['o.order_type'] = 3;//参与点赞订单
        $field_apply = 'o.*, ac.*,a.*';
        $res_dz = M('order')->alias("o")->join($join_a)->join($join_b)->field($field_apply)->where($where_apply)->order($order)->select();
        foreach ($res_dz as $k => $v){
            $data_dz[$k]['title'] = $v['title'];//title
            $data_dz[$k]['images'] = $v['images'];//商品图片
            $data_dz[$k]['order_price'] = $v['order_money'];//金额
            $data_dz[$k]['product_num'] = $v['product_num'];//数量
            $data_dz[$k]['period_time'] = $v['period_time'];//活动期数
            $data_dz[$k]['order_status'] = $v['order_status'];//订单状态
            $data_dz[$k]['order_time'] = $v['order_time'];//订单时间
            $data_dz[$k]['order_type'] = $v['order_type'];//订单类型
        }
       $order_list = array_merge($data,$data_apply,$data_dz);
        
        
        if (!$order_list) {
            $ret['status'] = 2;
            $ret['msg'] = '空列表';
            $this->ajaxReturn($ret);
            die;
        }else{
            $ret['status'] = 0;
            $ret['msg'] = '获取成功';
            $ret['order_list'] = $order_list;
            $this->ajaxReturn($ret);
            die;
        }
    }
    
    //取消订单
    public function cancelOrder() {
        $order_id = $_REQUEST['order_id'];
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        //检查是否本人删除和订单状态
        $order_info = M('order')->where(array('order_id' => $order_id))->find();
        if (!$order_info) {
            $ret['status'] = 2;
            $ret['msg'] = '操作错误,订单不存在';
            $this->ajaxReturn($ret);
            die;
        }
        if ($order_info['user_id'] != $user_id) {
            $ret['status'] = 3;
            $ret['msg'] = '操作错误,没有权限删除此订单';
            $this->ajaxReturn($ret);
            die;
        }
        $res = M('order')->where(array('order_id' => $order_id))->save(array('order_status' => 3));
        if ($res) {
            $ret['status'] = 0;
            $ret['msg'] = '删除订单成功';
            $this->ajaxReturn($ret);
            die;
        }else{
            $ret['status'] = 4;
            $ret['msg'] = '操作失败';
            $this->ajaxReturn($ret);
            die;
        }
    }
    
    //立即购买
    public function buyNow(){
        $user_id = $_POST['user_id']?$_POST['user_id']:$_SESSION['user_id'];
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        $product_id = $_POST['product_id'];
        $period_id = $_POST['period_id'];
        if (!$product_id || !$period_id) {
            $ret['status'] = 2;
            $ret['msg'] = '缺少参数';
            $this->ajaxReturn($ret);
            die;
        }
        //检查商品状态和活动状态
        $product_info = M('product')->where(array('product_id' => $period_id))->find();
        $period_info = M('period')->where(array('period_id' => $period_id))->find();
        if (!$product_info || !$period_info) {
            $ret['status'] = 3;
            $ret['msg'] = '参数错误';
            $this->ajaxReturn($ret);
            die;
        }
        if ($product_info['status'] == 0) {
            $ret['status'] = 4;
            $ret['msg'] = '此商品已下架';
            $this->ajaxReturn($ret);
            die;
        }
        if ($period_info['status_period'] == 0) {
            $ret['status'] = 5;
            $ret['msg'] = '此活动已过期';
            $this->ajaxReturn($ret);
            die;
        }
        if ($period_info['p_id'] != $period_id) {
            $ret['status'] = 6;
            $ret['msg'] = '参数错误';
            $this->ajaxReturn($ret);
            die;
        }
        $model = M();
        $db_prefix = C('DB_PREFIX');
        try{
            $model->startTrans();
        //生成未支付的订单
        $order_data['order_sn'] = D('Support')->orderNumber();
        $order_data['user_id'] = $user_id;
        $order_data['order_type'] = 1;
        $order_data['period_time'] = $period_info['period_time'];
        $order_data['order_product_id'] = $product_id;
        $order_data['order_money'] = $period_info['period_price'];
        $order_data['order_status'] = 0;
        $order_data['order_time'] = time();
        $order_data['addressee'] = $user_info['real_name']?:$user_info['user_name'];
        $order_data['province'] = $user_info['province'];
        $order_data['city'] = $user_info['city'];
        $order_data['county'] = $user_info['county'];
        $order_data['address'] = $user_info['address'];
        $order_data['tel'] = $user_info['tel'];
        $order_data['order_note'] = $user_info['order_note'];
        $r = $model->table($db_prefix . 'order')->add($order_data);
        $model->commit();
        $this->ajaxReturn(array(
            'status' => 0,
            'msg' => '操作成功',
            'order_info' => array('order_price' => $period_info['period_price'],'product_num' => 1,'product_name' => $period_info['period_name']),
        ));
        }catch (Exception $e){
            $model->rollback();
            $this->ajaxReturn(array(
                'status' => 7,
                'msg' => '操作失败',
            ));
        }
    }


}
