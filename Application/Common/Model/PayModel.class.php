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
    //微信H5支付
    function h5Pay($body,$order_id,$total_fee)
    {

        require_once ("WxPay/WxPay.Api.php");
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
        $input->SetScene_info('{"h5_info": {"type":"Wap","wap_url": "https://pay.qq.com","wap_name": "腾讯充值"}} ');
        //$input->SetOpenid($openId);
        //---
        //var_dump($input);
        //die;
        $order = \WxPayApi::unifiedOrder($input);
        return $order;
    }
    //支付宝支付(h5)
    public function alipay($out_trade_no,$subject,$total_amount,$body='')
    {
        //require_once ("alipay//service/AlipayTradeService.php");
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'alipay/wappay/service/AlipayTradeService.php'; 
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'alipay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
        require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'alipay/config.php';

        if (!empty($out_trade_no)&& trim($out_trade_no)!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            //$out_trade_no = $_POST['WIDout_trade_no'];

            //订单名称，必填
            //$subject = $_POST['WIDsubject'];

            //付款金额，必填
            //$total_amount = $_POST['WIDtotal_amount'];

            //商品描述，可空
            //$body = $_POST['WIDbody'];

            //超时时间
            $timeout_express="1m";
            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
            $payResponse = new \AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
            //var_dump($result);
            return ;
        }
    }
    //支付宝APP支付
    public function alipayApp($amount,$subject,$body,$out_trade_no)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');

        require_once 'aop/AopClient.php';
        require_once 'aop/request/AlipayTradeAppPayRequest.php';

        // 获取支付金额
        // $amount='';
        // if($_SERVER['REQUEST_METHOD']=='POST'){
        //     $amount=$_POST['total'];
        // }else{
        //     $amount=$_GET['total'];
        // }

        $total = floatval($amount);
        if(!$total){
            $total = 1;
        }

        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2016040601271593";
        $aop->rsaPrivateKey = 'MIICXgIBAAKBgQDY6Xu8PgNfPiLcs5mN1Hdy536LHmJKwhVUha26ovop0U3VpFI91p9uPT2uD/CEaWao2fxk1Xai7wGkKAPcArfhq2lff2h1FgGgOsEnU2cCPCYfZzkykEuAcLu8FDpTgcmFsOmhkH2M1ggU4PTpxVtk2YhRuDJ4YVFbi96a+s/2TwIDAQABAoGAQ2qmA4a+o457ZV8IvbUofvUGNpT19chGuuNlcJmQ6QhdiaYtzXx0Rt4P2panqW/c8WP29xwFaHSibPPm5y2NGqrGAVdDdI0+eSYZM8vf/JqMVYddNpUitT7e2Yz2L6c2sF/AjA1QbDmL6ycBCO8oRJtDJlTEpiyKcpuQxJ9w+VECQQD8uFFnUsePD9RhfLe2CGWFQVADmUGCCtFFHo4mbFCV4tvEoWo4Y5PZmTEKT7V9aD9xzxo5ViRYxzTiVbGMX/pHAkEA27owzcjWtD1dCn4DCav5uW7uycGs1OAkzPmP9m4FXgPnAtSN49Psd/ahI/MQgoCKWHs/nZcHQ9DOn1ZcSe6fuQJBAIL5T4758tys+ofPqDJaJasrDzneOnoX+x8UV92H8zfLC9TbRv6UdPKoFydd6hRla8Qi7lda0pmEHF9UxCCZOU8CQQC1eQZ7v0dMX23rj32UzFuYwj0nlvTWcDUUsX0sC1wVdOWjmceChfJEdgZKeaKGFgGs6EX3MthGMzujr6DTUujJAkEA+cUqpWh4p51y6jdP8XlAlVFpks91lJ4fuLTHgX1UhpHBfuxaFxhmsqDegbYyJscow09PF3p0nSiDHXAie4DkjA==';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDY6Xu8PgNfPiLcs5mN1Hdy536LHmJKwhVUha26ovop0U3VpFI91p9uPT2uD/CEaWao2fxk1Xai7wGkKAPcArfhq2lff2h1FgGgOsEnU2cCPCYfZzkykEuAcLu8FDpTgcmFsOmhkH2M1ggU4PTpxVtk2YhRuDJ4YVFbi96a+s/2TwIDAQAB';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();

        // 异步通知地址
        $notify_url = urlencode('http://www.yckj888.com/notifyurl.php');
        // 订单标题
        //$subject = 'DCloud项目捐赠';
        // 订单详情
        //$body = 'DCloud致力于打造HTML5最好的移动开发工具，包括终端的Runtime、云端的服务和IDE，同时提供各项配套的开发者服务。'; 
        // 订单号，示例代码使用时间值作为唯一的订单ID号
        //$out_trade_no = date('YmdHis', time());

        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = "{\"body\":\"".$body."\","
                        . "\"subject\": \"".$subject."\","
                        . "\"out_trade_no\": \"".$out_trade_no."\","
                        . "\"timeout_express\": \"30m\","
                        . "\"total_amount\": \"".$total."\","
                        . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                        . "}";
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);

        // 注意：这里不需要使用htmlspecialchars进行转义，直接返回即可
        echo $response;
    }

}
