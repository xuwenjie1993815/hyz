<?php
namespace Home\Controller;
use Think\Controller;
class ProductController extends Controller {
	//获取商品列表
	public function getProductList()
	{
		$type = I('type');//筛选条件
		$sort = I('sequence');//列表顺序
		$sort = array('price' => 1,'create_time'=> 0,'period_time'=> 1);
		foreach (array_values($sort) as $k => $v){
            if ($v == 1) {
                $p = 'asc';
            }else{
                $p = 'desc';
            }
            $order[array_keys($sort)[$k]] = $p;
        }
        foreach ($order as $k => $v) {
        	$order_arr[]=$k.' '.$v;
        }
        $order_by = implode(',', $order_arr);
		switch ($type) {
			case '1'://所有商品
				$where = array('status_period'=>1);
				break;
			case '2'://旅游摄影
				$where = array('product_type'=>2,'status_period'=>1);
				break;
			case '3'://旅游项目
				$where = array('product_type'=>3,'status_period'=>1);
				break;
			case '4'://健身器材
				$where = array('product_type'=>4,'status_period'=>1);
				break;
			case '5'://生活用品
				$where = array('product_type'=>5,'status_period'=>1);
				break;
			
			default:
				
				break;
		}
		$res = M('period')->alias("a")->field('period_id,images,period_time,product_info,target_num,now_num')->join("left join hyz_product as b on a.p_id = b.product_id")->where($where)->order($order_by)->select();
		foreach ($res as $key => $value) {
			$res[$key]['surplus_num']=$res[$key]['target_num']-$res[$key]['now_num'];
		}
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>$res
           	);

        	$this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'没有商品'
           	);

        	$this->ajaxReturn($data);
		}
	}
	//商品详情
	public function getDetail()
	{
		$product_id = I('product_id');
		$product_id = addslashes($product_id);
		$res = M('period')->alias("a")->field('images,period_time,product_info,target_num,now_num,price')->join("left join hyz_product as b on a.p_id = b.product_id")->where(array('period_id'=>$product_id))->find();
		if ($res) {
			$res['surplus_num']=$res['target_num']-$res['now_num'];
		}
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>$res
           	);

        	$this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'没有该商品'
           	);

        	$this->ajaxReturn($data);
		}
	}
        
	//商品购买记录列表
        public function productRecord(){
            $product_id = $_REQUEST['product_id'];
            if (!$product_id) {
                $res = array(
                'status'=>1,
                'msg'=>'获取失败，参数不完整',
           	);
                $this->ajaxReturn($res);die;
            }
            $where['o.order_product_id'] = $product_id;
            $where['o.order_type'] = 1;
            $join = 'hyz_user AS u ON u.user_id = o.user_id';
            $order_list = M('order')->alias('o')->join($join)->where($where)->select();
            //需要数据  购买人  地址  购买时间  头像 购买次数
            foreach ($order_list as $key => $value) {
                $data[$key]['user_name'] = $value['user_name'];
                $data[$key]['address'] = $value['province'].' '.$value['city'].' '.$value['county'].' '.$value['address'];
                $data[$key]['order_time'] = D('Suppor')->check_time($value['order_time']);
                $data[$key]['user_img'] = $value['user_img'];
                $where_user['user_id'] = $value['user_id'];
                $where_user['order_status'] = array(array('eq',1),array('eq',2),'or');
                $where_user['order_type'] = 1;
                $num = M('order')->where($where_user)->count();
                $data[$key]['num'] = $num;
            }
            $res = array(
                'status'=>0,
                'msg'=>'获取成功',
                'data' => $data
           	);

        	$this->ajaxReturn($res);
        }
}
