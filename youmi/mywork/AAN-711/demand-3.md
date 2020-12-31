## 需求3
使用kepler进行操作，增加一个kafka消费者组，用于消费消息，将消息写入数据库

### 使用kepler
数据库表
```
create table if not exists stat_aid_api_fetch (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    dt datetime not null comment '请求时间',
    aid bigint(20) not null comment '媒介id',
    pid int not null default 0 comment '系统类别，android=3，ios=5,没有指定系统=0',
    total int(11) not null comment '符合要求的广告/总共应该拉取的广告的数量',
    num int(11) not null comment '本次请求拉取到的数量',
    page int(11) not null comment '拉取第几页',
    page_size int(11) not null comment '该页的大小',
    adlv_a int(11) not null comment '符合要求的广告中等级为A的广告的数量',
    adlv_b int(11) not null comment '符合要求的广告中等级为B的广告的数量',
    adlv_c int(11) not null comment '符合要求的广告中等级为C的广告的数量',
    adlv_d int(11) not null comment '符合要求的广告中等级为D的广告的数量',
    adlv_e int(11) not null comment '符合要求的广告中等级为E的广告的数量',
    adlv_x int(11) not null comment '符合要求的广告中等级为X的广告的数量',
    adlv_n int(11) not null comment '符合要求的广告中等级为N的广告的数量',
    last_edit timestamp not null default current_timestamp() on update current_timestamp() comment '最后更新时间',
    primary key (id,dt,aid,pid)
) comment '媒介拉取广告详情表'
;
```

配置文件
```
#stdin: true
TPL_SQL_WRITER: &TPL_SQL_WRITER
  driver: mysql
  dsn: youmi_db_gw:t2*O!h4!CU4BiR%I@tcp(db-20.ad.awsjp:3306)/youmi_stat?sql_mode=TRADITIONAL
#  dsn: root:root@tcp(172.16.1.55:3306)/youmi_stat?sql_mode=TRADITIONAL
#  debug: true
#  dryrun: true
#  logquery: true
  maxwritecnt: 500

kafka:
  brokers:
    - kafka-00.ad.awsjp:9092
  groupid:
    kepler-aid-api
  topics:
    - zbus4apj
  nthread: 4
kepler:
  format: adlog
  jobs:
    # 监控媒介拉取情况
    aid_api_fetch:
      sqlwriter:
        <<: *TPL_SQL_WRITER
        template: "insert into youmi_stat.stat_aid_api_fetch (dt,aid,pid,total,num,page,page_size,adlv_a,adlv_b,adlv_c,adlv_d,adlv_e,adlv_x,adlv_n) values\n{{range $i,$v:=.}}{{if $i}},\n{{end}}('{{.dt_5min}}','{{.aid}}','{{.os}}','{{.total}}','{{.num}}','{{.page}}','{{.page_size}}',{{.adlv_a}},{{.adlv_b}},{{.adlv_c}},{{.adlv_d}},{{.adlv_e}},{{.adlv_x}},{{.adlv_n}}){{end}}\n"
        dryrun: false
        logquery: false
      maxflushcnt: 10000
      maxflushinterval: 5m
      # 只记录offline的请求
      filters:
        - "dye eq 0"
      dims:
        dt_5min:
        aid:
        # url.query中的os字段
        os:
        dye:

      fields:
        total:
        num:
        page:
        page_size:
        adlv_a:
        adlv_b:
        adlv_c:
        adlv_d:
        adlv_e:
        adlv_x:
        adlv_n:

```


### 每天定时清空报表中45天前的数据

```
#!/bin/sh
str="delete from youmi_stat.stat_aid_api_fetch where dt < date_sub(curdate(), interval 45 day);"
//mysql -h localhost -P 3306 -uroot -p244143261 -e "$str"
```

### canary 报表
* 完成媒介拉取广告报表
```
stat_aid_api_fetch:
    <<: *TEMPLATE
    name: '10. Aid Fetch Report'
    driver:  $$youmi_stat
    table: 'stat_aid_api_fetch'
    dimensions:
        - {key: dt, name: Time, type: time, format: dt, not-filterable: 1}
        - {key: aid, name: 'aid', type: int}
        - {key: pid, name: 'pid', type: int}
        - {key: total, name: 'total', type: int}
        - {key: num, name: 'num', type: int}
        - {key: page, name: 'page', type: int}
        - {key: page_size, name: 'page_size', type: int}
        - {key: adlv_a, name: 'adlv_a', type: int,desc: 等级为a的广告的数量}
        - {key: adlv_b, name: 'adlv_b', type: int}
        - {key: adlv_c, name: 'adlv_c', type: int}
        - {key: adlv_d, name: 'adlv_d', type: int}
        - {key: adlv_e, name: 'adlv_e', type: int}
        - {key: adlv_x, name: 'adlv_x', type: int}
        - {key: adlv_n, name: 'adlv_n', type: int}
      measures:
        - {key: req, name: 'req', type: int}
        - {key: imp, name: 'imp', type: int}
        - {key: clk, name: 'clk', type: int}
        - {key: cov, name: 'cov', type: int}
```
