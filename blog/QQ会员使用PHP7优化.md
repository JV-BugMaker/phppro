#QQ会员中网站性能升级
>原网地址：<a href="http://blog.jobbole.com/103322/">链接</a>


##PHP7与HHVM

###1.HHVM和JIT 
HHVM是FB开源PHP虚拟机，使用了与JVM一样的JIT机制。即时编译技术，指在运行时才会去编译字节码转换为机器码。
	
在PHP5.5的时候，官方开发组就开始尝试了JIT在PHP中的使用，原先的PHP执行流程，是将PHP代码通过词法分析和语法分析，编译成opcode字节码（类似汇编），然后通过zend引擎去读取这些opcode命令，逐条解析执行。

PHP代码--Parse（词法语法分析）--opcode（字节码）--zend引擎

他们的尝试，在opcode环节之后通过typeinf判断，然后通过jit生成bytecodes，然后执行。

PHP代码--Parse（词法语法分析）--opcode（中间字节码）--typeinf（变量类型分析） -- JIT(及时编译)--bytecodes--执行

但是使用benchmark（测试程序）得到很好结果，但是在WordPress中却拿不到好的结果。在wordpress中使用JIT产生的机器码太大，引起CPU缓存命中率下降（CPU CACHE MISS）。

###2.PHP7在性能方面的优化
（1）将基础变量从struct（结构体）变为union（联合体），节省内存空间，间接减少CPU在内存分配和管理上的开销。

（2）部分基础变量（zend_array,zend_string等）采用内存空间连续分配的方式，降低CPU CACHE MISS 的发生概率，CPU从CPU CACHE 中获取数据和从内存中读取数据效率差会很大。

（3）通过宏定义和内联函数（inline），让编译器提前完成部分工作。不需要在运行时分配内存，能够实现类似函数的功能，却没有函数调用的压栈、弹栈开销，效率会更高。

###3.Apache2.4编译模式MPM
（1）prefork，多进程模式，1个进程服务于一个请求，成本比较高。但是稳定性最好。

（2）workers，多进程多线程，一个进程含有多个worker线程，1个worker线程服务于1个用户请求，因为线程更轻量，成本比较低。但是在keepAlive场景下，worker资源会被client占据，无法响应其他请求。

（3）event，多进程多线程模式，1个线程也含有多个worker线程，1个worker线程服务于1个用户请求。但是，它解决了KeepAlive场景下的worker线程被占据问题，它通过专门的线程来管理这些KeepAlive连接，然后再分配“工作”给具体处理的worker，工作worker不会因为KeepAlive而导致空等待。

##PHP7升级实践数据类型变化
（1）zval
php7的诞生始于zval结构的变化，PHP7不再需要指针的指针，绝大部分zval**需要修改成zval*。如果PHP7直接操作zval，那么zval*也需要改成zval，Z_*P()也要改成Z_*()，ZVAL_*(var, …)需要改成ZVAL_*(&var, …)，一定要谨慎使用&符号，因为PHP7几乎不要求使用zval*，那么很多地方的&也是要去掉的。

ALLOC_ZVAL，ALLOC_INIT_ZVAL，MAKE_STD_ZVAL这几个分配内存的宏已经被移除了。大多数情况下，zval*应该修改为zval，而INIT_PZVAL宏也被移除了。

```
/* 7.0zval结构源码 */
 
/* value字段，仅占一个size_t长度，只有指针或double或者long */
 
typedef union _zend_value {
 
zend_long         lval;                /* long value */
 
double            dval;                /* double value */
 
zend_refcounted  *counted;
 
zend_string      *str;
 
zend_array       *arr;
 
zend_object      *obj;
 
zend_resource    *res;
 
zend_reference   *ref;
 
zend_ast_ref     *ast;
 
zval             *zv;
 
void             *ptr;
 
zend_class_entry *ce;
 
zend_function    *func;
 
struct {
 
uint32_t w1;
 
uint32_t w2;
 
} ww;
 
} zend_value;
 
struct _zval_struct {
 
zend_value        value;            /* value */
 
union {
 
。。。
 
} u1;/* 扩充字段，主要是类型信息 */
 
union {
 
… …
 
} u2;/* 扩充字段，保存辅助信息 */
 
};
```
(2)整型

```
long->zend_long
 
/* 定义 */
 
typedef int64_t zend_long;
 
/* else */
 
typedef int32_t zend_long;
```

