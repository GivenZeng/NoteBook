### 简介
对log的封装，可以选择输出到kafka或者标准输出，通过logbus.conf来生成输出流


### 使用
```
type Conf struct {
	Kafka  *mika.Conf
	Stdout bool
}

logWriter, err := conf.New()


................
type mika.Conf struct {
	Topic   string `validate:"nonzero"`
	Brokers []string
	*sarama.Config
}
.............

logWriter.Write([]byte)
logWriter.Close()
```
