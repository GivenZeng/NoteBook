搞不懂。应该是存储到一个map中吧


### 使用
```
if c.Shards <= 1 {
		cache = gocache.New(c.TTL, c.CleanupInterval, c.MaxItemCount)
	} else {
		cache = gocache.NewSharded(c.Shards, c.TTL, c.CleanupInterval, c.MaxItemCount)
	}
	if c.NoCounter {
		return cache, nil
	}
	counter := mitrics.NewCounter("objcache", c.CounterTags...)
	mitrics.Register(func() map[string]float64 {
		return map[string]float64{
			"obj_cnt": float64(cache.ItemCount()),
		}
	}, "objcache", c.CounterTags...)
	cache = InstrumentObjCache(counter, cache)
	return cache, nil
```
