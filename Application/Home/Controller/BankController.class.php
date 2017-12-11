<?php
namespace Home\Controller;
use Think\Controller;
class BankController extends Controller {
	//添加银卡卡
	public function addBankCard()
	{
		$name = I('name');
		$card = I('card');
		$user_id = I('user_id');
		if (!$name) {
			$data = array(
                'status'=>1,
                'msg'=>'姓名不能为空'
           	);
        	$this->ajaxReturn($data);
		}
		if (!$card) {
			$data = array(
                'status'=>1,
                'msg'=>'银行卡不能为空'
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
		$check = M('bank_account')->field('id')->where(array('user_id'=>$user_id,'account_no'=>$card))->find();
		if ($check) {
			$data = array(
                'status'=>1,
                'msg'=>'银行卡已被添加'
           	);
        	$this->ajaxReturn($data);
		}
		$indata = array(
				'user_id'=>$user_id,
				'account_no'=>$card,
				'account_name'=>$name,
				'is_default'=>1,
				'add_time'=>time(),
			);
		$res = M('bank_account')->add($indata);
		if ($res) {
			$data = array(
                'status'=>0,
                'msg'=>'添加成功'
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
}