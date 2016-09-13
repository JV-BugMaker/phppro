<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/14
 * Time: 上午12:47
 */
namespace core\lib;
class Config
{
    public static $config;

    public static function get($name,$file)
    {
        /*
         * 处理config
         * */
        if(isset(self::$config[$file])){
            return self::$config[$file][$name];
        }else{
            $path = VIVI.'/core/conf/'.$file.'.conf.php';
            if(is_file($path)){
                $conf = include $path;
                if(isset($conf[$name])){
                    self::$config[$file] = $conf;
                    return $conf[$name];
                }else{
                    throw new \Exception('404 not found config');
                }
            }else{
                throw new \Exception('4040 not found conf');
            }
        }
    }

    //获取配置文件的全部参数

    public static function all($file)
    {
        if(isset(self::$config[$file])){
            return self::$config[$file];
        }else{
            $path = VIVI.'/core/conf/'.$file.'.conf.php';
            if(is_file($path)){
                $conf = include $path;
                if(isset($conf)){
                    self::$config[$file] = $conf;
                    return $conf;
                }else{
                    throw new \Exception('404 not found config');
                }
            }else{
                throw new \Exception('4040 not found conf');
            }
        }
    }
}