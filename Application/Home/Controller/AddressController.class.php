<?php
namespace Home\Controller;
use Think\Controller;
class AddressController extends Controller {
	//添加地址
	public function index()
	{
		$province = I('province');
		$city = I('city');
		$county = I('county');
		$address = I('address');
		$userid = I('userid');
		if (!$province) {
			$data = array(
                'status'=>1,
                'msg'=>'省份不能为空'
           	);

        	$this->ajaxReturn($data);
		}
		if (!$userid) {
			$data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空'
           	);

        	$this->ajaxReturn($data);
		}
		if (!$city) {
			$data = array(
                'status'=>1,
                'msg'=>'城市不能为空'
           	);

        	$this->ajaxReturn($data);
		}
		if (!$county) {
			$data = array(
                'status'=>1,
                'msg'=>'区县不能为空'
           	);

        	$this->ajaxReturn($data);
		}
		if (!$address) {
			$data = array(
                'status'=>1,
                'msg'=>'详细地址不能为空'
           	);

        	$this->ajaxReturn($data);
		}
		$adddata = array(
				'user_id'=>$userid,
				'province'=>$province,
				'city'=>$city,
				'county'=>$county,
				'address'=>$address,
				'ctime'=>time(),
			);
		$res = M('address')->add($adddata);
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>$res
           	);

        	$this->ajaxReturn($data);
		}else{
			$data = array(
                'status'=>1,
                'msg'=>'添加失败'
           	);

        	$this->ajaxReturn($data);
        }
	}
	//获取用户地址列表
	public function getList()
	{
		$userid = I('userid');
		if (!$userid) {
			$data = array(
                'status'=>1,
                'msg'=>'用户ID不能为空'
           	);

        	$this->ajaxReturn($data);
		}
		$res = M('address')->where(array('user_id'=>$userid))->select();
		$data = array(
                'status'=>0,
                'msg'=>$res
           	);
        $this->ajaxReturn($data);
	}
}