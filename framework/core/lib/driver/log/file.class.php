<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/14
 * Time: 下午9:18
 *
 * 日志文件形式
 */
namespace core\lib\driver\log;

use core\lib\Config;
class File
{
    public $path ;
    public function __construct()
    {
        $this->path = Config::get('OPTIONS','log')['PATH'];
    }

    public function log($message,$file='log'){
        //获取配置参数
        if(!is_dir($this->path)){
            mkdir($this->path,'0777',true);
        }
        $date = date('Y-m-d H:i:s',time());
        $log = "[log {$date}]{$message}";
        file_put_contents($this->path.$file.'.log',$log.PHP_EOL,FILE_APPEND);
    }
}