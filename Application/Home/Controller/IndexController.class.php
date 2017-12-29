<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
//        $this->display();
      }

    //生成首页轮播
    public function indexCarousel(){
        $join = 'hyz_advert_list as l on l.aid = a.id';
        $where['a.status'] = 1;
        $order = 'a.id desc';
        $advert_list = M('advert')->alias('a')->join($join)->where($where)->order($order)->select();
        if ($advert_list){
            $data = array(
                'status'=>0,
                'msg'=>'获取成功',
                'data'=>$advert_list
            );
        }else{
            $data = array(
                'status'=>1,
                'msg'=>'空数据',
                'data'=>$advert_list
            );
        }
        $this->ajaxReturn($data);
    }


}