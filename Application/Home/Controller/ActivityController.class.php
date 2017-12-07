<?php
namespace Home\Controller;
use Think\Controller;
use LT\ThinkSDK\ThinkOauth;
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
		
	}
}
