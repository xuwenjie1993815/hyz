<?php
namespace Home\Controller;
use Think\Controller;
class NoticeController extends Controller
{
    //通知列表
    function notice_list()
    {
        //中奖表  通知表
        $user_id = $_POST['user_id']?:3;
        $user_info = M('user')->where(array('user_id' => $user_id))->find();
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        $where['notice_status'] = '1';
        $where['user_id'] = $user_id;
        $notice_list = M('notice')->where($where)->select();
        $ret['status'] = 0;
        $ret['msg'] = '获取成功';
        $ret['notice_list'] = $notice_list;
        $this->ajaxReturn($ret);
        die;
    }

    //获取通知详情

}