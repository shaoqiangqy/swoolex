<?php
// +----------------------------------------------------------------------
// | WebSocket控制器基类
// +----------------------------------------------------------------------
// | Copyright (c) 2018 https://blog.junphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小黄牛 <1731223728@qq.com>
// +----------------------------------------------------------------------

namespace x;

class WebSocket
{
    /**
     * server 
    */
    private $setServer;
    /**
     * frame 
    */
    private $setFrame;

    /**
     * 注入WS对象
     * @todo 无
     * @author 小黄牛
     * @version v1.0.1 + 2020.05.27
     * @deprecated 暂不启用
     * @global 无
     * @param obj $setServer
     * @return void
    */
    public function setServer($setServer) {
        $this->setServer = $setServer;
    }

    /**
     * 注入WS状态
     * @todo 无
     * @author 小黄牛
     * @version v1.0.1 + 2020.05.27
     * @deprecated 暂不启用
     * @global 无
     * @param obj $setFrame
     * @return void
    */
    public function setFrame($setFrame) {
        $this->setFrame = $setFrame;
    }
    
    /** 
     * AES加密方法，对数据进行加密，返回加密后的数据 
     * @param string $data 要加密的数据 
     * @return string 
     */  
    private static function encrypt($data) {  
        $config = \x\Config::run()->get('websocket');
        return openssl_encrypt($data, $config['aes_type'], $config['aes_key'], 0, $config['aes_iv']);  
    }  
  
    /** 
     * AES解密方法，对数据进行解密，返回解密后的数据 
     * @param string $data 要解密的数据 
     * @return string 
     */  
    private static function decrypt($data) {  
        $config = \x\Config::run()->get('websocket');
        return openssl_decrypt($data, $config['aes_type'], $config['aes_key'], 0, $config['aes_iv']);  
    } 

    /**
     * 获取参数
     * @todo 无
     * @author 小黄牛
     * @version v1.0.1 + 2020.1.13
     * @deprecated 暂不启用
     * @global 无
     * @param void $data
     * @return void
    */
    public static function get_data($data) {
        $config = \x\Config::run()->get('websocket');
        // 启用加密方式
        if ($config['aes_key']) {
            $data = self::decrypt($data);
        }
        return json_decode($data, true);
    }

    /**
     * 发送数据包
     * @todo 无
     * @author 小黄牛
     * @version v1.0.13 + 2020.05.04
     * @deprecated 暂不启用
     * @global 无
     * @param Swoole $ws 实例
     * @param string $action 回调事件
     * @param string $msg 说明
     * @param mixed $data 结果集
     * @param string $fd 推送标记
     * @return void
    */
    public function fetch($action, $msg='success', $data=[], $fd=null) {
        if (!$fd) $fd = $this->setFrame->fd;
        $config = \x\Config::run()->get('websocket');
        $array = [
            'action' => $action,
            'msg' => $msg,
            'data' => $data
        ];
        $content = json_encode($array, JSON_UNESCAPED_UNICODE);

        // 启用加密方式
        if ($config['aes_key']) {
            $content = self::encrypt($content);
        }

        try {
            $ret = $this->setServer->push($fd, $content);
            return $ret;
        } catch (\Exception $e) {
            return false;
        }
    } 
}