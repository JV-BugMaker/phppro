<?php
/*
*算法背景描述
* 比如一个7个元素的数组，
*我想要取出这个数组中的5个元素，
*生成新的数组，
*这些数组是唯一的。
 */
$arr = array(1,2,3,4,5);
$total_arr = array();
$len = count($arr);
getAllComb(0);
function getAllComb($index=0)
{
    global $arr;
    global $total_arr;
    if($index>4){
        return false;
    }
    for ($i=0; $i < 5; $i++) {
      # code...
        //控制位置 5个元素的所有排列组合
        if(($index+$i)>4){
          $temp[$i] = $arr[$index+$i-5];
        }else{
          $temp[$i] = $arr[$index+$i];
        }
    }
    $total_arr[] = $temp;
    $index++;
    getAllComb($index);
}
echo "<pre>";
var_dump($total_arr);
