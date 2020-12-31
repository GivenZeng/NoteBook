可以看readme

# 进程内缓存

> 线程安全的
> 该lib直接封装 github.com/coocood/freecache 而成
> 支持更上层的分片，可指定不同的分片数量，内部分片算法使用 murmur3 mod n

# Exmaple
```
func main() {
	size := 512 * 1024 * 1024 // 512MB
	cache := NewCache(size, 1, 5)
	if nil == cache {
		t.Errorf("Initialize inner cache failed.")
	}
	defer cache.Close()

	testKey := "just4test"
	testValue := "just4test"
	ttl := 10 // 10 second

	// set an item
	err := cache.Set(testKey, []byte(testValue), ttl)
	if nil != err {
		return
	}

	// get an item
	value, err := cache.Get(testKey)
	if nil != err {
		return
	}

	// delete an item
	affected := cache.Del(testKey)
	if !affected {
		return
	}
}

```
