<?php

namespace Common\Model;

use Think\Model;

class PayModel extends Model {
    private $request;

    function __initial() {
        ini_set('date.timezone', 'Asia/Shanghai');
    }

    //获取配置文件
    function getConfig_array($key = NULL) {
        if ($key) {
            $configSearch = C("CONFIG_PATH") . "/pay-" . $key . ".config.php";
        } else {
            $configSearch = C("CONFIG_PATH") . "/pay.config.php";
        }
        $match = file_get_contents($configSearch);
        $match = substr($match, 15);
        $s_info = preg_replace("/\/\*[\s\S]+?\*\//", "", $match);
        $s_info = json_decode($s_info, true);
        return $s_info;
    }

    //检查支付前参数质量
    private function inputCheck($demand) {
        //检查order
        $order_id = (int) $this->request['order_id'];
        if (!$order_id || $order_id == 0) {
            return array('state' => 0, 'errormsg' => 'order_id missing');
        }
        $orderFind = M("pay")->where("ORDER_ID = '$order_id'")->find();
        if ($orderFind) {
            return array('state' => 0, 'errormsg' => 'order paid');
        }
        //检查账单价格
        $total_fee = (int) $this->request['total_fee'];
        if ($total_fee <= 0) {
            return array('state' => 0, 'errormsg' => 'total_fee incorrect');
        }
        require_once ("WxPay/WxPay.Config.php");
        $WxPayConfig = new \WxPayConfig();
        if ($total_fee > $WxPayConfig::FEE_MAX) {
            return array('state' => 0, 'errormsg' => 'fee cannot more than ' . ($WxPayConfig::FEE_MAX / 100) . 'RMB');
        }
        //检查账单参数(通过配置文件)
        $config = $this->getConfig_array();
        $demand = strtolower($demand);
        $config = $config[$demand];
        if (!$config) {
            return array('state' => 0, 'errormsg' => 'config demand incorrect');
        }
        foreach ($config['neccessary'] as $key => $reg) {
            if ($reg && !preg_match($reg, $this->request[$key])) {
                return array('state' => 0, 'errormsg' => "need correct $key");
            } elseif (!$this->request[$key]) {
                return array('state' => 0, 'errormsg' => "need $key");
            }
            $payData[$key] = $this->request[$key];
        }
        $payData['order_id'] = $order_id;
        $payData['total_fee'] = $total_fee;
        return array('state' => 1, 'errormsg' => 'ok', 'payData' => $payData);
    }

    //储存进数据库 此处强制执行储存
    private function saveIntoDatabase($data) {
        $order_id = $data['order_id'];
        $order_id_find = M('order')->where("ORDER_ID = '$order_id'")->find();
        if ($order_id_find) {
            return array('state' => 0, 'errormsg' => 'order_id paid');
        }

        $config = $this->getConfig_array("type");
        foreach ($config as $key => $arr) {
            $data[$key] = $arr[$data[$key]];
        }
        $sqlData = array_change_key_case($data, CASE_UPPER);
        $sqlData['user_id'] = D('user')->getUserId();
        $sqlData['create_time'] = time();
        $sqlData = array_change_key_case($sqlData, CASE_UPPER);
        $res = M('pay')->Add($sqlData);
        //回调改变状态
        $pay_info = M('pay')->where("ORDER_ID = '$order_id'")->find();
        if ($pay_info) {
            D('order')->payOrder($order_id);
        }
        return $res;
    }

    //根据条件调用储存进数据库
    private function paySaveIntoDatabase($result) {
        //var_dump($result);
        if ($result['state']) {
            $copy = "order_id,openid,total_fee,bank_type,trade_type,transaction_id";
            $sqlData = array(
                "channel" => 'wxpay',
            );
            foreach (explode(",", $copy) as $key) {
                $sqlData[$key] = $result['data'][$key];
                //var_dump($key,$result['data'][$key]);
            }
            //var_dump($sqlData);
            D('action')->add('pay receive', $order_id, 'wx.micro');
            $this->saveIntoDatabase($sqlData);
        } else {
            D('action')->add('pay receive', $order_id, 'fail');
        }
    }

    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------
    //--------------------------------------------查询订单支付情况-------------------------------------------