(3)字符串类型
PHP5.6版本中使用 char* + len的方式表示字符串，PHP7.0中做了封装，定义了zend_string类型：

```
struct _zend_string {
 
zend_refcounted_h gc;
 
zend_ulong        h;                /* hash value */
 
size_t            len;
 
char              val[1];
 
};
```
zend_string和char*的转换

```
zend_string *str;

char *cstr = NULL;

size_t slen = 0;

//...

/* 从zend_string获取char* 和 len的方法如下 */

cstr = ZSTR_VAL(str);

slen = ZSTR_LEN(str);

/* char* 构造zend_string的方法 */

zend_string * zstr = zend_string_init("test",sizeof("test"), 0);
```

扩展方法，解析参数时，使用字符串的地方，将‘s’替换成‘S'。

```
/* 例如 */

zend_string *zstr;

if (zend_parse_parameters(ZEND_NUM_ARGS() , "S", &zstr) == FAILURE)

{

RETURN_LONG(-1);

}
```

(4)自定义对象
源代码：

```
/* php7.0 zend_object 定义 */

struct _zend_object {

zend_refcounted_h gc;

uint32_t          handle;

zend_class_entry  *ce;

const zend_object_handlers  *handlers;

HashTable        *properties;

zval              properties_table[1];

};
```

zend_object是一个可变长度的结构。因此在自定义对象的结构中，zend_object需要放在最后一项：

```
/* 例子 */

struct clogger_object {

CLogger *logger;

zend_object  std;// 放在后面

};

/* 使用偏移量的方式获取对象 */

static inline clogger_object *php_clogger_object_from_obj(zend_object *obj) {

return (clogger_object*)((char*)(obj) - XtOffsetOf(clogger_object, std));

}

#define Z_USEROBJ_P(zv) php_clogger_object_from_obj(Z_OBJ_P((zv)))

/* 释放资源时 */

void tphp_clogger_free_storage(zend_object *object TSRMLS_DC)

{

clogger_object *intern = php_clogger_object_from_obj(object);

if (intern->logger)

{

delete intern->logger;

intern->logger = NULL;

}

zend_object_std_dtor(&intern->std);

}
```

(5)数组
7.0中的hash表定义如下，给出了一些注释：

```
/* 7.0中的hash表结构 */

typedef struct _Bucket { /* hash表中的一个条目 */

zval              val;   /* 删除元素zval类型标记为IS_UNDEF */

zend_ulong        h;                /* hash value (or numeric index)   */

zend_string      *key;              /* string key or NULL for numerics */

} Bucket;

typedef struct _zend_array HashTable;

struct _zend_array {

zend_refcounted_h gc;

union {

struct {

ZEND_ENDIAN_LOHI_4(

zend_uchar    flags,

zend_uchar    nApplyCount,

zend_uchar    nIteratorsCount,

zend_uchar    reserve)

} v;

uint32_t flags;

} u;

uint32_t          nTableMask;

Bucket           *arData; /* 保存所有数组元素 */

uint32_t          nNumUsed; /* 当前用到了多少长度， */

uint32_t          nNumOfElements; /* 数组中实际保存的元素的个数,一旦nNumUsed的值到达nTableSize，PHP就会尝试调整arData数组，让它更紧凑，具体方式就是抛弃类型为UDENF的条目 */

uint32_t          nTableSize; /* 数组被分配的内存大小为2的幂次方（最小值为8） */

uint32_t          nInternalPointer;

zend_long         nNextFreeElement;

dtor_func_t       pDestructor;

};
```
其中，PHP7在zend_hash.h中定义了一系列宏，用来操作数组，包括遍历key、遍历value、遍历key-value等，下面是一个简单例子：

```
/* 数组举例 */

zval *arr;

zend_parse_parameters(ZEND_NUM_ARGS() , "a", &arr_qos_req);

if (arr)

{

zval *item;

zend_string *key;

ZEND_HASH_FOREACH_STR_KEY_VAL(Z_ARRVAL_P(arr), key, item) {

/* ... */

}

}

/* 获取到item后，可以通过下面的api获取long、double、string值 */

zval_get_long(item)

zval_get_double(item)

zval_get_string(item)
```
PHP5.6版本中是通过zend_hash_find查找key，然后将结果给到zval **变量，并且查询不到时需要自己分配内存，初始化一个item，设置默认值。

