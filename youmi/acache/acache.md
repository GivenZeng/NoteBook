该模块一般不直接使用
### acache.go
* 定义了三个接口
  * type ObjCache interface //存储实例
  * type SerdeCache interface  
  * type ByteCache interface //存储[]byte
* SerdeCache、ByteCache接口包含：get\set\del，其中set可以设置ttl
* ObjCache包含：get、set、del、SetWithTTL、ItemCount、Flush
* 三个接口都提供ttl功能

### dummy.go
对三个接口的默认实现（结构体值对包内有效），这些实现内部并未进行任何缓存操作

```
type dummyObjCache struct{}
var DummyObjCache dummyObjCache
```

### instrument.go
* 定义了一个ByteCache接口的实现体
* 定义了一个ObjCache接口的实现体
* 两个实现体都是有计数的
* 实现体是包内部的，提供获取实例的方法

```
//对ByteCache的实现都是通过cache实现
//counter 记录每此get、set、del的次数，如果有err，也记录err次数，get不到目标，也计数
type instrumentCache struct {
	counter mitrics.Counter
	cache   ByteCache
}
//获取方法
func InstrumentCache(counter mitrics.Counter, cache ByteCache) ByteCache

//对ObjCache的实现都是通过cache实现
//counter 记录每此get、set、del的次数，如果有err，也记录err次数，get不到目标，也计数
type instrumentObjCache struct {
	counter mitrics.Counter
	cache   ObjCache
}
func InstrumentObjCache(counter mitrics.Counter, cache ObjCache) ObjCache

```


### layer.go
* 定义一个两层缓存的实现了ByteCache的结构体/类
```
type layeredCache struct {
	l1 ByteCache
	l2 ByteCache
}
func LayeredCache(l1 ByteCache, l2 ByteCache) ByteCache
```

### objcache.go
用户获取一个实现了ObjCache的实例
```
type ObjCacheConf struct {
	Disable         bool          `yaml:"disable"`
	TTL             time.Duration `yaml:"ttl" validate:"nonzero"`
	CleanupInterval time.Duration `yaml:"cleanup_interval"`
	MaxItemCount    int           `yaml:"max_item_cnt"`
	Shards          int           `yaml:"shards"`
	NoCounter       bool          `yaml:"no_counter"`
	CounterTags     []string      `yaml:"counter_tags"`
}
func (c *ObjCacheConf) New() (ObjCache, error)

//底层的ObjCache对象由lib/gocache/cache.go 提供，该cache使用的是map缓存
```

### redis.go
一个底层使用aredis.Cluster实现的实现了ByteCache接口的类，因此，其使用必须要先实例化一个aredis.Cluster对象
```
type redisCache struct {
	aredis.Cluster
}
func RedisCache(cluster aredis.Cluster) ByteCache {
	return redisCache{cluster}
}
```

### serde.go
用于将对象以[]Byte形式存储或从缓存获取[]Byte 然后转化为对象
```
type serdeCache struct {
	serde serde.Serde
	cache ByteCache
}

func NewSerdeCache(serde serde.Serde, cache ByteCache) SerdeCache {
	return serdeCache{serde, cache}
}
因此需要有一个ByteCache实例才能使用
```
