## intro
### GWLog
* 将我们的所有日志的所有字段封装为一个结构体GWLog
* func (d \*GWLog) FillDevice(di dv.DeviceInfo) 填充设备信息:imei，mac地址、android id ios-udid、imsi等，具体意思可以百度
* func (d \*GWLog) EncodeQuery() []byte  将对象变成url-query形式
* 日志各字段定义https://conf.umlife.net/pages/viewpage.action?pageId=51642386

### OrderLog
```
type OrderLog struct {
	Logtype int    `url:"logtype"`
	Version int    `url:"ver"`
	AC      int    `url:"ac"`
	Order   string `url:"order"` //订单的query串
}
func (d *OrderLog) WriteQueryTo(w io.Writer)  //将对象以query格式写入
```
* 关于订单的字段可见https://conf.umlife.net/pages/viewpage.action?pageId=46243105
* Order的定义可见adcb/orderschema/order.go
* 订单：用户广告的一次展示/点击/播放等，包含：广告id、广告位、手机信息、用户id、开发者id及收入、对广告主是否有效等


### memo
不同日志类型存储的目录


### 使用
```
d := AdLog{Logtype: 23, Ver: 3, Ac: 4, Serv: "test"}
d.WriteTo(ioutil.Discard)
```
