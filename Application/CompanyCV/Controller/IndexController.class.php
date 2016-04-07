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
                        $this->success("关注简历成功");
                    }
                }
            }else{
                $this->error('没有该用户的简历信息，请重新搜索','/Cominfo/index/index' );
            }
        }else{
            $this->error('你还没有登录，将进入企业登录页', '/Company/index/login');
        }


    }
}