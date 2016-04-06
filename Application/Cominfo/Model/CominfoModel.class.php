<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/5
 * Time: 21:42
 */
namespace Cominfo\Model;

use Think\Model;

class CominfoModel extends Model
{
    /**
     * 自动完成
     */
    protected $_auto = [
        ['updated_at', 'getTime', self::MODEL_BOTH, 'callback'],
        ['created_at', 'getTime', self::MODEL_INSERT, 'callback'],
    ];
    /**
     * 自动验证
     */
    protected $_validate = [
        ['name', '', '该用户名已经存在！', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['name', 'require', '用户名不能为空！'],
    ];
    public function  getTime()
    {
        return date('Y-m-d h:i:s');
    }
}