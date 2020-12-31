### 简介
一个用于将数据上传到aws dynamodb的工具库：

- 从kafka读取数据
- 将数据暂存到aws dynamodb

### example
conf.yml
```
kafka:
  topics:
    - lazycache
  groupid: 'group_id'
  # kafka broker列表
  brokers:
    - 'host1:port1'
    - 'host2:port2'
  nthread: 128
aws:
  accesskey: aws_access_key
  secretkey: aws_secret_key
  region: request_region
  maxretries: 20
table: 你在dynamodb的表
```


### TODO
可复用性不足，无法在在其他业务中使用。
- 配置优化
	- 配置kafka读取的数据的format，当前只有json
	- 配置需要存储的字段，如果没有指定，则存储所有字段/内容
	- 配置字段的默认值，如datetime等 

### 参考资料
- golang example:https://docs.aws.amazon.com/zh_cn/sdk-for-go/v1/developer-guide/common-examples.html
- golang aws sdk:https://docs.aws.amazon.com/zh_cn/sdk-for-go/v1/developer-guide/cloud9-go.html
- aws IAM:https://aws.amazon.com/cn/iam/
