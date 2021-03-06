<?php
// +----------------------------------------------------------------------
// | 当管理进程启动时
// +----------------------------------------------------------------------
// | Copyright (c) 2018 https://blog.junphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小黄牛 <1731223728@qq.com>
// +----------------------------------------------------------------------

namespace event;

class onManagerStart
{
    /**
	 * 启动实例
	*/
    public $server;

    /**
     * 统一回调入口
     * @todo 无
     * @author 小黄牛
     * @version v1.1.1 + 2020.07.08
     * @deprecated 暂不启用
     * @global 无
     * @param Swoole $server
     * @return void
    */
    public function run($server) {
        $this->server = $server;

        $config = \x\Config::run()->get('server');
        swoole_set_process_name($config['manager']);

        // 调用二次转发，不做重载
        $on = new \app\event\onManagerStart;
        $on->run($server);
    }
}

