# Redis聚合数据写入下游SQL与influxDB
- 定时读取redis
- 解析redis数据
- 将数据批量写入数据库

## 概要设计
https://conf.umlife.net/pages/viewpage.action?pageId=49709328


##  监控指标


measurment: mammon

tag

- name 聚合任务


fields

- cnt 任务执行次数
- latency 任务执行时间
- hLen HGETALL获取数据条数
- sqlWriteCnt 写入sql次数
- sqlWriteNum 写入sql行数
- sqlWriteOK 写入sql成功行数
- sqlWriteLatency sql写入延时

### example
conf.yml
```
# 你要读取的redis连接
redis: "redis_account:redis_password//redis_host:port/"
aggregates:
   # 一个查询任务
  - mitric: 
      # prefix+mark是你要读取的键值
      mark: "app_ad_day_record"
      prefix: "upsert:aggregation:"
      # 每隔300s读取一次redis
      runinterval: 300s
      # dims是tag的字段名及其默认值，是用于聚合的key
      dims:
        "aid": "0"
        "ctid": "0"
        "product": "0"
        "pay_type": "0"
        "point_type": "0"
        "dye": "0"
        "date": "0"
        "currency": "0"
    # sql设置    
    sql:
      # 插入的模板 
      template: "INSERT INTO 
                    youmi_stat.app_ad_day_record_partition 
                    (aid,ctid,product,pay_type,point_type,dye,date,currency,ocount,vcount,pcount,acount,ccount,income,cost) 
                    VALUES 
                    {{range $i,$v := .}}{{if $i}},{{end}}
                    ({{.aid}},{{.ctid}},{{.product}},{{.pay_type}},{{.point_type}},{{.dye}},'{{.date}}',{{.currency}},{{or (.ocount) 0}},{{or (.vcount) 0}},{{or (.pcount) 0}},{{or (.acount) 0}},{{or (.ccount) 0}},{{or (.income) 0}},{{or (.cost) 0}})
                    {{end}}
                    # 当发现主键重复的操作
                    ON DUPLICATE KEY UPDATE 
                    ocount = ocount+values(ocount), 
                    vcount = vcount+values(vcount), 
                    pcount = pcount+values(pcount), 
                    acount = acount+values(acount), 
                    ccount = ccount+values(ccount), 
                    income = income+values(income), 
                    cost = cost+values(cost)"
      # 数据库连接设置              
      dsn: "youmi_db_gw:t2*O!h4!CU4BiR%I@tcp(awsjp-db-10.cywhfyrskwsb.ap-northeast-1.rds.amazonaws.com:3306)/youmi_stat?charset=utf8&parseTime=true&loc=UTC"
      # 是否打印sql语句，一般用于调试
      logquery: false
      # 一次最多插入多少条数据
      maxflushcnt: 5000
      # 连续两次插入的最大间隔，用于定时刷入数据库
      maxflushinterval: 1s

```
### RUN
```
make -B mammon
./bin/mammon/mammon
```
