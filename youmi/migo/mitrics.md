## 简介
用于计数（废话）。例子：tag1，tag1_value,...,  cnt_name,cnt_value

### hash.go
* 有两个模块内函数，用于获取string或者[]string的hash值
* hash出是hash
* string/[]string 哈希的对象
```
func hashop(hash uint64, s string) uint64
func hashops(hash uint64, l []string) uint64

```

### rt.go


### mitrics.go
提供一个counter、repoter接口
```
// all methods should be thread-safe
type Counter interface {
	Name() string
	With(labelValues ...string) Counter
	Add(field string, delta float64)
	Batch(map[string]float64)
	// 返回维度数目
	Cardinality() int
	// 返回并重置所有数据
	Report() Points
}

type Reporter interface {
	Name() string
	Report() Points
}
```

### 使用方法
example:为名字为api的这个计数器的code自增1
* 初始化apiCounter:=mitrics.NewCounter("api")  //该创建方式会将counter注册到register，register是一个handler，并注册到DefaultServeMux
* apiCounter.With("code", migo.Itoa(code)).Add("cnt", 1)
* counter.With("src", req.Src).Batch(m)
* counter.withe("k1",v1,"k2","v2".....).Add("cnt_name",float64)
* 一般，如果提供计数器查询的方法，应该adn.StartLaddr(\*laddr)以使DefaultServeMux生效
