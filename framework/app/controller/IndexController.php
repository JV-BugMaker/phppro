<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/13
 * Time: 上午12:08
 * 具体路由指定
 */
namespace app\controller;
use \app\model\testModel;
use \core\lib\Model;

class IndexController extends \core\Vivi
{
    public function index(){
        $data = array(
            'name'=>'jv',
            'age'=>23
        );
        $model = new testModel();
        $model->insertOne($data);
        dump($model->getAll());die;
        p("it's indexController index");

    }

    public function show()
    {
        $this->assign('data','asdasd');
        $this->assign('title','jv');
        $this->display();
    }
}