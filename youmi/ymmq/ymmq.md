## 简介
* 带缓存的数据库查询的封装模块。各文件介绍：极其复杂，不介绍
* 提供对于部分数据库的常用数据的操作
* ymmq 缓存底层使用的rediscluster，因此，如果ymmq的conf提供了redis配置，那么你也可以使用rediscluster进行redis操作

## 使用方法
* 实例化一个 ymmq.Conf ，一般通过配置文件，文件example如下：
  ```
  ymmq:
  db:
    db_driver: mysql
    db_dsn: root:root@tcp(adxmi-hemc-adn:3306)/youmi_ad?charset=utf8&parseTime=true&loc=UTC
    db_dsn: root:password@tcp(127.0.0.1:3306)/youmi_ad?charset=utf8&parseTime=true&loc=UTC
    max_open_conn: 32
    max_idle_conn: 16
  redis:
    - redis://127.0.0.1:6379
  ```
* 实例化ymmq.Conf
    ```
    migo.Panic(miyaml.Load(conf, *confPath))  
    ```
* 初始化ymmmq
  ```
  migo.Panic(ymmq.Init(conf.YMMQ))
  ```
* 查询，各数据库提供的方法请看代码。。。。。。。
    ```
    ctls, err := ymmq.QueryPlatformControlsByApp(ctx, aid)
    ```
