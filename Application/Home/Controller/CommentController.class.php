<?php
namespace Home\Controller;
use Think\Controller;
class CommentController extends Controller{
    //活动评论列表
    public function commentList(){
        //用户名 用户头像 评论时间 评论内容 评论图片 评论点赞数 子评论数量
        $where['c.comment_type'] = 2;//活动评论
        $where['c.comment_status'] = 1;//活动评论
        $where['u.status'] = 1;
        $join = 'hyz_user AS u ON u.user_id = c.user_id';
        $field = 'c.*,u.user_img,u.user_name';
        $comment_info = M('comment')->alias('c')->join($join)->field($field)->where($where)->select();
        foreach ($comment_info as $k => $v){
            $comment_info[$k]['ctime'] = D('Support')->check_time($v['ctime']);
            //筛选子评论
            $comment_info[$k]['comment_count'] = M('comment')->where(array('comment_type' => 2,'comment_status' => 1,'pid' => $v['comment_id']))->count();
        }
        $this->ajaxReturn(array('status' => 1,'msg' => '获取成功','comment_list' => $comment_info));
    }
    //发表活动评论
    public function createComment() {
        $comment_type = $_POST['comment_type'];
        $source_id = $_POST['source_id'];
        $pid = $_POST['pid'];
        $content = $_POST['content'];
        $user_id = $_POST['user_id']?:$_SESSION['user_id'];
        $images = $_POST['images'];
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        if (!$comment_type || !$source_id || !$content) {
            $ret['status'] = 2;
            $ret['msg'] = '缺少参数';
            $this->ajaxReturn($ret);
            die;
        }
        $data['comment_type'] = $comment_type;
        $data['source_id'] = $source_id;
        $data['ctime'] = time();
        $data['pid'] = $pid;
        $data['user_id'] = $user_id;
        $data['content'] = $content;
        $data['images'] = $images;
        $re = M('comment')->add($data);
        if ($re) {
            $ret['status'] = 0;
            $ret['msg'] = '评论成功';
            $this->ajaxReturn($ret);
            die; 
        }else{
            $ret['status'] = 3;
            $ret['msg'] = '评论失败';
            $this->ajaxReturn($ret);
            die;
        }
    }
    //点赞评论
    public function dzComment() {
        $comment_id = $_POST['comment_id'];
        $user_id = $_POST['user_id']?:$_SESSION['user_id'];
        //确认用户登陆
        if (!$user_id) {
            $ret['status'] = 1;
            $ret['msg'] = '请先登陆';
            $this->ajaxReturn($ret);
            die;
        }
        //确认该用户是否已经点赞过该活动评论,该评论是否已经被删除
        $comment_info = M('comment')->where(array('comment_id' => $comment_id))->find();
        if ($comment_info['comment_status'] !== 1) {
            $ret['status'] = 2;
            $ret['msg'] = '改评论状态不正确，无法点赞';
            $this->ajaxReturn($ret);
            die;
        }
        if($comment_info['like_userid']){
            $like_users = explode(',', $comment_info['like_userid']);
            if (in_array($user_id,$like_users)) {
                $ret['status'] = 3;
                $ret['msg'] = '已点赞过，无法再次点赞';
                $this->ajaxReturn($ret);
                die;
            }
            array_unshift($like_users,$user_id);
            $data['like_userid'] = implode(',', $like_users);
        }
        $data['fabulous'] = $comment_info['fabulous'] + 1;
        $data['like_userid'] = $user_id;
        $re = M('comment')->where(array('comment_id' => $comment_id))->save($data);
        if ($re) {
            $ret['status'] = 0;
            $ret['msg'] = '点赞成功';
            $this->ajaxReturn($ret);
            die; 
        }else{
            $ret['status'] = 3;
            $ret['msg'] = '点赞失败';
            $this->ajaxReturn($ret);
            die;
        }
    }
    
    //删除评论
}

