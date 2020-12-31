定义并实现了一个redisserver处理器。关于redisserver的使用请等待文档完善或查看代码
```
func (a *Adfetch) HandleRedis(method string, args [][]byte) ([]byte, error)

主要功能：使用类redis的查询方法查询adfetch里面缓存的数据（adfetch里面的数据使用的是map和链表、树来存储）
功能包括：
  根据参数里面的ctid获取特定的某条广告；
  根据要求筛选广告（如国家、联盟等）并以相应格式返回；
  获取缓存数目；
  获取所有缓存的ctid；


在该方法中，根据method对参数args进行解析，然后从adfetch缓存中提取数据；

method包括：GET、UGET、get、len、LEN、CTIDS

```
关于args是如何生成尚未知道。
