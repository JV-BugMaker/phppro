<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/6
 * Time: 上午12:57
 * 选择排序
 * 每一趟从待排序的数据元素中选出最小（或最大）的一个元素，顺序放在已排好序的数列的最后，直到全部待排序的数据元素排完。
 * O(n2)
 */

function selectSort($arr){
    for($i=0,$len = count($arr);$i<$len;$i++){
        for($j=$i+1;$j<$len;$j++){
            if($arr[$j]<$arr[$i]){
                $tmp = $arr[$i];
                $arr[$i] = $arr[$j];
                $arr[$j] = $tmp;
            }
        }
    }
    return $arr;
}
$arr = array(49,38,65,97,76,13,27,49);
var_dump(selectSort($arr));