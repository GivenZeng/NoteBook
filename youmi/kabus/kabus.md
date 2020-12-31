消费kafka消息，现在的kabus是将kafka中的数据写到文件

```
type GroupConsumerConf struct {
	Brokers []string `validate:"nonzero"`
	GroupID string   `validate:"nonzero"`
	Topics  []string `validate:"nonzero"`
	*cluster.Config
	Nthread int `validate:"nonzero"`
}

// zbus-server配置文件定义
type zbusCfg struct {
	ServerAddr     string        // 日志传输对外接收地址
	PubSrvAddr     string        // 二次分发pub地址
	HeartbeatAddr  string        // 心跳汇报地址
	DataPath       string        // 数据写入根目录
	GrayDataPath   []string      // 灰度测试数据写入根目录
	LoggerConfig   string        // zbus-server seelog配置文件地址
	LogType        []zbusLogType // 日志类型
	LogTypeDefault string        // 默认日志类型
	Debug          int           // 测试模式
}

type Conf struct {
	Stdin bool
	Kafka mika.GroupConsumerConf
	Zbus  zbusCfg
}

```
### 使用
```
初始化conf
conf := new(Conf)
migo.Panic(miyaml.Load(conf, *confPath))

consumer, err := conf.Kafka.New()
migo.Panic(err)
defer consumer.Close()
go consumer.Run(handle)
```
