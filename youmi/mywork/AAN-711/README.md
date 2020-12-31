需求描述 https://jira.umlife.net/browse/AAN-711
暂时描述需求3、4、6

* 需求3：检测异常的拉取情况
* 需求4：对一天点击次数超过20w的ctid进行报警，一个ctid一天报警一次，30min检测一次
* 需求6：检测广告投放地区错误的ctid-country

需求4、6在同一批kafuka(ac4)文件中，因此可以同时处理;ac4的数据每10min压缩一次
## 需求4、6
### 启动时
读取一天的数据（有些早期数据会被压缩存放）；初始化以下各数据

### 读取文件
* 检测是否有新kafka文件，如果有，则读取
* 对于需求4，只处理at=1的数据
* 对于需求6，只处理code=-1303的数据

### 需求4
* map[ctid]int 记录该ctid点击次数； map[ctid]int 上一次读取到的点击次数； map[ctid]bool 报警状态-今天是否有报警（一天只报警一次）
* loop
  * 减除上一次的点击计数
  * 计算新日志文件的点击次数
  * 更新报警状态
  * 检测ctid点击次数是否超过阈值且map[ctid]bool 为false ，发出钉钉报警；更新报警状态

### 需求6
* map[ctid-country]int 记录投放错误次数；map[ctid]int 上一次读取到的投放错误次数；bool 是可以报警（如果不久前报过一次就不报，间隔作为参数启动程序）
* loop
  * 减除一天前得到的投放错误计数
  * 计算新日志文件的投放错误次数
  * 更新报警状态
  * 检测ctid投放次数是否超过阈值且可报警状态为true ，发出钉钉报警；更新报警状态
  * 附：报警的ctid可以进行排序

## 需求3
只检测offline；数据量较少,数据没有压缩。报警：拉取不完全；3小时未进行拉取行为
* map[aid]datetime 最近拉取时间； map[aid]{total,should_get,page_index,page_index_continuous}；bool是否处于可报警状态
* 根据过去数据初始化以上统计数据
* loop
  * 检测是否有新kafka文件，如果有，则读取
  * 更新拉取时间、拉取数目、报警状态
  * 筛选出异常（拉取不完全；3小时未进行拉取行为）数据并报警；更新报警状态
    - 3小时未进行拉取：now_time-datetime>3h
    - 页面是否连续page_index_continuous==false

待定：以上只是大概思路；具体实现以及数据结构尚未确定；监听新文件可以使用fsnotify
优化：提供接口用于更改阈值、间隔等参数







********************************************
4和6直接读取报表
通过配置规则选取数据


sql
```
select ctid , count(ctid) as clk_count
from
where at = 1
group by ctid


select ctid , country , code
from
where code = -1303
```

templdate
```

```
