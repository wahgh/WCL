<?php
namespace UserPost\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>', 'utf-8');
    }

    /**
     * 用户投递信息的新增
     */
    public function userPostAdd()
    {
        if (isset($_SESSION['sess_wcl']['is_auth']) && isset($_SESSION['sess_wcl']['is_auth'])) {
            $post_id = I('get.post_id');
            if ($post_id) {
                $user_id = $_SESSION['sess_wcl']['id'];
                $user_post = M('user_post');
                $is_exits = $user_post
                    ->where([
                        'user_id' => $user_id,
                        'post_id' => $post_id
                    ])
                    ->select();
                if ($is_exits) {
                    $this->error('简历不能重复投递！', '/CV/index/index');
                } else {
                    $user_post->user_id = $user_id;
                    $user_post->post_id = $post_id;
                    $user_post->created_at = date('y-m-d h:i:s');
                    $user_post->updated_at = date('y-m-d h:i:s');
                    $is_success = $user_post->add();
                    if ($is_success) {
                        $this->success('简历已经投递成功，您还可以查看已经投递的简历！', '/CV/index/index');
                    } else {
                        $this->error('简历没有投递成功，请重新投递！');
                    }
                }
            } else {
                $this->error('您还没有选择职位，不能投递,返回主页', '/User/index/index');
            }
        } else {
            $this->error('您还没有登录，请先登录', '/User/index/login');
        }
    }

    /**
     * 用户投递记录查询
     */
    public function userPostShow()
    {
        if (isset($_SESSION['sess_wcl']['is_auth']) && isset($_SESSION['sess_wcl']['is_auth'])) {
            $user_id = $_SESSION['sess_wcl']['id'];
            $user = M('user');
            $user_post = M('user_post');
            $post_list = $user
                ->where("wcl_user.id=$user_id")
                ->join('left join wcl_user_post on wcl_user.id=wcl_user_post.user_id')
                ->join('left join wcl_post on wcl_user_post.post_id=wcl_post.id')
                ->join('left join wcl_salary on wcl_post.salary_id=wcl_salary.id')
                ->join('left join wcl_worktime on wcl_post.worktime_id=wcl_worktime.id')
                ->join('left join wcl_function on wcl_post.function_id=wcl_function.id')
                ->field('wcl_post.peoplenumber,wcl_post.birthday,wcl_post.contact
            ,wcl_post.mobile,wcl_post.netaddress,wcl_post.context,wcl_post.house,wcl_salary.cash,
            wcl_worktime.name,wcl_function.name')
                ->select();
        } else {
            $this->error('您还没有登录，请先登录', '/User/index/login');
        }
    }
}