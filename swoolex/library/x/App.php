<?php
// +----------------------------------------------------------------------
// | 应用启动类-单例-只允许被调用一次
// +----------------------------------------------------------------------
// | Copyright (c) 2018 https://blog.junphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed (http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小黄牛 <1731223728@qq.com>
// +----------------------------------------------------------------------

namespace x;

class App
{
    private static $instance = null; // 创建静态对象变量,用于存储唯一的对象实例  
    private function __construct(){} // 私有化构造函数，防止外部调用
    private function __clone(){}     // 私有化克隆函数，防止外部克隆对象
    /**
     * 配置项 
    */
    private $config;
    /**
     * 支持的服务类型
    */
    private $_server_command = [
        'http',
        'websocket',
        'server',
    ];
    /**
     * 启动的服务参数
    */
    private $_server_start = [
        'server' => null, // 启动服务类型
        'option' => null, // 其余参数
    ];

    /**
     * 实例化对象方法，供外部获得唯一的对象
     * @todo 无
     * @author 小黄牛
     * @version v1.1.1 + 2020.07.08
     * @deprecated 暂不启用
     * @global 无
     * @return App
    */
    public static function run(){
        if (empty(self::$instance)) {
            self::$instance = new App();
            return self::$instance;
        }
    }

    /**
     * 启动服务
     * @todo 无
     * @author 小黄牛
     * @version v1.1.1 + 2020.07.08
     * @deprecated 暂不启用
     * @global 无
     * @return void
    */
    public function start() {
        $this->config = \x\Config::run()->get('server');

        global $argc, $argv;

		if ($argc <= 1 || $argc > 4 ) {
            $this->echo_handle_command();
            exit;
        }
        
        $command = $argv[1]; // 指令
        $this->_server_start['server'] = $argv[2] ?? null;
        $this->_server_start['option'] = $argv[3] ?? null;
        
        // 删除全局变量
        unset($argc);
        unset($argv);
        
        // 处理命令行执行
        switch ($command) {
            // 启动服务
            case 'start':
                if (empty($this->_server_start['server'])) {
                    $this->echo_swoolex_error('sw-x start missing parameter 2！');
                }
                $this->_server_start['server'] = strtolower($this->_server_start['server']);
                if (in_array($this->_server_start['server'], $this->_server_command) == false) {
                    $this->echo_swoolex_error('sw-x start [server] error，support only：'.implode('、', $this->_server_command));
                }
                if ($this->_server_start['option'] && $this->_server_start['option'] != '-d') {
                    $this->echo_swoolex_error('sw-x start daemonize error，support only：-d');
                }
                // 初始化连接池日志文件
                $this->create_mysql_pool_log();
                $this->create_redis_pool_log();
                // 打印服务器字幕
                $this->echo_start_command();
                // 先初始化路由表
                \x\doc\Table::run()->start();
                // 启动服务
                $service = new \x\service\Server(); 
                $service->start($this->_server_start['server'], $this->_server_start['option']);
            break;
            // 热重启
            case 'reload':
                $idJson = file_get_contents($this->config['pid_file']);  
				$idArray = json_decode($idJson, true);
                file_put_contents($this->config['worker_pid_file'], '');
                file_put_contents($this->config['tasker_pid_file'], '');
				posix_kill($idArray['manager_pid'], SIGUSR1);
            break;
            // 查看服务状态
            case 'status':
                // 打印服务器字幕
                $this->echo_start_command();
                if (is_file($this->config['worker_pid_file'] ) && is_file($this->config['tasker_pid_file'])) {
                    // 读取所有进程，并列出来
                    $idsJson = file_get_contents($this->config['pid_file']);
                    $idsArr = json_decode($idsJson, true);
                    $workerPidString = rtrim(file_get_contents($this->config['worker_pid_file']), '|');
                    $taskerPidString = rtrim(file_get_contents($this->config['tasker_pid_file']), '|');
                    $workerPidArr = explode('|', $workerPidString);
                    $taskerPidArr = explode('|', $taskerPidString);

                    echo "Worker-Pid：".PHP_EOL;
                    echo str_pad('Master', 22, ' ', STR_PAD_BOTH ),
                        str_pad('Manager', 14, ' ', STR_PAD_BOTH ),
                        str_pad('Worker_id', 5, ' ', STR_PAD_BOTH ),
                        str_pad('Pid', 12, ' ', STR_PAD_BOTH).PHP_EOL;

                    foreach ($workerPidArr as $workerPidItem) {
                        $tempIdPid = explode(':', $workerPidItem);
                        echo str_pad($idsArr['master_pid'], 22, ' ', STR_PAD_BOTH ),
                            str_pad($idsArr['manager_pid'], 14, ' ', STR_PAD_BOTH ),
                            str_pad($tempIdPid[0], 5, ' ', STR_PAD_BOTH);
                        if (isset($tempIdPid[1])) echo str_pad($tempIdPid[1], 12, ' ', STR_PAD_BOTH);
                        echo PHP_EOL.PHP_EOL;
                    }
                    echo "Tasker-Pid：".PHP_EOL;
                    echo str_pad('Master', 22, ' ', STR_PAD_BOTH ),
                        str_pad('Manager', 14, ' ', STR_PAD_BOTH ),
                        str_pad('Tasker_id', 5, ' ', STR_PAD_BOTH ),
                        str_pad('Pid', 12, ' ', STR_PAD_BOTH).PHP_EOL;
                    foreach ($taskerPidArr as $taskerPidItem) {
                        $tempIdPid = explode(':', $taskerPidItem);
                        echo str_pad($idsArr['master_pid'], 22, ' ', STR_PAD_BOTH ),
                            str_pad($idsArr['manager_pid'], 14, ' ', STR_PAD_BOTH ),
                            str_pad($tempIdPid[0], 5, ' ', STR_PAD_BOTH);
                        if (isset($tempIdPid[1])) echo str_pad($tempIdPid[1], 12, ' ', STR_PAD_BOTH);
                        echo PHP_EOL;
                    }
                }
            break;
            // 停止服务
            case 'stop':
                $idJson = file_get_contents($this->config['pid_file']);  
                $idArray = json_decode($idJson, true);
                
				@unlink($this->config['pid_file']);
				@unlink($this->config['worker_pid_file']);
				@unlink($this->config['tasker_pid_file']);
				var_dump(posix_kill($idArray['master_pid'], SIGKILL));
            break;
            // 没有的指令
            default:
				$this->echo_handle_command();
		  	break;
        }
    }

