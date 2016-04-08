<?php
namespace Company\Controller;

use Think\Controller;
use Think\Verify;

class IndexController extends Controller
{
    public function index()
    {
        $this->username = $_SESSION['sess_wcl']['username'];
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

    public function companyshow()
    {
        $user_id = $_SESSION['sess_wcl']['company_id'];
        if ($user_id) {
            /*
             * 通过user_id ,取得创建公司的用户名和id
             */

            // $company = M('company');
            // $user_id= $company->field('id')->select();
            $cominfo = M('cominfo');
            $companyinfo = $cominfo->where(['companyuser_id' => $user_id])->field('id,name')->select();

            // echo $company->getLastSql();
            if ($companyinfo) {
                /*
                 * 已经创建过公司就把公司基本信息拿过来
                 */
                $cominfo = M('cominfo');
                $company = M('company');
                $this->cominfo_list = $company
                    ->where(['wcl_cominfo.companyuser_id' => $user_id])
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

                $jianli = M('post');
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

            } else {
                $this->error('你还没有创建公司信息，将进入简历公司创建页', '/Cominfo/index/create');
            }
        } else {
            /*
             * 没有session，说明还没有登录，将进入企业登录页
             */
            $this->error('你还没有登录，将进入企业登录页', '/Company/index/login');
        }
    }

    /*
     * 简历搜索的search
     */
    public function search()
    {
        $industry = trim(I('get.industry_id'));
        $function = trim(I('get.function_id'));
        $degree = trim(I('get.degree_id'));


        if ($industry || $function || $degree) {
            /*
       * 取简历信息
       */

            $where_list = [
                "wcl_job.sfunction_id" => ['eq', $function],
                "wcl_cv.degree_id" => ['eq', $degree],
                'wcl_function.industry_id' => ['eq', $industry],
                '_logic' => 'OR'
            ];
            $user = M('user');
            $ok = $this->user_list = $user
                ->where($where_list)
                ->join('left join wcl_cv on wcl_user.id =  wcl_cv.user_id  ')
                ->join('left join wcl_job on  wcl_cv.id = wcl_job.cv_id ')
                ->join('left join wcl_function on  wcl_job.sfunction_id = wcl_function.id ')
                ->join('left join wcl_degree on wcl_degree.id = wcl_cv.degree_id')
                ->field('wcl_cv.id,wcl_cv.realname,wcl_cv.updated_at,wcl_function.name as function_name,wcl_degree.name as degree_name')
                ->select();
            $this->display();
        }

    }

    /*
     *简历的具体信息
     */

    public function cvshow()
    {
        $id = I('get.cv_id');

        if ($id) {
            $cv = M('cv');
            $cvinfo = $cv->where(['id' => $id])->field('id,realname')->select();
            if ($cvinfo) {
                /**
                 * 已经创建过简历,那就把简历信息拿出来
                 */
                $user = M('user');
                $cv = M('cv');
                /**
                 * 获取工作意向相关信息
                 */
                $this->job_cv = $cv
                    ->where([' wcl_cv.id' => $id])
                    // ->join('left join wcl_cv ON wcl_user.id = wcl_cv.id')
                    ->join('left join  wcl_job ON wcl_cv.id = wcl_job.cv_id')
                    ->join('left join  wcl_industry ON wcl_industry.id = wcl_job.sindustry_id')
                    ->join('left join  wcl_companytype ON wcl_companytype.id = wcl_job.scompanytype_id')
                    ->join('left join  wcl_function ON wcl_function.id = wcl_job.sfunction_id')
                    ->join('left join  wcl_salary ON wcl_salary.id = wcl_job.ssalary_id')
                    ->field('wcl_job.shouse,wcl_industry.name as industry_name,wcl_companytype.name as companytype_name,wcl_function.name as function_name,wcl_salary.cash as salary_cash')
                    ->select();

                /**
                 * 获取教育经历相关信息
                 */
                $this->edu_cv = $cv
                    ->where(['wcl_cv.id' => $id])
                    // ->join('wcl_cv ON wcl_user.id = wcl_cv.user_id')
                    ->join('wcl_edu ON wcl_cv.id = wcl_edu.cv_id')
                    ->join('wcl_degree ON wcl_degree.id = wcl_edu.degree_id')
                    ->field('wcl_edu.fromdate,wcl_edu.todate,wcl_edu.school,wcl_degree.name')
                    ->order('todate')
                    ->select();

                /**
                 * 获取基本信息
                 */
                $this->basic_cv = $cv
                    ->where([' wcl_cv.id' => $id])
                    ->join('left join wcl_degree ON wcl_degree.id = wcl_cv.degree_id')
                    ->join('left join wcl_nation ON wcl_nation.id = wcl_cv.nation_id')
                    ->join('left join wcl_worktime ON wcl_worktime.id = wcl_cv.worktime_id')
                    ->join('left join wcl_province ON wcl_province.id = wcl_cv.province_id')
                    ->join('left join wcl_city ON wcl_city.id = wcl_cv.city_id')
                    ->join('left join wcl_level ON wcl_level.id = wcl_cv.level_id')
                    ->field
                    (
                        'wcl_cv.id,wcl_cv.realname,wcl_cv.mobile,wcl_cv.email,wcl_cv.gender,wcl_cv.gender,
                    wcl_cv.birthday,wcl_cv.evaluation,wcl_cv.school,wcl_cv.language,wcl_degree.name
                     as degree_name,wcl_nation.name as nation_name,wcl_worktime.name as worktime_name,
                     wcl_province.name as province_name ,wcl_city.name as city_name,wcl_level.name as level_name'
                    )
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
        $this->redirect('Home/index/index');
    }

}