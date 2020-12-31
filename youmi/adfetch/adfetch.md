### 功能
* 接收请求广告的请求
* 使用adwatch监听广告池变化
* 使用map缓存部分广告（当然包含del等工作）


```
type AdfetchConf struct {
	Region uint8
	AdCap  *adcap.Conf
	AdSort *adsort.Conf
}

type Adfetch struct {
	conf           AdfetchConf
	adfetchCounter mitrics.Counter
	adwatchCounter mitrics.Counter
	idxmu          sync.RWMutex
	idx            Node
	adsmu          sync.RWMutex
	ads            map[string]*Ad
	filters        []Filter
	adsort         adsort.AdSort
}
```
