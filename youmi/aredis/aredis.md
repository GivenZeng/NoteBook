### 简介
一个redis访问工具，使用了连接池，是对[redigo](https://github.com/garyburd/redigo)的封装

### cluster.go
定义了两个接口
```
type Pool interface {
	Cluster
	// Deprecated
	Get() redis.Conn
}

type Cluster interface {
	Close() error
	// err = redis.ErrNil if not found
	Do(commandName string, args ...interface{}) (reply interface{}, err error)
}
Cluster 接口提供了一个假实现var Dummy dummy
```

### pool.go
是对redigo的封装，提供一个基于redigo的Pool的实现类
```
type Options struct {
	URL string
	// Options
	ConnectTimeout time.Duration
	ReadTimeout    time.Duration
	WriteTimeout   time.Duration
	MaxIdle        int
	IdleTimeout    time.Duration
	MaxActive      int
	Wait           bool
	PingTimeout    time.Duration
	Debug          bool
	NoPing         bool
}
func (c *Options) New() (Pool, error)
```

### 使用方法
* 获取连接池，两种方法
 * 调用该包的func Open(dsn string) (Pool, error)，即可得到一个redis连接池
 *  调用该包的func Parse(dsn string) (\*Options, error) ，然后自定义的修改Options的选项，在调用Options的New方法即可

* 连接池使用方法

```
.....other operation.....
p=aredis.Open(your_dsn)
c := p.Get()  
if c == nil{
    fmt.Println("get a nil conn")
}
//本次缓存操作完成后
defer p.Push(c)

//c.Do("MGET", "key1", "key2")
.....other operation.....

//再也不用缓存了
p.close
```
