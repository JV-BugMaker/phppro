<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/6
 * Time: 上午12:07
 * 插入排序
 * 每次将一个待排序的数据元素插入到前面已经排好序的数列中，使数列依然有序，知道待排序数据元素全部插入完为止。
 * O(n2)
 */

function insertSort($arr){
    $len = count($arr);
    for($i = 1;$i<$len;$i++){
        $tmp = $arr[$i];
        for($j=$i-1;$j>=0;$j--){
            //只有后面的数 比前面的数小 才不断往前遍历
            if($tmp < $arr[$j]){
                //前后替换 在插入 不断往前替换
                $arr[$j+1] = $arr[$j];
                $arr[$j] = $tmp;
            }else{
                break;
            }
        }
    }
    return $arr;
}

$arr = array(49,38,65,97,76,13,27,49);
var_dump(insertSort($arr));