    function wx_orderQuery($request) {
        $this->request = $request;
        $order_id = (int) $this->request['order_id'];
        if (!$order_id || $order_id == 0) {
            return array('state' => 0, 'errormsg' => 'order_id missing');
        }
        $orderFind = M("pay")->field('total_fee')->where("ORDER_ID = '$order_id'")->find();
        if ($orderFind) {
            return array('state' => 1, 'errormsg' => 'order paid', 'data' => array('total_fee' => $orderFind['total_fee']));
        }
        require_once "WxPay/WxPay.Api.php";
        $input = new \WxPayOrderQuery();
        $input->SetOut_trade_no($order_id);
        $result = \WxPayApi::orderQuery($input);
        if ($result['err_code_des']) {
            $result = array('state' => 0, 'errormsg' => $result['err_code_des']);
            return $result;
        }
        if ($result['trade_state'] == 'SUCCESS') {
            $result = array('state' => 1, 'errormsg' => 'pay success', 'data' => $result);
            D('order')->payOrder($order_id);
        } else {
            $result = array('state' => 0, 'errormsg' => $result['trade_state']);
        }
        $result['data']['order_id'] = $order_id;
        $this->paySaveIntoDatabase($result);
        return $result;
    }

    //--------------------------------------------刷卡支付启动函数--------------------------------------------
    //--------------------------------------------刷卡支付启动函数--------------------------------------------
    //--------------------------------------------刷卡支付启动函数--------------------------------------------
    //--------------------------------------------刷卡支付启动函数--------------------------------------------
    //--------------------------------------------刷卡支付启动函数--------------------------------------------
    //--------------------------------------------刷卡支付启动函数--------------------------------------------

    function wx_microPay($request) {
        $this->request = $request;
        $res = $this->inputCheck("wxMicroPay");
        if (!$res['state']) {
            return $res;
        }
        if (!isset($this->request["auth_code"]) || $this->request["auth_code"] == "") {
            return array('state' => 0, 'errormsg' => "auth_code missing");
        }
        $payData = $res['payData'];
        $auth_code = $this->request["auth_code"];

        //商品(订单)名称 价格 订单号
        $body = $payData['body'];
        $total_fee = $payData['total_fee'];
        $order_id = $payData['order_id'];

        //调用微信支付接口
        require_once ("WxPay/WxPay.MicroPay.php");
        $input = new \WxPayMicroPay();
        $input->SetAuth_code($auth_code);
        $input->SetBody($body);
        $input->SetTotal_fee($total_fee);
        $input->SetOut_trade_no($order_id);
        $microPay = new \MicroPay();
        $result = $microPay->pay($input);
        $result['data']['order_id'] = $order_id;
        $this->paySaveIntoDatabase($result);
        return $result;
    }

    //--------------------------------------------网页订单支付--------------------------------------------
    //--------------------------------------------网页订单支付--------------------------------------------
    //--------------------------------------------网页订单支付--------------------------------------------
    //--------------------------------------------网页订单支付--------------------------------------------
    //--------------------------------------------网页订单支付--------------------------------------------
    //--------------------------------------------网页订单支付--------------------------------------------
    //--------------------------------------------网页订单支付--------------------------------------------

