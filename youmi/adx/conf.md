配置
```
type Conf struct {
	YMMQ      ymmq.Conf
	Debug     bool                   `yaml:"debug"`
	Timeout   time.Duration          `yaml:"timeout" validate:"nonzero"` // 处理请求的超时时间
	ObjCache  acache.ObjCacheConf    `yaml:"objcache"`
	Upstreams map[string]interface{} `yaml:"upstreams"` // 每个upstream的配置
}

//该objectcache本质是使用map作缓存
type ObjCacheConf struct {
	Disable         bool          `yaml:"disable"`
	TTL             time.Duration `yaml:"ttl" validate:"nonzero"`
	CleanupInterval time.Duration `yaml:"cleanup_interval"`
	MaxItemCount    int           `yaml:"max_item_cnt"`
	Shards          int           `yaml:"shards"`
	NoCounter       bool          `yaml:"no_counter"`
	CounterTags     []string      `yaml:"counter_tags"`
}
```
