资料
  * https://www.ibm.com/developerworks/cn/data/library/bd-zookeeper/index.html
  * https://conf.umlife.net/pages/viewpage.action?pageId=23628965

### 安装zookeeper并启动
* ./usr/local/zookeeper-3.3.6/bin/zkServer.sh start

如果发生FAILED TO WRITE PID的错误，请确认是否有相应权限读写文件。当然你也可以使用sudo

### 启动kafka
* cd /usr/local/kafka_2.11-1.0.0
*  bin/kafka-server-start.sh config/server.properties
