<?php
namespace Home\Controller;
use Think\Controller;
class ActivityController extends Controller {
	//活动列表
	public function activityList()
	{
		$res = M('activity')->field('activity_id,images,title')->where(array('status'=>1))->order('ctime desc')->select();
		if ($res) {
			$data = array(
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
		$res = M('activity')->field('activity_id,images,activity_name,price,now_num,stop_time,activity_info')->where(array('activity_id'=>$activity_id))->find();
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
		$orderNumber = D('Support')->orderNumber();
		echo $orderNumber;die;
		$user_id = I('user_id');
		$activity_id = I('activity_id');
		$name = I('name');
		$sex = I('sex');
		$age = I('age');
		$job = I('job');
		$card = I('card');
		$phone = I('name');
		$company = I('company');
		$address = I('address');
		$djson = I('djson');
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

		//插入数据
		$indata = array(
					'apply_type'=>1,
					'activity_id'=>$activity_id,
					'user_id'=>$user_id,
					'apply_real_name'=>$name,
					'sex'=>$sex,
					'age'=>$age,
					'id_card_no'=>$card,
					'tel'=>$phone,
					'company'=>$company,
					'job'=>$job,
					'ctime'=>time(),
					'apply_status'=>1,
					'address'=>$address,
					'other_info'=>$djson
			);
		$res = M('apply')->add($indata);
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>'报名成功'
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
}
