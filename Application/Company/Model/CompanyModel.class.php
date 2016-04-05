<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/5
 * Time: 11:58
 */
namespace Company\Model;

use Think\Model;

class CompanyModel extends Model
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
        ['membername', '', '该会员名已经存在！', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['username', '', '该用户名已经存在！', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['membername', 'require', '用会员不能为空！'],
        ['username', 'require', '用户名不能为空！'],
        ['password', 'require', '密码不能为空！'],
        ['repassword', 'require', '确认密码不能为空！'],
        ['repassword', 'password', '确认密码不正确', self::EXISTS_VALIDATE, 'confirm'],
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