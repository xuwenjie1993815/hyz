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
		$res = M('period')->alias("a")->field('images,period_time,product_info,target_num,now_num')->join("left join hyz_product as b on a.p_id = b.product_id")->where($where)->order($order_by)->select();
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
}
