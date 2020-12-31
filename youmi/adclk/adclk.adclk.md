### 简介
用户点击触发一个请求，该请求回先发送到有米，有米记录响应的点击信息，在将请求重定向到广告主。
将点击信息存储起来，将信息写在kafka，并将该行为发送到celery
```
type ClickCache struct {
	ClickCacheKey `json:",inline" msgpack:",inline"`
	//
	Pid           int `json:"pid" msgpack:"pid"`
	dv.DeviceInfo `json:",inline" msgpack:",inline"`
	//}
	Sv      string `json:"sv,omitempty" msgpack:"sv"`
	Bssid   string `json:"bssid,omitempty" msgpack:"bssid"`
	Time    int64  `json:"time,omitempty" msgpack:"time"`       // 请求广告主的时间
	Chn     string `json:"chn,omitempty" msgpack:"chn"`         // 子渠道号
	User    string `json:"user,omitempty" msgpack:"user"`       // 开发者自定义用户ID
	Ver     int    `json:"ver,omitempty" msgpack:"ver"`         // 写日志时候用的
	IP      string `json:"ip,omitempty" msgpack:"ip"`           // ip地址
	Country string `json:"country,omitempty" msgpack:"country"` // 国家
	// API需要用到的参数
	AffSub1 string `json:"aff_sub1,omitempty" msgpack:"aff_sub1"`
	AffSub2 string `json:"aff_sub2,omitempty" msgpack:"aff_sub2"`
	AffSub3 string `json:"aff_sub3,omitempty" msgpack:"aff_sub3"`
	Flight  string `json:"flight,omitempty" msgpack:"flight"`

	//author zengjiwen@youmi.net
	//添加SubAdType字段
	SubAdType int `json:"sub_adtype,omitempty" msgpack:"sub_adtype"`
}
```
