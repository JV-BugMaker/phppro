<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/13
 * Time: 上午12:39
 */
namespace core\lib;

use \core\lib\Config;

class Model extends \PDO
{
    public function __construct()
    {
        //针对 这边出现localhost 去连接的时候出现错误 使用127.0.0.1
        $conf = Config::all('db');
        try{
            parent::__construct($conf['DSN'], $conf['USER'], $conf['PWD']);
        }catch (\PDOException $e){
            p($e->getMessage());
        }
    }
}