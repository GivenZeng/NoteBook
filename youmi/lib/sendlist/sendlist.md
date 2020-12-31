### 简介
用于进行实时性要求不高的http请求。我们调用sendlist将SendData写入redis缓存.有另一个服务负责从缓存获取SendData,并进行发送。
问题：如何获取返回结果。还是说只适用于无需返回结果的请求
```
type SendData struct {
	Method   string            `json:"method"`             // 发送的方式 get & post，默认为get
	URL      string            `json:"url"`                // 请求的URL地址
	Content  string            `json:"content,omitempty"`  // 发送的内容，仅Post有效
	Heads    map[string]string `json:"heads,omitempty"`    // 发送时附带的Head，默认为空
	Mark     string            `json:"mark,omitempty"`     // 请求Mark，写日志的时候会做标记
	Redirect bool              `json:"redirect,omitempty"` // 是否允许重定向，默认不允许
	Timeout  int               `json:"timeout,omitempty"`  // 超时时间ttps://git.umlife.net/gateway-ocean/gateway/issues/1
	TimeList []int             `json:"timelist,omitempty"` // 发送延迟的秒数
	FullLog  bool              `json:"fulllog,omitempty"`  // 是否打印全部的log
}

```

### 使用方法
```
//调用该函数初始化与redis的连接
func Init(dsn string) (err error) {
	gPool, err = aredis.Open(dsn)
	return
}

// 将数据写入redis
// TODO MAYBE use redis pipeline for high volume traffic
// see 网盟
func Send(d *SendData) error {
	b, _ := json.Marshal(d)
	_, err := gPool.Do("LPUSH", sendKey, b)
	return err
}

```
