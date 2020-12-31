介绍基础工具

## yugong
跨数据库搬数据的工具

背景：我们经常需要从其他库读取数据，然后写入另外的库。mysql客户端不支持跨库读写数据，因此我们瞎写了一个服务yugong来做这样的工作


样例：从dim.table_a 查询出维度a；根据维度a从src.table_b查询出数据；将从table_b查询出的数据写入dst.table_c
```
yugong \
-dim mysql://xxxxxxxx \
-src mysql://yyyyyyyy \
-dst mysql://zzzzzzzz \
-querydims "select a from table_a where ......" \
-prepare "update|delete|insert" \
-query "
        -- 插入数据到dst的table_c表
        insert into table_c(c1, c2) on duplicate key update ...
        -- 下面的select语句是在库src里面执行的
        select b1, b2 from table_b where b3 = {{.a}}
        group by ...
        having ...
        order by ..." \
-after "update|delete|insert" 
```
- prepare、after可以有多行，是针对dst进行操作的纯sql语句，可省
- querydims：查询出维度，可省。如果省略，那么prapre、query、after会按顺序执行一遍就退出
- query
    ```
    insert into dst.tbl(...) [on duplicate key update ...] 
    select col_1, col_2, ... from src.tbl
        [where ...]
        [group by ... [having ...]]
        [order by...] 
    ```

流程：
```
// 查询出维度
for row in dim.select(querydims):
    for pre_sql in prepares：
        dst.exec(pre)
    // 从数据源src查询出目标数据
    data = src.select(select_query, args=row)
    // 将数据写入dst
    dst.exec(insert_query, args=data)
    for aft_sql in afters:
        dst.exec(after)
```

## baton分布式任务调度
<p align="center">
<img src="./img/baton.png" alt="drawing" width="500"/>
</p>

## k2sql
读取kafka，将数据筛选、聚合后写入数据库、s3的流数据处理工具

<p align="center">
<img src="./img/k2sql.png" alt="drawing" width="500"/>
</p>

```
maxflushcount: 100
maxflushinterval: 100s
kafka:
  brokers: ['kafka-00.ad.awsjp:9092']
  groupid: k2sql4test
  topics: [au_click, au_click_err]
jobs:
  -
    jobname: "k2sql4test"
    writer:
      sql:
        dsn: "mysql://....."
        usetx: true // 是否使用事务处理，redshift没有事务性
        usetemplate: true // 是否使用template来写入，redshift只能使用该方式。usetemplate为true是，usetx无效
    query: "insert into stat(   // 形式就是一条标准的sql语句
              dt, aid, oid, revenue)
            select
              dt, aid, oid, sum(revenue) as sum_revenue
            from
              aulog // 此处的表明为schema
            where aid != 0 and ... // 只支持and逻辑
            group by dt, aid, oid // 聚合
            on duplicate key update cnt=values(cnt)+cnt
            "
```

维护、开发时，需要注意的模块有：
- 如何将kafka里面的数据进行反序列化，见registry package。现在的反序列化器可以通过tools package 自动生成，见其readme
- 如何将配置文件中的sql语句解析出：insert语句、filter、聚合器，见parser.go及其单元测试的输出(编译原理能力有限，写得不太好，哈哈哈)
- 如何将数据批量写入数据库等存储系统：bw（batch write）模块