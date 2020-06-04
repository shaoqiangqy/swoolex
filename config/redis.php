<?php
// +----------------------------------------------------------------------
// | Redis配置
// +----------------------------------------------------------------------
// | Copyright (c) 2018 https://blog.junphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小黄牛 <1731223728@qq.com>
// +----------------------------------------------------------------------

return [

    // +----------------------------------------------------------------------
    // | WebSocket Redis连接配置
    // +----------------------------------------------------------------------

    // 是否开启redis
    'status' => true,
    // host
    'host' => '127.0.0.1',
    // 端口 
    'port' => '6379',
    // 密码 
    'pwd' => 'root',
    // 库前缀 
    'table' => 'user_',
    // 连接的超时时间，默认为全局的协程 1S 
    'connect_timeout' => 1,
    // 超时时间，默认为全局的协程 (-1, 永不超时)
    'timeout' => -1,
    // 自动序列化，默认关闭
    'serialize' => false,
    // 自动连接尝试次数 默认1次
    'reconnect' => 1,
    // 是否兼容php/redis原生写法 默认关闭
    'compatibility_mode' => true,

    // +----------------------------------------------------------------------
    // | 连接池配置
    // +----------------------------------------------------------------------
    
    // 空闲回收定时检查时间 (S)
    'redis_timing_recovery' => 600,
    //最小连接数
    'pool_min' => 10,
    //最大连接数
    'pool_max' => 100,
    //空闲连接回收时间
    'pool_spare_time' => 3600,
];