<?php
// +----------------------------------------------------------------------
// | SWOOLE服务配置
// +----------------------------------------------------------------------
// | Copyright (c) 2018 https://blog.junphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小黄牛 <1731223728@qq.com>
// +----------------------------------------------------------------------

return [
    // host
    'host' => '0.0.0.0',
    // 端口
    'port' => 9502,
    // HTTPS证书
    'ssl_cert_file' => '',
    // HTTPS证书
    'ssl_key_file' => '',
    // HTTP2协议
    'open_http2_protocol' => false,
    // 启动的 Reactor 线程数
    'reactor_num' => false,
    // 启动的 Worker 进程数
    'worker_num' => false,
    // 设置 worker 进程的最大任务数
    'max_request' => 0,
    // 最大允许的连接数
    'max_conn' => false,
    // 配置 Task 进程的数量，不配置则不启动
    'task_worker_num' => 1,
    // 设置 Task 进程与 Worker 进程之间通信的方式
    'task_ipc_mode' => 1,
    // task 进程的最大任务数，如果不希望进程自动退出可以设置为 0
    'task_max_request' => 0,
    // 设置 task 的数据临时目录，如果投递的数据超过 8180 字节，将启用
    'task_tmpdir' => false,
    // 开启 Task 协程支持
    'task_enable_coroutine' => true,
    // 是否面向对象风格的 Task 回调格式
    'task_use_object' => false,
    // 数据包分发策略
    'dispatch_mode' => 2,
    // 是否开启守护进程模式
    'daemonize' => false,
    // 设置 Listen 队列长度
    'backlog' => false,
    // 指定 Swoole 错误日志文件，守护进程后建议指定文件
    'log_file' => false,
    // 设置 Server 错误日志打印的等级，范围是 0-6
    'log_level' => 0,
    // 是否自动检测死链接
    'open_tcp_keepalive' => 0,
    // 是否启用心跳检测
    'heartbeat_check_interval' => false,
    // 最大允许的空闲时间(S)
    'heartbeat_idle_time' => 120,

    // +-----------------------------
    // | Swoole 各个进程的进程别名
    // +-----------------------------

    'master'  => 'swoolex service master process', 
    'manager' => 'swoolex service manager process', 
    'worker'  => 'swoolex service worker process', 
    'tasker'  => 'swoolex service tasker process', 

    // +-----------------------------
    // | Swoole 服务的服务环境变量存储路径
    // +-----------------------------

    // 记录master和manager的进程id
    'pid_file' => ROOT_PATH.'/env/sw-x.pid', 
    // 记录worker的进程id
    'worker_pid_file' => ROOT_PATH.'/env/worker.pid', 
    // 记录tasker的进程id
    'tasker_pid_file' => ROOT_PATH.'/env/tasker.pid', 

    // +-----------------------------
    // | WebSocket 服务的独立配置
    // +-----------------------------

    // 是否启动HandShake事件监听，如果开启，请自己实现握手协议
    'is_onHandShake' => false,
    // 是否启用系统自带的onMessage分包控制，如果关闭，请自己实现业务逻辑
    'is_onMessage' => true,
    // AES 加密类型
    'aes_type' => 'AES-128-ECB',
    // AES 加密KEY
    'aes_key' => '',
    // AES 加密向量
    'aes_iv' => '',
    
    // +-----------------------------
    // | onRequst 跨域相关
    // +-----------------------------
    
    // 接口跨域设置
    'origin' => '*',
    // 接口数据请求类型
    'type' => '',
    // 接口跨域允许请求的类型
    'methods' => 'POST,GET,OPTIONS,DELETE',
    // 接口是否允许发送 cookies
    'credentials' => 'true',
    // 接口允许自定义请求头的字段
    'headers' => 'Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin, api_key',

];