<?php
namespace CompanyCV\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }

    public function companyCvAdd()
    {
        $companyuser_id = $_SESSION['sess_wcl']['company_id'];
        if($companyuser_id){
            $id =I('cv_id');
            if($id){
                $company_cv = M('company_cv');

                $is_exist = $company_cv
                    ->where([
                        'companyuser_id' =>$companyuser_id,
                        'cv_id' => $id,
                    ])->select();
                if($is_exist){
                    $this->error("你已关注过该简历",'/Cominfo/index/index');
                }else{
                    $result =[
                        'companyuser_id' =>$companyuser_id,
                        'cv_id' => $id,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s'),
                    ];
                   $is_success= $company_cv->data($result)->add();
                    if($is_success){
                        $this->success("关注简历成功，预览关注的简历");
                    }
                }
            }else{
                $this->error('没有该用户的简历信息，请重新搜索','/Cominfo/index/index' );
            }
        }else{
            $this->error('你还没有登录，将进入企业登录页', '/Company/index/login');
        }


    }

    public function companycvshow()
    {
        /*
         * 企业查看关注的简历
         */
        $companyuser_id = $_SESSION['sess_wcl']['company_id'];
        if($companyuser_id){
            $id = I('get.cv_id');
                $company_cv = M('company_cv');
            /**
             * 获取该企业投递简历数
             */
            $company=M('company');
                $this->count = $company
                    ->where(['wcl_company.id'=>$companyuser_id])
                    ->join('left join wcl_company_cv on wcl_company.id=wcl_company_cv.companyuser_id')
                    ->count();
            $this->cv_list = $company
                ->where(['wcl_company.id'=>$companyuser_id])
                ->join('left join wcl_company_cv on wcl_company.id=wcl_company_cv.companyuser_id')
                ->join('left join wcl_cv on wcl_cv.id = wcl_company_cv.cv_id')
                ->join('left join wcl_degree on wcl_cv.degree_id = wcl_degree.id')
                ->field('wcl_cv.id,wcl_degree.name as degree_name,wcl_cv.realname,wcl_cv.updated_at')
                ->select();
                $this->display();
        }else{
            $this->error('你还没有登录，将进入企业登录页', '/Company/index/login');
        }

    }

    public function detailcv(){
        /*
         * 根据简历的信息查看关注的简历的基本信息
         */
        $companyuser_id = $_SESSION['sess_wcl']['company_id'];
        if ($companyuser_id) {
            /*
             * 通过cv_id取得关注简历基本信息
             */
            $id = I('get.cv_id');
            if($id){
                $cv = M('cv');
                /**
                 * 获取工作意向相关信息
                 */
                $this->job_list = $cv
                    ->where([' wcl_cv.id' => $id])
                   // ->join('left join wcl_cv ON wcl_user.id = wcl_cv.user_id')
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
                $this->edu_list = $cv
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
                $this->basic_list = $cv
                    ->where([' wcl_cv.id' => $id])
                    ->join('left join wcl_degree ON wcl_degree.id = wcl_cv.degree_id')
                    ->join('left join wcl_nation ON wcl_nation.id = wcl_cv.nation_id')
                    ->join('left join wcl_worktime ON wcl_worktime.id = wcl_cv.worktime_id')
                    ->join('left join wcl_province ON wcl_province.id = wcl_cv.province_id')
                    ->join('left join wcl_city ON wcl_city.id = wcl_cv.city_id')
                    ->join('left join wcl_level ON wcl_level.id = wcl_cv.level_id')
                    ->field
                    (
                        'wcl_cv.realname,wcl_cv.mobile,wcl_cv.email,wcl_cv.gender,
                    wcl_cv.birthday,wcl_cv.evaluation,wcl_cv.school,wcl_cv.language,wcl_degree.name
                     as degree_name,wcl_nation.name as nation_name,wcl_worktime.name as worktime_name,
                     wcl_province.name as province_name ,wcl_city.name as city_name,wcl_level.name as level_name,wcl_cv.photo_name'
                    )
                    ->select();
                $this->display();
            } else {
                /*
                 * 简历预览失败，请重新预览
                 */
                $this->error('你还没有创建公司信息，将进入简历公司创建页', '/CompanyCV/index/companycvshow');
            }
        } else {
            /*
             * 没有session，说明还没有登录，将进入企业登录页
             */
            $this->error('你还没有登录，将进入企业登录页', '/Company/index/login');
        }
    }


}