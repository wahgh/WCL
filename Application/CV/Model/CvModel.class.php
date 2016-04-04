<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/4
 * Time: 7:17
 */
namespace CV\Model;

use Think\Model;

class CvModel extends Model
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
        ['realname', 'require', '用户名不能为空！'],
        ['email', 'require', '邮箱不能为空！'],
        ['email', '/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', '邮箱格式错误！', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT],
        ['mobile', 'require', '手机号码不能为空！'],
        ['mobile', '/^1[3-8][0-9]{9}$/', '手机号码格式错误！', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT]
    ];

    public function  getTime()
    {
        return date('Y-m-d h:i:s');
    }

}