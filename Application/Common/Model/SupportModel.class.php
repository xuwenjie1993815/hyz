<?php
//namespace Think\Model;
namespace Common\Model;
use Think\Model;

class  SupportModel extends Model {
    protected $autoCheckFields = false; //关闭检测字段
	//生成唯一订单编号
    public function orderNumber()
    {
    	$order_number = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    	return $order_number;
    }
    
    public function check_time($param){
		$today = time();
		$if = $today - $param;
		if($if < 30){
			$exp = '刚刚';
		}else if($if > 30 && $if < 60){
			$exp = '1分钟之前';
		}else if($if > 60 && $if < 120){
			$exp = '2分钟之前';
		}
		else if($if > 120 && $if < 180){
			$exp = '3分钟之前';
		}
		else if($if > 180 && $if < 240){
			$exp = '4分钟之前';
		}
		else if($if > 240 && $if < 300){
			$exp = '5分钟之前';
		}
		else if($if > 300 && $if < 600){
			$exp = '10分钟之前';
		}
		else if($if > 600 && $if < 1800){
			$exp = '30分钟之前';
		}
		else if($if > 1800 && $if < 3600){
			$exp = '1小时之前';
		}else{
			$exp = date('Y-m-d H:i',$param);
		}
		return $exp;
	}

}