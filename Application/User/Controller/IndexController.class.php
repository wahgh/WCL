<?php
namespace User\Controller;

use Think\Controller;
use Think\Verify;

class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }

    /**
     * 登录处理
     */
    public function login()
    {
        $this->display();
    }

    /**
     * 生成验证码，登录验证使用
     */
    public function yzm()
    {
        $Verify = new Verify();
        /**
         * 使用验证码背景图片
         */
        $Verify->useImgBg = true;
        $Verify->fontSize = 30;
        $Verify->length = 3;
        /**
         * 关闭验证码杂点
         */
        $Verify->useNoise = false;
        $Verify->entry();
    }

    public function logincheck()
    {
        $Verify = new Verify();
        $code = trim(I('post.yzm'));
        if ($Verify->check($code)) {
            $username = trim(I('post.username'));
            $password = trim(I('post.password'));
            if ($username && $password) {
                $user = M("user");
                $condition = [
                    'username' => $username,
                    'email' => $username,
                    'mobile' => $username,
                    '_logic' => 'or',
                ];
                $arr = $user->where($condition)->find();
                if ($arr && md5($password) == $arr['password']) {
                    /**
                     * 拿到用户id
                     */
                    $user_id = $arr['id'];
                    /**
                     * 说明有该用户，登录成功，把登录成功标志is_auth，登录用户id，登录用户用户名username放进session中
                     */
                    $_SESSION['sess_wcl'] = [
                        'is_auth' => true,
                        'id' => $user_id,
                        'username' => $arr['username'],
                    ];
                    $this->success('恭喜' . $arr['username'] . '用户登录成功，跳转到用户主页', '/CV/index/index');
                } else {
                    $this->error('用户名和密码错误！');
                }
            } else {
                $this->error('用户名和密码不能为空！');
            }
        } else {
            $this->error('验证码错误！');
        }
    }

    /**
     * 注册
     */

    public function register()
    {
        $this->display();
    }


    /**
     * 注册表单数据的处理
     */

    public function registerSave()
    {
        $user = D("user"); // 实例化User对象
        if (!$user->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($user->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $username = I('post.username');
            $result = $user->add();
            if ($result) {
                $condition['username'] = $username;
                $arr = $user->field('id,username')->where($condition)->find();
                /**
                 * 拿到用户id
                 */
                $user_id = $arr['id'];
                /**
                 * 说明有该用户，登录成功，把登录成功标志is_auth，登录用户id，登录用户用户名username放进session中
                 */
                $_SESSION['sess_wcl'] = [
                    'is_auth' => true,
                    'id' => $user_id,
                    'username' => $arr['username'],
                ];
                $this->success('注册成功，页面跳到个人专区主页', '/CV/index/index');
            } else {
                $this->error('写入错误');
            }
        }

    }

    /**
     * 登出
     */
    public function logout()
    {
        if (isset($_SESSION['sess_wcl']['is_auth'])) {
            unset($_SESSION['sess_wcl']);
        }
        $this->redirect('Home/index/index', '', 2, '页面跳转中...');
    }

    /**
     * 企业职位搜索
     */
    public function searchPost()
    {
        $condition = [];
        /**
         * 企业名称
         */
        $name = I('get.companyname');
        /**
         * 行业id
         */
        $industry_id = I('get.industry_id');
        /**
         * 职位id
         */
        $function_id = I('get.function_id');
        /**
         * 企业所在地省
         */
        $province_id = I('get.province_id');
        /**
         * 企业所在地市
         */
        $city_id = I('get.city_id');

        if ($name || $industry_id || $function_id || $province_id || $city_id) {
            $company = M('company');
            /**
             * 获取搜索职位信息
             */
            $condition = [
                'wcl_cominfo.name' => ['like', "%${name}%"],
                'wcl_cominfo.industry_id' => ['eq', $industry_id],
                'wcl_post.function_id' => ['eq', $function_id],
                'wcl_cominfo.province_id' => ['eq', $province_id],
                'wcl_cominfo.city_id' => ['eq', $city_id],
                '_logic' => 'OR'
            ];
            $this->post_list = $company
                ->where($condition)
                ->join('left join wcl_cominfo on wcl_company.id=wcl_cominfo.companyuser_id')
                ->join('left join wcl_post on wcl_cominfo.id=wcl_post.companyinfo_id')
                ->join('left join wcl_province on wcl_cominfo.province_id=wcl_province.id')
                ->join('left join wcl_function on wcl_post.function_id=wcl_function.id')
                ->join('left join wcl_city on wcl_cominfo.city_id=wcl_city.id')
                ->field('wcl_function.name as function_name,wcl_cominfo.name as cominfo_name
        ,wcl_province.name as province_name,wcl_city.name as city_name,wcl_post.updated_at,wcl_post.id')
                ->select();
            $this->display();
        } else {
            $this->error('至少填写一项才能搜索！');
        }
    }

    public function postShow()
    {
        $post_id = I('get.post_id');
        $this->assign('post_id', $post_id);
        $post = M('post');
        /**
         * 获取所选企业信息
         */
        $cominfo = M('cominfo');
        $company_list = $cominfo
            ->where("wcl_post.id=${post_id}")
            ->join('left join wcl_post on wcl_cominfo.id=wcl_post.companyinfo_id')
            ->select();
        /**
         * 获取相应职位信息
         */
        $this->post_list = $post
            ->where("wcl_post.id=${post_id}")
            ->join('left join wcl_salary on wcl_post.salary_id=wcl_salary.id')
            ->join('left join wcl_worktime on wcl_post.worktime_id=wcl_worktime.id')
            ->join('left join wcl_function on wcl_post.function_id=wcl_function.id')
            ->field('wcl_post.peoplenumber,wcl_post.birthday,wcl_post.contact
            ,wcl_post.mobile,wcl_post.netaddress,wcl_post.context,wcl_post.house,wcl_salary.cash,
            wcl_worktime.name,wcl_function.name')
            ->select();
        $this->display();
    }
}