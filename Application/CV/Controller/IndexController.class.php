<?php
namespace CV\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        /**
         * 获取行业信息
         */
        $industry = M('industry');
        $this->indsutry_list = $industry->field('id,name')->order('id')->select();
        /**
         * 调用home模板
         */
        $this->display();
    }

    /***
     * ajax方式获取行业对应的职位
     * @param $industry_id
     */
    public function getFunction($industry_id)
    {
        /**
         * ajax请求传过来的行业id,,为职位表的外键
         */
        $industry_id = I('post.industry_id');
        /**
         * 获取职位信息
         */
        $function = M('function');
        $function_list = $function->where(['industry_id' => $industry_id])->field('id,name')->order('id')->select();
        /**
         * 转换成json格式返回
         */
        $this->ajaxReturn($function_list);
    }

    /**
     * 创建一个简历
     */
    public function createcv()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            $this->display();
        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，不能创建简历', '/User/Index/login');
        }
    }
}