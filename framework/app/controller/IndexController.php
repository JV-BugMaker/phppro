<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/13
 * Time: 上午12:08
 * 具体路由指定
 */
namespace app\controller;
use \core\lib\Model;
class IndexController
{
    public function index(){
        p("it's indexController index");
        $model = new Model();
        $sql = 'SELECT * FROM test';
        $res = $model->query($sql);
    }
}