    function wx_jsApiPay($request) {
//        $user_id = D('user')->getUserId();
        $user_id = $request['user_id'];
        if (!$user_id) {
            return array('state'=>1,'errormsg'=>'not login');
        }
//        $openId = D('user')->getOpenId();
        $openId = $request['openId'];
        if (!$openId) {
            return array('state' => 2, 'errormsg' => 'need openid');
            require_once ("WxPay/WxPay.JsApiPay.php");
            $tools = new \JsApiPay();
            $openId = $tools->GetOpenid();
        }
        $this->request = $request;
        //检查数据
        $res = $this->inputCheck("wxJsApiPay");
        if (!$res['state']) {
            return $res;
        }
        $payData = $res['payData'];
        //商品(订单)名称 价格 订单号
        $body = $payData['body'];
        $total_fee = $payData['total_fee'];
        $order_id = $payData['order_id'];
        //
        require_once ("WxPay/WxPay.JsApiPay.php");
        $input = new \WxPayUnifiedOrder();
        //---data
        $input->SetBody($body);
        //$input->SetAttach("test");
        $input->SetOut_trade_no($order_id);
        $input->SetTotal_fee($total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        //$input->SetGoods_tag("test");
        require_once ("WxPay/WxPay.Config.php");
        $input->SetNotify_url(\WxPayConfig::NOTIFY_URL);
        $input->SetTrade_type("MWEB");
        $input->SetOpenid($openId);

        $input->SetScene_info('{"h5_info": {"type":"Wap","wap_url": "https://pay.qq.com","wap_name": "腾讯充值"}}');
        //---
//        var_dump($input);die;
        $order = \WxPayApi::unifiedOrder($input);
        //var_dump($input,$order);
        $tools = new \JsApiPay();
        $result = $tools->GetJsApiParameters($order);
        return $result;
    }

    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------
    //--------------------------------------------被动接收订单支付结果--------------------------------------------

    function wx_jsApiReceive() {
        require_once ("WxPay/log.php");
        require_once ("WxPay/WxPay.Config.php");
        $logHandler = new \CLogFileHandler(\WxPayConfig::LOG_PATH . date('Y-m-d') . '.log');
        $log = \Log::Init($logHandler, 15);

        require_once ("WxPay/WxPay.Api.php");
        require_once ("WxPay/WxPay.Notify.php");

        \Log::DEBUG("begin notify");
        $notify = new \PayNotifyCallBack();
        $data = $notify->Handle(true);
        if ($data) {
            $order_id = $data['out_trade_no'];
            $orderFind = M("pay")->where("ORDER_ID = '$order_id'")->find();
            if (!$orderFind) {
                $result = array('state' => 1, 'errormsg' => 'pay success', 'data' => $data);
                $result['data']['order_id'] = $order_id;
                $this->paySaveIntoDatabase($result);
            }
            //$result['order_id'] = $result['out_trade_no'];
        }
        //require_once ("WxPay/WxPay.JsApiPay.php");
    }

    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------
    //--------------------------------------------网页扫码支付--------------------------------------------

    function wx_nativePay($request) {
        /* $user_id = D('user')->getUserId();
          if(!$user_id){
          //return array('state'=>0,'errormsg'=>'not login');
          }
          $openId = D('user')->getOpenId();
          if(!$openId){
          return array('state'=>0,'errormsg'=>'need openid');
          require_once ("WxPay/WxPay.JsApiPay.php");
          $tools = new \JsApiPay();
          $openId = $tools->GetOpenid();
          } */
        $this->request = $request;
        //检查数据
        $res = $this->inputCheck("wxNativePay");
        if (!$res['state']) {
            return $res;
        }
        $payData = $res['payData'];
        //商品(订单)名称 价格 订单号
        $body = $payData['body'];
        $total_fee = $payData['total_fee'];
        $order_id = $payData['order_id'];
        //
        require_once ("WxPay/WxPay.NativePay.php");
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body);
        //$input->SetAttach("test");
        $input->SetOut_trade_no($order_id);
        $input->SetTotal_fee($total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        //$input->SetGoods_tag("test");
        require_once ("WxPay/WxPay.Config.php");
        $input->SetNotify_url(\WxPayConfig::NOTIFY_URL);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("-");
        //var_dump($input);
        $notify = new \NativePay();
        $result = $notify->GetPayUrl($input);
        if ($result["code_url"]) {
            $ret = array(state => 1, errormsg => 'ok', data => $result);
        } else {
            $ret = array(state => 0, errormsg => $result['err_code'], data => $result);
        }
        //$url2 = $result["code_url"];
        return $ret;
    }

    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    //--------------------------------------------退款--------------------------------------------
    function refund($order_id) {
        $user_id = D('user')->getUserId();
        if (!$user_id) {
            return array('state' => 0, 'errormsg' => 'not logined');
        }
        $order_id = (int) $order_id;
        //$this->request = $request;
        //$order_id = (int)$this->request['order_id'];
        if (!$order_id || $order_id == 0) {
            return array('state' => 0, 'errormsg' => 'order_id missing');
        }
        $orderFind = M("pay")->field('total_fee,pay_id')->where("ORDER_ID = '$order_id'")->find();
        if (!$orderFind) {
            return array('state' => 0, 'errormsg' => 'order incorrect');
        }
        $pay_id = $orderFind['pay_id'];
        $refundFind = M("pay_refund")->where("PAY_ID = '$pay_id'")->find();
        if ($refundFind) {
            D('action')->add('refund', $pay_id, 'refunded');
            return array('state' => 0, 'errormsg' => 'order refunded');
        }
        $refund_fee = $total_fee = $orderFind['total_fee'];
        $out_trade_no = $order_id;
        /* $total_fee = $this->request["total_fee"];
          $refund_fee = $this->request["refund_fee"];
          if(!$total_fee || total_fee == ""){
          return array('state'=>0,'errormsg'=>'total_fee missing');
          }
          if(!$refund_fee || refund_fee == ""){
          return array('state'=>0,'errormsg'=>'refund_fee missing');
          } */
        //var_dump($refund_fee);
        require_once "WxPay/WxPay.Api.php";
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no(\WxPayConfig::MCHID . date("YmdHis"));
        $input->SetOp_user_id(\WxPayConfig::MCHID);
        $result = \WxPayApi::refund($input);
        //var_dump($result);
        if ($result['result_code'] == "SUCCESS") {
            $ret = array('state' => 1, 'errormsg' => 'refund success', 'data' => $result);
            $arr = array('pay_id' => $pay_id, 'user_id' => $user_id, 'create_time' => time());
            $arr = array_change_key_case($arr, CASE_UPPER);
            M('pay_refund')->add($arr);
            D('action')->add('refund', $pay_id, 'success');
        } else {
            $ret = array('state' => 0, 'errormsg' => $result['err_code']);
            D('action')->add('refund', $pay_id, 'fail');
        }
        return $ret;
    }

    function getListForExcel() {
        $m = M('pay')->select();
        return $m;
    }
    
    function getListForAdmin(){
       $where = ""; 
       if (!$filter['page']) {
            $page = 0;
        } else {
            $page = $filter['page'];
        }
        if (!$filter['maxPerPage']) {
            $maxPerPage = 10;
        } else {
            $maxPerPage = $filter['maxPerPage'];
        }
        // var_dump($filter['keywords']);
        if ($filter['keywords']) {
            $keywords = $filter['keywords'];
            $where .= " AND ORDER_ID = '$keywords'";
        }
       $payList = M('pay')->where($where)->order('CREATE_TIME desc')->limit($page * $maxPerPage, $maxPerPage)->select();
       $pages = $page + 1;
       $payList_a = M('pay')->where($where)->order('CREATE_TIME desc')->limit($pages * $maxPerPage, $maxPerPage)->select();
       $productdata = $payList_a[0];
       if ($productdata) {
           $more = 1;
       } else {
           $more = 0;
       }
       return array('data' => array('list' => $order_list_info_b, 'more' => $more, 'maxPerPage' => $maxPerPage), 'state' => 1, 'errormsg' => 'ok');
    }

}
