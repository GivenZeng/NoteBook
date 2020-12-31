### 请求controller
* 用于构建处理外部请求的基础:
 - SDK通用上报参数的处理
 - 非业务相关的处理逻辑, 如日志, 限流, 等等

* 使用
```
	mux := new(ahttp.ServeMux)
	mux.HandleFunc("/v3/eff", ymapi.Handler("eff", conf.Timeout, eff.HandleEff))  
  adn.StartLaddr(*laddr)
  migo.Panic(ahttp.ListenAndServe(*addr, mux))
```
* HandleEff实现具体的业务功能：HandleEff(ctx \*ymapi.Context)
* ymapi.Handler:func Handler(name string, timeout time.Duration, f func(ctx \*Context)) http.HandlerFunc

### Context
```
// 一次请求相关的东西
type Context struct {
	context.Context

	//{
	// 内部传递的一些上下文参数
	// TODO 避免业务逻辑信息暴露
	Ver       int // 接口版本号
	AdSource  string
	Product   int
	SubAdType int
	AdNum     int
	Mediation bool
	SlotID    string
	AdType    int
	ECPM      float64
	FSP       string
	Dye       string
	At        int             // TODO
	AppCtl    ymmq.AppControl // 渠道ALL控制参数 暂时放这里吧 太恶心了
	//}

	// 解析客户端的请求（s参数）
	YMReq Request // php:$this->params['s']
	//}

	// 通用请求信息
	Param Location

	req *http.Request //
	q   url.Values

	ResponseWriter http.ResponseWriter

	Code int // 错误码
	//Tags []string // mitrics 统计用
}
```
