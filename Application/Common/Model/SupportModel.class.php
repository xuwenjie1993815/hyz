<?php
namespace Think\Model;
namespace Common\Model;
use Think\Model;

class  SupportModel extends Model {
	//生成唯一订单编号
    public function orderNumber()
    {
    	$order_number = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    	return $order_number;
    }
}