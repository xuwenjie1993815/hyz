<?php
namespace Home\Controller;
use Think\Controller;
class NoticeController extends Controller
{
    //通知列表
    public function notice_list()
    {
        //中奖表  通知表
        $user_id = $_POST['user_id'];
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        if ($_POST['type']){
            $where['n.type'] = $_POST['type'];
        }
        $where['n.notice_status'] = '1';
        $where['n.user_id'] = $user_id;
        $join = 'hyz_admin as a on a.id = n.shop_id';
        $notice_list = M('notice')->alias('n')->join($join)->field('n.notice_title,n.content,n.add_time,a.nick_name')->where($where)->select();
        foreach ($notice_list as $k => $v){
           $notice_list[$k]['add_time'] = D('Support')->check_time($v['add_time']);
        }
        $ret['status'] = 0;
        $ret['msg'] = '获取成功';
        $ret['data'] = $notice_list;
        $this->ajaxReturn($ret);
        die;
    }

    //获取通知详情
    public function notice_info(){
        $user_id = $_POST['user_id'];
        $notice_id = $_POST['notice_id'];
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        if (!$notice_id){
            $ret['status'] = 2;
            $ret['msg'] = '缺少参数';
            $this->ajaxReturn($ret);
            die;
        }
        $where['n.notice_id'] = $notice_id;
        $join = 'hyz_admin as a on a.id = n.shop_id';
        $notice_info = M('notice')->alias('n')->join($join)->field('n.*,a.nick_name')->where($where)->find();
        $notice_info['add_time'] = D('Support')->check_time($notice_info['add_time']);
        if (!$notice_info){
            $ret['status'] = 3;
            $ret['msg'] = '获取失败';
            $this->ajaxReturn($ret);
            die;
        }
        if($notice_info['notice_status'] !== '1'){
            $ret['status'] = 4;
            $ret['msg'] = '通知已删除';
            $this->ajaxReturn($ret);
            die;
        }
        if($notice_info['user_id'] != $user_id){
            $ret['status'] = 5;
            $ret['msg'] = '您无权查看';
            $this->ajaxReturn($ret);
            die;
        }
        $ret['status'] = 0;
        $ret['msg'] = '获取成功';
        $ret['data'] = $notice_info;
        $this->ajaxReturn($ret);
        die;
    }

    //发送通知

    //删除通知
}