###PHP7中的api变化
（1）duplicate参数
>PHP5.6中很多API中都需要填入一个duplicate参数，表明一个变量是否需要复制一份，尤其是string类的操作，PHP7.0中取消duplicate参数，对于string相关操作，只要有duplicate参数，直接删掉即可。因为PHP7.0中定义了zval_string结构，对字符串的操作，不再需要duplicate值，底层直接使用zend_string_init初始化一个zend_string即可，而在PHP5.6中string是存放在zval中的，而zval的内存需要手动分配。
涉及的API汇总如下：

```
add_index_string、add_index_stringl、add_assoc_string_ex、add_assoc_stringl_ex、add_assoc_string、add_assoc_stringl、add_next_index_string、add_next_index_stringl、add_get_assoc_string_ex、add_get_assoc_stringl_ex、add_get_assoc_string、add_get_assoc_stringl、add_get_index_string、add_get_index_stringl、add_property_string_ex、add_property_stringl_ex、add_property_string、add_property_stringl、ZVAL_STRING、ZVAL_STRINGL、RETVAL_STRING、RETVAL_STRINGL、RETURN_STRING、RETURN_STRINGL
```
(2) MAKE_STD_ZVAL
>PHP5.6中，zval变量是在堆上分配的，创建一个zval变量需要先声明一个指针，然后使用MAKE_STD_ZVAL进行分配空间。PHP7.0中，这个宏已经取消，变量在栈上分配，直接定义一个变量即可，不再需要MAKE_STD_ZVAL，使用到的地方，直接去掉就好。

(3) ZEND_RSRC_DTOR_FUNC
>修改参数名为rsrc为res

```
/* PHP5.6 */

typedef struct _zend_rsrc_list_entry {

void *ptr;

int type;

int refcount;

} zend_rsrc_list_entry;

typedef void (*rsrc_dtor_func_t)(zend_rsrc_list_entry *rsrc TSRMLS_DC);

#define ZEND_RSRC_DTOR_FUNC(name)        void name(zend_rsrc_list_entry *rsrc TSRMLS_DC)

/* PHP7.0 */

struct _zend_resource {

zend_refcounted_h gc;/*7.0中对引用计数做了结构封装*/

int               handle;

int               type;

void             *ptr;

};

typedef void (*rsrc_dtor_func_t)(zend_resource *res);

#define ZEND_RSRC_DTOR_FUNC(name) void name(zend_resource *res)
```
PHP7.0中，将zend_rsrc_list_entry结构升级为zend_resource，在新版本中只需要修改一下参数名称即可。

(4)二级指针宏，即Z_*_PP

>PHP7.0中取消了所有的PP宏，大部分情况直接使用对应的P宏即可。

(5)zend_object_store_get_object被取消
>根据官方wiki，可以定义如下宏，用来获取object，实际情况看，这个宏用的还是比较频繁的：

```
static inline user_object *user_fetch_object(zend_object *obj) {

return (user_object *)((char*)(obj) - XtOffsetOf(user_object, std));

}

/* }}} */

#define Z_USEROBJ_P(zv) user_fetch_object(Z_OBJ_P((zv)))
```
(6)zend_hash_exists、zend_hash_find
>对所有需要字符串参数的函数，PHP5.6中的方式是传递两个参数（char* + len），而PHP7.0中定义了zend_string，因此只需要一个zend_string变量即可。

返回值变成了zend_bool类型：

```
/* 例子 */

zend_string * key;

key = zend_string_init("key",sizeof("key"), 0);

zend_bool res_key = zend_hash_exists(itmeArr, key);
```

##参考资料
>1、php5 to phpng，http://yaoguais.com/?s=md/php/php7-vm.md
>2、PHP扩展开发及内核应用， http://www.walu.cc/phpbook/10.1.md
>
>3、PHP 7中新的Hashtable实现和性能改进 ，http://gywbd.github.io/posts/2014/12/php7-new-hashtable-implementation.html
>
>4、深入理解PHP7之zval， https://github.com/laruence/php7-internal/blob/master/zval.md
>
>5、官方wiki， https://wiki.php.net/phpng-upgrading
>
>6、php手册 ，http://php.net/manual/zh/index.php
>
>7、PHP7 使用资源包裹第三方扩展的实现及其源码解读 ，https://mengkang.net/684.html

