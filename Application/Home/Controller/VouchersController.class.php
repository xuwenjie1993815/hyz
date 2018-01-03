<?php
namespace Home\Controller;
use Think\Controller;
class VouchersController extends Controller {
	//代金券
	public function index()
	{
		$user_id = 	I('user_id');
		$user_id= addslashes($user_id);
		$info = M('vouchers')->alias('v')
				->field('v.code,v.instructions,p.period_time,p.period_name,s.shop_name,s.tel,s.province,s.city,s.county,s.shop_address')
				->join("left join hyz_period as p on v.period_id = p.period_id")
				->join("left join hyz_shop as s on v.shop_id = s.shop_id")
				->where(array('v.user_id'=>$user_id,'v.status'=>1))
				->select();
		$this->ajaxReturn($info);
	}
}