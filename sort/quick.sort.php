<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/6
 * Time: 上午12:32
 * 快速排序
 * 通过一趟排序将要排序的数据分割成独立的两部分，其中一部分的所有数据都比另外一部分的所有数据都要小
 * ，然后再按此方法对这两部分数据分别进行快速排序，整个排序过程可以递归进行，以此达到整个数据变成有序序列。
 * O(nlog2n)
 */

function quickSort($array){
    if (count($array) <= 1) return $array;

    $key = $array[0];
    $left_arr = array();
    $right_arr = array();
    for ($i=1; $i<count($array); $i++){
        if ($array[$i] <= $key)
            $left_arr[] = $array[$i];
        else
            $right_arr[] = $array[$i];
    }
    $left_arr = quickSort($left_arr);
    $right_arr = quickSort($right_arr);

    return array_merge($left_arr, array($key), $right_arr);
}
$arr = array(49,38,65,97,76,13,27,49);
var_dump(quickSort($arr));