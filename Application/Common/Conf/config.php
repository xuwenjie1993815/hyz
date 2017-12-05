<?php //
return array(
    //数据库配置信息
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => '', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => 'hyz_', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
    // 允许访问的模块列表
    'MODULE_ALLOW_LIST'    =>    array('Home','Admin','User'),
    'DEFAULT_MODULE'       =>    'Home',  // 默认模块
    //路由配置信息
    'URL_ROUTER_ON'   => true, //开启路由
    'URL_MODEL' => '3', //url访问模式为rewrite模式
    'URL_HTML_SUFFIX' =>'.html', //开启伪静态
    'LOAD_EXT_CONFIG' => 'sdk_config',
    'URL_ROUTE_RULES' => array( //定义路由规则
        'form/:id\d'    => 'Home/Form/edit'
    )
);