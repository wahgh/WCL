<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        /**
         * 到首页有三种情况，
         * 1：第一次进首页，谁都可以进
         * 2：登录之后点击首页地址
         * 3：退出之后，无论是一般用户还是企业用户退出都到首页
         */
        if (isset($_SESSION['sess_wcl']['is_auth']) && $_SESSION['sess_wcl']['is_auth']) {
            /**
             * 是普通用户登录之后从其他页面过来的，并没有退出，这时候拿到session里面的name显示在页面上
             */
            $this->username = $_SESSION['sess_wcl']['username'];
            $this->mark = 'user';
        } else {
            if (isset($_SESSION['sess_wcl']['company_auth']) && $_SESSION['sess_wcl']['company_auth']) {
                /**
                 * 是普通用户登录之后从其他页面过来的，并没有退出，这时候拿到session里面的name显示在页面上
                 */
                $this->username = $_SESSION['sess_wcl']['username'];
                $this->mark = 'company';
            } else {
                /**
                 * 非登录状态赋值为‘’
                 */
                $this->username = '';
                $this->mark = '';
            }
        }

        $this->display();
    }
}