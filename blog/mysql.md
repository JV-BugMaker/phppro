#mysql主从

##ERROR 1201 (HY000):Could not initialize master info structure的问题

>ERROR 1201 (HY000):Could not initialize master info structure的问题
 
>今天在做MySQL主从复制时遇到个ERROR 1201 (HY000): Could not initialize master info structure .
>出现这个问题的原因是之前曾做过主从复制！
>解决方案是：运行命令 stop slave;
>成功执行后继续运行 reset slave;
>然后进行运行GRANT命令重新设置主从复制。
具体过程如下：
Command代码  

```
mysql> change master to master_host='127.0.0.1', master_user='user', master_pass  
word='user', master_log_file='mysql-bin-000202', master_log_pos=553;  
ERROR 1201 (HY000): Could not initialize master info structure; more error messa  
ges can be found in the MySQL error log  
mysql> stop slave;  
Query OK, 0 rows affected, 1 warning (0.00 sec)  
  
mysql> reset slave;  
Query OK, 0 rows affected (0.00 sec)  
  
mysql> change master to master_host='127.0.0.1', master_user='user', master_pass  
word='user', master_log_file='mysql-bin-000202', master_log_pos=553;  
Query OK, 0 rows affected (0.11 sec)  
```
##主从不同步问题解决
>查看主从同步问题

```
slave:show slave status;

```
>主要查看数据项是:Slave_IO_Running 和  Slave_SQL_Running

>方案一:忽略错误后,继续同步

>该方法适用于主从数据库相差不大,或者数据可以不完全一致，就是忽略之前的数据.

```
//首先停掉从库
slave>stop slave;

//跳过一步错误或者多步
set global sql_slave_skip_counter =1;
//开启丛库
start slave;

```

>再次查看从库状态 


>方案二:重新做主从，完全同步

```
//首先进入master 锁表 保持数据不更新
master>flush tables with read lock;
//进行数据备份
master> mysqldump -u mysql -p -h localhost > /tmp/mysql.20160923.sql
//备份sql文件发送到从库服务器
scp mysql.20160923.sql mysql@192.168.0.2:/tmp/
//停止从库的状态
slave>stop slave;
//执行数据导入命令
slave> source /tmp/mysql.20160923.sql
//设置从库同步 master_log_file 以及master_log_pos 是主库状态一致
change master to master_host = '192.168.0.1', master_user = 'mysql ', master_port=3306, master_password='', master_log_file = 'mysqld-bin.000001', master_log_pos=3260;

//重新开启丛库
slave>start slave;
```
