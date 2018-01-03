<?php
namespace Home\Controller;
use Think\Controller;
class RewardController extends Controller {
    //获取中奖信息
    public function rewardInfo() {
        $user_id = $_POST['user_id'];
        $period_id = $_POST['period_id']?:1;
        if (!$period_id) {
            $ret['status'] = 1;
            $ret['msg'] = '缺少参数';
            $this->ajaxReturn($ret);
            die;
        }
        //中奖信息
        $reward_info = M('reward')->where(array('period_id' => $period_id))->find();
        if (!$reward_info) {
            $ret['status'] = 2;
            $ret['msg'] = '找不到对应信息';
            $this->ajaxReturn($ret);
            die;
        }
        //活动信息
        $period_info = M('period')->where(array('period_id' => $period_id))->find();
        if (!$period_info) {
            $ret['status'] = 2;
            $ret['msg'] = '找不到对应信息';
            $this->ajaxReturn($ret);
            die;
        }
        //商品信息
        $product_info = M('product')->where(array('product_id' => $period_info['p_id']))->find();
        //用户信息
        $user_info = M('user')->where(array('user_id' => $reward_info['user_id']))->find();
        $data['user_img'] = $user_info['user_img'];
        $data['user_name'] = $user_info['user_name'];
        $data['ctime'] = D('Support')->check_time($reward_info['ctime']);
        $data['now_num'] = $period_info['now_num'];
        $data['reward_number'] = $reward_info['reward_number'];
        $data['images'] = $product_info['images'];
        $data['product_name'] = $product_info['product_name'];
        $data['price'] = $product_info['price'];
        $data['period_time'] = $period_info['period_time'];
        $data['period_name'] = $period_info['period_name'];
        $data['status_period'] = $period_info['status_period'];
        //上期商品详情
        $last_product_id = M('period')->field('p_id')->where(array('period_time' => array('LT',$period_info['period_time'],'status_period' => '1','order_product_id' => array('neq',''))))->find();
        $last_product_info = M('product')->where(array('product_id' => $last_product_id['p_id']))->find();
        $data['last_product_info'] = $last_product_info;
        //下期正常众筹的期次
        $next_period_info = M('period')->where(array('period_time' => array('GT',$period_info['period_time'],'status_period' => '1')))->find();
        $data['next_period_time'] = $next_period_info['period_time'];
        $data['next_period_id'] = $next_period_info['period_id'];
        //晒单量
        $data['comment_num'] = M('comment')->where(array('source_id' => $period_id))->count();
        $ret['status'] = 0;
        $ret['msg'] = '获取成功';
        $ret['data'] = $data;
        $this->ajaxReturn($ret);
    }

    //首页最新一期获奖信息
    public function indexRewardInfo(){
        $reward_info = M('reward')->where(array('reward_status' => '1'))->order('period_id desc')->find();
        $user_info = M('user')->field('user_name,real_name')->where(array('user_id' => $reward_info['user_id']))->find();
        $product_info = M('product')->field('product_name,price')->where(array('product_id' => $reward_info['product_id']))->find();
        $data['user_info'] = $user_info;
        $data['product_info'] = $product_info;
        $ret['status'] = 0;
        $ret['msg'] = '获取成功';
        $ret['data'] = $data;
        $this->ajaxReturn($ret);
    }

    //期次列表
    public function getPeriodList(){
        $period_list = M('period')->field('period_id,period_time')->where(array('status_period'=> array('eq',2)))->select();
        foreach ($period_list as $k => $v){
            $list[$v['period_time']] = $v['period_id'];
        }
        $list['period_sum'] = count($period_list);
        $ret['status'] = 0;
        $ret['msg'] = '获取成功';
        $ret['data'] = $list;
        $this->ajaxReturn($ret);
    }

    //用户待兑奖列表
}
