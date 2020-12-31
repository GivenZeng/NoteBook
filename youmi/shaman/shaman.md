### 简介
一个定时报警功能。定时每隔一段时间查询数据库（或其他），并将查询结果发送到钉钉或者influx等等。

### limit功能
limit功能：如ctid的点击超量一天只报警一次。
使用方法：为每一个报警增加以一个limit和ttl属性，当limit对应的值如某ctid在ttl时间段内报警过一次，就不再报警。使用limit功能要开启redis（因为使用adlimit实现的）。limit字段应该出现在

### 样例
conf.yml
```
# 如果使用limit功能，则提供redis
redis: "redis://127.0.0.1:6379"
targets:
 # 单个广告点击超过20w报警，每五秒查询一次
  -
    source:
      -
        type: misql
        conf: ctid_clk_warn.yml
    transmit_type: dingding
    transmit_conf: ctid_clk_warn.yml
    freq: '*/5 * * * *'
    #单个广告的报警时间间隔：20s(取名为clk_ctid是为了避免和下面单个广告错误国家点击报警的ctid重复)
    #
    limit: 'clk_ctid'
    ttl: 20
```

 ctid_clk_warn.yml
```
 # 单个广告点击超过20w报警
 # 暂时只选取点击数超过20w的前10
dsn: root:244143261@tcp(localhost:3306)/youmi_stat?charset=utf8&parseTime=true&loc=UTC
name: ctid_clk_warn
webhook: https://oapi.dingtalk.com/robot/send?access_token=ada591012aaa9fb702c0783194faa461b8183e9eb23510eb69be8fb4a55c6fa8

data:
  -     
    name: "ctid_clk_warn"
    sql: "select ctid as clk_ctid , sum(clk) as clk_cnt
          from youmi_stat.stat_ctid_aid_hour
          where to_days(dt) = to_days(now())
          group by ctid
          having sum(clk) > 200000
          limit 10
         "
    tags: "clk_ctid"

template: '
## **今日广告点击超过20w** ##  


** | ctid | 点击数 |  **


{{range .}}
  | {{with .Tags}}{{.clk_ctid}}{{end}} | {{with .Fields}}{{.clk_cnt}}{{end}} |  


{{end}}
'    
```
