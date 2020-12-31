定义和实现了三个结构体
```
type Set struct {
	ks map[string]struct{}  //key为ctid
	m  map[string]*Ad  //key为ctid
}//包好该结构体的增删改查及迭代

//该结构体未发现使用
type Index struct {
	mu   sync.Mutex
	all  map[string]*Ad
	root Node
}

//我不知道这个结构是用来干什么的，大概是用来作缓存的索引查询用吧。类的作用、方法的参数和返回结果都没说明。
// TODO make interface
type Node struct {
	all  *Node            // 全部
	next map[string]*Node // 有该维度属性的
	val  *Set             // leaf node
}
```