    /**
     * 启动脚本错误，输出命令行内容
     * @todo 无
     * @author 小黄牛
     * @version v1.1.1 + 2020.07.08
     * @deprecated 暂不启用
     * @global 无
     * @param string $error
     * @return void
    */
    private function echo_swoolex_error($error) {
        $this->echo_swoolex_command();
        echo 'SwooleX-ERROR：'.$error.PHP_EOL.PHP_EOL;
        exit;
    }

    /**
     * 打印SwooleX的命令行图标
     * @todo 无
     * @author 小黄牛
     * @version v1.1.1 + 2020.07.08
     * @deprecated 暂不启用
     * @global 无
     * @return void
    */
    private function echo_swoolex_command() {
        echo PHP_EOL;
        echo "+----------------------------------------------------------+".PHP_EOL;
        echo "|   _____                              _         __    __  |".PHP_EOL;
        echo "|  / ____|                            | |        \ \  / /  |".PHP_EOL;
        echo "| | (___   __      __   ___     ___   | |   ___   \ \/ /   |".PHP_EOL;
        echo "|  \___ \  \ \ /\ / /  / _ \   / _ \  | |  / _ \   \  /    |".PHP_EOL;
        echo "|  ____) |  \ V  V /  | (_) | | (_) | | | |  __/   /  \    |".PHP_EOL;
        echo "| |_____/    \_/\_/    \___/   \___/  |_|  \___|  / /\ \   |".PHP_EOL;
        echo "|                                                /_/  \_\  |".PHP_EOL;   
        echo "+----------------------------------------------------------+".PHP_EOL; 
        echo PHP_EOL;
    }

    /**
     * 打印启动项的使用说明
     * @todo 无
     * @author 小黄牛
     * @version v1.1.1 + 2020.07.08
     * @deprecated 暂不启用
     * @global 无
     * @return void
    */
    private function echo_handle_command() {
        $this->echo_swoolex_command();

        echo 'USAGE: php sw-x commond'.PHP_EOL;
        echo '1. start [服务类型]，以DeBug模式开启服务，此时服务不会以Daemon形式运行'.PHP_EOL;
        echo '2. start [服务类型] -d，以Daemon模式开启服务'.PHP_EOL;
        echo '3. status，查看服务器的状态'.PHP_EOL;
        echo '4. stop，停止服务器'.PHP_EOL;
        echo '5. reload，热加载所有业务代码'.PHP_EOL.PHP_EOL;
        echo 'SERVER: Types of services supported'.PHP_EOL;
        echo '1. http，WEB服务'.PHP_EOL;
        echo '2. websocket，WebSocket服务'.PHP_EOL;
        echo '3. server，Tcp服务'.PHP_EOL.PHP_EOL;
        exit;
    }

