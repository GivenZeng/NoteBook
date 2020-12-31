* https://zhuanlan.zhihu.com/p/27690280
* http://blog.cyeam.com/golang/2017/06/12/expvar

提供了一个标准的公有变量接口Var。操作是线程安全的
* 它支持对变量的基本操作，修改、查询这些；
* 整形类型，可以用来做计数器；
* 操作都是线程安全的。这点很不错。相信大家都自己整过全局变量，除了变量还得整的锁，自己写确实挺麻烦的；
* 此外还提供了调试接口，/debug/vars。它能够展示所有通过这个包创建的变量；
* 所有的变量都是Var类型，可以自己通过实现这个接口扩展其它的类型；

### Var接口
```
type Var interface {
        // String returns a valid JSON value for the variable.
        // Types with String methods that do not return valid JSON
        // (such as time.Time) must not be used as a Var.
        String() string
}
```

### 发布变量和获取变量
```
func Publish(name string, v Var)
func Get(name string) Var
```
### 新建变量
内置了Float\String\Map\Int等。如Float
```
func NewFloat(name string) *Float
func (v *Float) Add(delta float64)
func (v *Float) Set(value float64)
func (v *Float) String() string
func (v *Float) Value() float64
```
其他类型看官方文档


### 遍历所有全局变量
```
func Do(f func(KeyValue))
```


### Func
无法理解
```
type Func func() interface{}
func (f Func) String() string
func (f Func) Value() interface{}
```
