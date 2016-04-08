<?php
namespace Post\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $user_id = $_SESSION['sess_wcl']['company_id'];
        if(isset($_SESSION['sess_wcl']['company_auth'])&&$_SESSION['sess_wcl']['company_auth']) {
            /**
             * 获取行业信息
             */
            $industry = M('industry');
            $this->indsutry_list = $industry->field('id,name')->order('id')->select();
            if ($user_id) {
                /**
                 * 取companyinfo_id
                 */
                $company = M('Company');
                $cominfo = $company
                    ->where(['wcl_cominfo.companyuser_id' => $user_id])
                    ->join('left join wcl_cominfo on wcl_company.id=wcl_cominfo.companyuser_id')
                    ->select();
                $post = M('post');
                $this->post_list= $post
                    ->where(['wcl_post.companyinfo_id' =>$cominfo[0]['id']])
                    ->join('left join wcl_function on wcl_post.function_id=wcl_function.id')
                    ->field('wcl_post.id,wcl_post.updated_at,wcl_function.name as function_name')
                    ->select();

                $this->display();
            }

        }else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页！', '/Company/Index/login');
        }
    }

    /**
     * 新增基本信息
     */

    public function postAdd()
    {
        $user_id = $_SESSION['sess_wcl']['company_id'];
        if ($user_id) {
            /**
             * 取companyinfo_id
             */
            $company = M('Company');
            $cominfo = $company
                ->where(['wcl_cominfo.companyuser_id' => $user_id])
                ->join('left join wcl_cominfo on wcl_company.id=wcl_cominfo.companyuser_id')
                ->field('wcl_cominfo.id')
                ->select();
            if( $cominfo) {
                $this->assign('companyinfo_id', $cominfo[0]['id']);
                /**
                 * 行业职位
                 */
                $industry = M('industry');
                $this->indsutry_list = $industry->field('id,name')->order('id')->select();
                /**
                 * 薪水
                 */
                $salary = M('salary');
                $this->salary_list = $salary->field('id,cash')->select();
                /**
                 * 工作年限列表
                 */
                $worktime = M('worktime');
                $this->worktime_list = $worktime->field('id,name')->select();
                $this->display();
            }else {
                $this->error('您还没有创建企业基本信息，将跳转到企业基本信息添加页', '/Cominfo/Index/comInfoAdd');
            }
        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页！', '/Company/Index/login');
        }
    }

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
     * 创建一个职位模板
     */
    public function createPost()
    {
        $user_id = $_SESSION['sess_wcl']['company_id'];
        if ($user_id) {
            /**
             * 到编辑页面的主页面
             */
            $this->display();
        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，不能创建简历模板', '/Company/Index/login');
        }
    }

    public function PostSave()
    {
        $post = D("post"); // 实例化User对象
        if (!$post->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($post->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $result = $post->add();
            if ($result) {
                $this->success('基本信息保存成功', '/Post/Index/index');
            } else {
                $this->error('写入错误');
            }
        }
    }

    /**
     * 修改职位发布信息
     */
    public function PostInput()
    {
        $this->id=I('post_id');
        if ($this->id) {
            /**
             * 取companyinfo_id
             */
            $post = M('post');

            $post_list = $post->find($this->id);

            if( $post_list) {
                /**
                 * 职位
                 */
                $function = M('function');
                $this->function_list = $function->field('id,name')->select();
                /**
                 * 薪水
                 */
                $salary = M('salary');
                $this->salary_list = $salary->field('id,cash')->select();
                /**
                 * 工作年限列表
                 */
                $worktime = M('worktime');
                $this->worktime_list = $worktime->field('id,name')->select();

                $this->post_list=$post_list;
                $this->display();
            }else {
                $this->error('您还没有创建企业基本信息，将跳转到企业基本信息添加页', '/Cominfo/Index/comInfoAdd');
            }
        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页！', '/Company/Index/login');
        }

    }
    /**
     * 保存修改的简历
     */
    public function postInputSave()
    {
//        $id=I('companyinfo_id');
//        if($id) {
            $post=M('post');
//            var_dump($post);exit;
            if (!$post->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($post->getError());
            } else {
                // 验证通过 可以进行其他数据操作
//                var_dump($_POST);exit;
                $result = $post->save();
                if ($result) {

                    $this->success('基本信息更新成功', '/Company/index/companyshow');
                } else {
                    $this->error('修改错误');
                }
            }

//        } else {
//            $this->error('无法访问该网页','/Post/index/postInput');
//        }
    }
    /**
     * 删除职位发布信息
     */

    public function PostDelete()
    {
        $id=I('post_id');
        if($id) {
            $post = M('post');
            $post->delete($id);
            $this->success('删除成功', '/Post/index/index');
        } else {
            $this->error('要删除的简历不存在,请先创建简历','/Post/index/postAdd');
        }
    }

    /**
     * 职位预览
     */
    public function showPost()
    {
        $user_id = $_SESSION['sess_wcl']['company_id'];
        if ($user_id) {
            /**
             * 通过company_id 查看该企业找到companyinfo_id
             */
            $company = M('Company');
            $cominfo = $company
                ->where(['wcl_cominfo.companyuser_id' => $user_id])
                ->join('left join wcl_cominfo on wcl_company.id=wcl_cominfo.companyuser_id')
                ->select();
            $post = M('post');
            $this->post_list= $post
                ->where(['wcl_post.companyinfo_id' =>$cominfo[0]['id']])
                ->join('left join wcl_function on wcl_post.function_id=wcl_function.id')
                ->join('left join wcl_salary on wcl_post.salary_id=wcl_salary.id')
                ->join('left join wcl_worktime on wcl_post.worktime_id=wcl_worktime.id')
                ->field('wcl_post.id,wcl_post.created_at,wcl_function.name as function_name,
                wcl_salary.cash as salary_cash,wcl_worktime.name as worktime_name,wcl_post.peoplenumber,
                wcl_post.birthday,wcl_post.context,wcl_post.contact,wcl_post.mobile,wcl_post.netaddress')
                ->select();
        $this->display();
        }

    }
}