    /**
     * 打印启动服务信息
     * @todo 无
     * @author 小黄牛
     * @version v1.2.2 + 2020.07.20
     * @deprecated 暂不启用
     * @global 无
     * @return void
    */
    private function echo_start_command() {
        // 设置master进程别名
        swoole_set_process_name($this->config['master']);

        $this->echo_swoolex_command();

        echo "\033[1A\n\033[K-----------------------\033[47;30m SwooleX Server \033[0m--------------------------\n\033[0m";
        echo "Swoole-Version：".swoole_version().PHP_EOL;
        echo "CPU_nums：".swoole_cpu_num().PHP_EOL;
        echo "SwooleX-Version：".VERSION." Beta".PHP_EOL;
        echo "PHP Version：".PHP_VERSION.PHP_EOL;
        echo "Server Type：".$this->_server_start['server'].PHP_EOL;
        echo "Host：".$this->config['host'].PHP_EOL;
        echo "Port：".$this->config['port'].PHP_EOL;
        if ($this->config['ssl_cert_file'] && $this->config['ssl_key_file']) {
            echo "SSL：Yes".PHP_EOL;
        } else {
            echo "SSL：No".PHP_EOL;
        }
        if ($this->_server_start['option']=='-d') {
            echo "Daemonize：Yes".PHP_EOL;
        } else {
            if ($this->config['daemonize'] == true) {
                echo "Daemonize：Yes".PHP_EOL;
            } else {
                echo "Daemonize：-- 未知，参考status指令 查看进程是否存活".PHP_EOL;
            }
        }
        echo "Memory_get_usage：".$this->memory().PHP_EOL;
        echo "Container_count：".\x\Container::getInstance()->sum().PHP_EOL;

        echo "Mysql_connect_count（5S）：".$this->create_mysql_pool_log(false).PHP_EOL;
        echo "Redis_connect_count（5S）：".$this->create_redis_pool_log(false).PHP_EOL;

        echo PHP_EOL;
    }

    /**
     * 获取当前内存占用大小
     * @todo 无
     * @author 小黄牛
     * @version v1.2.1 + 2020.07.17
     * @deprecated 暂不启用
     * @global 无
     * @return void
    */
    private function memory(){ 
        $size = memory_get_usage();
        $unit = '';
        if ($size >= 1073741824) {
            $size = ($size / 1073741824);
            $unit = 'G';
        } elseif ($size >= 1048576) {
            $size = ($size / 1048576);
            $unit = 'M';
        } elseif ($size >= 1024) {
            $size = ($size / 1024);
            $unit = 'K';
        } else {
            $size = $size;
        }
        return round($size, 2).' '.$unit.'B';
    }

    /**
     * 读取Mysql连接数日志
     * @todo 无
     * @author 小黄牛
     * @version v1.2.1 + 2020.07.17
     * @deprecated 暂不启用
     * @global 无
     * @param bool $status 是否用于初始化
     * @return void
    */
    private function create_mysql_pool_log($status=true) {
        // MYSQL连接数
        $path = ROOT_PATH.'/env/mysql_pool_num.count';
        // 清空并创建
        if ($status) {
            return file_put_contents($path, '{}');
        }
        $json = file_get_contents($path);
        $array = [];
        if ($json) {
            $array = json_decode($json, true);
        }
        $mysql_pool_num = 0;
        foreach ($array as $v) {
            $mysql_pool_num += $v;
        }
        return $mysql_pool_num;
    }
    
    /**
     * 读取Redis连接数日志
     * @todo 无
     * @author 小黄牛
     * @version v1.2.1 + 2020.07.17
     * @deprecated 暂不启用
     * @global 无
     * @param bool $status 是否用于初始化
     * @return void
    */
    private function create_redis_pool_log($status=true) {
        //Redis连接数
        $path = ROOT_PATH.'/env/redis_pool_num.count';
        // 清空并创建
        if ($status) {
            return file_put_contents($path, '{}');
        }
        $json = file_get_contents($path);
        $array = [];
        if ($json) {
            $array = json_decode($json, true);
        }
        $redis_pool_num = 0;
        foreach ($array as $v) {
            $redis_pool_num += $v;
        }
        return $redis_pool_num;
    }
}