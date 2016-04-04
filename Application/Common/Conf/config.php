<?php
return array(
    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'wcl', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'wcl_', // 数据库表前缀
    'LAYOUT_ON' => true, // 是否启用布局
    'URL_MODEL' => 2, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
    'URL_HTML_SUFFIX' => '', // URL伪静态后缀设置
    'TMPL_PARSE_STRING' => [
        'WEB_NAME' => '职来职往',
        'IMAGE_PATH' => './Public/images/',
        'UPLOAD_PATH' => './upload/images/'
    ],
    // 显示页面Trace信息
    'SHOW_PAGE_TRACE' =>true,
    //'配置项'=>'配置值'
);