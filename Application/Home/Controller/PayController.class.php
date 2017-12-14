<?php

namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
	
	public function scan(){//扫码
		$pay = D("pay");
		$this->ajaxReturn($pay->wx_microPay($_REQUEST));
	}
	public function wx_webcode(){//微信APP支付
		$pay = D("pay");
		$this->ajaxReturn($pay->wx_jsApiPay($_REQUEST));
	}
	public function wx_qrcode(){//微信二维码支付
		$pay = D("pay");
		$this->ajaxReturn($pay->wx_nativePay($_REQUEST));
	}
	
	public function wx_receive(){//微信APP支付 微信扫码支付 被动回执接口 可以在查看配置的logs文件夹中查看模型操作
		$pay = D("pay");
		$pay->wx_jsApiReceive($_REQUEST);
		//$this->ajaxReturn();
	}
	public function query(){
		$pay = D("pay");
		$this->ajaxReturn($pay->wx_orderQuery($_REQUEST));
	}
	public function excel(){
		$pay = D("pay");
		$data = $pay->getListForExcel();
		$filename = 'bill-'.time();
		$content = array(
						 'filecontent'=> $data,'title'=>$title,'filename'=>$filename,'sheet'=>$sheet
						 
						 
						 );
		D('support')->exportExcel( $content );
	}
}
