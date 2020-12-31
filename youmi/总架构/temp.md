* sdk->adapj请求广告列表
  - adapj->adfetch
  - adfetch 从广告池拉取广告。监听依赖于mysql_notifier模块
  - adwatcher 监听广告库变动，选取部分广告放入广告池


聚合广告
* 下游投放平台->adin
  - adin->adx
* 展示成功/获取成功->ad_eff

* 广告主回调->adcb


日志数据->zbus(kafka)
* celery/upsert消费
* logtransform将kafka数据写为arvo
  - 后续将arvo转化为parquet
* todo：EMR与聚合日志校准


??
  - api点击跳转
  - adclk
