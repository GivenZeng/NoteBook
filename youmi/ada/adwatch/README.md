adwatch
### 增量同步
* /home/zjw/go/src/git.umlife.net/adxmi/adn/ada/adnotify/mysql_modify_notifier.go
  使用redis作为消息队列监听db conf变化，
  如果是以下表"targeting",
		"targeting_app",
		"targeting_region",
		"targeting_carrier",
		"schedule_budget",
		//"unit",
		"creative_pay",
		"integration",
		"creative":
    消息包含：表名、更改之后的某个ctid

    "campaign", "link_meta"：消息包含：表名、更改之后的某个cpid。根据cpid查询ctids，并将ctids写入chan


问题：redis是在何时写入的


### 全量同步
/home/zjw/go/src/git.umlife.net/adxmi/adn/ada/adnotify/fsync_notifier.go
* 定时使用ymmq查询正在跑的广告（status=1）的ctids，将ctids顺序打乱，写入chan


### addwork
/home/zjw/go/src/git.umlife.net/adxmi/adn/ada/adnotify/query_pool.go

获取各个notifier的chan的ctids，根据ctid查询广告详细信息，将广告详细信息写入ad_pool

如何查询：/home/zjw/go/src/git.umlife.net/adxmi/adn/ada/pb/query.go
