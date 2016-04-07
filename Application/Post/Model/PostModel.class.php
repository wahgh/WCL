<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/4
 * Time: 11:17
 */
namespace Job\Model;

use Think\Model;

class JobModel extends Model
{
    /**
     * 自动完成
     */
    protected $_auto = [
        ['updated_at', 'getTime', self::MODEL_BOTH, 'callback'],
        ['created_at', 'getTime', self::MODEL_INSERT, 'callback'],
    ];

    public function  getTime()
    {
        return date('Y-m-d h:i:s');
    }

}