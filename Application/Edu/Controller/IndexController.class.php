<?php
namespace Edu\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>', 'utf-8');
    }

    public function eduAdd()
    {
        /**
         * 新增求职意向
         */

        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            /**
             * 通过user_id 查看该用户是否已经创建了简历基本信息，如果已经创建，则不能重复创建
             */
            $cv = M('cv');
            $cvinfo = $cv->where(['user_id' => $user_id])->field('id,realname')->select();
            if ($cvinfo) {
                /**
                 * 基本信息已经创建，才能增加工作意向,把cv_id值也传过去，求职表新增值可以直接插入了
                 */
                $this->assign('cv_id', $cvinfo[0]['id']);
                /**
                 * 获取学位表信息
                 */
                $degree = M('degree');
                $this->degree_list = $degree->field('id,name')->order('id')->select();
                $this->display();
            } else {
                /**
                 * 说明没有创建基本信息，那就先去创建基本信息
                 */
                $this->error('您还没有创建个人基本信息', '/CV/Index/BasicAdd');
            }

        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，不能创建简历', '/User/Index/login');
        }

    }

    /**
     * 求职意向保存
     */
    public function eduSave()
    {
        $edu = D("edu"); // 实例化User对象
        if (!$edu->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($edu->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $result = $edu->add();
            if ($result) {
                /**
                 * 回到简历主页
                 */
                $this->success('教育经历信息保存成功，您可以添加多个教育经历！', '/Edu/Index/eduAdd');
            } else {
                $this->error('写入错误');
            }
        }
    }

    public function eduEditList()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            /**
             * 通过user_id 查看该用户是否已经创建了简历，如果还没有创建，就先去创建简历
             */
            $cv = M('cv');
            $cvinfo = $cv->where(['user_id' => $user_id])->field('id,realname')->select();
            if ($cvinfo) {
                /**
                 * 已经创建过简历,那就把简历信息拿出来
                 */
                $user = M('user');
                $cv = M('cv');

                /**
                 * 获取教育经历相关信息
                 */
                $this->edu_list = $user
                    ->where(['wcl_cv.user_id' => $user_id])
                    ->join('wcl_cv ON wcl_user.id = wcl_cv.user_id')
                    ->join('wcl_edu ON wcl_cv.id = wcl_edu.cv_id')
                    ->join('wcl_degree ON wcl_degree.id = wcl_edu.degree_id')
                    ->field('wcl_edu.fromdate,wcl_edu.todate,wcl_edu.school,wcl_degree.name,wcl_edu.id,wcl_edu.cv_id')
                    ->order('todate')
                    ->select();
                $this->display();
            } else {
                /**
                 * 还没有创建简历，进入简历创建页
                 */
                $this->error('您还没有创建简历，将进入简历创建页', '/CV/Index/createcv');
            }

        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页', '/User/Index/login');
        }
    }

    public function eduEdit()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            /**
             * 通过user_id 查看该用户是否已经创建了简历基本信息，如果已经创建，则不能重复创建
             */
            $cv = M('cv');
            $cv_id = I('get.cv_id');
            $cvinfo = $cv->where(['id' => $cv_id])->select();
            if ($cvinfo) {
                /**
                 * 基本信息已经创建，才能增加工作意向,把cv_id值也传过去，求职表新增值可以直接插入了
                 */
                $this->assign('cv_id', $cvinfo[0]['id']);
                $edu = M('edu');
                /**
                 * 看是否有教育经历信息
                 */
                $id = I('get.id');
                $edu_list = $edu->where(['id' => $id])->select();
                if ($edu_list) {
                    $user = M('user');
                    /**
                     * 获取教育经历相关信息
                     */
                    $this->edu = $edu
                        ->where(['wcl_edu.id' => $id])
                        ->join('wcl_cv ON  wcl_edu.cv_id = wcl_cv.id')
                        ->join('wcl_degree ON wcl_degree.id = wcl_edu.degree_id')
                        ->field('wcl_edu.fromdate,wcl_edu.todate,wcl_edu.school,wcl_degree.id as degree_id,wcl_edu.id,wcl_edu.cv_id')
                        ->order('todate')
                        ->select();
                    /**
                     * 学历列表
                     */
                    $degree = M('degree');
                    $this->degree_list = $degree->field('id,name')->select();
                    $this->display();
                } else {
                    $this->error('非法请求，无法访问该网页', '/User/Index/login');
                }
            } else {
                /**
                 * 说明没有创建基本信息，那就先去创建基本信息
                 */
                $this->error('您还没有创建个人基本信息', '/CV/Index/BasicAdd');
            }

        } else {
            $this->error('无法访问该网页', '/User/Index/login');
        }
    }

    /**
     * 教育经历修改保存
     */
    public function eduEditSave()
    {
        $id = I('post.id');
        if ($id) {

            $edu = D("edu");  // 实例化Job对象
            if (!$edu->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($edu->getError());
            } else {
                // 验证通过 可以进行其他数据操作
                $result = $edu->save();
                if ($result) {
                    $this->success('教育经历更新成功', '/CV/Index/editcv');
                } else {
                    $this->error('写入错误');
                }
            }
        } else {
            $this->error('无法访问该网页', '/User/Index/login');
        }
    }

    public function deleteEdu()
    {
        if (IS_GET) {
            $id=I('get.id');
            $edu = M("edu"); // 实例化Job对象
            $success=$edu->where(['id'=>$id])->delete();
            if($success) {
                $this->success('删除成功', '/Edu/Index/eduEditList');
            }else {

            }
        } else {
            $this->error('非法请求!', '/User/Index/login');
        }
    }
}