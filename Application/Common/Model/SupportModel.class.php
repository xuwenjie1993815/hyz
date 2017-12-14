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
	    //上传图片
    public function upload()
    {
      $upload = new \Think\Upload();// 实例化上传类
      $upload->maxSize   =     3145728 ;// 设置附件上传大小
      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
      $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
      $upload->savePath  =     ''; // 设置附件上传（子）目录
      $upload->saveName = 'com_create_guid';
      $upload->subName = array('date','Ymd');
      // 上传文件 
      $info   =   $upload->upload();

      if(!$info) {// 上传错误提示错误信息
        $this->error($upload->getError());
        return;
      }else{// 上传成功
         foreach($info as $file){
         $filename[] =  $file['savepath'].$file['savename'];
        }
        return $filename;
      }
    }

}