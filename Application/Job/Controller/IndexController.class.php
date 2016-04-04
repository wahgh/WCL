<?php
namespace Job\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>', 'utf-8');
    }

    /**
     * 新增求职意向
     */
    public function jobAdd()
    {
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
                 * 获取薪水表信息
                 */
                $salary = M('salary');
                $this->salary_list = $salary->field('id,cash')->order('id')->select();
                /**
                 * 获取公司性质表信息
                 */
                $companytype = M('companytype');
                $this->companytype_list = $companytype->field('id,name')->order('id')->select();
                /**
                 * 获取行业信息
                 */
                $industry = M('industry');
                $this->indsutry_list = $industry->field('id,name')->order('id')->select();
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
    public function jobSave()
    {
        $job = D("job"); // 实例化User对象
        if (!$job->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($job->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $result = $job->add();
            if ($result) {
                /**
                 * 回到简历主页
                 */
                $this->success('工作意向信息保存成功，您可以添加多个工作意向！', '/Job/Index/jobAdd');
            } else {
                $this->error('写入错误');
            }
        }
    }

    /**
     * 列出要修改或删除的工作意向表
     */
    public function jobEditList()
    {
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
                $job = M('job');
                /**
                 * 看是否有意向表
                 */
                $job_list = $job->where(['cv_id' => $cvinfo[0]['id']])->select();
                if ($job_list) {
                    /**
                     * 已经创建过简历,那就把简历信息拿出来
                     */
                    $user = M('user');
                    /**
                     * 获取工作意向相关信息
                     */
                    $this->job_list = $user
                        ->where([' wcl_cv.user_id' => $user_id])
                        ->join('left join wcl_cv ON wcl_user.id = wcl_cv.user_id')
                        ->join('left join  wcl_job ON wcl_cv.id = wcl_job.cv_id')
                        ->join('left join  wcl_industry ON wcl_industry.id = wcl_job.sindustry_id')
                        ->join('left join  wcl_companytype ON wcl_companytype.id = wcl_job.scompanytype_id')
                        ->join('left join  wcl_function ON wcl_function.id = wcl_job.sfunction_id')
                        ->join('left join  wcl_salary ON wcl_salary.id = wcl_job.ssalary_id')
                        ->field('wcl_cv.realname,wcl_job.shouse,wcl_industry.name as industry_name,wcl_companytype.name as companytype_name,wcl_function.name as function_name,wcl_salary.cash as salary_cash,wcl_job.id,wcl_job.cv_id')
                        ->select();
                    $this->display();
                } else {
                    $this->error('您还没有创建工作意向表，跳到创建页面', '/Job/Index/jobAdd');
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

    public function jobEdit()
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
                $job = M('job');
                /**
                 * 看是否有意向表
                 */
                $id = I('get.id');
                $job_list = $job->where(['id' => $id])->select();
                if ($job_list) {
                    /**
                     * 已经创建过简历,那就把简历信息拿出来
                     */
                    $job = M('job');
                    /**
                     * 获取工作意向相关信息
                     */
                    $this->job = $job
                        ->where(['wcl_job.id' => $id])
                        ->join('left join  wcl_cv ON wcl_job.cv_id = wcl_cv.id')
                        ->join('left join  wcl_industry ON wcl_industry.id = wcl_job.sindustry_id')
                        ->join('left join  wcl_companytype ON wcl_companytype.id = wcl_job.scompanytype_id')
                        ->join('left join  wcl_function ON wcl_function.id = wcl_job.sfunction_id')
                        ->join('left join  wcl_salary ON wcl_salary.id = wcl_job.ssalary_id')
                        ->field('wcl_job.shouse,wcl_industry.id as industry_id,wcl_companytype.id as companytype_id,wcl_function.name,wcl_function.id as function_id,wcl_salary.id as salary_id,wcl_job.id as job_id,wcl_job.cv_id')
                        ->select();
                    /**
                     * 获取薪水表信息
                     */
                    $salary = M('salary');
                    $this->salary_list = $salary->field('id,cash')->order('id')->select();
                    /**
                     * 获取公司性质表信息
                     */
                    $companytype = M('companytype');
                    $this->companytype_list = $companytype->field('id,name')->order('id')->select();
                    /**
                     * 获取行业信息
                     */
                    $industry = M('industry');
                    $this->indsutry_list = $industry->field('id,name')->order('id')->select();
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

    public function jobEditSave()
    {
        $id = I('post.id');
        if ($id) {

            $job = D("job");  // 实例化Job对象
            if (!$job->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($job->getError());
            } else {
                // 验证通过 可以进行其他数据操作
                $result = $job->save();
                if ($result) {
                    $this->success('求职意向更新成功', '/CV/Index/editcv');
                } else {
                    $this->error('写入错误');
                }
            }
        } else {
            $this->error('无法访问该网页', '/User/Index/login');
        }
    }

    /**
     *
     */
    public function deleteJob()
    {
        if (IS_GET) {
            $id=I('get.id');
            $job = M("job"); // 实例化Job对象
            $success=$job->where(['id'=>$id])->delete();
            if($success) {
                $this->success('删除成功', '/Job/Index/jobEditList');
            }else {

            }
        } else {
            $this->error('非法请求!', '/User/Index/login');
        }
    }
}