查询用户安装列表

python

[有米iOS广告安装查询接口文档](https://conf.umlife.net/pages/viewpage.action?pageId=19078791&preview=%2F19078791%2F28639302%2FYouMi+iOS+%E5%B9%BF%E5%91%8A%E5%AE%89%E8%A3%85%E6%9F%A5%E8%AF%A2%E6%8E%A5%E5%8F%A3%E6%96%87%E6%A1%A3+v1.20.pdf)

### 代码仓库
https://git.umlife.net/gateway/cpa_query_interface

功能：根据用户的idfa，向广告主查询该用户是否安装了应用。广告主应该按照《有米iOS广告安装查询接口文档》提供接口，但是部分广告主的接口不遵循文档建议的，因此需要对这部分广告主的接口进行特殊配置。

机器：国内 log_01   ~/vhost

### 配置方法
见该项目的readme，主要是请求参数的配置、请求方法、回调结果解析

### 部署方法
应该使用https://codeship-awscn.umlife.net 系统进行发布。以下代码是在国内跳板机执行（并不推荐这种方法发布）
```
cd ~/ansible/gwupdater
ansible-playbook cpa_query_interface-pull.yml
```
