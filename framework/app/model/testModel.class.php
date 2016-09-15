<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/15
 * Time: 下午6:45
 */
namespace app\model;

use core\lib\Model;

class testModel extends Model
{
    public static function getSource(){
        return "test";
    }

    public function getAll(){
        $data = $this->select(self::getSource(),"*");
        return $data;
    }

    public function getOne($id){
        $data = $this->select(self::getSource(),"*",array('id'=>$id));
        return $data;
    }

    public function upOne($id,$data){
        $ret = $this->update(self::getSource(),$data,array('id'=>$id));
        return $ret;
    }
    public function delOne($id){
        $ret = $this->delete(self::getSource(),array('id'=>$id));
        return $ret;
    }
    public function insertOne($data){
        $ret = $this->insert(self::getSource(),$data);
        return $ret;
    }
}