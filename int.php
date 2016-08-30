<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/8/30
 * Time: 下午11:56
 */
/*struct _zval_struct {
    /// Variable information
    zvalue_value value;     // value
    zend_uint refcount;
    zend_uchar type;    // active type
    zend_uchar is_ref;
};*/
//php中的底层变量结构体
//实际上保存变量值的是zvalue_value联合体
/*typedef union _zvalue_value {
    long lval;                  // long value
    double dval;                // double value
    struct {
        char *val;
        int len;
    } str;
    HashTable *ht;              // hash table value
    zend_object_value obj;
} zvalue_value;*/

//在不同操作系统上面 long value的长度随着编译器  有可能是32bit  有可能64bit
//但是double 双浮点型都是64bit 的
$a = 9223372036854775807; //64位有符号数最大值
$b = 9223372036854775808; //最大值+1
var_dump($a); //int(9223372036854775807)
var_dump($b); //float(9.22337203685E+18)
// PHP在词法分析阶段, 对于一个字面量的数值, 会去判断, 是否超出了当前系统的long的表值范围, 如果不是, 则用lval来保存, zval为IS_LONG,
// 否则就用dval表示, zval IS_FLOAT.
//但是如果超过系统的最大值 就是发生一个问题 精度缺失
var_dump($a===($b-1)); //false 这边我个人感觉是类型不一致了


