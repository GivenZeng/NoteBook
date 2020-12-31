### Logtransformer

由原来[python版本](https://git.umlife.net/gateway-ocean/log_transformer/blob/master/sync_avro_hourly.py)重构而来, 每小时解压指定类型的日志文件，转换成avro格式并上传S3。

具体需求参见 https://conf.umlife.net/pages/viewpage.action?pageId=43225569

日志字段 https://conf.umlife.net/pages/viewpage.action?pageId=43224214

- 遍历日志目录
- 读取数据
- 将数据上传到s3

### TODO
难以用于其他业务的日志转化和上传，因为代码依赖与文件目录结构

优化
- 直接配置文件路径
- 转化与具体golang struct耦合，如果要使用转化函数，还要自定义一个结构，并实现一下方法
	```
	func (r *your_struct) Schema() string 
	func (r *your_struct) Serialize(w io.Writer) error 
	```
### example

```
ver: mysql
    db_dsn: "user:pass@tcp(host:port)/db_name?charset=utf8&parseTime=true&loc=UTC"
    max_open_conn: 16
    max_idle_conn: 8
  redis:
    - "redis://127.0.0.1:6379/12"

alert:
    secret_key: "xxxxxxx-xxxxx"
    receiver: "xxxxxx-xxxxxxxx-xxxxxx"

log_bak_path: "/data2/bak"
schema_file: "etc/gateway_ocean.avsc"

aws_region: "region"
aws_access_id: "xxxxxxxxxxxxxxxxx"
aws_secret: "xxxxxxxxxxxxxx"

s3_bucket: "xxxx"
s3_prefix: "hive/avro"
s3_team: "adn"
```
