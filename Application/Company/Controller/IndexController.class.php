<?php
namespace Company\Controller;

use Think\Controller;
use Think\Verify;

class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }
    /**
     * 注册部分
     */
    public function register()
    {
        $this->display();
    }

    /**
     * 登录
     */
    public function login()
    {
        $this->display();
    }

    /*
     * 公司基本信息
     */

    public  function companyshow(){
        $user_id = $_SESSION['sess_wcl']['company_id'];
        if($user_id){
            /*
             * 通过user_id ,取得创建公司的用户名和id
             */

            // $company = M('company');
            // $user_id= $company->field('id')->select();
            $cominfo = M('cominfo');
            $companyinfo = $cominfo->where(['companyuser_id' => $user_id ])->field('id,name')->select();

            // echo $company->getLastSql();
            if($companyinfo){
                /*
                 * 已经创建过公司就把公司基本信息拿过来
                 */
                $cominfo = M('cominfo');
                $company = M('company');
                $this->cominfo_list = $company
                    ->where(['wcl_cominfo.companyuser_id' =>$user_id])
                    ->join('left join wcl_cominfo on   wcl_company.id  = wcl_cominfo.companyuser_id')
                    ->join('left join wcl_companytype on wcl_companytype.id = wcl_cominfo.companytype_id ')
                    ->join('left join wcl_province on wcl_province.id = wcl_cominfo.province_id')
                    ->join('left join wcl_city on wcl_city.id = wcl_cominfo.city_id')
                    ->join('left join wcl_industry on wcl_industry.id = wcl_cominfo.industry_id')
                    ->field('wcl_companytype.name as companytype_name,wcl_province.name as province_name,wcl_city.name as city_name,wcl_industry.name as industry_name,wcl_cominfo.registercash,wcl_cominfo.people,wcl_cominfo.cetificate,wcl_cominfo.zidcode,wcl_cominfo.address,wcl_cominfo.name,wcl_cominfo.imagepath,wcl_cominfo.imagename')
                    ->select();


                /*
                 * 把职位的基本信息拿出来
                 */

                $jianli =M('post');
                $ok = $this->post_list = $jianli
                    //->where(['wcl_cominfo.companyuser_id' =>$user_id])
                    //->join('left join wcl_company_cv on wcl_company_cv.companyuser_id = wcl_company.id')
                    ->join('left join wcl_cominfo on wcl_post.companyinfo_id = wcl_cominfo.id')
                    //->join('left join wcl_cv on wcl_cv.id = wcl_company_cv.cv_id')
                    //->join('left join wcl_post on wcl_company.id = wcl_post.companyinfo_id')
                    ->join('left join wcl_salary on wcl_salary.id = wcl_post.salary_id')
                    ->join('left join wcl_worktime on wcl_worktime.id = wcl_post.worktime_id')
                    ->join('left join wcl_function on wcl_function.id = wcl_post.function_id')
                    ->join('left join wcl_industry on wcl_industry.id = wcl_function.industry_id')
                    ->field('wcl_post.peoplenumber,wcl_salary.cash,wcl_post.birthday,wcl_worktime.name as worktime_name,wcl_post.contact,wcl_post.mobile,wcl_post.netaddress,wcl_post.context,wcl_function.name as function_name,wcl_industry.name as industry_name')
                    ->select();
                $this->display();

            }else{
                $this->error('你还没有创建公司信息，将进入简历公司创建页','/Cominfo/index/create');
            }
        }else{
            /*
             * 没有session，说明还没有登录，将进入企业登录页
             */
            $this->error('你还没有登录，将进入企业登录页','/Company/index/login');
        }
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

    /**
     * 登录验证
     */
    public function logincheck()
    {
        $Verify = new Verify();
        $code = trim(I('post.yzm'));
        if ($Verify->check($code)) {
            $membername = trim(I('post.membername'));
            $username = trim(I('post.username'));
            $password = trim(I('post.password'));
            if ($username && $password && $membername) {
                $company = M("company");
                $condition = [
                    'membername' => $membername,
                    'username' => $username,
                    '_logic' => 'or',
                ];
                $arr = $company->where($condition)->find();
                if ($arr && md5($password) == $arr['password']) {
                    /**
                     * 拿到用户id
                     */
                    $user_id = $arr['id'];
                    /**
                     * 说明有该用户，登录成功，把登录成功标志is_auth，登录用户id，登录用户用户名username放进session中
                     */
                    $_SESSION['sess_wcl'] = [
                        'company_auth' => true,
                        'company_id' => $user_id,
                        'username' => $arr['username'],
                    ];
                    $this->success('恭喜' . $arr['username'] . '用户登录成功，跳转到企业用户主页', '/Company/index/index');
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
     * 注册表单数据的处理
     */

    public function registerSave()
    {
        $user = D("company"); // 实例化User对象
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
                    'company_auth' => true,
                    'company_id' => $user_id,
                    'username' => $arr['username'],
                ];
                $this->success('注册成功，页面跳到个人专区主页', '/Company/index/index');
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
        if (isset($_SESSION['sess_wcl']['company_auth'])) {
            unset($_SESSION['sess_wcl']);
        }
        $this->redirect('Home/index/index', '', 2, '页面跳转中...');
    }

}