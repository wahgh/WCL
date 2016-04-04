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

    public function logout()
    {
        if (isset($_SESSION['sess_wcl']['is_auth'])) {
            unset($_SESSION['sess_wcl']);
        }
        $this->redirect('Home/index/index', '', 2, '页面跳转中...');
    }
}