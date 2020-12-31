## 广告拉取

### 功能
- 请求广告主接口，将广告转化为网盟广告形式，存储到数据库
- 如果一个广告是新广告，就放入链接检测队列，由sdk流量来进行检测；如果sdk长时间没有检测该广告，就使用代理流量来检测

### 流程
- 项目：https://git.umlife.net/gateway-ocean/gateway
    - 流程图：https://git.umlife.net/gateway-ocean/gateway/blob/master/README.md 请认真看完readme

## 相关模块和服务
### adapi
https://git.umlife.net/adsys/adapi

一个python服务，提供接口来对db10的广告、素材、定向设置进行更删查改。具体结构、细节我也不太知道，主要是维护阶段，只在出现故障或者特殊需求的时候才修改过。

这个服务的调用者有：
- [广告入库gateway](https://git.umlife.net/gateway-ocean/gateway)
- [白色后台](https://git.umlife.net/adsys/allblue-admin)：以前网盟的运营系统。
    - 这个系统现在已经不再增加新功能了，基本功能在demeter上也已经有。最常用的功能就是广告拉取记录。无需花费太多精力和时间去研究这个东西。
    - 可以在白色后台查/改api广告的详细信息（调用adapi的接口）（demter也有这些功能，所以出现问题也不用管，直接用demeter就好）
    - 无需修改、维护！！！