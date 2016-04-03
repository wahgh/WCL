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
            /**
             * 外语水平列表
             */
            $level = M('level');
            $this->level_list = $level->field('id,name')->select();
            /**
             * 学历列表
             */
            $degree = M('degree');
            $this->degree_list = $degree->field('id,name')->select();
            /**
             * 民族列表
             */
            $nation = M('nation');
            $this->nation_list = $nation->field('id,name')->select();
            /**
             * 省份列表
             */
            $province = M('province');
            $this->province_list = $province->field('id,name')->select();
            /**
             * 工作年限列表
             */
            $worktime = M('worktime');
            $this->worktime_list = $worktime->field('id,name')->select();
            $this->display();
        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，不能创建简历', '/User/Index/login');
        }
    }

    /***
     * ajax方式获取行业对应的职位
     * @param $industry_id
     */
    public function getCity($province_id)
    {
        /**
         * ajax请求传过来的行业id,,为城市表的外键
         */
        $province_id = I('post.province_id');
        /**
         * 获取城市信息
         */
        $city = M('city');
        $city_list = $city->where(['province_id' => $province_id])->field('id,name')->order('id')->select();
        /**
         * 转换成json格式返回
         */
        $this->ajaxReturn($city_list);
    }

    /**
     * 对简历的保存
     */
    public function save()
    {

    }
}