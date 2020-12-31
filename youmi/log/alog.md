## 简介
* 对develop的调试信息如何输出/写的定义与实现
* debug 模式下会输出日志所在文件信息
### alog.go
```

type Logger interface {
	WithField(key string, value interface{}) Logger
	WithFields(keyValues ...interface{}) Logger

  // 以下8个方法都是用于将args转化为str
	Debugf(format string, args ...interface{})
	Infof(format string, args ...interface{})
	Warnf(format string, args ...interface{})
	Errorf(format string, args ...interface{})

	Debug(args ...interface{})
	Info(args ...interface{})
	Warn(args ...interface{})
	Error(args ...interface{})
}
```

### log.go 有一个实现了上接口的结构体
```
type entry struct {
	*base //isdebug     out io.Writer
	keyvals []interface{}
}
func (e *entry) log(level string, msg string){
  str=time\level\
  if debug：str+=file line
  将之前调用WithFieldx得到的keyval追加到str
  str+=msg
  将str写入out
}
接口前两个方法WithFieldx将参数存储到 entry.keyvals，在log的时候会将其追加到str，因此每个log都会有这个信息
对接口的8个方法的实现，将得到的字符串作为参数传入log即写入输出流
```

###  hook.go
是一个钩子功能，当entry.log一条error日志、并且在符合时间要求（速度限制）时使用migo.alertover.Client将error日志的title、msg发送到某个地方
```
//配置
是否开启钩子：hooked，这个是根据环境变量初始化的
速度限制和爆发量组合得到的调节器rate.Limiter
发送器

//这些配置都在init函数中初始化
```

###  init.go
提供两个方法
```
//初始化设置，默认日志输出到标准输出流
func init()
//提供一个接口，用于查询设置或更改设置，如`curl -XPUT localhost:laddr/alog?debug=0`
func handleHTTP(w http.ResponseWriter, r *http.Request)
```
## 使用方法
* 设置自己的环境变量，如钩子的数据的目标点"ALERTOVER_DSN"
* 调用log默认初始化方法init.go/init
* 如果需要钩子，调用钩子默认初始化方法hook.go/init
* 如果需要自定义配置：日志输出到非标准输出流，恩，暂时好像没有实现，主要是用于测试，故没有必要实现
* 开启log配置更改接口
* 调用alog的 func Default() Logger函数获取logger
* 使用logger的方法
