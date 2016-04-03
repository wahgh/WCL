<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/3
 * Time: 9:35
 */
namespace User\Model;

use Think\Model;

class UserModel extends Model
{
    /**
     * 自动完成
     */
    protected $_auto = [
        ['updated_at', 'getTime', self::MODEL_BOTH, 'callback'],
        ['created_at', 'getTime', self::MODEL_INSERT, 'callback'],
        ['password', 'md5JM', self::MODEL_INSERT, 'callback'],
    ];
    /**
     * 自动验证
     */
    protected $_validate = [
        ['username', '', '该用户名已经存在！', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['username', 'require', '用户名不能为空！'],
        ['password', 'require', '密码不能为空！'],
        ['repassword', 'require', '确认密码不能为空！'],
        ['repassword', 'password', '确认密码不正确', self::EXISTS_VALIDATE, 'confirm'],
        ['email', 'require', '邮箱不能为空！'],
        ['email', '', '邮箱已经存在！', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['email', '/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', '邮箱格式错误！', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT],
        ['mobile', 'require', '手机号码不能为空！'],
        ['mobile', '', '手机号码已经存在！', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['mobile', '/^1[3-8][0-9]{9}$/', '手机号码格式错误！', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT]
    ];

    public function  getTime()
    {
        return date('Y-m-d h:i:s');
    }

    public function  md5JM($password)
    {
        return md5($password);
    }

}