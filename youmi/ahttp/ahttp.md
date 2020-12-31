### 简介
用来处理http请求、进行http请求

请先阅读golang的http源码，以对官方的实现有一定认知在来看此自定义的handler，servemux

### 服务端

#### ServeMux
一个继承官方ServeMux的自定义multiplexer，只是用来注册自定义的mux而已
方法：
```
func (mux *ServeMux) HandleFunc(pattern string, handler func(http.ResponseWriter, *http.Request)) 
func (mux *ServeMux) Handle(pattern string, handler http.Handler) 
```
二者本质相同，都是往ServeMux注册一个自定义封装的Handler

#### metricHandler
一个自定义的Handler，封装了官方的Handler，具备计数功能：路径+返回码---请求数、路径+返回码---延迟。可以使用以下方法生成：
```
func InstrumentedHandler(path string, handler http.Handler) http.Handler 
```
ServeMux的Handle方法也是调用了该方法来生成要注册的Handler

#### redirect
```
// 重定向，code为重定向状态码，主要有301、308   302、303、307
func Redirect(w http.ResponseWriter, urlStr string, code int) 
```
#### 写响应
```
// 以json方式序列化v，并将其写入w
func WriteResponseJSON(w http.ResponseWriter, v interface{})
```
#### 使用介绍
* 监听函数：func ListenAndServe(addr string, handler http.Handler, fs ...ServeOptionFunc) error
  * 例子1：
  ```
  migo.Panic(ahttp.ListenAndServe(\*addr, ahttp.InstrumentedHandler("path", adcb)))
  ```
  > adcb是一个实现了http.Handler接口的结构的实例。InstrumentedHandler是对一个请求进行计数而已，在此不必了解。如果该接口无需计数，你可以直接将adcb作为第二个参数。

* 处理函数：func WriteResponseJSON(w http.ResponseWriter, v interface{})  
   * 例子：ahttp.WriteResponseJSON(w, map[string]int{"c": code,})
   * 第二个参数可以是一个结构体或是结构体的指针

* 重定向：func Redirect(w http.ResponseWriter, urlStr string, code int)
  * 例子：ahttp.Redirect(w, urlStr, http.StatusFound)//http.StatusFound=302重定向

* 锁： 部分请求不可重入

```
	mux := new(ahttp.ServeMux)
	mux.HandleFunc("/v3/req", ymapi.Handler("req", conf.Timeout, adin.HandleAds))
	mux.HandleFunc("/v3/req_self", ymapi.Handler("req_self", conf.Timeout, adin.HandleSelf))
	migo.Panic(ahttp.ListenAndServe(*addr, mux))
```

### 客户端
用于发送请求

新建一个客户端
  ```
  func NewEndpointClient(endpoint string, client Client) (EndpointClient, error)
  type endpointClient struct  //http.Clent的一个封装子类，可以使用func NewClient(timeout time.Duration) Client获取
  ```
  > endpoint 是请求的url+path，client可以是http.Client或其子类，可以 用ahttp.NewClient(time.Duration)得到；如果要设置请求计数，可以使用func InstrumentClient(counter mitrics.Counter, client Client) Client根据已设置超时的client来获取一个带有计数功能的client

* 新建一个客户端
```
 ahttp.NewEndpointClient(conf.URL, ahttp.NewClient(conf.Timeout))
```
* 客户端发送请求，有多个方法，在此只介绍一个：
```

type EndpointClient interface {
	Client
	// request:请求的参数的结构体，我们回将其转化为json
	DoJSON(ctx context.Context, method string, request interface{}, header http.Header) (*http.Response, error)
	// helper method
	// 将请求参数结构体request转化为json。并将响应body反序列化到response
	JSON(ctx context.Context, method string, request interface{}, header http.Header, response interface{}) error

	// TODO rawquery support
	// 使用raw查询，q为url问号后面的串
	DoQuery(ctx context.Context, q string, header http.Header) (*http.Response, error)
	// 返回基础url，貌似并无用处
	Endpoint() string
}

```
* 例子：res, err := up.Client.DoJSON(ctx, "POST", req, nil)

#### ClientExample
```
c, err := ahttp.NewEndpointClient(conf.URL, ahttp.NewClient(conf.Timeout))
resp, err := c.DoQuery(ctx, raw, header)
或
c.JSON(ctx, "GET", req, header, &response)
```
